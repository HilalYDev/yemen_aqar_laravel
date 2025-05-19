{{-- @extends('layouts.master')
@section('title', 'إدارة المستخدمين')
@section('PageTitle', 'قائمة المستخدمين') --}}
@extends('layouts.master')
@section('title', 'إدارة المستخدمين')
@section('page-header')
@section('PageTitle', 'قائمة المستخدمين')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">جميع المستخدمين</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="users-table" class="table table-bordered table-striped text-right">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الهاتف</th>
                            <th>النوع</th>
                            <th>حالة الموافقة</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                @if($user->type == 'office')
                                    <span class="badge bg-success">مكتب</span>
                                @else
                                    <span class="badge bg-primary">مستخدم عادي</span>
                                @endif
                            </td>
                            <td>
                                @if($user->admin_approved)
                                    <span class="badge bg-success">موافق</span>
                                @else
                                    <span class="badge bg-danger">غير موافق</span>
                                @endif
                            </td>
                            <td>{{ $user->expiry_date ?? 'غير محدد' }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <form action="#" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $users->links() }}
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

@section('scripts')
<!-- DataTables -->
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(function () {
        $('#users-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
            },
            "paging": false,
            "searching": false,
            "info": false
        });
    });
</script>
@endsection