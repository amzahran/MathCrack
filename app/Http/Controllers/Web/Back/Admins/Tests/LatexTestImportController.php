<?php

namespace App\Http\Controllers\Web\Back\Admins\Tests;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Services\Tests\LatexImportArchiveExtractor;
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

    public function preview(
        Request $request,
        LatexTestParser $parser,
        LatexTestImportValidator $validator,
        LatexImportArchiveExtractor $extractor
    )
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

        $originalFileName = $request->file('latex_file')->getClientOriginalName();
        $processed = $this->processUploadedImport($request, $test, $parser, $validator, $extractor);
        $parsed = $processed['parsed'];
        $validation = $processed['validation'];

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
        LatexTestImporter $importer,
        LatexImportArchiveExtractor $extractor
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

        $originalFileName = $request->file('latex_file')->getClientOriginalName();
        $processed = $this->processUploadedImport($request, $test, $parser, $validator, $extractor, cleanupAfterProcessing: false);
        $parsed = $processed['parsed'];
        $validation = $processed['validation'];

        if (!$validation['valid']) {
            $extractor->cleanup($processed['root_path']);

            return view('themes.default.back.admins.tests.latex-import.preview', compact(
                'test',
                'parsed',
                'validation',
                'originalFileName'
            ));
        }

        $payload = $parsed;
        $payload['questions'] = $validation['questions'];

        try {
            $importResult = $importer->import($test, $payload, $processed['archive_images']);
        } finally {
            $extractor->cleanup($processed['root_path']);
        }

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
            'latex_file' => 'required|file|max:25600',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$request->hasFile('latex_file')) {
                return;
            }

            $extension = strtolower($request->file('latex_file')->getClientOriginalExtension());

            if (!in_array($extension, ['tex', 'txt', 'zip'], true)) {
                $validator->errors()->add('latex_file', 'The import file must be a .tex, .txt, or .zip file.');
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

    private function processUploadedImport(
        Request $request,
        Test $test,
        LatexTestParser $parser,
        LatexTestImportValidator $validator,
        LatexImportArchiveExtractor $extractor,
        bool $cleanupAfterProcessing = true
    ): array {
        $file = $request->file('latex_file');
        $extension = strtolower($file->getClientOriginalExtension());
        $rootPath = null;
        $archiveImages = null;

        try {
            if ($extension === 'zip') {
                $archive = $extractor->extract($file->getRealPath());
                $rootPath = $archive['root_path'] ?? null;
                $archiveImages = $archive['images'] ?? [];

                if (!empty($archive['errors'])) {
                    return $this->failedProcessedImport(
                        $archive['errors'],
                        $archive['warnings'] ?? [],
                        $rootPath,
                        $archiveImages
                    );
                }

                $texPath = $archive['tex_path'] ?? null;
                $latex = is_string($texPath) && is_readable($texPath) ? file_get_contents($texPath) : false;

                if ($latex === false) {
                    return $this->failedProcessedImport(
                        ['Unable to read the TeX file inside the uploaded ZIP archive.'],
                        $archive['warnings'] ?? [],
                        $rootPath,
                        $archiveImages
                    );
                }

                $parsed = $parser->parse($latex);
                $parsed['warnings'] = array_values(array_merge($archive['warnings'] ?? [], $parsed['warnings'] ?? []));
                $validation = $validator->validate($test, $parsed, $archiveImages);

                return [
                    'parsed' => $parsed,
                    'validation' => $validation,
                    'archive_images' => $archiveImages,
                    'root_path' => $rootPath,
                ];
            }

            $latex = $this->readUploadedLatex($request);

            if ($latex === null) {
                return $this->failedProcessedImport(['Unable to read the uploaded LaTeX file.']);
            }

            $parsed = $parser->parse($latex);
            $validation = $validator->validate($test, $parsed);

            return [
                'parsed' => $parsed,
                'validation' => $validation,
                'archive_images' => null,
                'root_path' => null,
            ];
        } finally {
            if ($cleanupAfterProcessing) {
                $extractor->cleanup($rootPath);
            }
        }
    }

    private function failedProcessedImport(
        array $errors,
        array $warnings = [],
        ?string $rootPath = null,
        ?array $archiveImages = null
    ): array {
        $parsed = [
            'title' => null,
            'modules' => [],
            'errors' => [],
            'warnings' => $warnings,
        ];

        $validation = [
            'valid' => false,
            'errors' => $errors,
            'warnings' => $warnings,
            'module_summary' => [],
            'questions' => [],
        ];

        return [
            'parsed' => $parsed,
            'validation' => $validation,
            'archive_images' => $archiveImages,
            'root_path' => $rootPath,
        ];
    }
}
