@extends('themes.default.layouts.back.master')

@section('title')
    LaTeX Import Preview - {{ $test->name }}
@endsection

@section('css')
    <style>
        .latex-preview-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #fff;
            border-radius: 18px;
            padding: 24px 26px;
            margin-bottom: 22px;
            box-shadow: 0 14px 34px rgba(30, 64, 175, 0.16);
        }

        .latex-preview-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        }

        .text-excerpt {
            max-width: 360px;
            white-space: normal;
        }

        .summary-table th,
        .summary-table td {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    @php
        $parsedErrors = $parsed['errors'] ?? [];
        $validationErrors = $validation['errors'] ?? [];
        $warnings = array_values(array_unique(array_merge($parsed['warnings'] ?? [], $validation['warnings'] ?? [])));
        $isValid = (bool) ($validation['valid'] ?? false);
        $parsedQuestionLookup = [];

        foreach (($parsed['modules'] ?? []) as $parsedModule) {
            foreach (($parsedModule['questions'] ?? []) as $parsedQuestion) {
                if (isset($parsedQuestion['source_index'])) {
                    $parsedQuestionLookup[$parsedQuestion['source_index']] = $parsedQuestion;
                }
            }
        }

        $hasImageReferences = collect($parsedQuestionLookup)->contains(function ($parsedQuestion) {
            return !empty($parsedQuestion['question_image_source']) || !empty($parsedQuestion['explanation_image_source']);
        });

        $allErrors = array_values(array_unique(array_merge($parsedErrors, $validationErrors)));

        $imageReferenceStatus = function ($sourceIndex, $imageSource) use ($allErrors) {
            if (empty($imageSource)) {
                return 'None';
            }

            $needle = 'Question ' . $sourceIndex . ':';

            foreach ($allErrors as $error) {
                if (
                    str_contains($error, $needle)
                    && str_contains($error, (string) $imageSource)
                    && (
                        str_contains($error, 'image not found')
                        || str_contains($error, 'unsupported image path')
                    )
                ) {
                    return 'Not resolved';
                }
            }

            return 'Resolved';
        };
    @endphp

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="latex-preview-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="mb-1 text-white">LaTeX Import Preview</h4>
                        <p class="mb-0">File: {{ $originalFileName }}</p>
                    </div>
                    <a href="{{ route('dashboard.admins.tests-latex-import') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card latex-preview-card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Selected Test Summary</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <strong>Test:</strong> {{ $test->name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Initial Score:</strong> {{ $test->initial_score }}
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                @if ($isValid)
                                    <span class="badge bg-success">Ready to import</span>
                                @else
                                    <span class="badge bg-danger">Needs fixes</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if (!empty($parsedErrors) || !empty($validationErrors))
                    <div class="alert alert-danger">
                        <strong>Errors</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($allErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!empty($warnings))
                    <div class="alert alert-warning">
                        <strong>Warnings</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($warnings as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card latex-preview-card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Module Summary</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered summary-table">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>Expected</th>
                                        <th>Existing</th>
                                        <th>Incoming</th>
                                        <th>Final</th>
                                        <th>Remaining Before Import</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (($validation['module_summary'] ?? []) as $moduleNumber => $summary)
                                        <tr>
                                            <td>Module {{ $moduleNumber }} ({{ $summary['part'] ?? 'part' . $moduleNumber }})</td>
                                            <td>{{ $summary['expected'] ?? 0 }}</td>
                                            <td>{{ $summary['existing'] ?? 0 }}</td>
                                            <td>{{ $summary['incoming'] ?? 0 }}</td>
                                            <td>{{ $summary['final'] ?? 0 }}</td>
                                            <td>{{ $summary['remaining_before_import'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card latex-preview-card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Question Preview</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered summary-table">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>Order</th>
                                        <th>Type</th>
                                        <th>Difficulty</th>
                                        <th>Content</th>
                                        <th>Score</th>
                                        <th>Text</th>
                                        @if ($hasImageReferences)
                                            <th>Question Image</th>
                                            <th>Explanation Image</th>
                                        @endif
                                        <th>Answer / Options</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse (($validation['questions'] ?? []) as $question)
                                        @php
                                            $sourceIndex = $question['source_index'] ?? null;
                                            $parsedQuestion = $sourceIndex && isset($parsedQuestionLookup[$sourceIndex])
                                                ? $parsedQuestionLookup[$sourceIndex]
                                                : [];
                                            $choices = $parsedQuestion['choices'] ?? [];
                                            $correctChoices = collect($choices)->where('is_correct', true)->count();
                                            $answerStatus = ($question['type'] ?? '') === 'mcq'
                                                ? count($choices) . ' options, ' . $correctChoices . ' correct'
                                                : (($parsedQuestion['answer'] ?? '') !== '' ? 'Answer provided' : 'Missing answer');
                                            $excerpt = \Illuminate\Support\Str::limit($parsedQuestion['text'] ?? '', 120);
                                            $questionImageSource = $parsedQuestion['question_image_source'] ?? null;
                                            $explanationImageSource = $parsedQuestion['explanation_image_source'] ?? null;
                                            $questionImageStatus = $imageReferenceStatus($sourceIndex, $questionImageSource);
                                            $explanationImageStatus = $imageReferenceStatus($sourceIndex, $explanationImageSource);
                                        @endphp
                                        <tr>
                                            <td>{{ $question['module_number'] ?? '' }}</td>
                                            <td>{{ $question['question_order'] ?? '' }}</td>
                                            <td>{{ $question['type'] ?? '' }}</td>
                                            <td>{{ $question['difficulty'] ?? '' }}</td>
                                            <td>{{ $question['content'] ?? '' }}</td>
                                            <td>{{ $question['calculated_score'] ?? '' }}</td>
                                            <td class="text-excerpt">{{ $excerpt }}</td>
                                            @if ($hasImageReferences)
                                                <td>
                                                    @if ($questionImageSource)
                                                        <div>{{ $questionImageSource }}</div>
                                                        <span class="badge {{ $questionImageStatus === 'Resolved' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $questionImageStatus }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">None</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($explanationImageSource)
                                                        <div>{{ $explanationImageSource }}</div>
                                                        <span class="badge {{ $explanationImageStatus === 'Resolved' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $explanationImageStatus }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">None</span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td>{{ $answerStatus }}</td>
                                            <td>
                                                @if (($question['status'] ?? '') === 'valid')
                                                    <span class="badge bg-success">Valid</span>
                                                @else
                                                    <span class="badge bg-danger">Invalid</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $hasImageReferences ? 11 : 9 }}" class="text-center text-muted">No questions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if ($isValid)
                    <div class="card latex-preview-card">
                        <div class="card-body">
                            <div class="alert alert-info">
                                For safety, upload the same LaTeX file again to confirm import. The file will be re-parsed and re-validated before any database changes.
                            </div>

                            <form action="{{ route('dashboard.admins.tests-latex-import-store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="test_id" value="{{ $test->id }}">

                                <div class="mb-3">
                                    <label for="latex_file" class="form-label fw-bold">Confirm LaTeX File or ZIP Archive</label>
                                    <input type="file" class="form-control" id="latex_file" name="latex_file" accept=".tex,.txt,.zip" required>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-file-import me-1"></i> Import Questions
                                </button>
                                <a href="{{ route('dashboard.admins.tests-latex-import') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        Import is disabled until all parser and validation errors are fixed.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
