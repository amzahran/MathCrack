@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.purchase_test'): {{ $test->name }}
@endsection

@section('css')
    <style>
        .purchase-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
        }

        .purchase-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: -100px;
            width: 200px;
            height: 100%;
            background: rgba(255,255,255,0.1);
            transform: skewX(-15deg);
        }

        .purchase-header h1 {
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 10px;
            color: white !important;
        }

        .purchase-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
            color: white !important;
        }

        .test-preview-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .preview-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .preview-header h3 {
            margin: 0;
            color: #1f2937;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .preview-body {
            padding: 30px;
        }

        .test-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .test-description {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 1rem;
        }

        .test-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .detail-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #3b82f6;
            text-align: center;
        }

        .detail-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 5px;
            line-height: 1;
        }

        .detail-label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .course-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .course-info h6 {
            color: #0c4a6e;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .course-info p {
            color: #0369a1;
            margin: 0;
        }

        .price-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .price-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 20px 25px;
        }

        .price-header h3 {
            margin: 0;
            color: white !important;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .price-body {
            padding: 30px;
            text-align: center;
        }

        .price-display {
            font-size: 4rem;
            font-weight: 700;
            color: #059669;
            margin-bottom: 10px;
            line-height: 1;
        }

        .price-currency {
            font-size: 1.2rem;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .price-features {
            text-align: left;
            margin-bottom: 25px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #1f2937;
        }

        .feature-icon {
            width: 20px;
            height: 20px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
        }

        .payment-methods {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .payment-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .payment-header h3 {
            margin: 0;
            color: #1f2937;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .payment-body {
            padding: 25px;
        }

        .payment-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .payment-option {
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .payment-option:hover {
            border-color: #3b82f6;
            background: #f0f9ff;
        }

        .payment-option.selected {
            border-color: #1e40af;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }

        .payment-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .payment-icon img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 4px;
        }

        .payment-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
        }

        .security-notice {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .security-notice .notice-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .security-notice .notice-icon {
            color: #0369a1;
            font-size: 1.1rem;
        }

        .security-notice .notice-title {
            color: #0c4a6e;
            font-weight: 600;
            margin: 0;
        }

        .security-notice .notice-content {
            color: #0369a1;
            margin: 0;
            font-size: 0.9rem;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            min-width: 200px;
            justify-content: center;
        }

        .btn-success-action {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-success-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-secondary-action {
            background: white;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary-action:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .terms-section {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .terms-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .terms-list li {
            color: #6b7280;
            margin-bottom: 8px;
            position: relative;
            padding-left: 20px;
            font-size: 0.9rem;
        }

        .terms-list li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #3b82f6;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .purchase-header h1 {
                font-size: 2rem;
            }

            .price-display {
                font-size: 3rem;
            }

            .test-details-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .payment-options {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-action {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <!-- Purchase Header -->
        <div class="purchase-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>@lang('l.purchase_test')</h1>
                        <p>@lang('l.secure_payment_process')</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-3">
                            <i class="fas fa-shopping-cart fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Test Preview -->
                <div class="test-preview-card">
                    <div class="preview-header">
                        <h3>@lang('l.test_details')</h3>
                    </div>
                    <div class="preview-body">
                        <!-- Course Info -->
                        <div class="course-info">
                            <h6>@lang('l.course')</h6>
                            <p>{{ $test->course->name ?? '' }}</p>
                        </div>

                        <h2 class="test-title">{{ $test->name }}</h2>

                        @if($test->description)
                            <p class="test-description">{{ $test->description }}</p>
                        @endif

                        <!-- Test Details -->
                        <div class="test-details-grid">
                            <div class="detail-item">
                                <div class="detail-number">{{ $test->total_questions_count }}</div>
                                <div class="detail-label">@lang('l.questions')</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-number">{{ $test->total_time_minutes }}</div>
                                <div class="detail-label">@lang('l.minutes')</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-number">{{ $test->total_score }}</div>
                                <div class="detail-label">@lang('l.points')</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-number">{{ $test->initial_score }}</div>
                                <div class="detail-label">@lang('l.initial_score')</div>
                            </div>
                        </div>

                        <!-- Test Features -->
                        <div class="price-features">
                            <h5 class="mb-3">@lang('l.included_features')</h5>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>@lang('l.unlimited_access_after_purchase')</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>@lang('l.detailed_results_and_analysis')</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>@lang('l.math_equations_support')</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>@lang('l.two_part_test_with_break')</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>@lang('l.auto_save_answers')</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <!-- Payment Methods -->
                <div class="payment-methods">
                    <div class="payment-header">
                        <h3>@lang('l.payment_methods')</h3>
                    </div>
                    <div class="payment-body">
                        <div class="payment-options">
                            <div class="payment-option selected" data-method="kashier">
                                <div class="payment-icon">
                                    <img src="{{ asset('images/payment/kashier.png') }}" alt="Kashier">
                                </div>
                                <div class="payment-name">@lang('l.online_payment')</div>
                            </div>
                            <div class="payment-option" data-method="vodafone">
                                <div class="payment-icon">
                                    <img src="{{ asset('images/payment/vodafone.png') }}" alt="Vodafone Cash">
                                </div>
                                <div class="payment-name">@lang('l.vodafone_cash')</div>
                            </div>
                            <div class="payment-option" data-method="bank">
                                <div class="payment-icon">
                                    <img src="{{ asset('images/payment/bank.png') }}" alt="Bank Transfer">
                                </div>
                                <div class="payment-name">@lang('l.bank_transfer')</div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- Terms and Conditions -->
                <div class="terms-section">
                    <h5 class="terms-title">@lang('l.terms_and_conditions')</h5>
                    <ul class="terms-list">
                        <li>@lang('l.test_purchase_term_1')</li>
                        <li>@lang('l.test_purchase_term_2')</li>
                        <li>@lang('l.test_purchase_term_3')</li>
                        <li>@lang('l.test_purchase_term_4')</li>
                        <li>@lang('l.test_purchase_term_5')</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Price Section -->
                <div class="price-section">
                    <div class="price-header">
                        <h3>@lang('l.purchase_summary')</h3>
                    </div>
                    <div class="price-body">
                        @if($test->price > 0)
                            <div class="price-display">{{ number_format($test->price, 2) }}</div>
                            <div class="price-currency">@lang('l.currency')</div>
                        @else
                            <div class="price-display" style="color: #10b981;">@lang('l.free')</div>
                            <div class="price-currency">@lang('l.no_payment_required')</div>
                        @endif

                        <!-- Security Notice -->
                        <div class="security-notice">
                            <div class="notice-header">
                                <i class="fas fa-shield-alt notice-icon"></i>
                                <h6 class="notice-title">@lang('l.secure_payment')</h6>
                            </div>
                            <div class="notice-content">
                                @lang('l.payment_security_note')
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            @if($test->price > 0)
                                                            <form action="{{ route('dashboard.users.process-payment') }}" method="POST" id="payment-form">
                                @csrf
                                <input type="hidden" name="test_id" value="{{ $test->id }}">
                                <input type="hidden" name="payment_type" value="single_test">

                                    <button type="submit" class="btn-action btn-success-action" id="purchase-btn">
                                        <i class="fas fa-credit-card"></i>
                                        @lang('l.proceed_to_payment')
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('dashboard.users.tests.show', $test->id) }}" class="btn-action btn-success-action">
                                    <i class="fas fa-gift"></i>
                                    @lang('l.access_free_test')
                                </a>
                            @endif

                            <a href="{{ route('dashboard.users.tests') }}" class="btn-action btn-secondary-action">
                                <i class="fas fa-arrow-left"></i>
                                @lang('l.back_to_tests')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Payment method selection
            $('.payment-option').click(function() {
                $('.payment-option').removeClass('selected');
                $(this).addClass('selected');

                const method = $(this).data('method');
                $('#payment-method-input').val(method);

                // Update button text based on payment method
                updateButtonText(method);
            });

            // Add entrance animations
            $('.test-preview-card, .price-section, .payment-methods').each(function(index) {
                $(this).css('opacity', '0').css('transform', 'translateY(20px)').delay(index * 100).animate({
                    opacity: 1
                }, 600).css('transform', 'translateY(0)');
            });
        });

        function updateButtonText(method) {
            const button = $('#purchase-btn');
            const icon = button.find('i');

            switch(method) {
                case 'kashier':
                    icon.removeClass().addClass('fas fa-credit-card');
                    button.find('span').text() || button.append('<span>@lang("l.proceed_to_payment")</span>');
                    break;
                case 'vodafone':
                    icon.removeClass().addClass('fas fa-mobile-alt');
                    button.find('span').text() || button.append('<span>@lang("l.pay_with_vodafone")</span>');
                    break;
                case 'bank':
                    icon.removeClass().addClass('fas fa-university');
                    button.find('span').text() || button.append('<span>@lang("l.bank_transfer")</span>');
                    break;
            }
        }

                // Form submission handling
        $('#payment-form').submit(function(e) {
            const form = this;
            e.preventDefault();

            Swal.fire({
                title: '@lang("l.confirm_purchase")',
                text: '@lang("l.confirm_test_purchase_message")',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '@lang("l.yes_purchase")',
                cancelButtonText: '@lang("l.cancel")'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: '@lang("l.processing_payment")',
                        text: '@lang("l.please_wait")',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    form.submit();
                }
            });
        });
    </script>
@endsection