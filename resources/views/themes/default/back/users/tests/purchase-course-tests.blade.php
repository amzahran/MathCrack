@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.purchase_course_tests'): {{ $course->name }}
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

        .course-overview {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .overview-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .overview-header h3 {
            margin: 0;
            color: #1f2937;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .overview-body {
            padding: 30px;
        }

        .course-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .course-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            border-left: 4px solid #3b82f6;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 5px;
            line-height: 1;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .price-comparison {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .comparison-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 20px 25px;
        }

        .comparison-header h3 {
            margin: 0;
            color: white !important;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .comparison-body {
            padding: 30px;
        }

        .price-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .price-option {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .price-option:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
        }

        .price-option.recommended {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .recommendation-badge {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: #10b981;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .option-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .option-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 10px;
            line-height: 1;
        }

        .option-currency {
            color: #6b7280;
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .option-description {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .savings-highlight {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 15px;
        }

        .savings-text {
            color: #92400e;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .savings-amount {
            color: #d97706;
            font-weight: 700;
        }

        .tests-list {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .tests-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .tests-header h3 {
            margin: 0;
            color: #1f2937;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .tests-body {
            padding: 0;
        }

        .test-item {
            border-bottom: 1px solid #e5e7eb;
            padding: 20px 25px;
            transition: all 0.3s ease;
        }

        .test-item:last-child {
            border-bottom: none;
        }

        .test-item:hover {
            background: #f8fafc;
        }

        .test-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .test-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .test-price {
            font-size: 1rem;
            font-weight: 600;
            color: #059669;
            background: #f0fdf4;
            padding: 4px 12px;
            border-radius: 15px;
            border: 1px solid #bbf7d0;
        }

        .test-description {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .test-stats {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .test-stat {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #6b7280;
            font-size: 0.85rem;
        }

        .test-stat i {
            color: #3b82f6;
        }

        .payment-section {
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
            margin-bottom: 25px;
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

            .price-options {
                grid-template-columns: 1fr;
            }

            .option-price {
                font-size: 2rem;
            }

            .course-stats {
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

            .test-header {
                flex-direction: column;
                gap: 10px;
            }

            .test-stats {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
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
                        <h1>@lang('l.purchase_course_tests')</h1>
                        <p>@lang('l.get_access_to_all_tests')</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-3">
                            <i class="fas fa-graduation-cap fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Course Overview -->
                <div class="course-overview">
                    <div class="overview-header">
                        <h3>@lang('l.course_overview')</h3>
                    </div>
                    <div class="overview-body">
                        <h2 class="course-title">{{ $course->name }}</h2>

                        <div class="course-stats">
                            <div class="stat-item">
                                <div class="stat-number">{{ $tests->count() }}</div>
                                <div class="stat-label">@lang('l.tests')</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">{{ $tests->sum('total_questions') }}</div>
                                <div class="stat-label">@lang('l.total_questions')</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">{{ $tests->sum('total_time') }}</div>
                                <div class="stat-label">@lang('l.total_minutes')</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">{{ $tests->sum('total_score') }}</div>
                                <div class="stat-label">@lang('l.total_points')</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Comparison -->
                <div class="price-comparison">
                    <div class="comparison-header">
                        <h3>@lang('l.pricing_options')</h3>
                    </div>
                    <div class="comparison-body">
                        <div class="price-options">
                            <!-- Individual Purchase -->
                            <div class="price-option">
                                <div class="option-title">@lang('l.individual_purchase')</div>
                                <div class="option-price">{{ number_format($paymentInfo['individual_price'], 2) }}</div>
                                <div class="option-currency">@lang('l.currency')</div>
                                <div class="option-description">
                                    @lang('l.buy_tests_individually')
                                </div>
                            </div>

                            <!-- Course Package -->
                            <div class="price-option recommended">
                                <div class="recommendation-badge">@lang('l.recommended')</div>
                                <div class="option-title">@lang('l.course_package')</div>
                                <div class="option-price">{{ number_format($paymentInfo['amount'], 2) }}</div>
                                <div class="option-currency">@lang('l.currency')</div>
                                <div class="option-description">
                                    @lang('l.get_all_tests_package')
                                </div>

                                @if($paymentInfo['savings'] > 0)
                                    <div class="savings-highlight">
                                        <div class="savings-text">
                                            @lang('l.you_save'): <span class="savings-amount">{{ number_format($paymentInfo['savings'], 2) }} @lang('l.currency')</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tests List -->
                <div class="tests-list">
                    <div class="tests-header">
                        <h3>@lang('l.included_tests') ({{ $tests->count() }})</h3>
                    </div>
                    <div class="tests-body">
                        @foreach($tests as $test)
                            <div class="test-item">
                                <div class="test-header">
                                    <h4 class="test-name">{{ $test['name'] }}</h4>
                                    <div class="test-price">
                                        @if($test['price'] > 0)
                                            {{ number_format($test['price'], 2) }} @lang('l.currency')
                                        @else
                                            @lang('l.free')
                                        @endif
                                    </div>
                                </div>

                                @if($test['description'])
                                    <div class="test-description">{{ Str::limit($test['description'], 100) }}</div>
                                @endif

                                <div class="test-stats">
                                    <div class="test-stat">
                                        <i class="fas fa-question-circle"></i>
                                        <span>{{ $test['total_questions'] }} @lang('l.questions')</span>
                                    </div>
                                    <div class="test-stat">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $test['total_time'] }} @lang('l.minutes')</span>
                                    </div>
                                    <div class="test-stat">
                                        <i class="fas fa-star"></i>
                                        <span>{{ $test['total_score'] }} @lang('l.points')</span>
                                    </div>
                                    @if($test['has_purchased'])
                                        <div class="test-stat" style="color: #10b981;">
                                            <i class="fas fa-check-circle"></i>
                                            <span>@lang('l.purchased')</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="terms-section">
                    <h5 class="terms-title">@lang('l.terms_and_conditions')</h5>
                    <ul class="terms-list">
                        <li>@lang('l.course_tests_purchase_term_1')</li>
                        <li>@lang('l.course_tests_purchase_term_2')</li>
                        <li>@lang('l.course_tests_purchase_term_3')</li>
                        <li>@lang('l.course_tests_purchase_term_4')</li>
                        <li>@lang('l.course_tests_purchase_term_5')</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Payment Section -->
                <div class="payment-section">
                    <div class="payment-header">
                        <h3>@lang('l.complete_purchase')</h3>
                    </div>
                    <div class="payment-body">
                        <!-- Final Price Display -->
                        <div class="text-center mb-4">
                            <div style="font-size: 3rem; font-weight: 700; color: #10b981; margin-bottom: 10px;">
                                {{ number_format($paymentInfo['amount'], 2) }}
                            </div>
                            <div style="color: #6b7280; font-size: 1.1rem; margin-bottom: 15px;">
                                @lang('l.currency')
                            </div>
                            <div style="color: #059669; font-weight: 600;">
                                @lang('l.for_all_tests') {{ $tests->count() }} @lang('l.tests')
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        {{-- <div class="payment-options">
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
                        </div> --}}

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
                            <form action="{{ route('dashboard.users.process-payment') }}" method="POST" id="payment-form">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                <input type="hidden" name="payment_type" value="course_tests">

                                <button type="submit" class="btn-action btn-success-action" id="purchase-btn">
                                    <i class="fas fa-credit-card"></i>
                                    @lang('l.proceed_to_payment')
                                </button>
                            </form>

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
            $('.course-overview, .price-comparison, .tests-list, .payment-section').each(function(index) {
                $(this).css('opacity', '0').css('transform', 'translateY(20px)').delay(index * 100).animate({
                    opacity: 1
                }, 600).css('transform', 'translateY(0)');
            });

            // Animate savings highlight
            if ($('.savings-highlight').length > 0) {
                setInterval(function() {
                    $('.savings-highlight').animate({opacity: 0.7}, 1000).animate({opacity: 1}, 1000);
                }, 3000);
            }
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

            const amount = '{{ number_format($paymentInfo["amount"], 2) }}';
            const testsCount = '{{ $tests->count() }}';

            Swal.fire({
                title: '@lang("l.confirm_purchase")',
                html: `@lang("l.confirm_course_tests_purchase_message")<br><br>
                       <strong>${amount} @lang("l.currency")</strong><br>
                       <small>@lang("l.for") ${testsCount} @lang("l.tests")</small>`,
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
