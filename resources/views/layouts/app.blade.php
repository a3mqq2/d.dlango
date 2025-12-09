{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
{{-- العنوان الرئيسي (قابل للتغيير عبر @section('title')) --}}
<title>@yield('title', __('messages.app_name'))</title>
{{-- [Meta] --}}
<meta charset="utf-8" />
<meta
  name="viewport"
  content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"
/>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta
  name="description"
  content="حلول E-POS - نظام نقاط البيع المتكامل لإدارة المبيعات والمخزون"
/>
<meta
  name="keywords"
  content="حلول E-POS، نظام مبيعات، نقاط البيع، إدارة المخزون، POS"
/>
<meta name="author" content="حلول E-POS" />

<meta name="ast" content="{{ request()->cookie('access_token') }}" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />

{{-- [Flatpickr CSS] --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/flatpickr/dark.css') }}">

{{-- [Select2 CSS] --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">

{{-- [Favicon] --}}
<link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />

{{-- [Font] Family - Changa (Local) --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/changa/changa.css') }}">
<link rel="stylesheet" href="{{ asset('assets/fonts/inter/inter.css') }}" id="main-font-link" />
{{-- [phosphor Icons] https://phosphoricons.com/ --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
{{-- [Tabler Icons] https://tablericons.com --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
{{-- [Feather Icons] https://feathericons.com --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
{{-- [Font Awesome Icons] https://fontawesome.com/icons --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
{{-- [Material Icons] https://fonts.google.com/icons --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
{{-- [Template CSS Files] --}}
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
<script src="{{ asset('assets/js/tech-stack.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />


    <!-- [Font] Family -->
<link rel="stylesheet" href="{{ asset('fonts/inter/inter.css') }}" id="main-font-link" />
<!-- [Phosphor Icons] https://phosphoricons.com/ -->
<link rel="stylesheet" href="{{ asset('fonts/phosphor/duotone/style.css') }}" />
<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{ asset('fonts/tabler-icons.min.css') }}" />
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{ asset('fonts/feather.css') }}" />
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{ asset('fonts/fontawesome.css') }}" />
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{ asset('fonts/material.css') }}" />
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ asset('css/style.css') }}" id="main-style-link" />
<script src="{{ asset('js/tech-stack.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/style-preset.css') }}" />


<style>
  :root {
    --hulul-purple: #b65f7a;
    --hulul-purple-light: #4a2d91;
    --hulul-purple-dark: #b65f7a;
    --hulul-black: #1a1a1a;
    --hulul-white: #ffffff;
    --primary-color: #b65f7a !important;
    --bs-primary: #b65f7a !important;
  }

  body, * {
    font-family: 'Changa', sans-serif !important;
  }

  .datepicker
  {
    width: auto !important;
  }

  /* تجاوز ألوان preset للبنفسجي الداكن */
  [data-pc-preset=preset-1] {
    --pc-sidebar-active-color: #b65f7a !important;
    --bs-blue: #b65f7a !important;
    --bs-primary: #b65f7a !important;
    --bs-primary-rgb: 41, 26, 107 !important;
    --bs-primary-light: #e8e6fa !important;
    --bs-link-color: #b65f7a !important;
    --bs-link-hover-color: #b65f7a !important;
  }

  .pc-sidebar {
    background: linear-gradient(180deg, #b65f7a 0%, #b65f7a 100%) !important;
  }

  .pc-sidebar .pc-navbar > .pc-item.active > .pc-link,
  .pc-sidebar .pc-navbar > .pc-item:focus > .pc-link,
  .pc-sidebar .pc-navbar > .pc-item:hover > .pc-link {
    background: rgba(255, 255, 255, 0.1) !important;
  }

  .btn-primary {
    background-color: #b65f7a !important;
    border-color: #b65f7a !important;
  }

  .btn-primary:hover, .btn-primary:focus {
    background-color: #b65f7a !important;
    border-color: #b65f7a !important;
  }

  .bg-primary {
    background-color: #b65f7a !important;
  }

  .text-primary {
    color: #b65f7a !important;
  }

  a {
    color: #b65f7a;
  }

  a:hover {
    color: #b65f7a;
  }

  .loader-fill {
    background: #b65f7a !important;
  }

  .form-control:focus {
    border-color: #b65f7a !important;
    box-shadow: 0 0 0 0.2rem rgba(41, 26, 107, 0.25) !important;
  }

  .pc-header .pc-head-link:hover,
  .pc-header .pc-head-link.active {
    color: #b65f7a !important;
  }

  .page-link:hover {
    color: #b65f7a !important;
  }

  .page-item.active .page-link {
    background-color: #b65f7a !important;
    border-color: #b65f7a !important;
  }

  .nav-link.active {
    color: #b65f7a !important;
  }

  .badge.bg-primary {
    background-color: #b65f7a !important;
  }

</style>

{{-- Allow child views to inject extra CSS if needed --}}

{{-- vite resources --}}
@vite('resources/js/app.js')


<style>
  mark, .mark { all: unset !important; }
  </style>
  
@stack('styles')



</head>

<body
data-pc-preset="preset-1"
data-pc-sidebar-caption="true"
data-pc-layout="vertical"
data-pc-direction="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
data-pc-theme_contrast=""
data-pc-theme="light"
>
<!-- [ مُحمّل الصفحة ] بداية -->
<div class="loader-bg">
  <div class="loader-track">
    <div class="loader-fill"></div>
  </div>
</div>
<!-- [ مُحمّل الصفحة ] نهاية -->

<!-- [ القائمة الجانبية ] بداية -->
<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="{{ route('home') }}" class="b-brand text-primary">
        <img src="{{ asset('dlango-white.png') }}" class="logo" width="200" alt="شعار حلول E-POS">
      </a>
    </div>
    <div class="navbar-content">
      <div class="card pc-user-card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <img alt="صورة المستخدم" class="user-avtar wid-45 rounded-circle" src="{{ asset('https://ui-avatars.com/api/?name=' . implode('+', explode(' ', Auth::user()->name)) . '&background=e8a1b7&color=fff') }}">
            </div>
            <div class="flex-grow-1 ms-3 me-2">
              <h6 class="mb-0 ">{{ Auth::user()->name }}</h6>
            </div>
            <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
              <svg class="pc-icon">
                <use xlink:href="#custom-sort-outline"></use>
              </svg>
            </a>
          </div>
          <div class="collapse pc-user-links" id="pc_sidebar_userlink">
            <div class="pt-3">
              <a>
                <i class="ti ti-user"></i>
                <span>{{ __('messages.my_profile') }}</span>
              </a>
              <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="ti ti-lock"></i>
                <span>{{ __('messages.logout') }}</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      {{-- القائمة الرئيسية للشريط الجانبي --}}
      <ul class="pc-navbar">



        <li class="pc-item pc-caption">
          <label>{{ __('messages.navigation') }}</label>
        </li>

       
        @include('layouts.menus.menu')

      </ul>
    </div>
  </div>
</nav>
<!-- [ القائمة الجانبية ] نهاية -->

<!-- [ شريط الرأس ] بداية -->
<header class="pc-header">
  <div class="header-wrapper">
    <!-- [كتلة الوسائط المتنقلة] بداية -->
    <div class="me-auto pc-mob-drp">
      <ul class="list-unstyled">
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <li class="pc-h-item pc-sidebar-popup">
          <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <li class="pc-h-item d-none d-md-inline-flex">
          <form class="form-search">
            <i class="search-icon">
              <svg class="pc-icon">
                <use xlink:href="#custom-search-normal-1"></use>
              </svg>
            </i>
            <input type="search" class="form-control" placeholder="{{ __('messages.search') }}" />
          </form>
        </li>
      </ul>
    </div>

    {{-- Language Switcher --}}
    <div class="ms-auto">
      <ul class="list-unstyled">
        <li class="dropdown pc-h-item">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
            <i class="ti ti-language"></i>
            <span class="d-none d-md-inline-block ms-1">{{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}</span>
          </a>
          <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
            <a href="{{ route('language.switch', 'ar') }}" class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
              <span class="me-2">🇸🇦</span> العربية
            </a>
            <a href="{{ route('language.switch', 'en') }}" class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">
              <span class="me-2">🇺🇸</span> English
            </a>
          </div>
        </li>
      </ul>
    </div>

  </div>
</header>
<!-- [ شريط الرأس ] نهاية -->

{{-- نافذة الإعلانات المنبثقة --}}
<div
  class="offcanvas pc-announcement-offcanvas offcanvas-end"
  tabindex="-1"
  id="announcement"
  aria-labelledby="announcementLabel"
>
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="announcementLabel">{{ __('messages.whats_new') }}</h5>
    <button
      type="button"
      class="btn btn-close"
      data-bs-dismiss="offcanvas"
      aria-label="{{ __('messages.close') }}"
    ></button>
  </div>
  <div class="offcanvas-body">
    <p class="text-span">{{ __('messages.today') }}</p>
    <div class="card mb-3">
      <div class="card-body">
        <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
          <div class="badge bg-light-success f-12">{{ __('messages.important_news') }}</div>
          <p class="mb-0 text-muted">{{ __('messages.minutes_ago') }}</p>
          <span class="badge dot bg-warning"></span>
        </div>
        <h5 class="mb-3">{{ __('messages.welcome_system') }}</h5>
        <p class="text-muted">
          {{ __('messages.system_description') }}
        </p>
        <img
          src="{{ asset('assets/images/layout/img-announcement-1.png') }}"
          alt="{{ __('messages.app_name') }}"
          class="img-fluid mb-3"
        />
        <div class="row">
          <div class="col-12">
            <div class="d-grid">
              <a
                class="btn btn-outline-secondary"
                href="#"
                >{{ __('messages.discover_more') }}</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- [ المحتوى الرئيسي ] بداية -->
<div class="pc-container">
  <div class="pc-content">
    {{-- اختياري: قسم مسار التنقل منفصل --}}
    {{--
        <div class="page-header">
          @yield('breadcrumb')
        </div>
    --}}

    <!-- مسار التنقل الافتراضي -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            @include('layouts.messages')
          </div>
          <div class="col-md-12">
            <ul class="breadcrumb">
                @yield('breadcrumb')
            </ul>
          </div>

          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">
                @yield('title', __('messages.dashboard'))
              </h2>
            </div>
          </div>

        </div>
      </div>
    </div>



    @yield('content')

  </div>
</div>
<footer class="pc-footer">
  <div class="footer-wrapper container-fluid">
    <div class="row">
      <div class="col my-1">
        <p class="m-0">
          {{ __('messages.all_rights_reserved') }}. {{ __('messages.app_name') }}
        </p>
      </div>
    </div>
  </div>
</footer>

<!-- Required Js -->
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('assets/js/pcoded.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>


<!-- [Page Specific JS] start -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
@hasSection('skip-dashboard-analytics')
    {{-- Skip dashboard-analytics.js when section is defined --}}
@else
    {{-- <script src="{{ asset('assets/js/pages/dashboard-analytics.js') }}"></script> --}}
@endif
<!-- [Page Specific JS] end -->

<!-- Required JS -->

<script>
  layout_change('light');
</script>

<script>
  change_box_container('false');
</script>

<script>
  layout_caption_change('true');
</script>

<script>
  layout_rtl_change('{{ app()->getLocale() == 'ar' ? 'true' : 'false' }}');
</script>

<script>
  preset_change('preset-1');
</script>

<script>
  main_layout_change('vertical');
</script>

<div
  class="offcanvas border-0 pct-offcanvas offcanvas-end"
  tabindex="-1"
  id="offcanvas_pc_layout"
>
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">{{ __('messages.settings') }}</h5>
    <button
      type="button"
      class="btn btn-icon btn-link-danger ms-auto"
      data-bs-dismiss="offcanvas"
      aria-label="{{ __('messages.close') }}"
    >
      <i class="ti ti-x"></i>
    </button>
  </div>
  <div class="pct-body customizer-body">
    <div class="offcanvas-body py-0">
      <ul class="list-group list-group-flush">
        {{-- بقية إعدادات النافذة المنبثقة --}}
        <li class="list-group-item">
          <div class="d-grid">
            <button class="btn btn-light-danger" id="layoutreset">{{ __('messages.reset_layout') }}</button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>

{{-- السكريبتات النهائية مع إمكانية إضافة سكريبتات من الصفحات الفرعية --}}
<script>
  function changebrand(presetColor) {
    removeClassByPrefix(document.querySelector('body'), 'preset-');
    document.querySelector('body').classList.add(presetColor);
  }
  localStorage.setItem('layout', 'color-header');
</script>

<script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("form").forEach(function (form) {
      form.addEventListener("submit", function (event) {
        let submitButton = form.querySelector("[type='submit']");

        if (submitButton) {
          submitButton.disabled = true;
          submitButton.innerHTML = "{{ __('messages.loading') }}";
        }
      });
    });
  });
</script>
<script src="{{ asset('assets/vendor/flatpickr/flatpickr.min.js') }}"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    flatpickr(
      'input[type="date"], input.datepicker',
      {
        dateFormat: "Y-m-d",   // what gets submitted
        altInput: true,
        altFormat: "F j, Y",    // what the user sees
        allowInput: true
      }
    );
  });
</script>
<script src="{{ asset('assets/vendor/select2/js/select2.min.js') }}"></script>

<script>
$(document).ready(function() {
    $('.select2').select2();
});
</script>

@stack('scripts')

{{-- Logout Form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

</body>
</html>
