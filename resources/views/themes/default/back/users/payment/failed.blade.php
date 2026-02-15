@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.payment_failed')
@endsection

@section('css')
    <style>
        .failed-card {
            border: none;
            box-shadow: 0 0 30px rgba(220,53,69,0.2);
            border-radius: 20px;
            overflow: hidden;
        }

        .failed-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .failed-icon {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .failed-body {
            padding: 2rem;
        }

        .error-details {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            border-radius: 0 10px 10px 0;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-danger-custom {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            color: white;
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

        .btn-danger-custom:hover,
        .btn-outline-secondary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .help-section {
            background: #e7f3ff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .failed-header {
                padding: 2rem 1rem;
            }

            .failed-body {
                padding: 1.5rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12">
                <div class="card failed-card">
                    <!-- Header -->
                    <div class="failed-header">
                        <div class="failed-icon">
                            <i class="fas fa-times fa-3x"></i>
                        </div>
                        <h2 class="mb-2">@lang('l.payment_failed')</h2>
                        <p class="mb-0 opacity-90">@lang('l.payment_could_not_be_processed')</p>
                    </div>

                    <!-- Body -->
                    <div class="failed-body">
                        <div class="text-center mb-4">
                            <h5 class="text-danger">@lang('l.payment_was_not_successful')</h5>
                            <p class="text-muted">@lang('l.please_try_again_or_contact_support')</p>
                        </div>

                        <!-- تفاصيل الخطأ -->
                        <div class="error-details">
                            <h6 class="mb-3">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>@lang('l.transaction_details')
                            </h6>

                            <div class="detail-row mb-2">
                                <strong>@lang('l.invoice_number'):</strong> #{{ $invoice->id }}
                            </div>

                            <div class="detail-row mb-2">
                                <strong>@lang('l.item'):</strong> {{ $invoice->type_value_display }}
                            </div>

                            <div class="detail-row mb-2">
                                <strong>@lang('l.amount'):</strong> {{ number_format($invoice->amount, 2) }} @lang('l.currency')
                            </div>

                            <div class="detail-row mb-2">
                                <strong>@lang('l.attempt_time'):</strong> {{ $invoice->updated_at->format('Y-m-d H:i') }}
                            </div>

                            <div class="detail-row">
                                <strong>@lang('l.status'):</strong>
                                <span class="badge bg-danger">@lang('l.failed')</span>
                            </div>
                        </div>

                        <!-- أزرار العمل -->
                        <div class="action-buttons">
                            @if($invoice->category == 'quiz')
                                @if($invoice->type == 'single')
                                    @php
                                        $test = \App\Models\Test::find($invoice->type_value);
                                    @endphp
                                    @if($test)
                                        <a href="{{ route('dashboard.users.tests.purchase.test', $test->id) }}"
                                           class="btn btn-danger-custom">
                                            <i class="fas fa-redo me-2"></i>@lang('l.try_again')
                                        </a>
                                    @endif
                                @else
                                    @php
                                        $course = \App\Models\Course::find($invoice->type_value);
                                    @endphp
                                    @if($course)
                                        <a href="{{ route('dashboard.users.tests.purchase.course-tests', $course->id) }}"
                                           class="btn btn-danger-custom">
                                            <i class="fas fa-redo me-2"></i>@lang('l.try_again')
                                        </a>
                                    @endif
                                @endif

                                <a href="{{ route('dashboard.users.tests') }}"
                                   class="btn btn-outline-secondary-custom">
                                    <i class="fas fa-home me-2"></i>@lang('l.back_to_tests')
                                </a>
                            @else
                                @if($invoice->lecture)
                                    <a href="{{ route('dashboard.users.courses-lectures-show', ['id' => encrypt($invoice->lecture->id)]) }}"
                                       class="btn btn-danger-custom">
                                        <i class="fas fa-redo me-2"></i>@lang('l.try_again')
                                    </a>
                                @else
                                    <a href="{{ route('dashboard.users.courses') }}"
                                       class="btn btn-danger-custom">
                                        <i class="fas fa-redo me-2"></i>@lang('l.try_again')
                                    </a>
                                @endif

                                <a href="{{ route('dashboard.users.courses') }}"
                                   class="btn btn-outline-secondary-custom">
                                    <i class="fas fa-home me-2"></i>@lang('l.back_to_courses')
                                </a>
                            @endif
                        </div>

                        <!-- قسم المساعدة -->
                        <div class="help-section">
                            <h6 class="mb-3">
                                <i class="fas fa-question-circle me-2 text-primary"></i>@lang('l.need_help')
                            </h6>
                            <p class="mb-2">@lang('l.payment_failure_reasons'):</p>
                            <ul class="small">
                                <li>@lang('l.insufficient_funds')</li>
                                <li>@lang('l.card_expired_or_blocked')</li>
                                <li>@lang('l.incorrect_payment_details')</li>
                                <li>@lang('l.network_connectivity_issues')</li>
                            </ul>
                            <div class="mt-3">
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-headset me-1"></i>@lang('l.contact_support')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>ش
    </div>
</div>
@endsection
