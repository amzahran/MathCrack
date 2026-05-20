<?php

namespace App\Http\Controllers\Web\Back\Admins\Lectures;

use App\Http\Controllers\Controller;
use App\Models\LectureAssignment;
use App\Services\Assignments\LatexAssignmentImporter;
use App\Services\Assignments\LatexAssignmentImportValidator;
use App\Services\Assignments\LatexAssignmentParser;
use App\Services\Tests\LatexImportArchiveExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class LatexAssignmentImportController extends Controller
{
    public function create()
    {
        $assignments = LectureAssignment::with('lecture')
            ->withCount('studentAssignments')
            ->orderBy('student_assignments_count')
            ->orderBy('title')
            ->get();

        return view('themes.default.back.admins.lectures.assignments.latex-import.create', compact('assignments'));
    }

    public function preview(
        Request $request,
        LatexAssignmentParser $parser,
        LatexAssignmentImportValidator $validator,
        LatexImportArchiveExtractor $extractor
    ) {
        $inputValidator = $this->validateImportRequest($request);

        if ($inputValidator->fails()) {
            return redirect()->back()
                ->withErrors($inputValidator)
                ->withInput();
        }

        $assignment = $this->findImportAssignment((int) $request->input('lecture_assignment_id'));

        if ($this->assignmentHasStudentSubmissions($assignment)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selected assignment already has student submissions and cannot be imported into.');
        }

        $originalFileName = $request->file('latex_file')->getClientOriginalName();
        $processed = $this->processUploadedImport($request, $assignment, $parser, $validator, $extractor);
        $parsed = $processed['parsed'];
        $validation = $processed['validation'];

        return view('themes.default.back.admins.lectures.assignments.latex-import.preview', compact(
            'assignment',
            'parsed',
            'validation',
            'originalFileName'
        ));
    }

    public function store(
        Request $request,
        LatexAssignmentParser $parser,
        LatexAssignmentImportValidator $validator,
        LatexAssignmentImporter $importer,
        LatexImportArchiveExtractor $extractor
    ) {
        $inputValidator = $this->validateImportRequest($request);

        if ($inputValidator->fails()) {
            return redirect()->back()
                ->withErrors($inputValidator)
                ->withInput();
        }

        $assignment = $this->findImportAssignment((int) $request->input('lecture_assignment_id'));

        if ($this->assignmentHasStudentSubmissions($assignment)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selected assignment already has student submissions and cannot be imported into.');
        }

        $originalFileName = $request->file('latex_file')->getClientOriginalName();
        $processed = $this->processUploadedImport(
            $request,
            $assignment,
            $parser,
            $validator,
            $extractor,
            cleanupAfterProcessing: false
        );

        $parsed = $processed['parsed'];
        $validation = $processed['validation'];

        try {
            if (!$validation['valid']) {
                return view('themes.default.back.admins.lectures.assignments.latex-import.preview', compact(
                    'assignment',
                    'parsed',
                    'validation',
                    'originalFileName'
                ));
            }

            $importResult = $importer->import(
                $assignment,
                [
                    'parsed' => $parsed,
                    'validation' => $validation,
                ],
                $processed['archive_images']
            );
        } finally {
            $extractor->cleanup($processed['root_path']);
        }

        if (Route::has('dashboard.admins.lectures-questions')) {
            return redirect()
                ->route('dashboard.admins.lectures-questions', ['id' => encrypt($assignment->id)])
                ->with('success', 'LaTeX assignment imported successfully.')
                ->with('import_result', $importResult);
        }

        // TODO: Replace this fallback after the assignment import routes are wired into the admin UI.
        return redirect()->back()
            ->with('success', 'LaTeX assignment imported successfully.')
            ->with('import_result', $importResult);
    }

    private function validateImportRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lecture_assignment_id' => 'required|exists:lecture_assignments,id',
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

    private function findImportAssignment(int $assignmentId): LectureAssignment
    {
        return LectureAssignment::with(['questions', 'studentAssignments'])->findOrFail($assignmentId);
    }

    private function assignmentHasStudentSubmissions(LectureAssignment $assignment): bool
    {
        if ($assignment->relationLoaded('studentAssignments')) {
            return $assignment->studentAssignments->isNotEmpty();
        }

        return $assignment->studentAssignments()->exists();
    }

    private function processUploadedImport(
        Request $request,
        LectureAssignment $assignment,
        LatexAssignmentParser $parser,
        LatexAssignmentImportValidator $validator,
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
                $validation = $validator->validate($assignment, $parsed, $archiveImages);

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
            $validation = $validator->validate($assignment, $parsed);

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

    private function readUploadedLatex(Request $request): ?string
    {
        $file = $request->file('latex_file');

        if (!$file || !$file->isValid()) {
            return null;
        }

        $contents = file_get_contents($file->getRealPath());

        return $contents === false ? null : $contents;
    }

    private function failedProcessedImport(
        array $errors,
        array $warnings = [],
        ?string $rootPath = null,
        ?array $archiveImages = null
    ): array {
        $parsed = [
            'title' => null,
            'questions' => [],
            'errors' => [],
            'warnings' => $warnings,
        ];

        $validation = [
            'valid' => false,
            'errors' => $errors,
            'warnings' => $warnings,
            'questions' => [],
            'summary' => [
                'total_questions' => 0,
                'mcq' => 0,
                'numeric' => 0,
                'tf' => 0,
                'essay' => 0,
                'total_points' => 0,
            ],
        ];

        return [
            'parsed' => $parsed,
            'validation' => $validation,
            'archive_images' => $archiveImages,
            'root_path' => $rootPath,
        ];
    }
}
