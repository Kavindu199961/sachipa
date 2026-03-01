<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Sachipa Curtain</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- Bootstrap 5 CSS (must come after app.min.css so it can override if needed) -->
   <!-- SweetAlert2 CSS -->
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">




  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('assets/img/invoice.png') }}" type="image/png">
</head>


<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar sticky">
        <div class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn">
                <i data-feather="align-justify"></i></a></li>
            <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                <i data-feather="maximize"></i>
              </a></li>
          </ul>
        </div>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown dropdown-list-toggle">
            <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle d-flex align-items-center">
              <i class="fas fa-user-circle" style="font-size: 35px; color:rgb(0, 0, 0);"></i>
              <span class="ml-2 d-none d-md-inline text-dark font-weight-bold" style="font-size: 16px;">
                {{ auth()->user()->name ?? 'User' }}
              </span>
            </a>

            <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
              <div class="dropdown-header">
                Profile
                <div class="float-right"></div>
              </div>
              <div class="dropdown-list-content">
                <div class="dropdown-item text-center">
                  @php
                    $user = auth()->user();
                    $shopDetail = $user ? App\Models\MyShopDetail::where('user_id', $user->id)->first() : null;
                  @endphp
                  @if($shopDetail && $shopDetail->logo_image)
                    <img src="{{ Storage::url($shopDetail->logo_image) }}" alt="User Image" class="rounded-circle mt-2" width="60" height="60">
                  @else
                    <img src="{{ asset('/assets/img/user.png') }}" alt="Default User" class="rounded-circle mt-2" width="60" height="60">
                  @endif

                  <span class="message-user d-block font-weight-bold mt-2">
                    Hello, {{ auth()->user()->name ?? 'User' }}
                  </span>

                  <form action="{{ route('logout') }}" method="POST" class="mt-2 mb-2">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                      <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </nav>

      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand text-center py-3">
            @php
              $user = auth()->user();
              $shopDetail = $user ? App\Models\MyShopDetail::where('user_id', $user->id)->first() : null;
            @endphp

            <a href="/dashboard">
              @if($shopDetail && $shopDetail->logo_image)
                <img src="{{ Storage::url($shopDetail->logo_image) }}" alt="Logo" class="header-logo" style="width:65px; height: auto;">
              @else
                <img src="{{ asset('/assets/logo/logo.png') }}" alt="Logo" class="header-logo" style="width:120px; height: auto;">
              @endif

              <div class="logo-name mt-2" style="font-weight: bold; font-size: 12px; color: #333;">
                {{ $shopDetail->shop_name ?? 'User ID: ' . ($user->id ?? 'CeylonGIT') }}
              </div>
            </a>
          </div>

          <ul class="sidebar-menu mt-4">
            <li class="menu-header">Navigations</li>

            <li class="dropdown {{ request()->is('dashboard*') ? 'active' : '' }}">
              <a href="{{ route('dashboard') }}" class="nav-link">
                <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
              </a>
              </li>

            <li class="dropdown {{ request()->is('user/myshop*') ? 'active' : '' }}">
              <a href="{{ route('user.myshop.index') }}" class="nav-link">
                <i class="fas fa-home"></i><span>My Shop Details</span>
              </a>
            </li>

            <li class="dropdown {{ request()->is('user/stock*') ? 'active' : '' }}">
              <a href="{{ route('user.stock.index') }}" class="nav-link">
                <i class="fas fa-boxes"></i><span>Stock</span>
              </a>
            </li>

            <li class="dropdown {{ request()->is('user/today-item*') ? 'active' : '' }}">
              <a href="{{ route('user.today_item.index') }}" class="nav-link">
                <i class="fas fa-shopping-cart"></i><span>Today's Items</span>
              </a>
            </li>

            <li class="dropdown {{ request()->is('cost-calculator*') || request()->routeIs('cost.calculator') ? 'active' : '' }}">
              <a href="{{ route('cost.calculator') }}" class="nav-link">
                <i class="fas fa-calculator"></i>
                <span>Cost Calculator</span>
              </a>
            </li>

            <li class="dropdown {{ request()->is('user/customer*') ? 'active' : '' }}">
              <a href="{{ route('user.customer.index') }}" class="nav-link">
                <i class="fas fa-ruler-combined"></i>
                <span>Fabric Calculation</span>
              </a>
            </li>

            <li class="dropdown {{ request()->is('invoices*') ? 'active' : '' }}">
              <a href="{{ route('invoices.index') }}" class="nav-link">
                <i class="fas fa-file-invoice"></i>
                <span>Invoices</span>
              </a>
            </li>

           <li class="dropdown {{ request()->is('reports/invoices*') ? 'active' : '' }}">
            <a href="{{ route('reports.invoices.index') }}" class="nav-link">
              <i class="fas fa-chart-bar"></i>
             <span>Invoice Report</span>
            </a>
            </li>
            <li class="dropdown">
              <a href="{{ route('logout') }}" class="nav-link mt-5"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
          </ul>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        @yield('content')
      </div>

      <footer class="main-footer">
        <div class="footer-left">
          <a href="https://ceylongit.lk/" target="_blank">Powered by CeylonGIT</a>
        </div>
        <div class="footer-right"></div>
      </footer>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════
       SCRIPTS — strict load order to avoid Bootstrap conflicts
       1. jQuery (once)
       2. SweetAlert2
       3. Toastr
       4. Your template scripts (app.min.js etc.) — these may
          bundle Bootstrap 4 internally, so we load BS5 AFTER.
       5. Bootstrap 5 Bundle (includes Popper) — loaded LAST
          so it wins and data-bs-* attributes work correctly.
  ══════════════════════════════════════════════════════ --}}

  <!-- SweetAlert2 JS -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- 1. jQuery (single copy) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- 2. SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- 3. Toastr -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <!-- 4. Template / legacy scripts (may include BS4 internally) -->
  <script src="{{ asset('assets/js/app.min.js') }}"></script>
  <script src="{{ asset('assets/bundles/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/js/page/index.js') }}"></script>
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>

  <!-- 5. Bootstrap 5 Bundle — MUST be last so bootstrap.Modal works -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar active scroll -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const activeItem = document.querySelector('.main-sidebar .sidebar-menu .active');
      if (activeItem) {
        setTimeout(() => {
          activeItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
          activeItem.classList.add('highlight-active');
        }, 300);
      }
    });
  </script>

  <!-- Page-specific scripts injected here -->
  @stack('scripts')

</body>
</html>