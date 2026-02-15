@extends('themes.default.layouts.back.master')

@section('title')
    {{ Str::title($contact->name) }}
@endsection

@section('content')
    @can('show contact_us')
        <div class="main-content">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                        <div class="position-relative">
                            <div class="bg-primary text-white py-4 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">
                                        <i class="fas fa-envelope-open-text me-2"></i>@lang('l.Message Details')
                                    </h3>
                                    <span class="badge bg-white text-primary fs-6">
                                        #{{ $contact->id }}
                                    </span>
                                </div>
                            </div>
                            <div class="wave-container">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120">
                                    <path fill="{{$settings['primary_color']}}" fill-opacity="1" d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,0L1360,0C1280,0,1120,0,960,0C800,0,640,0,480,0C320,0,160,0,80,0L0,0Z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="card-body p-4 pt-0">
                            <div class="row g-4">
                                <!-- User Info Section -->
                                <div class="col-md-6">
                                    <div class="contact-info-card bg-light-primary p-4 rounded-4 h-100">
                                        <h5 class="text-primary mb-4"><i class="fas fa-user-circle me-2"></i>@lang('l.Sender Information')</h5>
                                        <div class="mb-4">
                                            <label class="text-muted small mb-1">@lang('l.Name')</label>
                                            <div class="form-control-lg bg-white rounded-3 p-3 border-0 shadow-sm">
                                                {{ $contact->name }}
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="text-muted small mb-1">@lang('l.Email')</label>
                                            <div class="form-control-lg bg-white rounded-3 p-3 border-0 shadow-sm">
                                                {{ $contact->email }}
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="text-muted small mb-1">@lang('l.Phone')</label>
                                            <div class="form-control-lg bg-white rounded-3 p-3 border-0 shadow-sm">
                                                {{ $contact->phone }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Message Section -->
                                <div class="col-md-6">
                                    <div class="message-card bg-light-secondary p-4 rounded-4 h-100">
                                        <h5 class="text-primary mb-4"><i class="fas fa-envelope me-2"></i>@lang('l.Message Details')</h5>
                                        <div class="mb-4">
                                            <label class="text-muted small mb-1">@lang('l.Subject')</label>
                                            <div class="form-control-lg bg-white rounded-3 p-3 border-0 shadow-sm">
                                                {{ $contact->subject}}
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="text-muted small mb-1">@lang('l.Message')</label>
                                            <div class="form-control-lg bg-white rounded-3 p-3 border-0 shadow-sm" style="min-height: 150px;">
                                                {{ $contact->details }}
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="text-muted small">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $contact->created_at->format('Y-m-d H:i') }}
                                            </div>
                                            @if ($contact->status == 1)
                                                <div class="badge bg-info">@lang('l.Read')</div>
                                            @elseif ($contact->status == 2)
                                                <div class="badge bg-success">@lang('l.Contacted')</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <a href="{{ route('dashboard.admins.contacts') }}"
                                   class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm hover-lift">
                                    <i class="fas fa-arrow-left me-2"></i>@lang('l.Back')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('css')
{{-- <style>
    .bg-light-primary {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .bg-light-secondary {
        background-color: rgba(108, 117, 125, 0.05);
    }
    .wave-container {
        margin-top: -2px;
    }
    .hover-lift {
        transition: transform 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-3px);
    }
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style> --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
