@extends('themes.default.layouts.back.student-master')

@section('title', __('l.Purchase Live Session') . ' - ' . $live->name)

@section('content')
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="fas fa-shopping-cart"></i>
                        {{ __('l.Purchase Live Session') }}
                    </h4>
                    <a href="{{ route('dashboard.users.lives') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('l.Back to List') }}
                    </a>
                </div>
                <div class="card-body">
                    @if(session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <!-- معلومات اللايف -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('l.Live Session Information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4>{{ $live->name }}</h4>
                                            <p><strong>{{ __('l.Course') }}:</strong> {{ $live->course->name }}</p>
                                            <p><strong>{{ __('l.Type') }}:</strong>
                                                @switch($live->type)
                                                    @case('price')
                                                        <span class="badge bg-primary">{{ __('l.Paid') }}</span>
                                                        @break
                                                    @case('month')
                                                        <span class="badge bg-warning">{{ __('l.Monthly') }}</span>
                                                        @break
                                                    @case('course')
                                                        <span class="badge bg-info">{{ __('l.Course') }}</span>
                                                        @break
                                                @endswitch
                                            </p>
                                            @if($live->start_at)
                                                <p><strong>{{ __('l.Start Time') }}:</strong> {{ $live->start_at->format('Y-m-d H:i') }}</p>
                                            @endif
                                            @if($live->duration)
                                                <p><strong>{{ __('l.Duration') }}:</strong> {{ $live->duration }} {{ __('l.minutes') }}</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            @if($live->image)
                                                <img src="{{ asset($live->image) }}" alt="{{ $live->name }}"
                                                     class="img-fluid rounded" style="max-width: 100%;">
                                            @endif
                                        </div>
                                    </div>

                                    @if($live->description)
                                        <div class="mt-3">
                                            <h6>{{ __('l.Description') }}:</h6>
                                            <p>{{ $live->description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- خيارات الشراء -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('l.Purchase Options') }}</h5>
                                </div>
                                <div class="card-body">
                                    @switch($live->type)
                                        @case('price')
                                            <!-- شراء منفصل -->
                                            <div class="purchase-option">
                                                <h6>{{ __('l.Single Purchase') }}</h6>
                                                <p class="text-muted">{{ __('l.Purchase this live session only') }}</p>
                                                <div class="price-display">
                                                    <span class="price">{{ $live->price }}</span>
                                                    <span class="currency">{{ __('l.currency') }}</span>
                                                </div>
                                                <button type="button" class="btn btn-primary" onclick="purchaseLive('single')">
                                                    <i class="fas fa-shopping-cart"></i> {{ __('l.Purchase') }}
                                                </button>
                                            </div>
                                            @break

                                        @case('month')
                                            <!-- اشتراك شهري -->
                                            <div class="purchase-option">
                                                <h6>{{ __('l.Monthly Subscription') }}</h6>
                                                <p class="text-muted">{{ __('l.Access all monthly content for this course') }}</p>
                                                <div class="price-display">
                                                    <span class="price">{{ $live->course->price }}</span>
                                                    <span class="currency">{{ __('l.currency') }}</span>
                                                    <span class="period">/ {{ __('l.Month') }}</span>
                                                </div>
                                                <button type="button" class="btn btn-warning" onclick="purchaseLive('month')">
                                                    <i class="fas fa-calendar-alt"></i> {{ __('l.Subscribe Monthly') }}
                                                </button>
                                            </div>
                                            @break

                                        @case('course')
                                            <!-- شراء كورس كامل -->
                                            <div class="purchase-option">
                                                <h6>{{ __('l.Full Course Purchase') }}</h6>
                                                <p class="text-muted">{{ __('l.Access all content for this course') }}</p>
                                                <div class="price-display">
                                                    <span class="price">{{ $live->course->price }}</span>
                                                    <span class="currency">{{ __('l.currency') }}</span>
                                                </div>
                                                <button type="button" class="btn btn-info" onclick="purchaseLive('course')">
                                                    <i class="fas fa-graduation-cap"></i> {{ __('l.Purchase Course') }}
                                                </button>
                                            </div>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- ملخص الشراء -->
                            <div class="card sticky-top" style="top: 20px;">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('l.Purchase Summary') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="summary-item">
                                        <span>{{ __('l.Live Session') }}:</span>
                                        <span>{{ $live->name }}</span>
                                    </div>
                                    <div class="summary-item">
                                        <span>{{ __('l.Course') }}:</span>
                                        <span>{{ $live->course->name }}</span>
                                    </div>
                                    <div class="summary-item">
                                        <span>{{ __('l.Type') }}:</span>
                                        <span>
                                            @switch($live->type)
                                                @case('price')
                                                    {{ __('l.Single Purchase') }}
                                                    @break
                                                @case('month')
                                                    {{ __('l.Monthly Subscription') }}
                                                    @break
                                                @case('course')
                                                    {{ __('l.Full Course') }}
                                                    @break
                                            @endswitch
                                        </span>
                                    </div>
                                    <hr>
                                    <div class="summary-item total">
                                        <span>{{ __('l.Total') }}:</span>
                                        <span id="totalPrice">
                                            @switch($live->type)
                                                @case('price')
                                                    {{ $live->price }} {{ __('l.currency') }}
                                                    @break
                                                @case('month')
                                                    {{ $live->course->price }} {{ __('l.currency') }}
                                                    @break
                                                @case('course')
                                                    {{ $live->course->price }} {{ __('l.currency') }}
                                                    @break
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- معلومات إضافية -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('l.What You Get') }}</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.Access to live session') }}</li>
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.Recording after session') }}</li>
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.Support materials') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تأكيد الشراء -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseModalLabel">{{ __('l.Confirm Purchase') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('l.Are you sure you want to proceed with this purchase?') }}</p>
                <div id="purchaseDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('l.Cancel') }}</button>
                <form action="{{ route('dashboard.users.process-payment') }}" method="POST">
                    @csrf
                    @if($live->type === 'price')
                        <input type="hidden" name="live_id" value="{{ $live->id }}">
                    @elseif($live->type === 'month')
                        <input type="hidden" name="monthly_subscription" value="{{ $live->course_id }}">
                        <input type="hidden" name="course_id" value="{{ $live->course_id }}">
                    @elseif($live->type === 'course')
                        <input type="hidden" name="course_id" value="{{ $live->course_id }}">
                    @endif
                    <button type="submit" class="btn btn-primary">{{ __('l.Confirm Purchase') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
let selectedPurchaseType = '';
let selectedPrice = 0;

function purchaseLive(type) {
    selectedPurchaseType = type;

    let details = '';
    let price = 0;

    switch(type) {
        case 'single':
            details = '{{ __("l.Single Purchase") }}: {{ $live->name }}';
            price = {{ $live->price ?? 0 }};
            break;
        case 'month':
            details = '{{ __("l.Monthly Subscription") }}: {{ $live->course->name }}';
            price = {{ $live->course->price ?? 0 }};
            break;
        case 'course':
            details = '{{ __("l.Full Course") }}: {{ $live->course->name }}';
            price = {{ $live->course->price ?? 0 }};
            break;
    }

    selectedPrice = price;

    document.getElementById('purchaseDetails').innerHTML = `
        <div class="alert alert-info">
            <strong>${details}</strong><br>
            <strong>{{ __('l.Price') }}:</strong> ${price} {{ __('l.currency') }}
        </div>
    `;

    // إظهار المودل
    const modal = new bootstrap.Modal(document.getElementById('purchaseModal'));
    modal.show();
}

// إضافة jQuery إذا لم يكن موجوداً
if (typeof $ === 'undefined') {
    // إنشاء jQuery بسيط
    window.$ = function(selector) {
        if (selector.startsWith('#')) {
            return document.getElementById(selector.substring(1));
        }
        return document.querySelector(selector);
    };
}
</script>
@endsection

@section('css')
<style>
.purchase-option {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    text-align: center;
}

.purchase-option:last-child {
    margin-bottom: 0;
}

.price-display {
    margin: 20px 0;
}

.price {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
}

.currency {
    font-size: 1.2rem;
    color: #6c757d;
}

.period {
    font-size: 1rem;
    color: #6c757d;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.summary-item.total {
    font-weight: bold;
    font-size: 1.1rem;
    color: #007bff;
}

.sticky-top {
    position: sticky;
    top: 20px;
}
</style>
@endsection
