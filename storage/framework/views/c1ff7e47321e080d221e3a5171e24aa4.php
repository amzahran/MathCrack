<!DOCTYPE html>
<script>
    window.primaryColor = "<?php echo e($settings['primary_color'] ?? '#FFAB1D'); ?>";
</script>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

        <title><?php echo $__env->yieldContent('title'); ?></title>

        <meta name="keywords" content="<?php echo $__env->yieldContent('meta_keywords'); ?>" />
        <meta name="description" content="<?php echo $__env->yieldContent('meta_description'); ?>" />
        <meta name="author" content="<?php echo e($settings['author']); ?>">

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset($settings['favicon'])); ?>" />

        
        <link rel="canonical" href="<?php echo e(request()->fullUrl()); ?>">

        
        <link rel="sitemap" type="application/xml" href="<?php echo e(url('/sitemap.xml')); ?>" />

        
        <link rel="alternate" hreflang="x-default" href="<?php echo e(url('') . substr(request()->getRequestUri(), 3)); ?>" />

        <!-- google recaptcha -->
        <script async src="https://www.google.com/recaptcha/api.js?hl=<?php echo e(app()->getLocale()); ?>"></script>

        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

        <!--! BEGIN: Bootstrap CSS-->
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/themes/default/css/bootstrap.min.css')); ?>" />
        <!--! END: Bootstrap CSS-->
        <!--! BEGIN: Vendors CSS-->
        <link rel="stylesheet" type="text/css"
            href="<?php echo e(asset('assets/themes/default/vendors/css/vendors.min.css')); ?>" />
        <link rel="stylesheet" type="text/css"
            href="<?php echo e(asset('assets/themes/default/vendors/css/daterangepicker.min.css')); ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/themes/default/vendors/css/select2.min.css')); ?>">
        <link rel="stylesheet" type="text/css"
            href="<?php echo e(asset('assets/themes/default/vendors/css/select2-theme.min.css')); ?>">
        <!--! END: Vendors CSS-->
        <!--! BEGIN: Custom CSS-->
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/themes/default/css/theme.min.css')); ?>" />

        <!-- Student Layout Custom Styles -->
        <style>
            /* Remove sidebar and adjust layout */
            .student-layout {
                padding-left: 0 !important;
            }

            .student-container {
                width: 100%;
                max-width: none;
                margin: 0;
                padding: 0;
            }

            .student-content {
                flex: 1 0 auto;
                padding: 20px;
            }

            /* Student Navigation Bar */
            .student-navbar {
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                padding: 15px 0;
                box-shadow: 0 2px 15px rgba(30, 64, 175, 0.2);
                position: sticky;
                top: 0;
                z-index: 1000;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .student-navbar .navbar-brand {
                color: white !important;
                font-weight: 700;
                font-size: 1.6rem;
                text-decoration: none;
                transition: all 0.3s ease;
                text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }

            .student-navbar .navbar-brand:hover {
                color: white !important;
                transform: scale(1.05);
            }

            .student-navbar .navbar-brand img {
                height: 45px;
                margin-right: 12px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            }

            .student-nav-links {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .student-nav-item {
                position: relative;
            }

            .student-nav-link {
                display: flex;
                align-items: center;
                padding: 12px 20px;
                color: rgba(255,255,255,0.9) !important;
                text-decoration: none;
                border-radius: 10px;
                margin: 0 5px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-weight: 500;
                position: relative;
                overflow: hidden;
            }

            .student-nav-link::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }

            .student-nav-link:hover::before {
                left: 100%;
            }

            .student-nav-link:hover,
            .student-nav-link.active {
                background: rgba(255,255,255,0.2);
                color: white !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(30, 64, 175, 0.4);
                border-radius: 10px;
            }

            .student-nav-link i {
                margin-right: 8px;
                font-size: 1.1rem;
            }

            /* User dropdown */
            .student-user-dropdown {
                margin-left: auto;
            }

            .student-user-dropdown .dropdown-toggle {
                background: rgba(255,255,255,0.15);
                border: 1px solid rgba(255,255,255,0.3);
                color: white !important;
                border-radius: 30px;
                padding: 10px 18px;
                display: flex;
                align-items: center;
                gap: 10px;
                transition: all 0.3s ease;
                font-weight: 500;
            }

            .student-user-dropdown .dropdown-toggle:hover {
                background: rgba(255,255,255,0.25);
                transform: translateY(-1px);
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            }

            .student-user-dropdown .dropdown-toggle:focus {
                box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
            }

            .student-user-dropdown .dropdown-menu {
                border-radius: 10px;
                border: none;
                box-shadow: 0 5px 25px rgba(0,0,0,0.15);
                margin-top: 10px;
            }

            /* Page header adjustments */
            .student-page-header {
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                padding: 25px 0;
                margin-bottom: 25px;
                border-bottom: 1px solid #cbd5e1;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            }

            .student-page-title {
                color: #1e293b;
                font-weight: 700;
                margin-bottom: 8px;
                font-size: 1.75rem;
                text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            }

            .student-breadcrumb {
                margin: 0;
                padding: 0;
                background: transparent;
                font-size: 0.9rem;
            }

            .student-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
                content: "›";
                color: #6c757d;
            }

                        /* Mobile responsiveness */
            @media (max-width: 768px) {
                .student-navbar {
                    padding: 10px 0;
                }

                .student-nav-links {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                    width: 100%;
                    padding: 20px;
                    background: rgba(255,255,255,0.08);
                    border-radius: 12px;
                    margin-top: 15px;
                }

                .student-nav-link {
                    justify-content: center;
                    text-align: center;
                    margin: 0;
                    padding: 15px 12px;
                    border-radius: 8px;
                    background: rgba(255,255,255,0.1);
                    font-size: 0.9rem;
                    min-height: 60px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 5px;
                }

                .student-nav-link i {
                    margin-right: 0;
                    font-size: 1.2rem;
                }

                .student-nav-link:hover,
                .student-nav-link.active {
                    background: rgba(255,255,255,0.25);
                    transform: scale(1.05);
                }

                .student-user-dropdown {
                    margin-left: 0;
                    margin-top: 0;
                    order: -1;
                }

                .student-content {
                    padding: 15px;
                }

                .student-page-header {
                    padding: 15px 0;
                    margin-bottom: 15px;
                }

                .student-page-title {
                    font-size: 1.4rem;
                }
            }

            /* Modal fixes */
            body.modal-open .student-navbar {
                filter: none !important;
            }

            .modal-backdrop {
                z-index: 1050 !important;
            }

            .modal {
                z-index: 1060 !important;
            }

            /* Footer */
            html, body {
                height: 100%;
            }

            .student-wrapper {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }



                                    /* Scroll to top button */
            .scroll-to-top {
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                color: white;
                border: none;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 20px rgba(30, 64, 175, 0.3);
                transition: all 0.3s ease;
                z-index: 999;
                opacity: 0;
                visibility: hidden;
            }

            .scroll-to-top.show {
                opacity: 1;
                visibility: visible;
            }

            .scroll-to-top:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 25px rgba(30, 64, 175, 0.4);
            }

                        /* Global Color Theme Override for Student Pages */
            .btn-primary {
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
                border-color: #1e40af !important;
                color: white !important;
            }

            .btn-primary:hover,
            .btn-primary:focus,
            .btn-primary:active {
                background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%) !important;
                border-color: #1d4ed8 !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3) !important;
            }

            .text-primary {
                color: #1e40af !important;
            }

            .bg-primary {
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
            }

            .border-primary {
                border-color: #1e40af !important;
            }

            .card {
                border: none;
                box-shadow: 0 2px 15px rgba(30, 64, 175, 0.08);
                border-radius: 12px;
            }

            .card-header {
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                border-bottom: 1px solid #cbd5e1;
                border-radius: 12px 12px 0 0 !important;
            }

            .table thead th {
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                color: white;
                border: none;
            }

            .badge-primary,
            .badge.bg-primary {
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
            }

            .progress-bar {
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
            }

            .form-control:focus {
                border-color: #1e40af;
                box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
            }

            .form-select:focus {
                border-color: #1e40af;
                box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
            }

            a {
                color: #1e40af;
            }

            a:hover {
                color: #1d4ed8;
            }

            /* Additional color overrides */
            .btn-success {
                background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%) !important;
                border-color: #16a34a !important;
            }

            .btn-warning {
                background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%) !important;
                border-color: #d97706 !important;
            }

            .btn-info {
                background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%) !important;
                border-color: #0891b2 !important;
            }

            /* Override any remaining old colors */
            .bg-gradient-primary {
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
            }

            .text-info {
                color: #0891b2 !important;
            }

            .text-success {
                color: #16a34a !important;
            }

            .text-warning {
                color: #d97706 !important;
            }
<-- التحكم في حجم الخط اناف بار العلوي-->
/* تكبير الخطوط في النافبار العلوي */
.student-nav-link {
    font-size: 20px !important;
    font-weight: 700 !important;
    padding: 15px 25px !important;
}

.student-nav-link i {
    font-size: 1.3rem !important;
    margin-right: 10px !important;
}

.student-user-dropdown .dropdown-toggle {
    font-size: 18px !important;
    font-weight: 600 !important;
    padding: 12px 20px !important;
}

.navbar-brand {
    font-size: 1.8rem !important;
    font-weight: 800 !important;
}

/* إذا كنت تريد تكبير أكبر */
.student-nav-link {
    font-size: 16px !important;
    font-weight: 800 !important;
    padding: 18px 30px !important;
}

.student-nav-link i {
    font-size: 1.5rem !important;
    margin-right: 12px !important;
}

.student-user-dropdown .dropdown-toggle {
    font-size: 20px !important;
    font-weight: 700 !important;
    padding: 15px 25px !important;
}

.navbar-brand {
    font-size: 2rem !important;
    font-weight: 900 !important;
}





            /* Ensure all page headers have white text */
            .page-headers,
            .page-headers h1,
            .page-headers h2,
            .page-headers h3,
            .page-headers h4,
            .page-headers h5,
            .page-headers h6,
            .page-headers p,
            .course-header,
            .course-header h1,
            .course-header h2,
            .course-header h3,
            .course-header h4,
            .course-header h5,
            .course-header h6,
            .course-header p,
            .assignment-header,
            .assignment-header h1,
            .assignment-header h2,
            .assignment-header h3,
            .assignment-header h4,
            .assignment-header h5,
            .assignment-header h6,
            .assignment-header p,
            .purchase-header,
            .purchase-header h1,
            .purchase-header h2,
            .purchase-header h3,
            .purchase-header h4,
            .purchase-header h5,
            .purchase-header h6,
            .purchase-header p,
            .results-header,
            .results-header h1,
            .results-header h2,
            .results-header h3,
            .results-header h4,
            .results-header h5,
            .results-header h6,
            .results-header p,
            .lecture-hero,
            .lecture-hero h1,
            .lecture-hero h2,
            .lecture-hero h3,
            .lecture-hero h4,
            .lecture-hero h5,
            .lecture-hero h6,
            .lecture-hero p {
                color: white !important;
            }
        </style>

<!-- Logo centered 'cutout' style -->
<style>
/* Keep navbar visible for overlay elements */
.student-navbar{ overflow: visible !important; }

/* Brand container: create white rounded "cutout" behind the logo */
.student-navbar .navbar-brand{
  position: relative;
  z-index: 1010;
  display: inline-flex;
  align-items: center;            /* center vertically */
  justify-content: center;
  padding: 4px;                   /* slight breathing room */
  transform: translateY(0);       /* no upward shift */
}

.student-navbar .navbar-brand::before{
  content: "";
  position: absolute;
  inset: -8px -10px;              /* extends around the image */
  background: #ffffff;            /* white cutout */
  border-radius: 16px;            /* rounded */
  box-shadow: 0 10px 24px rgba(0,0,0,0.25);
  z-index: -1;
}

/* Logo itself */
.student-navbar .navbar-brand img{
  height: 54px !important;
  border-radius: 10px;            /* keep logo corners soft */
  box-shadow: 0 2px 10px rgba(0,0,0,0.15);
}

/* Mobile fine‑tune */
@media (max-width: 640px){
  .student-navbar .navbar-brand::before{ inset: -6px -8px; border-radius: 14px; }
  .student-navbar .navbar-brand img{ height: 48px !important; }
}
</style>



        <!-- Page CSS -->
        <?php echo $__env->yieldContent('css'); ?>

        
        <?php echo $settings['headerCode']; ?>

    
<!-- Username size tweak -->
<style>
.student-user-dropdown .dropdown-toggle span{
  font-size: 0.95rem !important;  /* reduce name size */
  font-weight: 600;                /* keep readable */
  letter-spacing: .2px;
}
/* keep overall button compact on large screens */
.student-user-dropdown .dropdown-toggle{
  padding: 8px 14px !important;
  gap: 8px !important;
}
/* On small screens, make it even tidier */
@media (max-width: 640px){
  .student-user-dropdown .dropdown-toggle span{ font-size: 0.9rem !important; }
  .student-user-dropdown .dropdown-toggle{ padding: 6px 12px !important; }
}
</style>
</head>

    <body class="student-layout">
        <div class="student-wrapper">
            <!-- Student Navigation Bar -->
            <nav class="student-navbar">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <!-- Brand -->
                        <a class="navbar-brand d-flex align-items-center" href="<?php echo e(route('home')); ?>">
                            <img src="<?php echo e(asset($settings['logo'])); ?>" alt="Logo" />
                        </a>

                        <!-- Navigation Links - Centered -->
                        <div class="d-flex justify-content-center flex-grow-1 d-none d-md-block">
                            <ul class="student-nav-links">
                                <li class="student-nav-item">
                                    <a href="<?php echo e(route('dashboard.users.courses')); ?>" class="student-nav-link <?php echo e(request()->is('dashboard/users/courses*') ? 'active' : ''); ?>">
                                        <i class="feather-book"></i>
                                        <?php echo app('translator')->get('l.Courses'); ?>
                                    </a>
                                </li>
                                <li class="student-nav-item">
                                    <a href="<?php echo e(route('dashboard.users.tests')); ?>" class="student-nav-link <?php echo e(request()->is('dashboard/users/tests*') ? 'active' : ''); ?>">
                                        <i class="feather-edit-3"></i>
                                        <?php echo app('translator')->get('l.Tests'); ?>
                                    </a>
                                </li>
                                <li class="student-nav-item">
                                    <a href="<?php echo e(route('dashboard.users.lives')); ?>" class="student-nav-link <?php echo e(request()->is('dashboard/users/lives*') ? 'active' : ''); ?>">
                                        <i class="feather-tv"></i>
                                        <?php echo app('translator')->get('l.Live'); ?>
                                    </a>
                                </li>
                                <li class="student-nav-item">
                                    <a href="<?php echo e(route('dashboard.users.score-calc')); ?>" class="student-nav-link <?php echo e(request()->is('dashboard/users/score-calc*') ? 'active' : ''); ?>">
                                        <i class="feather-percent"></i>
                                        <?php echo app('translator')->get('l.Score Calc'); ?>
                                    </a>
                                </li>
                                <!--  desmos اخفاء برنامج --> 
                                <!-- <li class="student-nav-item">
                                    <a href="https://www.desmos.com/calculator" class="student-nav-link" target="_blank" rel="noopener">
                                        <i class="feather-activity"></i>
                                        <?php echo app('translator')->get('l.Desmos'); ?>
                                    </a>
                                </li> -->
                            </ul>
                        </div>

                        <!-- User Dropdown -->
                        <div class="student-user-dropdown dropdown">
                            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <img src="<?php echo e(asset(auth()->user()->photo ?? 'assets/themes/default/images/default-avatar.png')); ?>"
                                     alt="Avatar" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                                <span class="d-none d-sm-inline"><?php echo e(auth()->user()->firstname); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('dashboard.profile')); ?>">
                                        <i class="feather-user me-2"></i>
                                        <?php echo app('translator')->get('l.Profile Details'); ?>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item">
                                            <i class="feather-log-out me-2"></i>
                                            <?php echo app('translator')->get('l.Log Out'); ?>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>

                        <!-- Mobile Menu Toggle -->
                        <button class="btn d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav"
                                style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 8px 12px;">
                            <i class="feather-menu text-white"></i>
                        </button>
                    </div>

                    <!-- Mobile Navigation -->
                    <div class="collapse" id="mobileNav">
                        <ul class="student-nav-links mt-3">
                            <li class="student-nav-item">
                                <a href="<?php echo e(route('dashboard.users.courses')); ?>" class="student-nav-link <?php echo e(request()->is('dashboard/users/courses*') ? 'active' : ''); ?>">
                                    <i class="feather-book"></i>
                                    <?php echo app('translator')->get('l.Courses'); ?>
                                </a>
                            </li>
                            <li class="student-nav-item">
                                <a href="<?php echo e(route('dashboard.users.tests')); ?>" class="student-nav-link <?php echo e(request()->is('dashboard/users/tests*') ? 'active' : ''); ?>">
                                    <i class="feather-edit-3"></i>
                                    <?php echo app('translator')->get('l.Tests'); ?>
                                </a>
                            </li>
                            <li class="student-nav-item">
                                <a href="<?php echo e(route('dashboard.users.score-calc')); ?>" class="student-nav-link <?php echo e(request()->is('dashboard/users/score-calc*') ? 'active' : ''); ?>">
                                    <i class="feather-percent"></i>
                                    <?php echo app('translator')->get('l.Score Calc'); ?>
                                </a>
                            </li>
                            <li class="student-nav-item">
                                <a href="https://www.desmos.com/calculator" class="student-nav-link" target="_blank" rel="noopener">
                                    <i class="feather-activity"></i>
                                    <?php echo app('translator')->get('l.Desmos'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="student-container">
                <div class="student-content">
                    <!-- Page Content -->
                    <div class="container-fluid">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                </div>


            </main>
        </div>

        <!-- Scroll to Top Button -->
        <button class="scroll-to-top" onclick="scrollToTop()">
            <i class="feather-arrow-up"></i>
        </button>

        <!--! ================================================================ !-->
        <!--! [End] Main Content !-->
        <!--! ================================================================ !-->

        <!--! BEGIN: Vendors JS !-->
        <script src="<?php echo e(asset('assets/themes/default/vendors/js/vendors.min.js')); ?>"></script>
        <!-- vendors.min.js {always must need to be top} -->
        <script src="<?php echo e(asset('assets/themes/default/vendors/js/daterangepicker.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/themes/default/vendors/js/apexcharts.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/themes/default/vendors/js/circle-progress.min.js')); ?>"></script>
        <!--! END: Vendors JS !-->
        <!--! BEGIN: Apps Init  !-->
        <script src="<?php echo e(asset('assets/themes/default/js/common-init.min.js')); ?>"></script>
        <!--! END: Apps Init !-->
        <!--! BEGIN: Theme Customizer  !-->
        <script src="<?php echo e(asset('assets/themes/default/js/theme-customizer-init.min.js')); ?>"></script>
        <!--! END: Theme Customizer !-->

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

        <!-- Select2 -->
        <script src="<?php echo e(asset('assets/themes/default/vendors/js/select2.min.js')); ?>"></script>

        <!-- Sweet Alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Live DateTime Update -->
        <script>
            function updateDateTime() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const formatted = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                const element = document.getElementById('live-datetime');
                if (element) {
                    element.textContent = formatted;
                }
            }
            updateDateTime();
            setInterval(updateDateTime, 1000);

            // Scroll to Top functionality
            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // Show/hide scroll to top button
            window.addEventListener('scroll', function() {
                const scrollButton = document.querySelector('.scroll-to-top');
                if (window.pageYOffset > 300) {
                    scrollButton.classList.add('show');
                } else {
                    scrollButton.classList.remove('show');
                }
            });
        </script>

        <?php echo $__env->yieldContent('js'); ?>

        
        <?php echo $settings['footerCode']; ?>

    </body>

</html><?php /**PATH /home/u121952710/domains/mathcrack.com/public_html/resources/views/themes/default/layouts/back/student-master.blade.php ENDPATH**/ ?>