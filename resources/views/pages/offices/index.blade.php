@extends('layouts.master')
@section('title', 'إدارة المكاتب')
@section('PageTitle', 'قائمة المكاتب')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">جميع المكاتب</h3>
           
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="offices-table" class="table table-bordered table-striped text-right">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المكتب</th>
                            <th>صاحب المكتب</th>
                            <th>رقم الهوية</th>
                            <th>حالة الموافقة</th>
                            <th>حالة الصلاحية</th>
                            <th>تاريخ الانتهاء</th>
                            <th>هاتف المكتب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offices as $office)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $office->office_name }}</td>
                            <td>{{ $office->user->name }}</td>
                            <td>{{ $office->identity_number }}</td>
                            
                            <!-- عمود حالة الموافقة -->
                       <td>
    <form action="{{ route('users.toggle-approval', $office->user->id) }}" method="POST" style="display:inline">
        @csrf
        @method('POST')
        <button type="submit" class="btn btn-sm {{ $office->user->admin_approved ? 'btn-danger' : 'btn-success' }}">
            {{ $office->user->admin_approved ? 'إلغاء التفعيل' : 'تفعيل الحساب' }}
        </button>
    </form>
</td>
                            
                            <!-- عمود حالة الصلاحية -->
                            {{-- <td>
                                @if($office->user->expiry_date)
                                    @php
                                        $expiryDate = \Carbon\Carbon::parse($office->user->expiry_date);
                                        $now = \Carbon\Carbon::now();
                                    @endphp
                                    
                                    @if($now->lt($expiryDate))
                                        <span class="badge bg-success">ساري</span>
                                    @else
                                        <span class="badge bg-danger">منتهي</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">غير محدد</span>
                                @endif
                            </td> --}}
                            <td>
    @if($office->user->expiry_date)
        @php
            $expiryDate = \Carbon\Carbon::parse($office->user->expiry_date);
            $now = \Carbon\Carbon::now();
            $isExpired = $now->gt($expiryDate);
        @endphp
        
        @if($isExpired)
            <form action="{{ route('users.renew-subscription', $office->user->id) }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning">
                    <i class="fas fa-sync-alt"></i> تجديد لمدة سنة
                </button>
            </form>
        @else
            <span class="badge bg-success">
                ساري حتى {{ $expiryDate->format('Y-m-d') }}
            </span>
        @endif
    @else
        <form action="{{ route('users.renew-subscription', $office->user->id) }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-check-circle"></i> تفعيل لمدة سنة
            </button>
        </form>
    @endif
</td>
                            <!-- عمود تاريخ الانتهاء -->
                            <td>
                                {{ $office->user->expiry_date ? \Carbon\Carbon::parse($office->user->expiry_date)->format('Y-m-d') : '---' }}
                            </td>
                            
                            <td>{{ $office->office_phone }}</td>
                            <td>
                            
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $offices->links() }}
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
@endsection

@section('styles')
<!-- Toastr -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/toastr/toastr.min.css') }}">
@endsection

@section('scripts')
<!-- DataTables -->
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('adminlte/plugins/toastr/toastr.min.js') }}"></script>

<script>
    $(function () {
        // تهيئة DataTable
        $('#offices-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
            },
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": [4, 8] } // تعطيل الترتيب لأعمدة الإجراءات وحالة الموافقة
            ]
        });

        // تفعيل/إلغاء تفعيل الحساب
      
    });
</script>
@endsection