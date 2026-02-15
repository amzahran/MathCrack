@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.purchase_lecture') - {{ $lecture->name }}
@endsection

@section('css')
    <style>
        .purchase-card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 15px;
            overflow: hidden;
        }

        .purchase-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .lecture-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.3);
            margin: 0 auto 1rem;
        }

        .purchase-body {
            padding: 2rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .info-value {
            font-weight: 500;
            color: #333;
        }

        .price-display {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            margin: 1.5rem 0;
        }

        .price-amount {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 0.5rem;
        }

        .price-type {
            color: #666;
            font-size: 1.1rem;
        }

        .purchase-button {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }

        .purchase-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40,167,69,0.4);
            color: white;
        }

        .purchase-button:disabled {
            background: #6c757d;
            transform: none;
            box-shadow: none;
        }

        .features-list {
            list-style: none;
            padding: 0;
        }

        .features-list li {
            padding: 0.5rem 0;
            position: relative;
            padding-left: 2rem;
        }

        .features-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .course-badge {
            background: #17a2b8;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .type-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .type-price { background: #e3f2fd; color: #1976d2; }
        .type-course { background: #f3e5f5; color: #7b1fa2; }
        .type-month { background: #e8f5e8; color: #388e3c; }

        @media (max-width: 768px) {
            .purchase-header, .purchase-body {
                padding: 1.5rem;
            }

            .price-amount {
                font-size: 2rem;
            }

            .info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- العودة -->
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('dashboard.users.courses-lectures', ['id' => encrypt($lecture->course_id)]) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>@lang('l.back_to_course')
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card purchase-card">
                    <!-- Header -->
                    <div class="purchase-header">
                        @if($lecture->image)
                            <img src="{{ asset($lecture->image) }}" alt="{{ $lecture->name }}" class="lecture-image">
                        @else
                            <div class="lecture-image d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.2);">
                                <i class="fas fa-play-circle fa-3x"></i>
                            </div>
                        @endif
                        <h2 class="mb-2">{{ $lecture->name }}</h2>
                        <p class="mb-0 opacity-90">@lang('l.purchase_required_to_access')</p>
                    </div>

                    <!-- Body -->
                    <div class="purchase-body">
                        <!-- معلومات المحاضرة -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle me-2 text-primary"></i>@lang('l.lecture_details')
                            </h5>

                            <div class="info-item">
                                <span class="info-label">@lang('l.lecture_name')</span>
                                <span class="info-value">{{ $lecture->name }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">@lang('l.Course')</span>
                                <span class="info-value">
                                    <span class="course-badge">{{ $lecture->course->name }}</span>
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">@lang('l.Type')</span>
                                <span class="info-value">
                                    <span class="type-badge type-{{ $lecture->type }}">
                                        @if($lecture->type == 'price')
                                            @lang('l.paid_lecture')
                                        @elseif($lecture->type == 'course')
                                            @lang('l.course_subscription')
                                        @elseif($lecture->type == 'month')
                                            @lang('l.monthly_subscription')
                                        @endif
                                    </span>
                                </span>
                            </div>

                            @if($lecture->description)
                            <div class="info-item">
                                <span class="info-label">@lang('l.Description')</span>
                                <span class="info-value">{{ Str::limit($lecture->description, 100) }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- تفاصيل الدفع -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-credit-card me-2 text-success"></i>@lang('l.payment_details')
                            </h5>

                            @php
                                // حساب معلومات الدفع
                                $user = auth()->user();
                                switch ($lecture->type) {
                                    case 'price':
                                        // دفع محاضرة واحدة
                                        $paymentInfo = [
                                            'type' => 'single',
                                            'amount' => $lecture->price,
                                            'description' => __('l.single_lecture_payment')
                                        ];
                                        break;
                                    case 'course':
                                    case 'month':
                                        // دفع الكورس بالكامل (لكل من course و month)
                                        $coursePrice = $lecture->course->price ?? 0;
                                        $paymentInfo = [
                                            'type' => 'course',
                                            'amount' => $coursePrice,
                                            'description' => __('l.full_course_access')
                                        ];
                                        break;
                                    default:
                                        $paymentInfo = [
                                            'type' => 'unknown',
                                            'amount' => 0,
                                            'description' => __('l.unknown_payment_type')
                                        ];
                                }
                            @endphp

                            <div class="price-display">
                                <div class="price-amount">{{ number_format($paymentInfo['amount'], 2) }} @lang('l.currency')</div>
                                <div class="price-type">{{ $paymentInfo['description'] }}</div>
                            </div>

                            <!-- الميزات المشمولة -->
                            <div class="mt-3">
                                <h6 class="mb-2">@lang('l.included_features'):</h6>
                                <ul class="features-list">
                                    @if($lecture->type == 'price')
                                        <li>@lang('l.access_to_this_lecture')</li>
                                        <li>@lang('l.download_materials')</li>
                                        @if($lecture->assignments->count() > 0)
                                            <li>@lang('l.access_to_assignments') ({{ $lecture->assignments->count() }})</li>
                                        @endif
                                        <li>@lang('l.lifetime_access')</li>
                                    @elseif($lecture->type == 'course' || $lecture->type == 'month')
                                        <li>@lang('l.access_to_all_course_lectures')</li>
                                        <li>@lang('l.access_to_all_assignments')</li>
                                        <li>@lang('l.download_all_materials')</li>
                                        <li>@lang('l.lifetime_access')</li>
                                        <li>@lang('l.full_course_purchase')</li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <!-- زر الدفع -->
                        <form action="{{ route('dashboard.users.process-payment') }}" method="POST" id="payment-form">
                            @csrf
                            <input type="hidden" name="lecture_id" value="{{ $lecture->id }}">
                            <input type="hidden" name="payment_type" value="{{ $paymentInfo['type'] }}">
                            <input type="hidden" name="amount" value="{{ $paymentInfo['amount'] }}">

                            <button type="submit" class="purchase-button" id="purchase-btn">
                                <i class="fas fa-credit-card me-2"></i>
                                @lang('l.proceed_to_payment')
                                <span class="ms-2">({{ number_format($paymentInfo['amount'], 2) }} @lang('l.currency'))</span>
                            </button>
                        </form>

                        <!-- معلومات إضافية -->
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                @lang('l.secure_payment_message')
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('purchase-btn');
    const originalText = btn.innerHTML;

    // تعطيل الزر وإظهار loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>@lang("l.processing_payment")...';

    // إرسال النموذج
    setTimeout(() => {
        this.submit();
    }, 1000);
});
</script>
@endsection
