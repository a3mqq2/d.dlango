<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
<title>@yield('title', __('messages.pos')) - {{ __('messages.app_name') }}</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- PWA Meta Tags --}}
<meta name="theme-color" content="#b65f7a">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Hulul POS">
<meta name="mobile-web-app-capable" content="yes">
<link rel="manifest" href="{{ asset('manifest.json') }}">
<link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.png') }}">

{{-- Favicon --}}
<link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Changa:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

{{-- Icons --}}
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />

{{-- Bootstrap CSS --}}
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />

<style>
  :root {
    --hulul-primary: #b65f7a;
    --hulul-primary-dark: #8b4558;
    --hulul-primary-light: #e8a1b7;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Changa', sans-serif !important;
    background: #f4f5f7;
    overflow: hidden;
    height: 100vh;
  }

  /* POS Header */
  .pos-header {
    background: linear-gradient(135deg, var(--hulul-primary) 0%, var(--hulul-primary-dark) 100%);
    color: white;
    padding: 0.5rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 50px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .pos-header .logo {
    height: 35px;
  }

  .pos-header .header-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }

  .pos-header .header-info .info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
  }

  .pos-header .header-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .pos-header .btn-header {
    background: rgba(255,255,255,0.15);
    border: none;
    color: white;
    padding: 0.4rem 0.75rem;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.85rem;
    transition: all 0.2s;
    text-decoration: none;
  }

  .pos-header .btn-header:hover {
    background: rgba(255,255,255,0.25);
    color: white;
  }

  .pos-header .btn-exit {
    background: rgba(220, 53, 69, 0.8);
  }

  .pos-header .btn-exit:hover {
    background: rgba(220, 53, 69, 1);
  }

  /* Main Content */
  .pos-main {
    margin-top: 50px;
    height: calc(100vh - 50px);
    padding: 0.75rem;
    overflow: hidden;
  }

  /* Primary Color Overrides */
  .btn-primary {
    background-color: var(--hulul-primary) !important;
    border-color: var(--hulul-primary) !important;
  }

  .btn-primary:hover, .btn-primary:focus {
    background-color: var(--hulul-primary-dark) !important;
    border-color: var(--hulul-primary-dark) !important;
  }

  .btn-outline-primary {
    color: var(--hulul-primary) !important;
    border-color: var(--hulul-primary) !important;
  }

  .btn-outline-primary:hover {
    background-color: var(--hulul-primary) !important;
    border-color: var(--hulul-primary) !important;
    color: white !important;
  }

  .text-primary {
    color: var(--hulul-primary) !important;
  }

  .bg-primary {
    background-color: var(--hulul-primary) !important;
  }

  .form-control:focus {
    border-color: var(--hulul-primary) !important;
    box-shadow: 0 0 0 0.2rem rgba(182, 95, 122, 0.25) !important;
  }

  .form-select:focus {
    border-color: var(--hulul-primary) !important;
    box-shadow: 0 0 0 0.2rem rgba(182, 95, 122, 0.25) !important;
  }

  /* Scrollbar Styling */
  ::-webkit-scrollbar {
    width: 6px;
    height: 6px;
  }

  ::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }

  ::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: #aaa;
  }
</style>

@stack('styles')
</head>

<body data-pc-direction="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

{{-- POS Header --}}
<header class="pos-header">
  <div class="d-flex align-items-center gap-3">
    <a href="{{ route('home') }}">
      <img src="{{ asset('dlango-white.png') }}" class="logo" alt="{{ __('messages.app_name') }}">
    </a>
  </div>

  <div class="header-info d-none d-md-flex">
    <div class="info-item">
      <i class="ti ti-user"></i>
      <span>{{ Auth::user()->name }}</span>
    </div>
    <div class="info-item">
      <i class="ti ti-calendar"></i>
      <span>{{ now()->format('Y-m-d') }}</span>
    </div>
    <div class="info-item">
      <i class="ti ti-clock"></i>
      <span id="currentTime">{{ now()->format('H:i') }}</span>
    </div>
  </div>

  <div class="header-actions">
    {{-- Language Switcher --}}
    <div class="dropdown">
      <button class="btn-header dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="ti ti-language"></i>
        <span class="d-none d-sm-inline">{{ app()->getLocale() == 'ar' ? 'ع' : 'EN' }}</span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('language.switch', 'ar') }}">العربية</a></li>
        <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">English</a></li>
      </ul>
    </div>

    {{-- Fullscreen Toggle --}}
    <button class="btn-header" id="fullscreenBtn" title="{{ __('messages.fullscreen') }}">
      <i class="ti ti-maximize" id="fullscreenIcon"></i>
    </button>

    {{-- Back to Dashboard --}}
    <a href="{{ route('home') }}" class="btn-header btn-exit" title="{{ __('messages.exit') }}">
      <i class="ti ti-arrow-left"></i>
      <span class="d-none d-sm-inline">{{ __('messages.exit') }}</span>
    </a>
  </div>
</header>

{{-- Main Content --}}
<main class="pos-main">
  @yield('content')
</main>

{{-- Required Scripts --}}
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>

<script>
// Update clock every minute
function updateClock() {
  const now = new Date();
  const timeStr = now.toLocaleTimeString('{{ app()->getLocale() }}', { hour: '2-digit', minute: '2-digit', hour12: false });
  const clockEl = document.getElementById('currentTime');
  if (clockEl) clockEl.textContent = timeStr;
}
setInterval(updateClock, 60000);

// Fullscreen toggle
document.getElementById('fullscreenBtn').addEventListener('click', function() {
  const icon = document.getElementById('fullscreenIcon');
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen();
    icon.classList.remove('ti-maximize');
    icon.classList.add('ti-minimize');
  } else {
    document.exitFullscreen();
    icon.classList.remove('ti-minimize');
    icon.classList.add('ti-maximize');
  }
});

document.addEventListener('fullscreenchange', function() {
  const icon = document.getElementById('fullscreenIcon');
  if (document.fullscreenElement) {
    icon.classList.remove('ti-maximize');
    icon.classList.add('ti-minimize');
  } else {
    icon.classList.remove('ti-minimize');
    icon.classList.add('ti-maximize');
  }
});
</script>

@stack('scripts')

{{-- PWA Scripts --}}
<script src="{{ asset('js/pwa.js') }}" defer></script>

</body>
</html>
