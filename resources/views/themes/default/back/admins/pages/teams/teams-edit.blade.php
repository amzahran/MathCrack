@extends('themes.default.layouts.back.master')


@section('title')
    {{ __('l.Team Member Edit') }}
@endsection

@section('css')
    <style>
        #imagePreview {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            display: none;
        }
    </style>
@endsection


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <h5 class="card-title">{{ __('l.Team Member Edit') }}</h5>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('dashboard.admins.teams-update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="id" value="{{ $team->id ? encrypt($team->id) : '' }}">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('l.Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" required
                                        value="{{ $team->name }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('l.Job Title') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="job" required
                                        value="{{ $team->job }}">
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="form-label">{{ __('l.Profile Image') }}</label>
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="me-3">
                                            <div class="mb-2">{{ __('l.Current Image') }}:</div>
                                            @if ($team->image)
                                                <img src="{{ asset($team->image) }}" alt="{{ $team->name }}"
                                                    width="100" height="100" class="rounded">
                                            @else
                                                <img src="{{ asset('images/default-user.png') }}"
                                                    alt="{{ $team->name }}" width="100" height="100"
                                                    class="rounded">
                                            @endif
                                        </div>
                                        <div>
                                            <div class="mb-2" id="previewLabel" style="display: none;">
                                                {{ __('l.New Image Preview') }}:</div>
                                            <img id="imagePreview" src="#" alt="{{ __('l.New Image Preview') }}"
                                                width="100" height="100" class="rounded">
                                        </div>
                                    </div>
                                    <input type="file" class="form-control" name="image" id="imageInput"
                                        accept="image/*">
                                    <small
                                        class="text-muted">{{ __('l.Allowed JPG, GIF or PNG. Max size of 800K') }}</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('l.Facebook URL') }}</label>
                                    <input type="url" class="form-control" name="facebook"
                                        value="{{ $team->facebook }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('l.Twitter URL') }}</label>
                                    <input type="url" class="form-control" name="twitter" value="{{ $team->twitter }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('l.Instagram URL') }}</label>
                                    <input type="url" class="form-control" name="instagram"
                                        value="{{ $team->instagram }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('l.LinkedIn URL') }}</label>
                                    <input type="url" class="form-control" name="linkedin"
                                        value="{{ $team->linkedin }}">
                                </div>

                                <div class="col-12 mt-3 text-end">
                                    <a href="{{ route('dashboard.admins.teams') }}"
                                        class="btn btn-outline-secondary me-2">{{ __('l.Cancel') }}</a>
                                    <button type="submit" class="btn btn-primary">{{ __('l.Save Changes') }}</button>
                                </div>
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
            // معاينة الصورة عند اختيارها
            $('#imageInput').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result);
                        $('#imagePreview').css('display', 'block');
                        $('#previewLabel').css('display', 'block');
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
