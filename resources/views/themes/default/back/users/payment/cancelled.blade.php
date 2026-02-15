@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.payment_cancelled')
@endsection

@section('css')
    <style>
        .cancelled-card {
            border: none;
            box-shadow: 0 0 30px rgba(255,193,7,0.2);
            border-radius: 20px;
            overflow: hidden;
        }

        .cancelled-header {
            background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
            color: #212529;
            padding: 3rem 2rem;
            text-align: center;
        }

        .cancelled-icon {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .cancelled-body {
            padding: 2rem;
        }

        .info-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            color: #212529;
            font-weight: 500;
            flex: 1;
        }

        .btn-outline-secondary-custom {
            border: 2px solid #6c757d;
            color: #6c757d;
            background: transparent;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            flex: 1;
        }

        .btn-warning-custom:hover,
        .btn-outline-secondary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .cancelled-header {
                padding: 2rem 1rem;
            }

            .cancelled-body {
                padding: 1.5rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card cancelled-card">
                    <!-- Header -->
                    <div class="cancelled-header">
                        <div class="cancelled-icon">
                            <i class="fas fa-pause-circle fa-3x"></i>
                        </div>
                        <h2 class="mb-2">@lang('l.payment_cancelled')</h2>
                        <p class="mb-0 opacity-90">@lang('l.payment_process_was_cancelled')</p>
                    </div>

                    <!-- Body -->
                    <div class="cancelled-body">
                        <div class="text-center mb-4">
                            <h5 class="text-warning">@lang('l.no_charges_applied')</h5>
                            <p class="text-muted">@lang('l.payment_was_cancelled_safely')</p>
                        </div>

                        <!-- معلومات -->
                        <div class="info-box">
                            <h6 class="mb-3">
                                <i class="fas fa-info-circle me-2 text-warning"></i>@lang('l.what_happened')
                            </h6>
                            <p class="mb-2">@lang('l.payment_cancelled_explanation')</p>
                            <ul class="small mb-0">
                                <li>@lang('l.no_money_charged')</li>
                                <li>@lang('l.transaction_not_processed')</li>
                                <li>@lang('l.can_try_again_anytime')</li>
                            </ul>
                        </div>

                        <!-- أزرار العمل -->
                        <div class="action-buttons">
                            <a href="{{ url()->previous() }}"
                               class="btn btn-warning-custom">
                                <i class="fas fa-redo me-2"></i>@lang('l.try_payment_again')
                            </a>

                            <a href="{{ route('dashboard.users.courses') }}"
                               class="btn btn-outline-secondary-custom">
                                <i class="fas fa-home me-2"></i>@lang('l.back_to_courses')
                            </a>
                        </div>

                        <!-- رسالة تشجيعية -->
                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-heart me-1 text-danger"></i>
                                @lang('l.we_are_here_when_ready')
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
