@extends('themes.default.layouts.back.master')

@section('title')
    Import LaTeX Assignment
@endsection

@section('css')
    <style>
        .latex-import-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #fff;
            border-radius: 18px;
            padding: 24px 26px;
            margin-bottom: 22px;
            box-shadow: 0 14px 34px rgba(30, 64, 175, 0.16);
        }

        .latex-import-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        }

        .format-reference {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 16px;
        }

        .format-reference code {
            display: block;
            color: #0f172a;
            white-space: pre-wrap;
            line-height: 1.7;
        }

        .import-note {
            border-left: 4px solid #2563eb;
            background: #eff6ff;
            padding: 12px 14px;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="latex-import-header">
                    <h4 class="mb-1 text-white">Import LaTeX Assignment</h4>
                    <p class="mb-0">Upload a controlled LaTeX file, or a ZIP with LaTeX and optional images, into an existing assignment.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Validation error</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card latex-import-card">
                            <div class="card-body">
                                <form action="{{ route('dashboard.admins.lectures-assignments-latex-import-preview') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="lecture_assignment_id" class="form-label fw-bold">Target Assignment</label>
                                        <select class="form-select" id="lecture_assignment_id" name="lecture_assignment_id" required>
                                            <option value="">Select an assignment</option>
                                            @foreach ($assignments as $importAssignment)
                                                @php
                                                    $submissionsCount = (int) ($importAssignment->student_assignments_count ?? 0);
                                                @endphp
                                                <option value="{{ $importAssignment->id }}" {{ old('lecture_assignment_id') == $importAssignment->id ? 'selected' : '' }}>
                                                    {{ $importAssignment->title }}
                                                    @if ($importAssignment->lecture)
                                                        - {{ $importAssignment->lecture->name }}
                                                    @endif
                                                    @if ($submissionsCount > 0)
                                                        - Warning: has {{ $submissionsCount }} student submission{{ $submissionsCount === 1 ? '' : 's' }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">
                                            Assignments with student submissions will be blocked before preview/import.
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="latex_file" class="form-label fw-bold">LaTeX File or ZIP Archive</label>
                                        <input type="file" class="form-control" id="latex_file" name="latex_file" accept=".tex,.txt,.zip" required>
                                        <small class="form-text text-muted">Accepted file types: .tex, .txt, .zip</small>
                                    </div>

                                    <div class="import-note mb-3">
                                        Assignment LaTeX uses no modules and no content/domain. Difficulty defaults to medium if omitted. Points are optional; missing points use the assignment defaults by difficulty.
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i> Preview Import
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card latex-import-card mb-4">
                            <div class="card-body">
                                <h5 class="mb-3">ZIP Structure</h5>
                                <div class="format-reference">
                                    <code>assignment.zip
 |-- assignment.tex
 `-- images/
      |-- q1.png
      `-- q1_exp.png</code>
                                </div>
                            </div>
                        </div>

                        <div class="card latex-import-card">
                            <div class="card-body">
                                <h5 class="mb-3">Image Commands</h5>
                                <div class="format-reference mb-3">
                                    <code>\questionimage{images/q1.png}
\explanationimage{images/q1_exp.png}</code>
                                </div>
                                <p class="text-muted mb-0">Images are optional. ZIP images are validated securely before import. TikZ and raw \includegraphics are not supported.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card latex-import-card mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">Supported Assignment Format</h5>
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <h6>MCQ</h6>
                                <div class="format-reference">
                                    <code>\assignmenttitle{Assignment Title}

\begin{question}
\type{mcq}
\difficulty{easy}
\points{1}
\text{Question text}
\choice[A]{Option A}
\choice[B]{Option B}\correct
\explanation{Explanation text}
\end{question}</code>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h6>Numeric</h6>
                                <div class="format-reference">
                                    <code>\begin{question}
\type{numeric}
\difficulty{medium}
\text{Write \(2/5\) as a decimal.}
\answer{0.4 or 2/5}
\explanation{\(2/5 = 0.4\).}
\end{question}</code>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h6>True/False</h6>
                                <div class="format-reference">
                                    <code>\begin{question}
\type{tf}
\difficulty{easy}
\text{The sum of angles in a triangle is \(180^\circ\).}
\answer{true}
\end{question}</code>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h6>Essay</h6>
                                <div class="format-reference">
                                    <code>\begin{question}
\type{essay}
\difficulty{hard}
\points{5}
\text{Explain your reasoning.}
\explanation{Review the rubric before grading.}
\end{question}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
