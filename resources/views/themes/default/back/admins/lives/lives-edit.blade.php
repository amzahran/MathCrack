@extends('themes.default.layouts.back.master')

@section('title', __('l.Edit Live Session'))

@section('content')
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('l.Edit Live Session') }}: {{ $live->name }}</h4>
                    <a href="{{ route('dashboard.admins.lives') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('l.Back to List') }}
                    </a>
                </div>
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
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    <form action="{{ route('dashboard.admins.lives-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PATCH')
                        <input type="hidden" name="id" value="{{ encrypt($live->id) }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('l.Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $live->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">{{ __('l.Course') }} <span class="text-danger">*</span></label>
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value="">{{ __('l.Select Course') }}</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ $live->course_id == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">{{ __('l.Type') }} <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="free" {{ $live->type == 'free' ? 'selected' : '' }}>{{ __('l.Free') }}</option>
                                        <option value="price" {{ $live->type == 'price' ? 'selected' : '' }}>{{ __('l.Paid') }}</option>
                                        {{-- <option value="month" {{ $live->type == 'month' ? 'selected' : '' }}>{{ __('l.Monthly') }}</option>
                                        <option value="course" {{ $live->type == 'course' ? 'selected' : '' }}>{{ __('l.Course') }}</option> --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">{{ __('l.Price') }}</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="{{ $live->price }}" disabled>
                                    <small class="form-text text-muted">{{ __('l.Required only for paid type') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_at" class="form-label">{{ __('l.Start Time') }}</label>
                                    <input type="datetime-local" class="form-control" id="start_at" name="start_at"
                                           value="{{ $live->start_at ? $live->start_at->format('Y-m-d\TH:i') : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">{{ __('l.Duration (minutes)') }}</label>
                                    <input type="number" class="form-control" id="duration" name="duration" min="1" value="{{ $live->duration }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">{{ __('l.Image') }}</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    @if($live->image)
                                        <div class="mt-2">
                                            <img src="{{ asset($live->image) }}" alt="Current Image" class="img-thumbnail" style="max-width: 100px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="link" class="form-label">{{ __('l.Link') }}</label>
                                    <input type="url" class="form-control" id="link" name="link" placeholder="https://..." value="{{ $live->link }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('l.Description') }}</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $live->description }}</textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('l.Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Handle price field visibility
    $('#type').on('change', function() {
        if ($(this).val() === 'price') {
            $('#price').closest('.mb-3').show();
            $('#price').prop('disabled', false);
        } else {
            $('#price').closest('.mb-3').show();
            $('#price').prop('disabled', true);
            $('#price').val('');
        }
    });

    // Initial state
    $('#type').trigger('change');
});
</script>
@endsection
