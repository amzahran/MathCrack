<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ $settings['name'] }}</title>
        <!-- favicons Icons -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($settings['favicon']) }}" />
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset($settings['favicon']) }}" />
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($settings['favicon']) }}" />
        <link rel="manifest" href="{{ asset($settings['favicon']) }}" />
        <meta name="description" content="{{ $settings['description'] }}" />
        <meta name="keywords" content="{{ $settings['keywords'] }}" />
        <!-- fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cormorant:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
            rel="stylesheet">

        <link rel="stylesheet"
            href="{{ asset('assets/themes/default/front/vendors/bootstrap/css/bootstrap.min.css') }}" />
        <link rel="stylesheet"
            href="{{ asset('assets/themes/default/front/vendors/bootstrap-select/bootstrap-select.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/themes/default/front/vendors/animate/animate.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/themes/default/front/vendors/fontawesome/css/all.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/themes/default/front/vendors/jquery-ui/jquery-ui.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/themes/default/front/vendors/jarallax/jarallax.css') }}" />
        <link rel="stylesheet"
            href="{{ asset('assets/themes/default/front/vendors/jquery-magnific-popup/jquery.magnific-popup.css') }}" />
        <link rel="stylesheet"
            href="{{ asset('assets/themes/default/front/vendors/nouislider/nouislider.min.css') }}" />
        <link rel="stylesheet"
            href="{{ asset('assets/themes/default/front/vendors/nouislider/nouislider.pips.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/themes/default/front/vendors/tiny-slider/tiny-slider.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/themes/default/front/vendors/eduhive-icons/style.css') }}" />
        <link rel="stylesheet"
            href="{{ asset('assets/themes/default/front/vendors/owl-carousel/css/owl.carousel.min.css') }}" />
        <link rel="stylesheet"
            href="{{ asset('assets/themes/default/front/vendors/owl-carousel/css/owl.theme.default.min.css') }}" />

        <!-- template styles -->
        @if (app()->getLocale() == 'ar')
            <link rel="stylesheet" href="{{ asset('assets/themes/default/front/css/eduhive-rtl.css') }}" />
            <link rel="stylesheet" href="{{ asset('assets/themes/default/front/css/eduhive-custom-rtl.css') }}" />
        @else
            <link rel="stylesheet" href="{{ asset('assets/themes/default/front/css/eduhive.css') }}" />
        @endif

<style>
/* =========================
   Contact form
========================= */
.contact-one__form .form-control{
    height:50px;
    background:#F6F6F6;
    border:none;
    padding:0 30px;
    width:100%;
    outline:none;
    color:var(--eduhive-black);
    font-size:16px;
    border-radius:5px;
    margin-bottom:10px;
}
.contact-one__form textarea.form-control{
    height:150px;
    padding-top:20px;
    resize:none;
}

/* =========================
   Toast
========================= */
.toast{opacity:1!important;}
.toast-container{direction:ltr;}
[dir="rtl"] .toast-container{
    left:0;
    right:auto;
}

/* =========================
   Menu spacing
========================= */
@media (min-width:992px){
    .main-menu__list{
        gap:25px!important;
        margin:0 140px!important;
    }
}

/* =========================
   Hero images
   بيضاوي أنيق + الصورة كاملة
========================= */
.main-slider-three__image{
    width:340px;
    height:340px;

    display:flex;
    align-items:center;
    justify-content:center;

    border-radius:50% / 60%;
    overflow:hidden;

    background:#fff;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

.main-slider-three__image img{
    width:100%!important;
    height:100%!important;

    max-width:100%!important;
    max-height:100%!important;

    object-fit:contain!important;
    object-position:center!important;

    border-radius:0!important;
    display:block;
}

/* موبايل */
@media (max-width:991px){
    .main-slider-three__image{
        width:260px;
        height:260px;
        border-radius:50% / 60%;
    }
}

/* =========================
   إلغاء أي دوائر/إطارات إضافية
   بدون تكسير شكل الإطار البيضاوي
========================= */
.main-slider-three__circle,
.main-slider-three__circle-one,
.main-slider-three__circle-two,
.main-slider-three__circle-three,
.main-slider-three__circle-four{
    display:none!important;
}

.main-slider-three__image::before,
.main-slider-three__image::after{
    content:none!important;
}

/* ============================
   REMOVE ALL CIRCULAR EFFECTS
   ============================ */

/* إلغاء أي قص دائري أو شكل بيضاوي */
.main-slider-three__image,
.main-slider-three__image *,
.main-slider-three__image::before,
.main-slider-three__image::after,
.main-slider-three__circle,
.main-slider-three__circle-one,
.main-slider-three__circle-two,
.main-slider-three__circle-three,
.main-slider-three__circle-four,
.main-slider-three__images *,
.main-slider-three__images::before,
.main-slider-three__images::after {
    border-radius: 0 !important;
    clip-path: none !important;
    mask: none !important;
    overflow: visible !important;
    box-shadow: none !important;
    background: none !important;
}

/* تثبيت حجم الصورة بدون قص */
.main-slider-three__image {
    width: 320px !important;
    height: auto !important;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* الصورة نفسها */
.main-slider-three__image img {
    width: 100% !important;
    height: auto !important;
    max-width: 100% !important;
    object-fit: contain !important;
}

/* إلغاء أي عناصر زخرفية دائرية */
.main-slider-three__circle,
.main-slider-three__shape-one,
.main-slider-three__shape-two,
.main-slider-three__shape-three,
.main-slider-three__shape-four {
    display: none !important;
}

</style>

    </head>

    <body class="custom-cursor">

        <div class="custom-cursor__cursor"></div>
        <div class="custom-cursor__cursor-two"></div>

        <div class="preloader">
            <div class="preloader__image"
                style="background-image: url({{ asset('assets/themes/default/front/images/loader.png') }});"></div>
        </div>
        <!-- /.preloader -->
        <div class="page-wrapper">
            <header
                class="main-header main-header--two main-header--three sticky-header sticky-header--three sticky-header--normal">
                <div class="container-fluid">
                    <div class="main-header__inner">
                        <div class="main-header__left">
                            <div class="main-header__logo logo-retina">
                                <a href="{{ route('index') }}">
                                    <img src="{{ asset($settings['logo']) }}" alt="{{ $settings['name'] }}"
                                        width="209">
                                </a>
                            </div><!-- /.main-header__logo -->
                        </div><!-- /.main-header__left -->
                        <div class="main-header__right">
                            <nav class="main-header__nav main-menu">
                                <ul class="main-menu__list one-page-scroll-menu">
                                    <li class="scrollToLink"><a href="{{ route('index') }}">@lang('front.Home')</a></li>
                                    <li class="scrollToLink"><a href="#about">@lang('front.About')</a></li>
                                    <li class="scrollToLink"><a href="#why">@lang('front.Why Us')</a></li>
                                    <li class="scrollToLink"><a href="#contact">@lang('front.Contact Us')</a></li>
                                </ul>
                            </nav><!-- /.main-header__nav -->
                            <div class="mobile-nav__btn mobile-nav__toggler">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div><!-- /.mobile-nav__toggler -->

                            <div class="main-header__cart dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-globe me-1"></i>
                                    {{ strtoupper(LaravelLocalization::getCurrentLocaleNative()) }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end animated fadeIn"
                                    aria-labelledby="languageDropdown">
                                    @foreach ($headerLanguages as $language)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() == $language->code ? 'active' : '' }}"
                                                href="{{ LaravelLocalization::getLocalizedURL($language->code, null, [], true) }}">
                                               {{ strtoupper($language->native) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <a href="{{ route('login') }}" class="main-header__btn eduhive-btn">
                                <span>@lang('front.Join for free')</span>
                                <span class="eduhive-btn__icon">
                                    <span class="eduhive-btn__icon__inner">
                                        @if(app()->getLocale() == 'ar')
                                            <i class="icon-left-arrow"></i>
                                        @else
                                            <i class="icon-right-arrow"></i>
                                        @endif
                                    </span>
                                </span>
                            </a><!-- /.main-header__btn eduhive-btn -->
                        </div><!-- /.main-header__right -->
                    </div><!-- /.main-header__inner -->
                </div><!-- /.container-fluid -->
            </header><!-- /.main-header -->

            <section class="main-slider-three" id="home">
                <div class="main-slider-three__carousel eduhive-owl__carousel eduhive-owl__carousel--basic-nav owl-carousel owl-theme"
                    data-owl-options='{
        "items": 1,
        "margin": 0,
        "animateIn": "fadeIn",
        "animateOut": "fadeOut",
        "loop": true,
        "smartSpeed": 1000,
        "nav": false,
        "dots": false,
        "autoplay": true,
        "navText": ["<span class=\"icon-arrow-left\"></span>","<span class=\"icon-arrow-right\"></span>"]
    }'>
                    <div class="main-slider-three__item">
                        <div class="main-slider-three__bg"
                            style="background-image: url({{ asset('assets/themes/default/front/images/shapes/main-slider-bg-2-1.png') }});">
                        </div><!-- /.main-slider-three__bg -->
                        <div class="container">
                            <div class="row gutter-y-60 align-items-center">
                                <div class="col-lg-12">
                                    <div class="main-slider-three__content">
                                        <div class="main-slider-three__video">
                                            <a href="{{$settings['home_video']}}"
                                                class="main-slider-three__video__btn video-btn video-popup">
                                                <i class="icon-play"></i>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                            </a><!-- /.video-btn -->
                                        </div><!-- /.main-slider-three__video -->
                                        <h2 class="main-slider-three__title">
                                            @lang('front.title1')
                                            <span class="main-slider-three__title__highlight">@lang('front.title2').</span><br>
                                            @lang('front.title3').
                                        </h2>
                                        <div class="main-slider-three__bottom">
                                            <div class="main-slider-three__bottom__inner">
                                                <a href="{{ route('home') }}"
                                                    class="main-slider-three__btn eduhive-btn">
                                                    <span class="eduhive-btn__text">@lang('front.Join Now')</span>
                                                    <span class="eduhive-btn__icon">
                                                        <span class="eduhive-btn__icon__inner">
                                                            @if(app()->getLocale() == 'ar')
                                                                <i class="icon-left-arrow"></i>
                                                            @else
                                                                <i class="icon-right-arrow"></i>
                                                            @endif
                                                        </span>
                                                    </span>
                                                </a><!-- /.eduhive-btn -->
                                            </div><!-- /.main-slider-three__bottom__inner -->
                                            <div class="main-slider-three__bottom__inner">
                                                <div class="main-slider-three__student">
                                                    <div class="main-slider-three__student__image">
                                                        <img src="{{ asset('assets/themes/default/front/images/9.webp') }}"
                                                            alt="student">
                                                        <img src="{{ asset('assets/themes/default/front/images/10.webp') }}"
                                                            alt="student">
                                                        <img src="{{ asset('assets/themes/default/front/images/11.webp') }}"
                                                            alt="student">
                                                        <img src="{{ asset('assets/themes/default/front/images/12.webp') }}"
                                                            alt="student">
                                                        <h4 class="main-slider-three__student__count count-box">
                                                            <span class="count-text" data-stop="9"
                                                                data-speed="1500">0</span><span>k+</span>
                                                        </h4><!-- /.student__count -->
                                                    </div><!-- /.main-slider-three__student__image -->
                                                    <p class="main-slider-three__student__text">@lang('front.students')</p>
                                                </div><!-- /.main-slider-three__student -->
                                            </div><!-- /.main-slider-three__bottom__inner -->
                                        </div><!-- /.button -->
                                        <div class="main-slider-three__content__circle-one main-slider-three__circle">
                                        </div><!-- /.circle-one -->
                                        <div class="main-slider-three__content__circle-two main-slider-three__circle">
                                        </div><!-- /.circle-two -->
                                    </div><!-- /.main-slider-three__content -->
                                    <div class="main-slider-three__images">
                                        <div class="main-slider-three__image main-slider-three__image--top">
                                            <img src="{{ asset('assets/themes/default/front/images/1.webp') }}"
                                                alt="main-slider" class="slider-image">
                                        </div><!-- /.main-slider-three__image -->
                                        <div class="main-slider-three__image">
                                            <img src="{{ asset('assets/themes/default/front/images/2.webp') }}"
                                                alt="main-slider" class="slider-image">
                                        </div><!-- /.main-slider-three__image -->
                                        <div class="main-slider-three__image">
                                            <img src="{{ asset('assets/themes/default/front/images/3.webp') }}"
                                                alt="main-slider" class="slider-image">
                                        </div><!-- /.main-slider-three__image -->
                                        <div class="main-slider-three__image main-slider-three__image--top">
                                            <img src="{{ asset('assets/themes/default/front/images/4.webp') }}"
                                                alt="main-slider" class="slider-image">
                                        </div><!-- /.main-slider-three__image -->
                                    </div><!-- /.main-slider-three__images -->
                                </div><!-- /.col-lg-12 -->
                            </div><!-- /.row gutter-y-60 -->
                        </div><!-- /.container -->
                        <img src="{{ asset('assets/themes/default/front/images/shapes/main-slider-shape-3-1.png') }}"
                            alt="shape" class="main-slider-three__shape-one slider-image">
                        <img src="{{ asset('assets/themes/default/front/images/shapes/main-slider-shape-3-2.png') }}"
                            alt="shape" class="main-slider-three__shape-two slider-image">
                        <div class="main-slider-three__box-one"></div><!-- /.box-one -->
                        <div class="main-slider-three__box-two"></div><!-- /.box-two -->
                        <div class="main-slider-three__circle-one main-slider-three__circle"></div>
                        <!-- /.circle-one -->
                        <div class="main-slider-three__circle-two main-slider-three__circle"></div>
                        <!-- /.circle-two -->
                        <div class="main-slider-three__circle-three main-slider-three__circle"></div>
                        <!-- /.circle-three -->
                        <div class="main-slider-three__circle-four main-slider-three__circle"></div>
                        <!-- /.circle-four -->
                    </div><!-- /.main-slider-three__item -->
                    <div class="main-slider-three__item">
                        <div class="main-slider-three__bg"
                            style="background-image: url({{ asset('assets/themes/default/front/images/shapes/main-slider-bg-2-1.png') }});">
                        </div><!-- /.main-slider-three__bg -->
                        <div class="container">
                            <div class="row gutter-y-60 align-items-center">
                                <div class="col-lg-12">
                                    <div class="main-slider-three__content">
                                        <div class="main-slider-three__video">
                                            <a href="{{$settings['home_video']}}"
                                                class="main-slider-three__video__btn video-btn video-popup">
                                                <i class="icon-play"></i>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                            </a><!-- /.video-btn -->
                                        </div><!-- /.main-slider-three__video -->
                                        <h2 class="main-slider-three__title">
                                            @lang('front.title1')
                                            <span class="main-slider-three__title__highlight">@lang('front.title2').</span><br>
                                            @lang('front.title3').
                                        </h2>
                                        <div class="main-slider-three__bottom">
                                            <div class="main-slider-three__bottom__inner">
                                                <a href="{{route('home')}}" class="main-slider-three__btn eduhive-btn">
                                                    <span class="eduhive-btn__text">@lang('front.Join Now')</span>
                                                    <span class="eduhive-btn__icon">
                                                        <span class="eduhive-btn__icon__inner">
                                                            @if(app()->getLocale() == 'ar')
                                                                <i class="icon-left-arrow"></i>
                                                            @else
                                                                <i class="icon-right-arrow"></i>
                                                            @endif
                                                        </span>
                                                    </span>
                                                </a><!-- /.eduhive-btn -->
                                            </div><!-- /.main-slider-three__bottom__inner -->
                                            <div class="main-slider-three__bottom__inner">
                                                <div class="main-slider-three__student">
                                                    <div class="main-slider-three__student__image">
                                                        <img src="{{ asset('assets/themes/default/front/images/9.webp') }}"
                                                            alt="student">
                                                        <img src="{{ asset('assets/themes/default/front/images/10.webp') }}"
                                                            alt="student">
                                                        <img src="{{ asset('assets/themes/default/front/images/11.webp') }}"
                                                            alt="student">
                                                        <img src="{{ asset('assets/themes/default/front/images/12.webp') }}"
                                                            alt="student">
                                                        <h4 class="main-slider-three__student__count count-box">
                                                            <span class="count-text" data-stop="9"
                                                                data-speed="1500">0</span><span>k+</span>
                                                        </h4><!-- /.student__count -->
                                                    </div><!-- /.main-slider-three__student__image -->
                                                    <p class="main-slider-three__student__text">@lang('front.students')</p>
                                                </div><!-- /.main-slider-three__student -->
                                            </div><!-- /.main-slider-three__bottom__inner -->
                                        </div><!-- /.button -->
                                        <div class="main-slider-three__content__circle-one main-slider-three__circle">
                                        </div><!-- /.circle-one -->
                                        <div class="main-slider-three__content__circle-two main-slider-three__circle">
                                        </div><!-- /.circle-two -->
                                    </div><!-- /.main-slider-three__content -->
                                    <div class="main-slider-three__images">
                                        <div class="main-slider-three__image main-slider-three__image--top">
                                            <img src="{{ asset('assets/themes/default/front/images/5.webp') }}"
                                                alt="main-slider" class="slider-image">
                                        </div><!-- /.main-slider-three__image -->
                                        <div class="main-slider-three__image">
                                            <img src="{{ asset('assets/themes/default/front/images/6.webp') }}"
                                                alt="main-slider" class="slider-image">
                                        </div><!-- /.main-slider-three__image -->
                                        <div class="main-slider-three__image">
                                            <img src="{{ asset('assets/themes/default/front/images/7.webp') }}"
                                                alt="main-slider" class="slider-image">
                                        </div><!-- /.main-slider-three__image -->
                                        <div class="main-slider-three__image main-slider-three__image--top">
                                            <img src="{{ asset('assets/themes/default/front/images/8.webp') }}"
                                                alt="main-slider" class="slider-image">
                                        </div><!-- /.main-slider-three__image -->
                                    </div><!-- /.main-slider-three__images -->
                                </div><!-- /.col-lg-12 -->
                            </div><!-- /.row gutter-y-60 -->
                        </div><!-- /.container -->
                        <img src="{{ asset('assets/themes/default/front/images/shapes/main-slider-shape-3-1.png') }}"
                            alt="shape" class="main-slider-three__shape-one slider-image">
                        <img src="{{ asset('assets/themes/default/front/images/shapes/main-slider-shape-3-2.png') }}"
                            alt="shape" class="main-slider-three__shape-two slider-image">
                        <div class="main-slider-three__box-one"></div><!-- /.box-one -->
                        <div class="main-slider-three__box-two"></div><!-- /.box-two -->
                        <div class="main-slider-three__circle-one main-slider-three__circle"></div>
                        <!-- /.circle-one -->
                        <div class="main-slider-three__circle-two main-slider-three__circle"></div>
                        <!-- /.circle-two -->
                        <div class="main-slider-three__circle-three main-slider-three__circle"></div>
                        <!-- /.circle-three -->
                        <div class="main-slider-three__circle-four main-slider-three__circle"></div>
                        <!-- /.circle-four -->
                    </div><!-- /.main-slider-three__item -->
                </div><!-- /.main-slider-three__carousel -->
            </section><!-- /.main-slider-three -->


            <section class="course-category course-category--home-3 section-space-bottom">
                <div class="container">
                    <div class="row gutter-y-30">
                        <div class="col-xl-3 col-lg-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                            data-wow-delay="00ms">
                            <div class="course-category__card course-category__card--1">
                                <div class="course-category__card__content">
                                    <div
                                        class="course-category__card__icon-box course-category__card__icon-box--secondary">
                                        <span class="course-category__card__icon">
                                            <i class="icon-briefcase"></i>
                                        </span>
                                    </div><!-- /.course-category__card__icon-box -->
                                    <h4 class="course-category__card__title">
                                        @lang('front.type1')
                                    </h4>
                                    <!-- /.course-category__card__title -->
                                </div><!-- /.course-category__card__content -->
                            </div><!-- /.course-category__card -->
                        </div><!-- /.col-xl-3 col-lg-4 col-sm-6 -->
                        <div class="col-xl-3 col-lg-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                            data-wow-delay="100ms">
                            <div class="course-category__card course-category__card--2">
                                <div class="course-category__card__content">
                                    <div class="course-category__card__icon-box course-category__card__icon-box--pink">
                                        <span class="course-category__card__icon">
                                            <i class="icon-art-studies"></i>
                                        </span>
                                    </div><!-- /.course-category__card__icon-box -->
                                    <h4 class="course-category__card__title">@lang('front.type2')</h4>
                                    <!-- /.course-category__card__title -->
                                </div><!-- /.course-category__card__content -->
                            </div><!-- /.course-category__card -->
                        </div><!-- /.col-xl-3 col-lg-4 col-sm-6 -->
                        <div class="col-xl-3 col-lg-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                            data-wow-delay="200ms">
                            <div class="course-category__card course-category__card--3">
                                <div class="course-category__card__content">
                                    <div
                                        class="course-category__card__icon-box course-category__card__icon-box--secondary">
                                        <span class="course-category__card__icon">
                                            <i class="icon-self-confidence"></i>
                                        </span>
                                    </div><!-- /.course-category__card__icon-box -->
                                    <h4 class="course-category__card__title">@lang('front.type3')</h4>
                                    <!-- /.course-category__card__title -->
                                </div><!-- /.course-category__card__content -->
                            </div><!-- /.course-category__card -->
                        </div><!-- /.col-xl-3 col-lg-4 col-sm-6 -->
                        <div class="col-xl-3 col-lg-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                            data-wow-delay="300ms">
                            <div class="course-category__card course-category__card--4">
                                <div class="course-category__card__content">
                                    <div
                                        class="course-category__card__icon-box course-category__card__icon-box--yellow2">
                                        <span class="course-category__card__icon">
                                            <i class="icon-setting"></i>
                                        </span>
                                    </div><!-- /.course-category__card__icon-box -->
                                    <h4 class="course-category__card__title">@lang('front.type4')</h4>
                                    <!-- /.course-category__card__title -->
                                </div><!-- /.course-category__card__content -->
                            </div><!-- /.course-category__card -->
                        </div><!-- /.col-xl-3 col-lg-4 col-sm-6 -->
                        <div class="col-xl-3 col-lg-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                            data-wow-delay="00ms">
                            <div class="course-category__card course-category__card--5">
                                <div class="course-category__card__content">
                                    <div class="course-category__card__icon-box course-category__card__icon-box--base">
                                        <span class="course-category__card__icon">
                                            <i class="icon-healthcare"></i>
                                        </span>
                                    </div><!-- /.course-category__card__icon-box -->
                                    <h4 class="course-category__card__title">@lang('front.type5')</h4>
                                    <!-- /.course-category__card__title -->
                                </div><!-- /.course-category__card__content -->
                            </div><!-- /.course-category__card -->
                        </div><!-- /.col-xl-3 col-lg-4 col-sm-6 -->
                        <div class="col-xl-3 col-lg-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                            data-wow-delay="100ms">
                            <div class="course-category__card course-category__card--6">
                                <div class="course-category__card__content">
                                    <div
                                        class="course-category__card__icon-box course-category__card__icon-box--primary">
                                        <span class="course-category__card__icon">
                                            <i class="icon-coding-1"></i>
                                        </span>
                                    </div><!-- /.course-category__card__icon-box -->
                                    <h4 class="course-category__card__title">@lang('front.type6')</h4>
                                    <!-- /.course-category__card__title -->
                                </div><!-- /.course-category__card__content -->
                            </div><!-- /.course-category__card -->
                        </div><!-- /.col-xl-3 col-lg-4 col-sm-6 -->
                        <div class="col-xl-3 col-lg-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                            data-wow-delay="200ms">
                            <div class="course-category__card course-category__card--7">
                                <div class="course-category__card__content">
                                    <div class="course-category__card__icon-box course-category__card__icon-box--blue">
                                        <span class="course-category__card__icon">
                                            <i class="icon-clapperboard"></i>
                                        </span>
                                    </div><!-- /.course-category__card__icon-box -->
                                    <h4 class="course-category__card__title">@lang('front.type7')</h4>
                                    <!-- /.course-category__card__title -->
                                </div><!-- /.course-category__card__content -->
                            </div><!-- /.course-category__card -->
                        </div><!-- /.col-xl-3 col-lg-4 col-sm-6 -->
                        <div class="col-xl-3 col-lg-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                            data-wow-delay="300ms">
                            <div class="course-category__card course-category__card--8">
                                <div class="course-category__card__content">
                                    <div class="course-category__card__icon-box course-category__card__icon-box--red">
                                        <span class="course-category__card__icon">
                                            <i class="icon-megaphone"></i>
                                        </span>
                                    </div><!-- /.course-category__card__icon-box -->
                                    <h4 class="course-category__card__title">@lang('front.type8')</h4>
                                    <!-- /.course-category__card__title -->
                                </div><!-- /.course-category__card__content -->
                            </div><!-- /.course-category__card -->
                        </div><!-- /.col-xl-3 col-lg-4 col-sm-6 -->
                    </div><!-- /.row gutter-y-30 -->
                </div><!-- /.container -->
            </section><!-- /.course-category section-space-bottom -->

            <section class="about-three" id="about">
                <div class="container-fluid">
                    <div class="about-three__inner section-space">
                        <div class="container">
                            <div class="row gutter-y-50 align-items-center">
                                <div class="col-lg-6 wow fadeInLeft" data-wow-duration="1500ms">
                                    <div class="about-three__image">
                                        <div class="about-three__image__inner">
                                            <img src="{{ asset('assets/themes/default/front/images/13.webp') }}"
                                                alt="about">
                                            <div class="about-three__experience">
                                                <span class="about-three__experience__icon">
                                                    <i class="icon-connectibity"></i>
                                                </span><!-- /.about-three__experience__icon -->
                                                <div class="about-three__experience__content">
                                                    <h3 class="about-three__experience__title">40+</h3>
                                                    <!-- /.experience__title -->
                                                    <p class="about-three__experience__text">@lang('Years teaching university-level mathematics')</p>
                                                    <h3 class="about-three__experience__title">15+</h3>
                                                    <p class="about-three__experience__text">@lang('Years preparing students for the American Diploma.')</p>

                                                    <!-- /.experience__text -->
                                                </div><!-- /.about-three__experience__content -->
                                            </div><!-- /.about-three__experience -->
                                            <div class="total-student">
                                                <div class="total-student__inner">
                                                    <div class="total-student__image">
                                                        <img src="{{ asset('assets/themes/default/front/images/14.webp') }}"
                                                            alt="student" class="slider-image">
                                                        <img src="{{ asset('assets/themes/default/front/images/15.webp') }}"
                                                            alt="student" class="slider-image">
                                                    </div><!-- /.total-student__image -->
                                                    <h4 class="total-student__text count-box">
                                                        <span class="count-text" data-stop="200"
                                                            data-speed="1500">0</span><span>k+ <br> @lang('front.students')</span>
                                                    </h4><!-- /.total-student__text -->
                                                </div><!-- /.total-student__inner -->
                                            </div><!-- /.total-student -->
                                            <div class="about-three__image__shape-one"></div>
                                            <!-- /.about-three__image__shape-one -->
                                            <div class="about-three__image__shape-two"></div>
                                            <!-- /.about-three__image__shape-two -->
                                        </div><!-- /.about-three__image__inner -->
                                        <div class="about-three__image__box"></div><!-- /.about-three__image__box -->
                                        <img src="{{ asset('assets/themes/default/front/images/shapes/about-shape-3-2.png') }}"
                                            alt="shape" class="about-three__image__shape-three">
                                    </div><!-- /.about-three__image -->
                                </div><!-- /.col-lg-6 -->
                                <div class="col-lg-6">
                                    <div class="about-three__content">
                                        <div class="sec-title wow fadeInUp" data-wow-duration="1500ms"
                                            data-wow-delay="00ms">
                                            <h6 class="sec-title__tagline">@lang('front.About')</h6><!-- /.sec-title__tagline -->
                                            <h3 class="sec-title__title">@lang('Learn & Grow Your Mathematics Skills From Anywhere')</h3>
                                            <!-- /.sec-title__title -->
                                        </div><!-- /.sec-title -->
                                        <div class="about-three__description wow fadeInUp" data-wow-duration="1500ms"
                                            data-wow-delay="00ms">
                                            <p class="about-three__text">
                                                <!-- @lang('front.EduHive is an innovative e-learning platform designed to empower learners of all ages and backgrounds. With a wide range of courses, expert instructors, and interactive resources, our platform makes it easy to gain new skills and advance your career from anywhere, at any time.') -->
                                            </p>Over 40 years of experience teaching university-level mathematics and more than 15 years preparing American Diploma students for SAT, ACT, EST and AP exams.
Through a clear and structured approach, I help students master complex math concepts with confidence and clarity.
My goal is to turn mathematics into an enjoyable and inspiring journey that leads every learner to outstanding results in both local and international exams</p>
                                            </p>
                                            <!-- /.about-three__text -->
                                        </div><!-- /.about-three__description -->
                                        <div class="about-three__content__inner">
                                            <div class="row gutter-y-30">
                                                <!-- <div class="col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                                                    data-wow-delay="00ms">
                                                    <div class="about-three__info">
                                                        <h3 class="about-three__info__title">@lang('front.Flexible Timings')</h3> -->
                                                        <!-- /.about-three__info__title -->
                                                        <!-- <p class="about-three__info__text">
                                                            @lang('front.Learn at your own pace with our flexible course schedules designed to fit your busy lifestyle.')
                                                        </p> -->
                                                        <!-- /.about-three__info__text -->
                                                    <!-- </div> -->
                                                    <!-- /.about-three__info -->
                                                <!-- </div> -->
                                                <!-- /.col-sm-6 -->
                                                <!-- <div class="col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                                                    data-wow-delay="100ms">
                                                    <div class="about-three__info">
                                                        <h3 class="about-three__info__title">@lang('front.expert instructors')</h3> -->
                                                        <!-- /.about-three__info__title -->
                                                        <!-- <p class="about-three__info__text">
                                                            @lang('front.Our instructors are industry experts with years of experience, dedicated to helping you achieve your learning goals.')
                                                        </p> -->
                                                        <!-- /.about-three__info__text -->
                                                    <!-- </div> -->
                                                    <!-- /.about-three__info -->
                                                <!-- </div> -->
                                                <!-- /.col-sm-6 -->
                                            </div>
                                            <!-- /.row gutter-y-30 -->
                                        </div><!-- /.about-three__content__inner -->
                                        <div class="about-three__bottom wow fadeInUp" data-wow-duration="1500ms">
                                            <a href="{{route('home')}}" class="eduhive-btn">
                                                <span>@lang('front.Join Now')</span>
                                                <span class="eduhive-btn__icon">
                                                    <span class="eduhive-btn__icon__inner"><i
                                                            class="icon-connectibity"></i></span>
                                                </span>
                                            </a><!-- /.eduhive-btn -->
                                            <div class="about-three__call">
                                                <span class="about-three__call__icon">
                                                    <i class="icon-telephone"></i>
                                                </span><!-- /.about-three__call__icon -->
                                                <div class="about-three__call__content">
                                                    <p class="about-three__call__title">@lang('front.Call Us Now')</p>
                                                    <!-- /.call__title -->
                                                    <h4 class="about-three__call__number">
                                                        <a href="tel:{{$settings['phone1']}}">{{$settings['phone1']}}</a>
                                                    </h4><!-- /.about-three__call__number -->
                                                </div><!-- /.about-three__call__content -->
                                            </div><!-- /.about-three__call -->
                                        </div><!-- /.about-three__bottom -->
                                    </div><!-- /.about-three__content -->
                                </div><!-- /.col-lg-6 -->
                            </div><!-- /.row gutter-y-50 -->
                        </div><!-- /.container -->
                        <img src="{{ asset('assets/themes/default/front/images/shapes/about-shape-3-1.png') }}"
                            alt="shape" class="about-three__shape-one">
                        <div class="about-three__circle-one"></div><!-- /.about-three__circle-one -->
                        <div class="about-three__circle-two"></div><!-- /.about-three__circle-two -->
                        <div class="about-three__circle-three"></div><!-- /.about-three__circle-three -->
                    </div><!-- /.about-three__inner section-space -->
                </div><!-- /.container-fluid -->
                <img src="{{ asset('assets/themes/default/front/images/shapes/about-shape-3-3.png') }}"
                    alt="shape" class="about-three__shape-two">
            </section><!-- /.about-three -->
<<section class="funfact-one funfact-one--home-3 funfact-one--round">
    <div class="container">
        <div class="funfact-one__grid">
            <!-- الطلاب الراضين -->
            <div class="funfact-one__item funfact-one__item--secondary wow fadeInUp"
                data-wow-duration="1500ms" data-wow-delay="00ms">
                <div class="funfact-one__icon">
                    <span class="funfact-one__icon__inner"><i class="icon-connectibity"></i></span>
                </div>
                <h3 class="funfact-one__title count-box">
                    <span class="count-text" data-stop="{{ $stats['satisfied_students'] }}" data-speed="1500">0</span>
                    <span>+</span>
                </h3>
                <p class="funfact-one__text">@lang('front.Satisfied Students')</p>
            </div>
            
            <!-- الكورسات المكتملة -->
            <div class="funfact-one__item funfact-one__item--primary wow fadeInUp"
                data-wow-duration="1500ms" data-wow-delay="100ms">
                <div class="funfact-one__icon">
                    <span class="funfact-one__icon__inner"><i class="icon-batch-assign"></i></span>
                </div>
                <h3 class="funfact-one__title count-box">
                    <span class="count-text" data-stop="{{ $stats['total_courses'] }}" data-speed="1500">0</span>
                    <span>+</span>
                </h3>
                <p class="funfact-one__text">@lang('Courses')</p>
            </div>
            
            <!-- الاختبارات المتاحة (جديد) -->
            <div class="funfact-one__item funfact-one__item--secondary wow fadeInUp"
                data-wow-duration="1500ms" data-wow-delay="200ms">
                <div class="funfact-one__icon">
                    <span class="funfact-one__icon__inner"><i class="icon-exam"></i></span>
                </div>
                <h3 class="funfact-one__title count-box">
                    <span class="count-text" data-stop="{{ $stats['Practice_Tests'] }}" data-speed="1500">0</span>
                    <span>+</span>
                </h3>
                <p class="funfact-one__text">@lang('Practice Tests')</p>
            </div>
            
            <!-- المدربين الخبراء -->
            <div class="funfact-one__item funfact-one__item--primary wow fadeInUp"
                data-wow-duration="1500ms" data-wow-delay="300ms">
                <div class="funfact-one__icon">
                    <span class="funfact-one__icon__inner"><i class="icon-instructors"></i></span>
                </div>
                <h3 class="funfact-one__title count-box">
                    <span class="count-text" data-stop="{{ $stats['expert_instructors'] }}" data-speed="1500">0</span>
                    <span>+</span>
                </h3>
                <p class="funfact-one__text">@lang('front.Experts Instructors')</p>
            </div>
        </div>
    </div>
</section>
            <section class="offer-one section-space-top" id="why">
                <div class="container">
                    <div class="row gutter-y-60">
                        <div class="col-lg-6 wow fadeInUp" data-wow-duration="1500ms">
                            <div class="offer-one__image">
                                <img src="{{ asset('assets/themes/default/front/images/16.webp') }}"
                                    alt="offer image" class="offer-one__image__one">
                                <img src="{{ asset('assets/themes/default/front/images/17.webp') }}"
                                    alt="offer image" class="offer-one__image__two">
                                <img src="{{ asset('assets/themes/default/front/images/shapes/offer-shape-1-1.png') }}"
                                    alt="offer shape" class="offer-one__image__shape">
                            </div><!-- /.offer-one__image -->
                        </div><!-- /.col-lg-6 -->
                        <div class="col-lg-6">
                            <div class="offer-one__content">
                                <div class="sec-title wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
                                    <h6 class="sec-title__tagline">@lang('front.Why Us')</h6><!-- /.sec-title__tagline -->
                                    <h3 class="sec-title__title"><span class="sec-title__title__shape">@lang('front.We Offer The Best Carrier')</h3>
                                    <!-- /.sec-title__title -->
                                </div><!-- /.sec-title -->
                                <div class="offer-one__inner">
                                    <div class="offer-one__item wow fadeInUp" data-wow-duration="1500ms">
                                        <div class="offer-one__item__icon offer-one__item__icon--primary">
                                            <i class="icon-instructors"></i>
                                        </div><!-- /.offer-one__item__icon -->
                                        <div class="offer-one__item__content">
                                            <h4 class="offer-one__item__title">@lang('We Offer the Best Learning Experience')</h4>
                                            <p class="offer-one__item__text">
                                                @lang('At MathCrack, students are at the heart of everything we do — empowering them to achieve excellence and top scores.')
                                            </p>
                                        </div><!-- /.offer-one__item__content -->
                                    </div><!-- /.offer-one__item -->
                                    <div class="offer-one__item wow fadeInUp" data-wow-duration="1500ms">
                                        <div class="offer-one__item__icon offer-one__item__icon--secondary">
                                            <i class="icon-copy-writing"></i>
                                        </div><!-- /.offer-one__item__icon -->
                                        <div class="offer-one__item__content">
                                            <h4 class="offer-one__item__title">@lang('The Largest and Most Supportive Student Community')</h4>
                                            <p class="offer-one__item__text">
                                                @lang('Join a vibrant community of motivated learners — share ideas, practice together, and grow in an inspiring environment designed for success.')
                                            </p>
                                        </div><!-- /.offer-one__item__content -->
                                    </div><!-- /.offer-one__item -->
                                    <div class="offer-one__item wow fadeInUp" data-wow-duration="1500ms">
                                        <div class="offer-one__item__icon offer-one__item__icon--base">
                                            <i class="icon-community"></i>
                                        </div><!-- /.offer-one__item__icon -->
                                        <div class="offer-one__item__content">
                                            <h4 class="offer-one__item__title">@lang('Expert Math Instructors')</h4>
                                            <p class="offer-one__item__text">
                                                @lang('Learn from highly experienced professors with decades of teaching in university mathematics and American Diploma programs — experts who truly understand how to help every student succeed.')
                                            </p>
                                        </div><!-- /.offer-one__item__content -->
                                    </div><!-- /.offer-one__item -->
                                </div><!-- /.offer-one__inner -->
                            </div><!-- /.offer-one__content -->
                        </div><!-- /.col-lg-6 -->
                    </div><!-- /.row gutter-y-60 -->
                </div><!-- /.container -->
                <img src="{{ asset('assets/themes/default/front/images/shapes/offer-shape-1-3.png') }}"
                    alt="shape" class="offer-one__shape">
                <div class="offer-one__shape-box"></div><!-- /.offer-one__shape -->
            </section><!-- /.offer-one section-space-top -->

            <section class="contact-one section-space-top section-space-bottom" id="contact">
                <div class="container">
                    <div class="contact-one__inner">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="contact-one__content">
                                    <div class="sec-title">
                                        <h6 class="sec-title__tagline">@lang('front.Contact Us')</h6>
                                        <h3 class="sec-title__title">@lang('front.Get in Touch With Us')</h3>
                                    </div>
                                    <div class="contact-one__info">
                                        <div class="contact-one__info__item mb-3">
                                            <div class="contact-one__info__icon">
                                                <span><i class="icon-location"></i> {{ $settings['address'] }}</span>
                                            </div>
                                        </div>
                                        <div class="contact-one__info__item mb-3">
                                            <div class="contact-one__info__icon">
                                                <span><i class="icon-telephone"></i> <a href="tel:{{ $settings['phone1'] }}">{{ $settings['phone1'] }}</a></span>
                                            </div>
                                        </div>
                                        <div class="contact-one__info__item mb-3">
                                            <div class="contact-one__info__icon">
                                                <span><i class="icon-email"></i> <a href="mailto:{{ $settings['email1'] }}">{{ $settings['email1'] }}</a></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="contact-one__form contact-form-validated">
                                    <form action="{{ route('contact') }}" method="POST" class="contact-one__form__inner">
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
                                                <button type="submit" class="eduhive-btn">
                                                    <span>@lang('front.Send Message')</span>
                                                    <span class="eduhive-btn__icon">
                                                        <span class="eduhive-btn__icon__inner">
                                                            @if(app()->getLocale() == 'ar')
                                                                <i class="icon-left-arrow"></i>
                                                            @else
                                                                <i class="icon-right-arrow"></i>
                                                            @endif
                                                        </span>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="main-footer section-space-top">
                <div class="container">
                    <div class="main-footer__top">
                        <a href="{{route('index')}}" class="main-footer__logo">
                            <img src="{{ asset($settings['logo']) }}"
                                width="209" alt="Eduhive HTML Template">
                        </a><!-- /.main-footer__logo -->
                        <div class="main-footer__top__inner">
                            <div class="main-footer__newsletter">
                                <div class="footer-widget footer-widget--about">
                                    {{-- <h2 class="footer-widget__title">{{ $settings['name'] }}</h2> --}}
                                    <!-- /.footer-widget__title -->
                                    <p class="footer-widget__about-text">{{ $settings['description'] }}</p>
                                    <!-- /.footer-widget__about-text -->
                                    <ul class="footer-widget__info">
                                        <li>
                                            <span class="footer-widget__info__icon"><i class="icon-location"></i></span>
                                            <a href="javascript:void(0)">{{ $settings['address'] }}</a>
                                        </li>
                                        <li>
                                            <span class="footer-widget__info__icon"><i class="icon-email"></i></span>
                                            <a href="mailto:{{ $settings['email1'] }}">{{ $settings['email1'] }}</a>
                                        </li>
                                        <li>
                                            <span class="footer-widget__info__icon"><i
                                                    class="icon-telephone"></i></span>
                                            <a href="tel:{{ $settings['phone1'] }}">{{ $settings['phone1'] }}</a>
                                        </li>
                                    </ul><!-- /.footer-widget__info -->
                                </div><!-- /.main-footer__newsletter__form mc-form -->
                                <div class="mc-form__response"></div><!-- /.mc-form__response -->
                            </div><!-- /.main-footer__newsletter -->
                            <div class="main-footer__social social-links">
                                <a href="{{$settings['facebook']}}">
                                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                    <span class="sr-only">@lang('l.Facebook')</span>
                                </a>
                                <a href="{{$settings['twitter']}}">
                                    <i class="icon-twitter" aria-hidden="true"></i>
                                    <span class="sr-only">@lang('l.Twitter')</span>
                                </a>
                                <a href="{{$settings['linkedin']}}">
                                    <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                    <span class="sr-only">@lang('l.Linkedin')</span>
                                </a>
                                <a href="{{$settings['youtube']}}">
                                    <i class="fab fa-youtube" aria-hidden="true"></i>
                                    <span class="sr-only">@lang('l.Youtube')</span>
                                </a>
                            </div><!-- /.main-footer__social social-links -->
                        </div><!-- /.main-footer__top__inner -->
                    </div><!-- /.main-footer__top -->
                </div><!-- /.container -->
                <div class="main-footer__bottom">
                    <div class="container">
                        <div class="main-footer__bottom__inner">
                            <p class="main-footer__copyright">
                                &copy; Copyright <span class="dynamic-year"></span> by <a href="https://adbrandagency.com">Adbrand Agency</a>.
                            </p>
                        </div><!-- /.main-footer__inner -->
                    </div><!-- /.container -->
                </div><!-- /.main-footer__bottom -->
                <img src="{{ asset('assets/themes/default/front/images/shapes/footer-shape-1.png') }}"
                    alt="shape" class="main-footer__shape-one">
                <img src="{{ asset('assets/themes/default/front/images/shapes/footer-shape-2.png') }}"
                    alt="shape" class="main-footer__shape-two">
                <img src="{{ asset('assets/themes/default/front/images/shapes/footer-shape-3.png') }}"
                    alt="shape" class="main-footer__shape-three">
                <img src="{{ asset('assets/themes/default/front/images/shapes/footer-shape-4.png') }}"
                    alt="shape" class="main-footer__shape-four">
                <img src="{{ asset('assets/themes/default/front/images/shapes/footer-shape-5.png') }}"
                    alt="shape" class="main-footer__shape-five">
            </footer><!-- /.main-footer section-space-top -->

        </div><!-- /.page-wrapper -->

        <div class="mobile-nav__wrapper">
            <div class="mobile-nav__overlay mobile-nav__toggler"></div><!-- /.mobile-nav__overlay -->
            <div class="mobile-nav__content">
                <span class="mobile-nav__close mobile-nav__toggler"><i class="icon-close"></i></span>
                <div class="logo-box logo-retina">
                    <a href="{{route('index')}}" aria-label="logo image">
                        <img src="{{ asset($settings['logo']) }}" width="209"
                            alt="" />
                    </a>
                </div><!-- /.logo-box -->
                <div class="mobile-nav__container"></div><!-- /.mobile-nav__container -->
                <ul class="mobile-nav__contact list-unstyled">
                    <li>
                        <span class="mobile-nav__contact__icon"><i class="fa fa-envelope"></i></span>
                        <a href="mailto:{{ $settings['email1'] }}">{{ $settings['email1'] }}</a>
                    </li>
                    <li>
                        <span class="mobile-nav__contact__icon"><i class="fa fa-phone-alt"></i></span>
                        <a href="tel:{{ $settings['phone1'] }}">{{ $settings['phone1'] }}</a>
                    </li>
                </ul><!-- /.mobile-nav__contact -->
                <div class="mobile-nav__social social-links-two">
                    <a href="{{$settings['facebook']}}">
                        <span class="social-links-two__icon">
                            <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        </span><!-- /.social-links-two__icon -->
                        <span class="sr-only">@lang('l.Facebook')</span>
                    </a>
                    <a href="{{$settings['twitter']}}">
                        <span class="social-links-two__icon">
                            <i class="fab fa-twitter" aria-hidden="true"></i>
                        </span><!-- /.social-links-two__icon -->
                        <span class="sr-only">@lang('l.Twitter')</span>
                    </a>
                    <a href="{{$settings['instagram']}}">
                        <span class="social-links-two__icon">
                            <i class="fab fa-instagram" aria-hidden="true"></i>
                        </span><!-- /.social-links-two__icon -->
                        <span class="sr-only">@lang('l.Instagram')</span>
                    </a>
                    <a href="{{$settings['youtube']}}">
                        <span class="social-links-two__icon">
                            <i class="fab fa-youtube" aria-hidden="true"></i>
                        </span><!-- /.social-links-two__icon -->
                        <span class="sr-only">@lang('l.Youtube')</span>
                    </a>
                </div><!-- /.mobile-nav__social -->
            </div><!-- /.mobile-nav__content -->
        </div><!-- /.mobile-nav__wrapper -->
        <div class="search-popup">
            <div class="search-popup__overlay search-toggler"></div>
            <!-- /.search-popup__overlay -->
            <div class="search-popup__content">
                <form role="search" method="get" class="search-popup__form" action="#">
                    <input type="text" id="search" placeholder="Search Here..." />
                    <button type="submit" aria-label="search submit" class="eduhive-btn">
                        <i class="icon-search"></i>
                    </button>
                </form>
            </div>
            <!-- /.search-popup__content -->
        </div>
        <!-- /.search-popup -->

        <a href="#" data-target="html" class="scroll-to-target scroll-to-top">
            <span class="scroll-to-top__text">@lang('l.back top')</span>
            <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
        </a>

        <script src="{{ asset('assets/themes/default/front/vendors/jquery/jquery-3.7.0.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/bootstrap-select/bootstrap-select.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/jarallax/jarallax.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/jquery-ui/jquery-ui.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/jquery-appear/jquery.appear.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/jquery-circle-progress/jquery.circle-progress.min.js') }}">
        </script>
        <script src="{{ asset('assets/themes/default/front/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js') }}">
        </script>
        <script src="{{ asset('assets/themes/default/front/vendors/jquery-validate/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/nouislider/nouislider.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/tiny-slider/tiny-slider.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/wnumb/wNumb.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/owl-carousel/js/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/owl-carousel/js/owlcarousel2-filter.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/owl-carousel/js/owlcarousel2-progressbar.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/wow/wow.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/imagesloaded/imagesloaded.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/isotope/isotope.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/countdown/countdown.min.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/jquery-circleType/jquery.circleType.js') }}"></script>
        <script src="{{ asset('assets/themes/default/front/vendors/jquery-lettering/jquery.lettering.min.js') }}"></script>
        <!-- template js -->
        <script src="{{ asset('assets/themes/default/front/js/eduhive.js') }}"></script>

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
