<?php

namespace App\Http\Controllers\Web\Back\Admins\Tests;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Services\Tests\LatexTestImporter;
use App\Services\Tests\LatexTestImportValidator;
use App\Services\Tests\LatexTestParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class LatexTestImportController extends Controller
{
    public function create()
    {
        $tests = Test::withCount('studentTests')
            ->orderBy('student_tests_count')
            ->orderBy('name')
            ->get();

        return view('themes.default.back.admins.tests.latex-import.create', compact('tests'));
    }

    public function preview(Request $request, LatexTestParser $parser, LatexTestImportValidator $validator)
    {
        $inputValidator = $this->validateImportRequest($request);

        if ($inputValidator->fails()) {
            return redirect()->back()
                ->withErrors($inputValidator)
                ->withInput();
        }

        $test = $this->findImportTest((int) $request->input('test_id'));

        if ($this->testHasStudentAttempts($test)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selected test already has student attempts and cannot be imported into.');
        }

        $latex = $this->readUploadedLatex($request);

        if ($latex === null) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to read the uploaded LaTeX file.');
        }

        $parsed = $parser->parse($latex);
        $validation = $validator->validate($test, $parsed);
        $originalFileName = $request->file('latex_file')->getClientOriginalName();

        return view('themes.default.back.admins.tests.latex-import.preview', compact(
            'test',
            'parsed',
            'validation',
            'originalFileName'
        ));
    }

    public function store(
        Request $request,
        LatexTestParser $parser,
        LatexTestImportValidator $validator,
        LatexTestImporter $importer
    ) {
        $inputValidator = $this->validateImportRequest($request);

        if ($inputValidator->fails()) {
            return redirect()->back()
                ->withErrors($inputValidator)
                ->withInput();
        }

        $test = $this->findImportTest((int) $request->input('test_id'));

        if ($this->testHasStudentAttempts($test)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selected test already has student attempts and cannot be imported into.');
        }

        $latex = $this->readUploadedLatex($request);

        if ($latex === null) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to read the uploaded LaTeX file.');
        }

        $parsed = $parser->parse($latex);
        $validation = $validator->validate($test, $parsed);
        $originalFileName = $request->file('latex_file')->getClientOriginalName();

        if (!$validation['valid']) {
            return view('themes.default.back.admins.tests.latex-import.preview', compact(
                'test',
                'parsed',
                'validation',
                'originalFileName'
            ));
        }

        $payload = $parsed;
        $payload['questions'] = $validation['questions'];
        $importResult = $importer->import($test, $payload);

        if (Route::has('dashboard.admins.tests-questions')) {
            return redirect()
                ->route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)])
                ->with('success', 'LaTeX test imported successfully.')
                ->with('import_result', $importResult);
        }

        // TODO: Replace this fallback after the import routes/views are wired into the admin UI.
        return redirect()->back()
            ->with('success', 'LaTeX test imported successfully.')
            ->with('import_result', $importResult);
    }

    private function validateImportRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tests,id',
            'latex_file' => 'required|file|max:5120',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$request->hasFile('latex_file')) {
                return;
            }

            $extension = strtolower($request->file('latex_file')->getClientOriginalExtension());

            if (!in_array($extension, ['tex', 'txt'], true)) {
                $validator->errors()->add('latex_file', 'The LaTeX file must be a .tex or .txt file.');
            }
        });

        return $validator;
    }

    private function findImportTest(int $testId): Test
    {
        return Test::with(['questions', 'studentTests'])->findOrFail($testId);
    }

    private function testHasStudentAttempts(Test $test): bool
    {
        if ($test->relationLoaded('studentTests')) {
            return $test->studentTests->isNotEmpty();
        }

        return $test->studentTests()->exists();
    }

    private function readUploadedLatex(Request $request): ?string
    {
        $file = $request->file('latex_file');

        if (!$file || !$file->isValid()) {
            return null;
        }

        $contents = file_get_contents($file->getRealPath());

        return $contents === false ? null : $contents;
    }
}
