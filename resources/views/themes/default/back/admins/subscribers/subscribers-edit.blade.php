@extends('themes.default.layouts.back.master')

@section('title')
    {{ __('l.Edit Subscriber') }}
@endsection

@section('css')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">{{ __('l.Subscribers') }} /</span> {{ __('l.Edit') }}
        </h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('l.Edit Subscriber') }}</h5>
                        <a href="{{ route('dashboard.admins.subscribers') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-1"></i>{{ __('l.Back') }}
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session()->get('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('dashboard.admins.subscribers-update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="id" value="{{ encrypt($subscriber->id) }}">

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="email">{{ __('l.Email') }}</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $subscriber->email }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="is_active">{{ __('l.Status') }}</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $subscriber->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">{{ __('l.Active') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">{{ __('l.Date Added') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $subscriber->created_at->format('Y-m-d H:i:s') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">{{ __('l.Unsubscribe Link') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $subscriber->unsubscribe_url }}" readonly>
                                        <button class="btn btn-outline-primary" type="button" id="copy-link" onclick="copyUnsubscribeLink()">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-sm-10">
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
    function copyUnsubscribeLink() {
        const linkInput = document.querySelector('input[value="{{ $subscriber->unsubscribe_url }}"]');
        linkInput.select();
        document.execCommand('copy');

        // Show a tooltip or notification
        const copyBtn = document.getElementById('copy-link');
        const originalInnerHTML = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fa fa-check"></i>';
        setTimeout(() => {
            copyBtn.innerHTML = originalInnerHTML;
        }, 2000);
    }

    // Note: The hidden input with value="0" ensures that when checkbox is unchecked,
    // the form still sends a value, allowing proper boolean validation
</script>
@endsection