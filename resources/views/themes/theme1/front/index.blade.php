<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densityDpi=device-dpi" />

    <title>{{ $settings['name'] }}</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($settings['favicon']) }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset($settings['favicon']) }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($settings['favicon']) }}" />
    <link rel="manifest" href="{{ asset($settings['favicon']) }}" />
    <meta name="description" content="{{ $settings['description'] }}" />
    <meta name="keywords" content="{{ $settings['keywords'] }}" />

    <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/venobox.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/animated_barfiller.css')}}">
    <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/slick.css')}}">
    <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/nice-select.css')}}">

    <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/spacing.css')}}">
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/style-rtl.css')}}">
        <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/responsive-rtl.css')}}">
    @else
        <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/style.css')}}">
        <link rel="stylesheet" href="{{asset('assets/themes/theme1/css/responsive.css')}}">
    @endif
</head>

<body>

    <!--=================================
        MAIN MENU START
    ==================================-->
    <nav class="navbar navbar-expand-lg main_menu">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <img src="{{asset($settings['logo'])}}" alt="Eduor" class="img-fluid w-100">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="far fa-bars menu_icon"></i>
                <i class="far fa-times close_icon"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('index')}}">@lang('front.Home')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">@lang('front.About')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#why">@lang('front.Why Us')</a>
                    </li>
                    <li class="nav-item" style="@if(app()->getLocale() == 'ar') margin-right: 25px; @else margin-left:25px; @endif">
                        <a class="nav-link" href="#contact">@lang('front.Contact Us')</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe me-1"></i>
                            {{ strtoupper(LaravelLocalization::getCurrentLocaleNative()) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end animated fadeIn" aria-labelledby="languageDropdown">
                            @foreach ($headerLanguages as $language)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() == $language->code ? 'active' : '' }}" style="height: 40px;"
                                       href="{{ LaravelLocalization::getLocalizedURL($language->code, null, [], true) }}">
                                        {{ strtoupper($language->native) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link common_btn" href="{{route('home')}}">@lang('front.Join for free')</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!--=================================
        MAIN MENU END
    ==================================-->


    <!--=================================
        BANNER START
    ==================================-->
    <section class="tf__banner">
        <div class="tf__banner_bg" style="position:absolute;top:0;right:0;bottom:0;left:0;background: url({{asset('assets/themes/theme1/images/banner_bg.jpg')}}) center/cover no-repeat;{{ app()->getLocale() == 'ar' ? ' transform: scaleX(-1); transform-origin: center;' : '' }} z-index:0; pointer-events:none;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row">
                <div class="col-xl-7 col-lg-8">
                    <div class="tf__banner_text wow fadeInUp" data-wow-duration="1.5s">
                        <h5 class="mb-5">@lang('front.Welcome')!</h5>
                        <h1>@lang('front.title1') <span>@lang('front.title2')</span> @lang('front.title3')</h1>
                        <ul class="d-flex flex-wrap align-items-center mt-5">
                            <li><a class="common_btn" href="{{route('home')}}">@lang('front.Join Now')</a></li>
                            <li>
                                <a class="venobox play_btn" data-autoplay="true" data-vbtype="video"
                                    href="{{$settings['home_video']}}">
                                    <i class="fas fa-play"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--=================================
        BANNER END
    ==================================-->


    <!--=================================
        CATEGORIES START
    ==================================-->
    <section class="tf__categories mt_95">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-md-8 col-lg-6 m-auto wow fadeInUp" data-wow-duration="1.5s">
                    <div class="tf__heading_area mb_15">
                        <h5>@lang('front.Experts Instructors')</h5>
                        <h2></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-md-6 wow fadeInUp" data-wow-duration="1.5s">
                    <div class="tf__single_category light_blue">
                        <div class="tf__single_category_icon">
                            <i class="fal fa-book"></i>
                        </div>
                        <div class="tf__single_category_text">
                            <h3>@lang('front.type1')</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 wow fadeInUp" data-wow-duration="1.5s">
                    <div class="tf__single_category blue">
                        <div class="tf__single_category_icon">
                            <i class="fal fa-book"></i>
                        </div>
                        <div class="tf__single_category_text">
                            <h3>@lang('front.type2')</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 wow fadeInUp" data-wow-duration="1.5s">
                    <div class="tf__single_category green">
                        <div class="tf__single_category_icon">
                            <i class="fal fa-book"></i>
                        </div>
                        <div class="tf__single_category_text">
                            <h3>@lang('front.type3')</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 wow fadeInUp" data-wow-duration="1.5s">
                    <div class="tf__single_category gray">
                        <div class="tf__single_category_icon">
                            <i class="fal fa-book"></i>
                        </div>
                        <div class="tf__single_category_text">
                            <h3>@lang('front.type4')</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 wow fadeInUp" data-wow-duration="1.5s">
                    <div class="tf__single_category orange">
                        <div class="tf__single_category_icon">
                            <i class="fal fa-book"></i>
                        </div>
                        <div class="tf__single_category_text">
                            <h3>@lang('front.type5')</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 wow fadeInUp" data-wow-duration="1.5s">
                    <div class="tf__single_category red">
                        <div class="tf__single_category_icon">
                            <i class="fal fa-book"></i>
                        </div>
                        <div class="tf__single_category_text">
                            <h3>@lang('front.type6')</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--=================================
        CATEGORIES END
    ==================================-->


    <!--=================================
        ABOUT START
    ==================================-->
    <section id="about" class="tf__about mt_100" style="background: url({{asset('assets/themes/theme1/images/about_bg.png')}});">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-md-9 col-lg-6 wow fadeInLeft" data-wow-duration="1.5s">
                    <div class="tf__about_text">
                        <div class="tf__heading_area tf__heading_area_left mb_25">
                            <h5>@lang('front.About')</h5>
                            <h2>@lang('front.Learn & Grow Your Skills From Anywhere')</h2>
                        </div>
                        <p>@lang('front.EduHive is an innovative e-learning platform designed to empower learners of all ages and backgrounds. With a wide range of courses, expert instructors, and interactive resources, our platform makes it easy to gain new skills and advance your career from anywhere, at any time.') </p>
                        <ul>
                            <li>@lang('front.Learn at your own pace with our flexible course schedules designed to fit your busy lifestyle.')</li>
                            <li>@lang('front.Our instructors are industry experts with years of experience, dedicated to helping you achieve your learning goals.')</li>
                        </ul>
                        <a href="{{route('home')}}" class="common_btn">@lang('front.Join for free') -></a>
                    </div>
                </div>
                <div class="col-xl-6 col-sm-9 col-md-8 col-lg-6 wow fadeInRight" data-wow-duration="1.5s">
                    <div class="tf__about_img">
                        <img src="{{asset('assets/themes/theme1/images/about_img.png')}}" alt="about" class="img-fluid w-100">
                        <div class="text">
                            <i class="far fa-check-circle"></i>
                            <h3>183k+</h3>
                            <p>@lang('front.students')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--=================================
        ABOUT END
    ==================================-->


    <!--=================================
        FAQ START
    ==================================-->
    <section id="why" class="tf__faq mt_100 pt_95 xs_pt_100 pb_100" style="background: url({{asset('assets/themes/theme1/images/faq_bg.png')}});">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-lg-6 wow fadeInLeft" data-wow-duration="1.5s">
                    <div class="tf__faq_img">
                        <img src="{{asset('assets/themes/theme1/images/faq_img.jpg')}}" alt="faqs" class="img-fluid w-100">
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 wow fadeInRight" data-wow-duration="1.5s">
                    <div class="tf__faq_text">
                        <div class="tf__heading_area tf__heading_area_left mb_25">
                            <h5>@lang('front.Why Us')</h5>
                            <h2>@lang('front.We Offer The Best Carrier')</h2>
                        </div>
                        <p class="description"></p>
                        <div class="tf__faq_accordion">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item orange">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true"
                                            aria-controls="collapseOne">
                                            @lang('front.Industry Expert Instructors')
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>@lang('front.Our instructors are seasoned professionals with real-world experience, dedicated to guiding you towards success in your learning journey.')</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item green">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo">
                                            @lang('front.Up-to-Date Course Content')
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                        aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>@lang('front.Stay ahead with our continuously updated courses, designed to reflect the latest trends and industry standards.')</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item red">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree">
                                            @lang('front.Biggest Student Community')
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                        aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>@lang('front.Join a vibrant community of learners, collaborate, share knowledge, and grow together in a supportive environment.')</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--=================================
        FAQ END
    ==================================-->


    <!--=================================
        VIDEO START
    ==================================-->
    <section class="tf__video" style="background: url({{asset('assets/themes/theme1/images/video_bg.jpg')}});">
        <div class="tf__video_overlay pt_100 pb_100">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6 m-auto wow fadeInUp" data-wow-duration="1.5s">
                        <div class="tf__video_text">
                            <a class="venobox play_btn mb-5" data-autoplay="true" data-vbtype="video"
                                href="{{$settings['home_video']}}">
                                <i class="fas fa-play"></i>
                            </a><br>
                            {{-- <h4>Let’s best Something Agency</h4>
                            <p>There are many variations of passages of agency
                                Lorem Ipsum Fasts injecte.</p> --}}
                            <a class="common_btn" href="{{route('home')}}">@lang('front.Join Now')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--=================================
        VIDEO END
    ==================================-->


    <!--=================================
        CONTACT START
    ==================================-->
    <section id="contact" class="tf__contact mt_100 pt_95 xs_pt_100 pb_100" style="background: url({{asset('assets/themes/theme1/images/faq_bg.png')}});" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-lg-6 wow fadeInLeft" data-wow-duration="1.5s">
                    <div class="tf__contact_content">
                        <div class="tf__heading_area tf__heading_area_left mb_25">
                            <h5>@lang('front.Contact Us')</h5>
                            <h2>@lang('front.Get in Touch With Us')</h2>
                        </div>
                        <div class="tf__contact_info">
                            <div class="tf__contact_info_item mb-3">
                                <div class="tf__contact_info_icon">
                                    <span><i class="icon-location"></i> {{ $settings['address'] }}</span>
                                </div>
                            </div>
                            <div class="tf__contact_info_item mb-3">
                                <div class="tf__contact_info_icon">
                                    <span><i class="icon-telephone"></i> <a href="tel:{{ $settings['phone1'] }}">{{ $settings['phone1'] }}</a></span>
                                </div>
                            </div>
                            <div class="tf__contact_info_item mb-3">
                                <div class="tf__contact_info_icon">
                                    <span><i class="icon-email"></i> <a href="mailto:{{ $settings['email1'] }}">{{ $settings['email1'] }}</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 wow fadeInRight" data-wow-duration="1.5s">
                    <div class="tf__contact_form">
                        <form action="{{ route('contact') }}" method="POST" class="tf__contact_form_inner">
                            @csrf
                            <div class="row gutter-y-20">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="@lang('front.Your Name')" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="@lang('front.Email Address')" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="@lang('front.Phone Number')" name="phone" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="@lang('front.Subject')" name="subject" required>
                                </div>
                                <div class="col-md-12">
                                    <textarea name="details" class="form-control" placeholder="@lang('front.Write Message')" required></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="common_btn">
                                        @lang('front.Send Message')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--=================================
        CONTACT END
    ==================================-->


    <!--=================================
        FOOTER START
    ==================================-->
    <footer class="tf__footer" style="background: url({{asset('assets/themes/theme1/images/footer_bg.jpg')}});">
        <div class="tf__footer_overlay pt_75">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-xl-3 col-sm-10 col-md-7 col-lg-6">
                        <div class="tf__footer_logo_area">
                            <a class="footer_logo mb-5" href="{{route('index')}}" >
                                <img src="{{asset($settings['logo'])}}" alt="Eduor" class="img-fluid w-100">
                            </a>
                            {{-- <p>{{$settings['description']}}</p> --}}
                            <ul class="d-flex flex-wrap">
                                <li><a href="{{$settings['facebook']}}"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="{{$settings['linkedin']}}"><i class="fab fa-linkedin-in"></i></a></li>
                                <li><a href="{{$settings['twitter']}}"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="{{$settings['instagram']}}"><i class="fab fa-instagram"></i></a></li>
                                <li><a href="{{$settings['youtube']}}"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-10 col-md-5 col-lg-5">
                        <div class="tf__footer_content xs_mt_50">
                            <h3>Quick Links</h3>
                            <ul>
                                <li><a href="#">@lang('front.Home')</a></li>
                                <li><a href="#about">@lang('front.About')</a></li>
                                <li><a href="#why">@lang('front.Why Us')</a></li>
                                <li><a href="#contact">@lang('front.Contact Us')</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-10 col-md-7 col-lg-col-lg-6">
                        <div class="tf__footer_content xs_mt_30">
                            <h3>Our Contacts</h3>
                            <p>{{$settings['address']}}</p>
                            <p>
                                <span> Phone: {{$settings['phone1']}}</span>
                            </p>
                            <p>
                                <span>Email: {{$settings['email1']}}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-10 col-md-5 col-lg-4 col-lg-5">
                        <div class="tf__footer_content xs_mt_45"><br><br>
                            {{-- <h3>News Letter</h3> --}}
                            <p>{{$settings['description']}}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="tf__copyright">
                            <p>Copyright © all rights reserved.</p>
                            <ul class="d-flex flex-wrap">
                                <li><a href="https://adbrandagency.com">AD Brand Agency</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--=================================
        FOOTER END
    ==================================-->


    <!--=============================
        SCROLL BUTTON START
    ==============================-->
    <div class="tf__scroll_btn">
        @if(app()->getLocale() == 'ar')
            <i class="fa fa-arrow-right"></i>
        @else
            <i class="fa fa-arrow-left"></i>
        @endif
    </div>
    <!--=============================
        SCROLL BUTTON END
    ==============================-->


    <!--jquery library js-->
    <script src="{{asset('assets/themes/theme1/js/jquery-3.6.3.min.js')}}"></script>
    <!--bootstrap js-->
    <script src="{{asset('assets/themes/theme1/js/bootstrap.bundle.min.js')}}"></script>
    <!--font-awesome js-->
    <script src="{{asset('assets/themes/theme1/js/Font-Awesome.js')}}"></script>
    <!--venobox js-->
    <script src="{{asset('assets/themes/theme1/js/venobox.min.js')}}"></script>
    <!--slick slider js-->
    <script src="{{asset('assets/themes/theme1/js/slick.min.js')}}"></script>
    <!--wow js-->
    <script src="{{asset('assets/themes/theme1/js/wow.min.js')}}"></script>
    <!--counterup js-->
    <script src="{{asset('assets/themes/theme1/js/jquery.waypoints.min.js')}}"></script>
    <script src="{{asset('assets/themes/theme1/js/jquery.countup.min.js')}}"></script>
    <!--animated barfiller js-->
    <script src="{{asset('assets/themes/theme1/js/animated_barfiller.js')}}"></script>
    <!--sticky sidebar js-->
    <script src="{{asset('assets/themes/theme1/js/sticky_sidebar.js')}}"></script>
    <!--nice select js-->
    <script src="{{asset('assets/themes/theme1/js/jquery.nice-select.min.js')}}"></script>

    <!--main/custom js-->
    <script src="{{asset('assets/themes/theme1/js/main.js')}}"></script>

    <!-- Toast Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
        @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif
    </div>

    <!-- Initialize Toasts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        var toastList = toastElList.map(function(toastEl) {
            var toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
            toast.show();
            return toast;
        });
    });
    </script>

</body>

</html>