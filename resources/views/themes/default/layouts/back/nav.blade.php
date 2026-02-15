@php
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
@endphp
<header class="nxl-header">
    <div class="header-wrapper">
        <!--! [Start] Header Left !-->
        <div class="header-left d-flex align-items-center gap-4">
            <!--! [Start] nxl-head-mobile-toggler !-->
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <!--! [Start] nxl-head-mobile-toggler !-->
            <!--! [Start] nxl-navigation-toggle !-->
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            <!--! [End] nxl-navigation-toggle !-->
        </div>
        <!--! [End] Header Left !-->
        <!--! [Start] Header Right !-->
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                <div class="dropdown nxl-h-item nxl-header-language d-none d-sm-flex">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 nxl-language-link" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <i class="feather-globe"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-language-dropdown" style="width: 200px;">
                        <div class="dropdown-divider mt-0"></div>
                        <div class="language-items-wrapper">
                            {{-- <div class="select-language px-4 py-2 hstack justify-content-between gap-4">
                                <div class="lh-lg">
                                    <h6 class="mb-0">Select Language</h6>
                                    <p class="fs-11 text-muted mb-0">12 languages avaiable!</p>
                                </div>
                                <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="tooltip" title="Add Language">
                                    <i class="feather-plus"></i>
                                </a>
                            </div>
                            <div class="dropdown-divider"></div> --}}
                            <div class="row px-4 pt-3">
                                @foreach ($headerLanguages as $language)
                                <div class="col-sm-6 col-6 language_select">
                                    <a href="{{ LaravelLocalization::getLocalizedURL($language->code, null, [], true) }}" class="d-flex align-items-center gap-2">
                                        {{-- <div class="avatar-image avatar-sm">
                                            <img src="{{asset('assets/themes/default/vendors/img/flags/1x1/sa.svg')}}" alt="" class="img-fluid" />
                                        </div> --}}
                                        <span>{{ $language->native }}</span>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>
                <div class="dropdown nxl-h-item ml-2">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="{{asset(auth()->user()->photo)}}" alt="user-image" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{asset(auth()->user()->photo)}}" alt="user-image" class="img-fluid user-avtar" />
                                <div>
                                    <h6 class="text-dark mb-0">{{auth()->user()->name}} <span class="badge bg-soft-success text-success ms-1">@lang('l.Active')</span></h6>
                                    <span class="fs-12 fw-medium text-muted">{{auth()->user()->email}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{route('dashboard.profile')}}" class="dropdown-item">
                            <i class="feather-settings"></i>
                            <span>@lang('l.Account &amp; Settings')</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="dropdown-item">
                            @csrf
                            <button type="submit" class="dropdown-item" style="border: none; background: none; padding: 0; margin: 0; width: 100%; text-align: left;">
                                <i class="feather-log-out"></i>
                                <span>@lang('l.Log Out')</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--! [End] Header Right !-->
    </div>
</header>