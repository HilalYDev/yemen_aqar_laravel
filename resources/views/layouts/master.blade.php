<!doctype html>
{{-- <html lang="en"> --}}
  <html lang="ar" dir="rtl">

<!--begin::Head-->

<head>
    @include('layouts.head')
</head>
<!--end::Head-->

<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        @include('layouts.main-header')
        <!--end::Header-->

        <!--begin::Sidebar-->
        @include('layouts.main-sidebar')
        <!--end::Sidebar-->

        <!--begin::App Main-->
        @yield('page-header')
     
        <div class="app-content-header">
          <br>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">@yield('PageTitle')</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end ">
                            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"
                                    class="default-color">dashboard</a></li>
                            <li class="breadcrumb-item active">@yield('PageTitle')</li>
                        </ol>

                    </div>
                </div>
                @yield('content')

 <br>
                {{-- @include('layouts.footer') --}}
            </div>
        </div>
                @include('layouts.footer')

    </div>
    <!--end::App Wrapper-->

    <!--begin::Script-->
    @include('layouts.footer-scripts')
    <!--end::Script-->
</body>
<!--end::Body-->

</html>
