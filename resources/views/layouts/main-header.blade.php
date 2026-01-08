<nav class="app-header navbar navbar-expand bg-body">
  <!--begin::Container-->
  <div class="container-fluid">
    <!--begin::Start Navbar Links-->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
          <i class="bi bi-list"></i>
        </a>
      </li>
      <li class="nav-item d-none d-md-block"><a href="{{ url('/') }}" class="nav-link">الصفحة الرئسية</a></li>
    </ul>
    
    <!--end::Start Navbar Links-->
    
    <!--begin::End Navbar Links-->
    <ul class="navbar-nav ms-auto">

      <!-- Navbar items here (same as original file) -->
      {{-- @include('partials.header-nav-items') --}}
    </ul>
    <!--end::End Navbar Links-->
  </div>
  <!--end::Container-->
</nav>