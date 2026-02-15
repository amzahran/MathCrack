@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Edit') @lang('l.Lecture')
@endsection

@section('css')
@endsection

@section('content')
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @can('edit lectures')
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0">@lang('l.Edit Lecture'): <span class="text-primary">{{ $lecture->name }}</span></h4>
                </div>
                <div>
                    <a href="{{ route('dashboard.admins.lectures') }}" class="btn btn-secondary waves-effect waves-light mb-2">
                        <i class="fa fa-arrow-left ti-xs me-1"></i>
                        @lang('l.Back to Lectures')
                    </a>
                </div>
            </div>

            <div class="card" id="div1" style="padding: 15px;">
                <div class="card-body">
                    <form action="{{ route('dashboard.admins.lectures-update') }}?id={{ encrypt($lecture->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">@lang('l.Lecture Name')</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $lecture->name }}" required>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('name')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">@lang('l.Course')</label>
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value="">@lang('l.Select Course')</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ $lecture->course_id == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('course_id')" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">@lang('l.Lecture Type')</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="free" {{ $lecture->type == 'free' ? 'selected' : '' }}>@lang('l.Free')</option>
                                        <option value="price" {{ $lecture->type == 'price' ? 'selected' : '' }}>@lang('l.Paid')</option>
                                        <option value="month" {{ $lecture->type == 'month' ? 'selected' : '' }}>@lang('l.Monthly')</option>
                                        <option value="course" {{ $lecture->type == 'course' ? 'selected' : '' }}>@lang('l.Course')</option>
                                    </select>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('type')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">@lang('l.Price')</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="{{ $lecture->price }}" {{ $lecture->type == 'free' || $lecture->type == 'course' || $lecture->type == 'month' ? 'disabled' : '' }}>
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('price')" />
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">@lang('l.Description')</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $lecture->description }}</textarea>
                            <x-input-error class="mt-2 error-message" :messages="$errors->get('description')" />
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="video_url" class="form-label">@lang('l.Video URL')</label>
                                    <input type="url" class="form-control" id="video_url" name="video_url" value="{{ $lecture->video_url }}">
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('video_url')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">@lang('l.Image')</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    @if($lecture->image)
                                        <div class="mt-2">
                                            <img src="{{ asset($lecture->image) }}" alt="lecture" width="80" height="80" class="rounded">
                                        </div>
                                    @endif
                                    <x-input-error class="mt-2 error-message" :messages="$errors->get('image')" />
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">@lang('l.Lecture Files')</label>
                            <input type="file" class="form-control" id="files" name="files" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.rar">
                            @if($lecture->files)
                                <div class="mt-2">
                                    <a href="{{ asset($lecture->files) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-download ti-xs me-1"></i>
                                        @lang('l.Download Current File')
                                    </a>
                                </div>
                            @endif
                            <x-input-error class="mt-2 error-message" :messages="$errors->get('files')" />
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save ti-xs me-1"></i>
                                @lang('l.Update')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Handle lecture type change
            $('#type').change(function() {
                var selectedType = $(this).val();
                var priceField = $('#price');
                var priceFieldContainer = priceField.closest('.mb-3');

                // Don't reset price field value for existing lectures unless the user wants to
                if (!priceField.attr('data-original-value')) {
                    priceField.attr('data-original-value', priceField.val());
                }

                if (selectedType === 'free' || selectedType === 'course' || selectedType === 'month') {
                    // Free, Course, and Monthly types - disable price field
                    priceField.prop('disabled', true);
                    priceField.attr('placeholder', '');

                    // Clear value for monthly type only
                    if (selectedType === 'month') {
                        priceField.val('');
                        if (!priceFieldContainer.find('.monthly-note').length) {
                            priceFieldContainer.append('<small class="text-info monthly-note d-block mt-1"><i class="fas fa-info-circle me-1"></i>المحاضرات الشهرية تتطلب اشتراك شهري للكورس (بدون سعر منفصل)</small>');
                        }
                    } else {
                        priceFieldContainer.find('.monthly-note').remove();
                    }
                } else if (selectedType === 'price') {
                    // Paid type - enable price field for amount
                    priceField.prop('disabled', false);
                    priceField.attr('type', 'number');
                    priceField.attr('step', '0.01');
                    priceField.attr('min', '0');
                    priceField.attr('max', '');
                    priceField.attr('placeholder', '@lang("l.Enter amount")');
                    priceFieldContainer.find('.monthly-note').remove();
                }
            });

            // Initialize on page load
            $('#type').trigger('change');
        });
    </script>
@endsection
