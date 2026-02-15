@extends('themes.default.layouts.back.master')

@section('title')
    403 | @lang('l.You are not authorized!')
@endsection

@section('css')
@endsection


@section('content')
    <div class="container-xxl container-p-y mt-5 text-center">
        <div class="misc-wrapper">
            <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem">403</h1>
            <h4 class="mb-2 mx-2">@lang('l.You are not authorized!') 🔐</h4>
            <p class="mb-6 mx-2">@lang('l.You do not have permission to view this page using the credentials that you have provided while login.')</p>
            <p class="mb-6 mx-2">@lang('l.Please contact your site administrator.')</p>
            <a href="{{ route('home') }}" class="btn btn-primary">@lang('l.Back to home')</a>
            <div class="mt-6">
                <img src="{{ asset('assets/themes/default/img/illustrations/girl-with-laptop-light.png') }}"
                    alt="page-misc-not-authorized-light" width="500" class="img-fluid"
                    data-app-light-img="illustrations/girl-with-laptop-light.png"
                    data-app-dark-img="illustrations/girl-with-laptop-dark.png">
            </div>
        </div>
    </div>
@endsection


@section('js')
@endsection
