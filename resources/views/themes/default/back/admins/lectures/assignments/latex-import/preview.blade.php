@extends('themes.default.layouts.back.master')

@section('title')
    LaTeX Assignment Import Preview - {{ $assignment->title }}
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

        .summary-tile {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px;
            background: #f8fafc;
        }
    </style>
@endsection

@section('content')
    @php
        $parsedErrors = $parsed['errors'] ?? [];
        $validationErrors = $validation['errors'] ?? [];
        $warnings = array_values(array_unique(array_merge($parsed['warnings'] ?? [], $validation['warnings'] ?? [])));
        $allErrors = array_values(array_unique(array_merge($parsedErrors, $validationErrors)));
        $isValid = (bool) ($validation['valid'] ?? false);
        $summary = $validation['summary'] ?? [];
        $parsedQuestions = array_values($parsed['questions'] ?? []);
        $parsedQuestionLookup = [];

        foreach ($parsedQuestions as $parsedQuestion) {
            if (isset($parsedQuestion['source_index'])) {
                $parsedQuestionLookup[$parsedQuestion['source_index']] = $parsedQuestion;
            }
        }

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
                        <h4 class="mb-1 text-white">LaTeX Assignment Import Preview</h4>
                        <p class="mb-0">File: {{ $originalFileName }}</p>
                    </div>
                    <a href="{{ route('dashboard.admins.lectures-assignments-latex-import') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card latex-preview-card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Selected Assignment Summary</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <strong>Assignment:</strong> {{ $assignment->title }}
                            </div>
                            <div class="col-md-4">
                                <strong>Lecture:</strong> {{ $assignment->lecture->name ?? 'N/A' }}
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

                @if (!empty($allErrors))
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
                        <h5 class="mb-3">Import Summary</h5>
                        <div class="row g-3">
                            <div class="col-md-2"><div class="summary-tile"><strong>{{ $summary['total_questions'] ?? 0 }}</strong><br>Total</div></div>
                            <div class="col-md-2"><div class="summary-tile"><strong>{{ $summary['mcq'] ?? 0 }}</strong><br>MCQ</div></div>
                            <div class="col-md-2"><div class="summary-tile"><strong>{{ $summary['numeric'] ?? 0 }}</strong><br>Numeric</div></div>
                            <div class="col-md-2"><div class="summary-tile"><strong>{{ $summary['tf'] ?? 0 }}</strong><br>TF</div></div>
                            <div class="col-md-2"><div class="summary-tile"><strong>{{ $summary['essay'] ?? 0 }}</strong><br>Essay</div></div>
                            <div class="col-md-2"><div class="summary-tile"><strong>{{ $summary['total_points'] ?? 0 }}</strong><br>Points</div></div>
                        </div>
                    </div>
                </div>

                <div class="card latex-preview-card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Question Preview</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Type</th>
                                        <th>Difficulty</th>
                                        <th>Points</th>
                                        <th>Text</th>
                                        <th>Answer / Options</th>
                                        <th>Question Image</th>
                                        <th>Explanation Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse (($validation['questions'] ?? []) as $question)
                                        @php
                                            $sourceIndex = $question['source_index'] ?? null;
                                            $parsedQuestion = $sourceIndex ? ($parsedQuestionLookup[$sourceIndex] ?? []) : [];
                                            $parsedQuestion = $parsedQuestion ?: ($parsedQuestions[$loop->index] ?? []);
                                            $mergedQuestion = array_merge($parsedQuestion, $question);
                                            $sourceIndex = $mergedQuestion['source_index'] ?? $sourceIndex;
                                            $text = $mergedQuestion['text'] ?? '';
                                            $choices = $mergedQuestion['choices'] ?? [];
                                            $answer = $mergedQuestion['answer'] ?? null;
                                            $explanation = $mergedQuestion['explanation'] ?? null;
                                            $questionImage = $mergedQuestion['question_image_source'] ?? null;
                                            $explanationImage = $mergedQuestion['explanation_image_source'] ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $question['question_order'] ?? '-' }}</td>
                                            <td>{{ $question['type'] ?? '-' }}</td>
                                            <td>{{ $question['difficulty'] ?? '-' }}</td>
                                            <td>{{ $question['points'] ?? '-' }}</td>
                                            <td class="text-excerpt">{{ \Illuminate\Support\Str::limit($text, 120) }}</td>
                                            <td>
                                                @if (($question['type'] ?? null) === 'mcq')
                                                    {{ count($choices) }} option{{ count($choices) === 1 ? '' : 's' }},
                                                    {{ collect($choices)->where('is_correct', true)->count() }} correct
                                                @elseif (($question['type'] ?? null) === 'essay')
                                                    Essay answer optional
                                                @else
                                                    {{ $answer ? \Illuminate\Support\Str::limit($answer, 80) : 'Missing answer' }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ $questionImage ?: 'None' }}
                                                <span class="badge bg-secondary d-block mt-1">{{ $imageReferenceStatus($sourceIndex, $questionImage) }}</span>
                                            </td>
                                            <td>
                                                {{ $explanationImage ?: 'None' }}
                                                <span class="badge bg-secondary d-block mt-1">{{ $imageReferenceStatus($sourceIndex, $explanationImage) }}</span>
                                            </td>
                                        </tr>
                                        <tr class="table-light">
                                            <td colspan="8">
                                                <strong>Explanation:</strong>
                                                <span class="ms-1">{{ $explanation ? \Illuminate\Support\Str::limit($explanation, 180) : 'No explanation' }}</span>
                                                @if ($explanationImage)
                                                    <div class="mt-2 small text-muted">
                                                        Explanation image: {{ $explanationImage }}
                                                        <span class="badge bg-secondary ms-1">{{ $imageReferenceStatus($sourceIndex, $explanationImage) }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No questions found in the uploaded file.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <a href="{{ route('dashboard.admins.lectures-assignments-latex-import') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>

                    @if ($isValid)
                        <div class="card latex-preview-card">
                            <div class="card-body">
                                <p class="mb-3 text-muted">For safety, upload the same LaTeX or ZIP file again to confirm import.</p>
                                <form action="{{ route('dashboard.admins.lectures-assignments-latex-import-store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="lecture_assignment_id" value="{{ $assignment->id }}">
                                    <div class="mb-3">
                                        <label for="latex_file_confirm" class="form-label fw-bold">Confirm Import File</label>
                                        <input type="file" class="form-control" id="latex_file_confirm" name="latex_file" accept=".tex,.txt,.zip" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-file-import me-1"></i> Import Assignment
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
