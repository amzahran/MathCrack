@extends('themes.default.layouts.back.master')

@section('title')
    Import LaTeX Test
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
                <div class="latex-import-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="mb-1 text-white">Import LaTeX Test</h4>
                        <p class="mb-0">Upload a controlled text-only LaTeX file into an existing test.</p>
                    </div>
                    <a href="{{ route('dashboard.admins.tests') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Back to Tests
                    </a>
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
                                <form action="{{ route('dashboard.admins.tests-latex-import-preview') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="test_id" class="form-label fw-bold">Target Test</label>
                                        <select class="form-select" id="test_id" name="test_id" required>
                                            <option value="">Select a test</option>
                                            @foreach ($tests as $importTest)
                                                @php
                                                    $attemptsCount = (int) ($importTest->student_tests_count ?? 0);
                                                @endphp
                                                <option value="{{ $importTest->id }}" {{ old('test_id') == $importTest->id ? 'selected' : '' }}>
                                                    {{ $importTest->name }}
                                                    @if ($attemptsCount > 0)
                                                        - Warning: has {{ $attemptsCount }} student attempt{{ $attemptsCount === 1 ? '' : 's' }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">
                                            Tests with student attempts will be blocked before preview/import.
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="latex_file" class="form-label fw-bold">LaTeX File</label>
                                        <input type="file" class="form-control" id="latex_file" name="latex_file" accept=".tex,.txt" required>
                                        <small class="form-text text-muted">Accepted file types: .tex, .txt</small>
                                    </div>

                                    <div class="import-note mb-3">
                                        Score is not read from LaTeX. Each question score is calculated from the selected Test settings using module number and difficulty.
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i> Preview Import
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card latex-import-card">
                            <div class="card-body">
                                <h5 class="mb-3">Accepted Format Reference</h5>
                                <div class="format-reference mb-3">
                                    <code>\begin{module}{1}
\begin{question}
\type{mcq|numeric|tf}
\difficulty{easy|medium|hard}
\content{algebra|advanced_math|problem_solving_and_data_analysis|geometry_and_trigonometry}
\text{...}
\choice[A]{...}\correct
\answer{...}
\end{question}
\end{module}</code>
                                </div>
                                <div class="alert alert-warning mb-0">
                                    Images, <code>\includegraphics</code>, and TikZ are not supported in the MVP.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
