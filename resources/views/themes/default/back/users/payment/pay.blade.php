@extends('themes.default.layouts.back.student-master')

@section('title', __('Payment Process'))

@section('css')
<style>
    .payment-card {
        border: none;
        box-shadow: 0 0 30px rgba(0,123,255,0.12);
        border-radius: 20px;
        overflow: hidden;
        margin-top: 40px;
        background: #fff;
    }
         .payment-header {
         background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
         color: white;
         padding: 3rem 2rem 2rem 2rem;
         text-align: center;
         border-radius: 20px 20px 0 0;
         position: relative;
     }
    .payment-icon {
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.18);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 3.5rem;
        box-shadow: 0 4px 24px rgba(0,123,255,0.08);
        animation: paymentPulse 2s infinite;
    }
    @keyframes paymentPulse {
        0% { transform: scale(1);}
        50% { transform: scale(1.07);}
        100% { transform: scale(1);}
    }
    .payment-body {
        padding: 2.5rem 2rem 2rem 2rem;
        background: #f8fafd;
        border-radius: 0 0 20px 20px;
        text-align: center;
    }
    .payment-message-title {
        font-weight: 600;
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
                 color: #1e40af;
    }
    .payment-message-desc {
        color: #6c757d;
        font-size: 1rem;
        margin-bottom: 2rem;
    }
    .payment-frame-container {
        min-height: 500px;
        border: 1px solid #e3e6f0;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 12px rgba(0,123,255,0.04);
        margin-top: 1.5rem;
    }
    .payment-frame-container iframe {
        width: 100%;
        height: 500px;
        border: none;
        background: #f8fafd;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="payment-card">
                <div class="payment-header">
                    <div class="payment-icon">
                        <i class="fas fa-credit-card text-white"></i>
                    </div>
                    <h4 class="mb-0">@lang('l.complete_payment')</h4>
                </div>
                <div class="payment-body">
                    {!! $link !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
