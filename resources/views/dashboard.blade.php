<!doctype html>
<html lang="ar" dir="rtl">
@section('title', 'Dashboard')

<head>
    @include('layouts.head')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">

        @include('layouts.main-header')
        @include('layouts.main-sidebar')

        <div class="app-content-header">
            <br>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Dashboard</h3>
                    </div>
                </div>

                <!--begin::App Content-->
                <div class="app-content">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- عدد المستخدمين -->
                               <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-primary">
                            <div class="inner">
                                <h3>{{ $userCount }}</h3>
                                <p>المستخدمون المسجلون</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            <a href="#" class="small-box-footer">
                                المزيد <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
             
                    <!-- إحصائية المكاتب -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-success">
                            <div class="inner">
                                <h3>{{ $officeCount }}</h3>
                                <p>المكاتب المسجلة</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/>
                                <path d="M7 12h2v5H7zm4-7h2v12h-2zm4 5h2v7h-2z"/>
                            </svg>
                            <a href="#" class="small-box-footer">
                                المزيد <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>

               <!-- المكاتب بدون موافقة -->
<div class="col-lg-3 col-6">
    <div class="small-box text-bg-warning">
        <div class="inner">
            <h3>{{ $unapprovedOffices }}</h3>
            <p>مكاتب بانتظار الموافقة</p>
        </div>
        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
        </svg>
        <a href="{{ route('admin.owners.index') }}?filter=unapproved" class="small-box-footer">
            معالجة الطلبات <i class="bi bi-link-45deg"></i>
        </a>
    </div>
</div>

                    <!-- المكاتب منتهية الصلاحية -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-danger">
                            <div class="inner">
                                <h3>{{ $expiredOffices }}</h3>
                                <p>مكاتب منتهية الصلاحية</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                                <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                            </svg>
                            <a href="#" class="small-box-footer">
                                المزيد <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>

                    <!-- المكاتب الجديدة -->
                    <div class="col-lg-3 col-6 mt-3">
                        <div class="small-box text-bg-info">
                            <div class="inner">
                                <h3>{{ $newOffices }}</h3>
                                <p>مكاتب جديدة (أسبوع)</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                            <a href="#" class="small-box-footer">
                                المزيد <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
    <!-- المستخدمين الجدد -->
                    <div class="col-lg-3 col-6 mt-3">
                        <div class="small-box text-bg-secondary">
                            <div class="inner">
                                <h3>{{ $newUsers }}</h3>
                                <p>مستخدمين جدد (أسبوع)</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            <a href="#" class="small-box-footer">
                                المزيد <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
                <br>
            </div>
        </div>

        @include('layouts.footer')

    </div>

    @include('layouts.footer-scripts')
</body>

</html>
