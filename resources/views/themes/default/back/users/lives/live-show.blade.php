@extends('themes.default.layouts.back.student-master')

@section('title', $live->name)

@section('content')
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ $live->name }}</h4>
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
                    @if(session()->has('info'))
                        <div class="alert alert-info">
                            {{ session()->get('info') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <!-- معلومات اللايف -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('l.Live Session Information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>{{ __('l.Course') }}:</strong> {{ $live->course->name }}</p>
                                            <p><strong>{{ __('l.Type') }}:</strong>
                                                @switch($live->type)
                                                    @case('free')
                                                        <span class="badge bg-success">{{ __('l.Free') }}</span>
                                                        @break
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
                                            @if($live->price && $live->type === 'price')
                                                <p><strong>{{ __('l.Price') }}:</strong> {{ $live->price }}</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            @if($live->start_at)
                                                <p><strong>{{ __('l.Start Time') }}:</strong> {{ $live->start_at->format('Y-m-d H:i') }}</p>
                                            @endif
                                            @if($live->duration)
                                                <p><strong>{{ __('l.Duration') }}:</strong> {{ $live->duration }} {{ __('l.minutes') }}</p>
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

                            <!-- رابط اللايف -->
                            @if($live->link)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('l.Live Stream Link') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <a href="{{ $live->link }}" target="_blank" class="btn btn-primary btn-lg">
                                                <i class="fas fa-external-link-alt"></i> {{ __('l.Join Live Stream') }}
                                            </a>
                                            <p class="mt-2 text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                {{ __('l.Click to join the live stream in a new tab') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('l.Live Stream') }}</h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ __('l.No live stream link available') }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- معلومات إضافية -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('l.Important Notes') }}</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.Ensure stable internet connection') }}</li>
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.Use headphones for better audio') }}</li>
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.Participate actively in discussions') }}</li>
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.The live stream will open in a new tab') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- صورة اللايف -->
                            @if($live->image)
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <img src="{{ asset($live->image) }}" alt="{{ $live->name }}"
                                             class="img-fluid rounded" style="max-width: 100%;">
                                    </div>
                                </div>
                            @endif

                            <!-- حالة اللايف -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('l.Live Status') }}</h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($live->start_at && $live->duration)
                                        @php
                                            $now = now();
                                            $startTime = $live->start_at;
                                            $endTime = $live->start_at->addMinutes($live->duration);
                                        @endphp

                                        @if($now < $startTime)
                                            <div class="live-status upcoming">
                                                <i class="fas fa-clock text-warning fa-2x"></i>
                                                <h6 class="mt-2">{{ __('l.Upcoming') }}</h6>
                                                <p class="text-muted">{{ $startTime->format('M d, H:i') }}</p>
                                            </div>
                                        @elseif($now >= $startTime && $now <= $endTime)
                                            <div class="live-status active">
                                                <i class="fas fa-broadcast-tower text-danger fa-2x"></i>
                                                <h6 class="mt-2">{{ __('l.Live Now') }}</h6>
                                                <p class="text-success">{{ __('l.Join now!') }}</p>
                                            </div>
                                        @else
                                            <div class="live-status ended">
                                                <i class="fas fa-stop-circle text-secondary fa-2x"></i>
                                                <h6 class="mt-2">{{ __('l.Ended') }}</h6>
                                                <p class="text-muted">{{ $endTime->format('M d, H:i') }}</p>
                                            </div>
                                        @endif
                                    @else
                                        <div class="live-status always">
                                            <i class="fas fa-infinity text-info fa-2x"></i>
                                            <h6 class="mt-2">{{ __('l.Always Available') }}</h6>
                                            <p class="text-info">{{ __('l.No time restrictions') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- معلومات إضافية -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('l.What You Get') }}</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.Access to live session') }}</li>
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.Interactive learning') }}</li>
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.Real-time questions') }}</li>
                                        <li><i class="fas fa-check text-success"></i> {{ __('l.Direct communication') }}</li>
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
@endsection

@section('css')
<style>
.live-status {
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.live-status.upcoming {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
}

.live-status.active {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
}

.live-status.ended {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.live-status.always {
    background-color: #e2e3e5;
    border: 1px solid #d6d8db;
}

.live-status i {
    margin-bottom: 10px;
}

.live-status h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.live-status p {
    margin-bottom: 0;
    font-size: 0.9rem;
}
</style>
@endsection
