<aside class="app-sidebar bg-body-secondary " data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
 <div class="sidebar-brand" style="background-color: white; padding: 10px;">
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('public/assets/img/logoH.png') }}" 
             class="brand-image" style="max-height: 50px; width: auto;">
    </a>
</div>
    <!--end::Sidebar Brand-->

    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                {{-- <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle-fill"></i>
                  <p>المستخدمين</p>
                </a>
              </li> --}}
              <li class="nav-item">
    <a href="{{ route('admin.users.index') }}" class="nav-link">
        <i class="nav-icon fas fa-users"></i>
        <p>إدارة المستخدمين</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.offices.index') }}" class="nav-link">
        <i class="nav-icon fas fa-building"></i>
        <p>إدارة المكاتب</p>
    </a>
</li>
            </ul>
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
