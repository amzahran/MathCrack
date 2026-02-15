<!DOCTYPE html>
<script>
    window.primaryColor = "{{ $settings['primary_color'] ?? '#FFAB1D' }}";
</script>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title')</title>

    <meta name="keywords" content="@yield('meta_keywords')" />
    <meta name="description" content="@yield('meta_description')" />
    <meta name="author" content="{{ $settings['author'] }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset($settings['favicon']) }}" />

    {{-- canonical --}}
    <link rel="canonical" href="{{ request()->fullUrl() }}">

    {{-- sitemap --}}
    <link rel="sitemap" type="application/xml" href="{{ url('/sitemap.xml') }}" />

    {{-- alternate --}}
    <link rel="alternate" hreflang="x-default" href="{{ url('') . substr(request()->getRequestUri(), 3) }}" />

    <!-- google recaptcha -->
    <script async src="https://www.google.com/recaptcha/api.js?hl={{ app()->getLocale() }}"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/themes/default/css/bootstrap.min.css') }}" />
    <!--! END: Bootstrap CSS-->

    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/themes/default/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/themes/default/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/themes/default/vendors/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/themes/default/vendors/css/select2-theme.min.css') }}">
    <!--! END: Vendors CSS-->

    <!--! BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/themes/default/css/theme.min.css') }}" />
    <!--! END: Theme CSS-->

    <!-- ===== RTL & Floating Add Button Fixes ===== -->
    <style>
      /* اتجاه عام */
      html[dir="rtl"]{direction: rtl;}
      html[dir="ltr"]{direction: ltr;}

      /* محاذاة نص افتراضية عند RTL */
      html[dir="rtl"] body{ text-align: right; }
      html[dir="rtl"] .text-start{ text-align: right !important; }
      html[dir="rtl"] .text-end{   text-align: left  !important;  }

      /* قلب بعض الهوامش المنطقية الشائعة عند RTL */
      html[dir="rtl"] .ms-auto{ margin-right: auto !important; margin-left: 0 !important; }
      html[dir="rtl"] .me-auto{ margin-left:  auto !important; margin-right:0 !important; }

      /* breadcrumb */
      html[dir="rtl"] .breadcrumb{ flex-direction: row-reverse; }
      html[dir="rtl"] .breadcrumb .breadcrumb-item + .breadcrumb-item::before{ float: right; }

      /* Select2 تصحيح العرض */
      .select2-selection__rendered{ padding: 0 !important; width: 88% !important; }

      /* إصلاحات المودال */
      body.modal-open .nxl-container,
      body.modal-open .nxl-header,
      body.modal-open .nxl-navigation,
      body.modal-open .page-header{ filter:none !important; }
      .modal-backdrop{ z-index:1050 !important; }
      .modal{ z-index:1060 !important; }

      /* Sticky footer */
      html,body{ height:100%; margin: 0; padding: 0; }
      .nxl-container{ min-height:100vh; display:flex; flex-direction:column; width: 100%; }
      .nxl-content{ flex:1 0 auto; }
      .footer{ margin-top:auto; }

      /* ===== الزر العائم الأصلي ===== */
      #floatingAddBtn,
      .floating-add-btn{
        position: fixed;
        bottom: 22px;
        width: 56px; height: 56px;
        border-radius: 50%;
        border: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        cursor: pointer;
        z-index: 2147483647 !important;
        pointer-events: auto !important;
        visibility: visible !important;
        opacity: 1 !important;
      }

      html[dir="rtl"] #floatingAddBtn,
      html[dir="rtl"] .floating-add-btn{
        right: 22px !important;
        left: auto !important;
      }

      html[dir="ltr"] #floatingAddBtn,
      html[dir="ltr"] .floating-add-btn{
        left: 22px !important;
        right: auto !important;
      }

      #floatingAddBtn.pulse, .floating-add-btn.pulse{
        animation: fabPulse 1.4s ease-in-out infinite;
      }
      @keyframes fabPulse{
        0%{ box-shadow: 0 0 0 0 rgba(40,167,69,.45); }
        70%{ box-shadow: 0 0 0 14px rgba(40,167,69,0); }
        100%{ box-shadow: 0 0 0 0 rgba(40,167,69,0); }
      }

      /* ===== التخطيط الرئيسي المصحح ===== */
      
      .nxl-container {
          display: flex;
          min-height: 100vh;
          position: relative;
          width: 100%;
          margin: 0;
          padding: 0;
      }

      /* الشريط الجانبي */
      .nxl-navigation {
          width: 280px;
          min-width: 280px;
          background: #f8f9fa;
          height: 100vh;
          position: fixed;
          top: 0;
          overflow-y: auto;
          z-index: 1000;
          transition: transform 0.3s ease;
      }

      html[dir="rtl"] .nxl-navigation {
          right: 0;
          left: auto;
          border-left: 1px solid #dee2e6;
      }

      html[dir="ltr"] .nxl-navigation {
          left: 0;
          right: auto;
          border-right: 1px solid #dee2e6;
      }

      /* المحتوى الرئيسي - مصحح تماماً */
      .nxl-content {
          flex: 1;
          min-height: 100vh;
          background: #fff;
          transition: all 0.3s ease;
          position: relative;
          box-sizing: border-box;
      }

      /* إصلاح المسافات للعربي */
      html[dir="rtl"] .nxl-content {
          margin-right: 280px;
          margin-left: 0;
          width: calc(100% - 280px);
      }

      /* إصلاح المسافات للإنجليزي - هذا هو المهم */
      html[dir="ltr"] .nxl-content {
          margin-left: 280px;
          margin-right: 0;
          width: calc(100% - 280px);
      }

      /* الهيدر ثابت */
      .nxl-header {
          position: fixed;
          top: 0;
          z-index: 1001;
          background: white;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
          transition: all 0.3s ease;
          box-sizing: border-box;
      }

      html[dir="rtl"] .nxl-header {
          right: 280px;
          left: 0;
          width: calc(100% - 280px);
      }

      html[dir="ltr"] .nxl-header {
          left: 280px;
          right: 0;
          width: calc(100% - 280px);
      }

      /* تحسين منطقة المحتوى */
      .page-header {
          margin-top: 80px;
          padding: 25px 30px 0 30px;
          background: white;
          width: 100%;
          box-sizing: border-box;
      }

      /* منطقة محتوى الصفحة - مصححة */
      .page-body {
          padding: 20px 30px;
          background: #f5f7fa;
          min-height: calc(100vh - 180px);
          width: 100%;
          box-sizing: border-box;
          margin: 0;
      }

      /* تأكد من أن الجداول والعناصر تأخذ العرض الكامل */
      .page-body .table-responsive,
      .page-body .card,
      .page-body .form-container {
          width: 100%;
          box-sizing: border-box;
      }

      /* زر toggle القائمة */
      .mobile-menu-toggle {
          position: fixed;
          top: 15px;
          z-index: 1002;
          background: #667eea;
          color: white;
          border: none;
          border-radius: 8px;
          padding: 10px 15px;
          cursor: pointer;
          box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      }

      html[dir="rtl"] .mobile-menu-toggle {
          right: 15px;
      }

      html[dir="ltr"] .mobile-menu-toggle {
          left: 15px;
      }

      /* تحسين استجابة للشاشات الصغيرة */
      @media (max-width: 1024px) {
          .nxl-navigation {
              transform: translateX(100%);
          }
          
          html[dir="ltr"] .nxl-navigation {
              transform: translateX(-100%);
          }
          
          .nxl-navigation.mobile-open {
              transform: translateX(0) !important;
          }
          
          .nxl-content {
              margin-right: 0 !important;
              margin-left: 0 !important;
              width: 100% !important;
          }
          
          .nxl-header {
              right: 0 !important;
              left: 0 !important;
              width: 100% !important;
          }
          
          .page-header {
              margin-top: 70px;
              padding: 20px 15px 0 15px;
          }
          
          .page-body {
              padding: 15px;
          }
          
          .mobile-menu-toggle {
              display: block !important;
          }
      }

      @media (min-width: 1025px) {
          .mobile-menu-toggle {
              display: none !important;
          }
      }

      .select2-selection__rendered{ padding:0 !important; width:88% !important; }
    </style>

    <!-- Page CSS -->
    @yield('css')

    {{-- header code --}}
    {!! $settings['headerCode'] !!}
  </head>

  <body>

    <!-- زر toggle للقائمة في الشاشات الصغيرة -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="feather-menu"></i>
    </button>

    <!-- الهيكل الأصلي المحفوظ مع تحسينات -->
    <div class="nxl-container">
      
      <!-- header -->
      @include('themes.default.layouts.back.header')
      
      <!-- navigation -->
      @include('themes.default.layouts.back.nav')

      <div class="nxl-content">
        
        <!-- [ page-header ] start -->
        <div class="page-header">
          <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
              <h5 class="m-b-10">@lang('l.Dashboard')</h5>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('l.Home')</a></li>
              <li class="breadcrumb-item">@yield('title')</li>
            </ul>
          </div>
          <div class="page-header-right ms-auto d-none d-md-block">
            <div class="page-header-right-items">
              <div class="d-flex d-md-none">
                <a href="javascript:void(0)" class="page-header-right-close-toggle">
                  <i class="feather-arrow-left me-2"></i>
                  <span>Back</span>
                </a>
              </div>
              <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                <div class="reportrange-picker d-flex align-items-center">
                  <span class="reportrange-picker-field">
                    <span id="live-datetime"></span>
                    <script>
                      function updateDateTime(){
                        const now = new Date();
                        const y = now.getFullYear();
                        const m = String(now.getMonth()+1).padStart(2,'0');
                        const d = String(now.getDate()).padStart(2,'0');
                        const h = String(now.getHours()).padStart(2,'0');
                        const i = String(now.getMinutes()).padStart(2,'0');
                        const s = String(now.getSeconds()).padStart(2,'0');
                        document.getElementById('live-datetime').textContent = `${y}-${m}-${d} ${h}:${i}:${s}`;
                      }
                      updateDateTime(); setInterval(updateDateTime, 1000);
                    </script>
                  </span>
                </div>
              </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
              <a href="javascript:void(0)" class="page-header-right-open-toggle">
                <i class="feather-align-right fs-20"></i>
              </a>
            </div>
          </div>
        </div>
        <!-- [ /page-header ] end -->

        <!-- محتوى الصفحة الرئيسي -->
        <div class="page-body">
          @if (session()->has('impersonated_by'))
            <a href="{{ route('impersonate.leave') }}"
              class="btn btn-sm btn-danger shadow"
              style="position: fixed; top: 50%; right: 20px; transform: translateY(-50%); z-index: 9999; border-radius: 20px; display: flex; align-items: center; justify-content: center; padding: 6px 16px; font-size: 15px;"
              title="@lang('l.Leave')">
              <i class="feather-log-out" style="font-size: 18px;"></i>
              Exit
            </a>
          @endif

          @yield('content')
        </div>

        @include('themes.default.layouts.back.footer')
      </div>
    </div>

    <!-- Core JS -->
    <!--! BEGIN: Vendors JS !-->
    <script src="{{ asset('assets/themes/default/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/themes/default/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/themes/default/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/themes/default/vendors/js/circle-progress.min.js') }}"></script>
    <!--! END: Vendors JS !-->

    <!--! BEGIN: Apps Init !-->
    <script src="{{ asset('assets/themes/default/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/themes/default/js/dashboard-init.min.js') }}"></script>
    <script src="{{ asset('assets/themes/default/vendors/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/themes/default/vendors/js/select2-active.min.js') }}"></script>
    <!--! END: Apps Init !-->

    <!--! BEGIN: Theme Customizer !-->
    <script src="{{ asset('assets/themes/default/js/theme-customizer-init.min.js') }}"></script>
    <!--! END: Theme Customizer !-->

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>

    <!-- google recaptcha required -->
    <script>
      window.addEventListener('load', () => {
        const $recaptcha = document.querySelector('#g-recaptcha-response');
        if ($recaptcha) $recaptcha.setAttribute('required','required');
      });
    </script>

    <!-- Tagify -->
    <script>
      document.querySelectorAll('[id^="meta_keywords"]').forEach(function(input){
        new Tagify(input);
      });
    </script>

    <!-- Quill toolbar config -->
    <script>
      const fullToolbar = [
        [{font:[]},{size:[]}],
        ['bold','italic','underline','strike'],
        [{color:[]},{background:[]}],
        [{script:'super'},{script:'sub'}],
        [{header:'1'},{header:'2'},'blockquote','code-block'],
        [{list:'ordered'},{list:'bullet'},{indent:'-1'},{indent:'+1'}],
        ['direction',{align:[]}],
        ['link','image','video','formula'],
        ['clean']
      ];
    </script>

    <!-- JavaScript المعدل لإدارة القائمة -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
          const mobileMenuToggle = document.getElementById('mobileMenuToggle');
          const mainNavigation = document.querySelector('.nxl-navigation');
          
          if (mobileMenuToggle && mainNavigation) {
              mobileMenuToggle.addEventListener('click', function() {
                  mainNavigation.classList.toggle('mobile-open');
              });
              
              // إغلاق القائمة عند النقر خارجها في الشاشات الصغيرة
              document.addEventListener('click', function(e) {
                  if (window.innerWidth <= 1024 && 
                      !mainNavigation.contains(e.target) && 
                      !mobileMenuToggle.contains(e.target)) {
                      mainNavigation.classList.remove('mobile-open');
                  }
              });
          }
          
          // إغلاق القائمة عند تغيير حجم النافذة إلى حجم كبير
          window.addEventListener('resize', function() {
              if (window.innerWidth > 1024) {
                  if (mainNavigation) {
                      mainNavigation.classList.remove('mobile-open');
                  }
              }
          });
      });
    </script>

    @yield('js')

    {{-- footer code --}}
    {!! $settings['footerCode'] !!}
  </body>
</html>