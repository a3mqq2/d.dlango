<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
  <head>
    <title>@yield('title', __('messages.login') . ' | ' . __('messages.app_name'))</title>
    <!-- [Meta] -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta
      name="description"
      content="حلول E-POS - نظام نقاط البيع المتكامل لإدارة المبيعات والمخزون"
    />

    {{-- favicon --}}
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon" />

    <meta
      name="keywords"
      content="حلول E-POS، نظام مبيعات، نقاط البيع، إدارة المخزون، POS"
    />
    <meta name="author" content="حلول E-POS" />

    <!-- [Favicon] icon -->

    <!-- [Font] Family - Changa من Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fonts/inter/inter.css') }}" id="main-font-link" />
    <!-- [phosphor Icons] -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
    <!-- [Tabler Icons] -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
    <!-- [Feather Icons] -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
    <!-- [Font Awesome Icons] -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
    <!-- [Material Icons] -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />

    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
    <script src="{{ asset('assets/js/tech-stack.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />

    <!-- تنسيق CSS للشاشات الصغيرة وألوان حلول E-POS -->
    <style>
      :root {
        --hulul-purple: #b65f7a;
        --hulul-purple-light: #4a2d91;
        --hulul-purple-dark: #b65f7a;
        --hulul-black: #1a1a1a;
        --hulul-white: #ffffff;
      }
      body, * {
        font-family: 'Changa', sans-serif !important;
      }
      /* تجاوز ألوان preset-3 للبنفسجي الداكن */
      [data-pc-preset=preset-3] {
        --pc-sidebar-active-color: #b65f7a !important;
        --bs-blue: #b65f7a !important;
        --bs-primary: #b65f7a !important;
        --bs-primary-rgb: 41, 26, 107 !important;
        --bs-primary-light: #e8e6fa !important;
        --bs-link-color: #b65f7a !important;
        --bs-link-hover-color: #b65f7a !important;
      }
      .btn-primary {
        background-color: #b65f7a !important;
        border-color: #b65f7a !important;
      }
      .btn-primary:hover, .btn-primary:focus {
        background-color: #b65f7a !important;
        border-color: #b65f7a !important;
      }
      .text-primary {
        color: #b65f7a !important;
      }
      .bg-primary {
        background-color: #b65f7a !important;
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
      @media (max-width: 768px) {
        .auth-wrapper {
          display: flex;
          flex-direction: column-reverse; /* وضع المحتوى الجانبي في الأعلى */
        }
        .auth-sidecontent {
          order: -1; /* التأكد من ظهوره قبل نموذج المصادقة */
          width: 100%;
        }
        .auth-form {
          width: 100%;
        }
      }
    </style>
  </head>
  <body
    data-pc-preset="preset-3"        {{-- نظام ألوان مخصص --}}
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

    <div class="auth-main">
      <div class="auth-wrapper v3">
        <div class="auth-form">
          {{-- رأس الصفحة مع الشعار --}}
          <div class="auth-header row">
            <div class="col my-1"
              style="display: flex; justify-content: center; align-items: center;"
            >
              {{-- شعار حلول E-POS --}}
              <a href="#">
                <img src="{{ asset('logo.png') }}" alt="{{ __('messages.app_name') }}" width="300" />
              </a>
            </div>
            <div class="col-auto my-1 d-flex align-items-center gap-3">
              {{-- Language Switcher --}}
              <div class="dropdown">
                <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="ti ti-language"></i>
                  {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('language.switch', 'ar') }}">🇸🇦 العربية</a></li>
                  <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">🇺🇸 English</a></li>
                </ul>
              </div>
              {{-- التحقق من تسجيل الدخول وإضافة زر الخروج --}}
              @if (auth()->check())
                <a href="{{ route('logout') }}" class="">{{ __('messages.logout') }} <i class="fa fa-sign-out-alt"></i></a>
              @endif
            </div>
          </div>

          {{-- حاوية البطاقة --}}
          <div class="card my-5">
            <div class="card-body">
              {{--
                هنا يتم عرض محتوى النماذج الديناميكية مثل تسجيل الدخول والتسجيل
                يتم استبدال كتلة المحتوى بالكامل باستخدام @yield('content')
              --}}
              @yield('content')
            </div>
          </div>

          {{-- نص التذييل --}}
          <div class="auth-footer">
            <p class="m-0">© 2024 {{ __('messages.app_name') }} - {{ __('messages.all_rights_reserved') }}</p>
          </div>
        </div>

        {{-- المحتوى الجانبي (العرض الدوار / الشهادات) --}}
        <div class="auth-sidecontent" style="background-color: #b65f7a;">
          <div class="p-3 px-lg-5 text-center">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                {{-- الشريحة الأولى --}}
                <div class="carousel-item active">
                  <p class="text-white">
                    {{ __('messages.slide_1') }}
                  </p>
                </div>

                <div class="carousel-item">
                  <p class="text-white">
                    {{ __('messages.slide_2') }}
                  </p>
                </div>

                <div class="carousel-item">
                  <p class="text-white">
                    {{ __('messages.slide_3') }}
                  </p>
                </div>
              </div>
              <div class="carousel-indicators position-relative mt-3">
                <button
                  type="button"
                  data-bs-target="#carouselExampleIndicators"
                  data-bs-slide-to="0"
                  class="active"
                  aria-current="true"
                  aria-label="الشريحة 1"
                ></button>
                <button
                  type="button"
                  data-bs-target="#carouselExampleIndicators"
                  data-bs-slide-to="1"
                  aria-label="الشريحة 2"
                ></button>
                <button
                  type="button"
                  data-bs-target="#carouselExampleIndicators"
                  data-bs-slide-to="2"
                  aria-label="الشريحة 3"
                ></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- [ نهاية المحتوى الرئيسي ] -->

    <!-- [ملفات JS خاصة بالصفحة] بداية -->
    <!-- يمكنك إضافة سكريبتات من صفحات فرعية باستخدام @push('scripts') -->
    @stack('scripts')
    <!-- [ملفات JS خاصة بالصفحة] نهاية -->

    <!-- ملفات JS المطلوبة -->
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

    {{-- إعدادات التخطيط الافتراضية --}}
    <script> layout_change('light'); </script>
    <script> change_box_container('false'); </script>
    <script> layout_caption_change('true'); </script>
    <script> layout_rtl_change('{{ app()->getLocale() == 'ar' ? 'true' : 'false' }}'); </script>
    <script> preset_change('preset-3'); </script>
    <script> main_layout_change('vertical'); </script>


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

  </body>
</html>
