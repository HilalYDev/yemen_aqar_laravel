@extends('layouts.master')

@section('title', request()->input('filter') == 'expired' ? 'مالكو العقارات منتهية الصلاحية' : 'قائمة مالكي العقارات')
@section('PageTitle', request()->input('filter') == 'expired' ? 'مالكو العقارات منتهية الصلاحية' : 'قائمة مالكي العقارات')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">إدارة مالكي العقارات</h3>
                    <div class="btn-group" role="group">
                        <!-- زر الكل -->
                        <a href="{{ route('admin.owners.index') }}" 
                           class="btn btn-sm {{ !request()->has('filter') ? 'btn-primary' : 'btn-outline-primary' }}">
                           الكل ({{ $totalCount ?? 0 }})
                        </a>

                        <!-- زر منتهي الصلاحية -->
                        <a href="{{ route('admin.owners.index', ['filter' => 'expired']) }}" 
                           class="btn btn-sm {{ request()->input('filter') == 'expired' ? 'btn-primary' : 'btn-outline-primary' }}">
                           منتهي الصلاحية ({{ $expiredCount ?? 0 }})
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive" style="overflow-x: auto;">
                    <table id="owners-table" class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                                <th>كود التحقق</th>
                                <th>حالة التفعيل</th>
                                <th>تاريخ الإنشاء</th>
                                <th>تاريخ الانتهاء</th>
                                <th>حالة الموافقة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($owners as $owner)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $owner->name ?? '---' }}</td>
                                <td>{{ $owner->phone ?? '---' }}</td>
                                <td>{{ $owner->verification_code }}</td>
                                <td>
                                    <span class="badge {{ $owner->approved ? 'bg-success' : 'bg-danger' }}">
                                        {{ $owner->approved ? 'مفعل' : 'غير مفعل' }}
                                    </span>
                                </td>
                                <td>{{ $owner->created_at ? $owner->created_at->format('Y-m-d') : '---' }}</td>
                            <td>
    @if($owner->expiry_date)
        @php
            $expiryDate = \Carbon\Carbon::parse($owner->expiry_date);
            $isExpired = now()->gt($expiryDate);
        @endphp

        @if($isExpired)
            <!-- منتهي الصلاحية → عرض زر التجديد -->
            <form action="{{ route('users.renew-subscription', $owner->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-xs btn-warning">
                    <i class="fas fa-sync-alt"></i> تجديد
                </button>
            </form>
        @else
            <!-- ساري المفعول -->
            <span class="badge bg-success">
                ساري حتى {{ $expiryDate->format('Y-m-d') }}
            </span>
        @endif
    @else
        <!-- بدون تاريخ → عرض زر التفعيل/تجديد -->
        <form action="{{ route('users.renew-subscription', $owner->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-xs btn-primary">
                <i class="fas fa-check-circle"></i> تفعيل / تجديد
            </button>
        </form>
    @endif
</td>

                                <td>
                                    <form action="{{ route('users.toggle-approval', $owner->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-xs {{ $owner->admin_approved ? 'btn-danger' : 'btn-success' }}">
                                            {{ $owner->admin_approved ? 'إلغاء التفعيل' : 'تفعيل' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $owners->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/toastr/toastr.min.css') }}">
<style>
    .table th, .table td { vertical-align: middle; }
    .badge { font-size: 0.85em; padding: 0.35em 0.65em; }
</style>
@endsection

@section('scripts')
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/toastr/toastr.min.js') }}"></script>
<script>
    $(function() {
        $('#owners-table').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json" },
            "responsive": true,
            "autoWidth": false,
            "paging": false,
            "info": false,
            "searching": false,
            "columnDefs": [ { "orderable": false, "targets": [3,7,8] } ],
            "initComplete": function() { this.api().columns.adjust().draw(); }
        });

        @if(session('success'))
            toastr.success('{{ session('success') }}', 'نجاح');
        @endif
        @if(session('error'))
            toastr.error('{{ session('error') }}', 'خطأ');
        @endif
    });
</script>
@endsection
