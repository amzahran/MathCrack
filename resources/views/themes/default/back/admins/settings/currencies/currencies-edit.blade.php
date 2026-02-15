@extends('themes.default.layouts.back.master')

@section('title')
    {{ $currency->name }} - {{ __('l.Currency Edit') }}
@endsection

@section('css')
    <style>
        .currency-edit-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            /* background: linear-gradient(135deg, #ffffff 0%, #f0f2f5 100%); */
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .currency-edit-form {
            display: grid;
            gap: 24px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-control {
            /* border: 2px solid #e0e0e0; */
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .switch-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            /* background: #f8f9fa; */
            border-radius: 10px;
            margin: 10px 0;
        }

        .switch-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            /* background: #f8f9fa; */
            border-radius: 10px;
            margin: 10px 0;
        }

        [dir="rtl"] .switch-wrapper {
            flex-direction: row;
        }

        [dir="rtl"] .manual-label {
            order: 3;
        }

        [dir="rtl"] .switch {
            order: 2;
        }

        [dir="rtl"] .auto-label {
            order: 1;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 80px;
            height: 40px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e0e0e0;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 32px;
            width: 32px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(40px);
        }

        [dir="rtl"] .switch-wrapper {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .slider:before {
            right: 4px;
            left: auto;
        }

        [dir="rtl"] input:checked + .slider:before {
            transform: translateX(-40px);
        }

        .switch-labels {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
    </style>
@endsection


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @can('edit settings')
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="currency-edit-container">
                <form action="{{ route('dashboard.admins.currencies-update') }}" method="POST"
                    class="currency-edit-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" value="{{ encrypt($currency->id) }}">

                    <div class="form-group">
                        <label for="name">@lang('l.Currency Name')</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ $currency->name }}" required>
                    </div>

                    <div class="form-group">
                        <label for="symbol">@lang('l.Currency Symbol') <small class="text-muted">($,€,£,etc...)</small></label>
                        <input type="text" class="form-control" id="symbol" name="symbol"
                            value="{{ $currency->symbol }}" required>
                    </div>

                    <div class="form-group">
                        <label for="code">@lang('l.Currency Code')</label>
                        <input type="text" class="form-control" id="code" value="{{ $currency->code }}"
                            disabled>
                    </div>

                    <div class="form-group">
                        <label for="rate">@lang('l.Exchange Rate')</label>
                        <input type="text" class="form-control" id="rate" name="rate"
                            value="{{ $currency->rate }}" {{ $currency->is_manual ? '' : 'disabled' }}>
                    </div>

                    <div class="form-group">
                        <label>@lang('l.Rate Update Method')
                            @if($currency->is_default)
                                <small class="text-muted">(it's default currency, you can't edit)</small>
                            @endif
                        </label>
                        <div class="switch-wrapper">
                            <div class="switch-label manual-label">@lang('l.Manual')</div>
                            <label class="switch">
                                <input type="hidden" name="is_manual" value="0">
                                <input type="checkbox"
                                       id="is_manual"
                                       name="is_manual"
                                       value="1"
                                       {{ $currency->is_manual ? 'checked' : '' }}
                                       {{ $currency->is_default ? 'disabled' : '' }}>
                                <span class="slider"></span>
                            </label>
                            <div class="switch-label auto-label">@lang('l.Automatic')</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('dashboard.admins.currencies') }}" class="btn btn-label-primary">@lang('l.Back')</a>
                        <button type="submit" class="btn btn-primary">@lang('l.Update Currency')</button>
                    </div>
                </form>
            </div>
        @endcan
    </div>
@endsection

@section('js')
    <script>
        window.onload = function() {
            const isManualCheckbox = document.getElementById('is_manual');
            const rateInput = document.getElementById('rate');

            function updateRateInputState() {
                rateInput.disabled = isManualCheckbox.checked;

                if (rateInput.disabled) {
                    rateInput.removeAttribute('name');
                } else {
                    rateInput.setAttribute('name', 'rate');
                }
            }

            updateRateInputState();

            isManualCheckbox.addEventListener('change', function() {
                updateRateInputState();
            });
        }
    </script>
@endsection
