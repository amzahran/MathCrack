@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.purchase_course') - {{ $course->name }}
@endsection

@section('css')
    <style>
        .purchase-card {
            border: none;
            box-shadow: 0 0 30px rgba(30, 64, 175, 0.12);
            border-radius: 20px;
            overflow: hidden;
            margin-top: 20px;
        }

        .course-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .course-info {
            padding: 2rem;
            background: #f8fafd;
        }

        .payment-section {
            padding: 2rem;
            background: white;
            border-top: 1px solid #e3e6f0;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        .feature-list li i {
            color: #16a34a;
            margin-right: 0.5rem;
        }

        .price-box {
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .price-amount {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .btn-purchase {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
        }

        .btn-purchase:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 64, 175, 0.3);
            color: white;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="purchase-card">
                <!-- Course Header -->
                <div class="course-header">
                    <h2 class="mb-2">@lang('l.purchase_course')</h2>
                    <h4 class="mb-0 opacity-90">{{ $course->name }}</h4>
                </div>

                <!-- Course Info -->
                <div class="course-info">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">@lang('l.course_details')</h5>

                            @if($course->description)
                                <p class="text-muted mb-3">{{ $course->description }}</p>
                            @endif

                            <h6 class="mb-2">@lang('l.included_features')</h6>
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>@lang('l.full_course_access')</li>
                                <li><i class="fas fa-check"></i>@lang('l.access_all_lectures')</li>
                                <li><i class="fas fa-check"></i>@lang('l.access_all_assignments')</li>
                                <li><i class="fas fa-check"></i>@lang('l.download_all_materials')</li>
                                <li><i class="fas fa-check"></i>@lang('l.lifetime_access')</li>
                                <li><i class="fas fa-check"></i>@lang('l.secure_payment_message')</li>
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <div class="price-box">
                                <div class="price-amount">{{ number_format($course->price, 2) }}</div>
                                <div>@lang('l.currency')</div>
                            </div>

                            <div class="course-stats">
                                <div class="mb-2">
                                    <i class="fas fa-play-circle text-primary me-2"></i>
                                    {{ $course->lectures->count() }} @lang('l.lectures')
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-tasks text-info me-2"></i>
                                    {{ $course->lectures->sum(function($lecture) { return $lecture->assignments->count(); }) }} @lang('l.assignments')
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    @lang('l.unlimited_time')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                                <!-- Payment Section -->
                <div class="payment-section">
                    <form action="{{ route('dashboard.users.process-payment') }}" method="POST">
                        @csrf
                        <input type="hidden" name="lecture_id" value="{{ $course->lectures->first()->id ?? $course->id }}">
                        <input type="hidden" name="payment_type" value="course">

                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2">@lang('l.payment_details')</h5>
                                <p class="text-muted mb-0">@lang('l.secure_payment_message')</p>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-purchase">
                                    <i class="fas fa-credit-card me-2"></i>
                                    دفع الآن
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


