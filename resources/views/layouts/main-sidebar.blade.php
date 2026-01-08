<aside class="app-sidebar bg-body-secondary" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand text-center py-3" style="background-color: white;">
        <a href="{{ url('/') }}" class="brand-link d-inline-block">
            <img src="{{ asset('public/assets/img/logoH.png') }}" 
                 class="brand-image" style="max-height: 50px; width: auto;" alt="الشعار">
        </a>
    </div>
    <!--end::Sidebar Brand-->

    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                
           

                <!-- إدارة المستخدمين -->
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>إدارة المستخدمين</p>
                    </a>
                </li>

                <!--  مالكي العقارات -->
                <li class="nav-item">
                    <a href="{{ route('admin.owners.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>مالكي العقارات</p>
                    </a>
                </li>

                     <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>قائمة الطلبات </p>
                    </a>
                </li>

                <!-- تسجيل الخروج -->
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>تسجيل الخروج</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
