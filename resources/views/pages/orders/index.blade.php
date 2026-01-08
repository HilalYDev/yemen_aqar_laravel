@extends('layouts.master')

@section('title', 'الطلبات')
@section('PageTitle', 'قائمة الطلبات')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h3 class="card-title mb-0">إدارة الطلبات</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الطلب</th>
                            <th>اسم المستخدم</th>
                            <th>رقم الهاتف</th>
                            <th>العقارات المطلوبة</th>
                            <th>الإجمالي</th>
                            <th>الحالة</th>
                            <th>تاريخ الطلب</th>
                        </tr>
                    </thead>
<tbody>
@foreach($orders as $order)
    @foreach($order->items as $item)
        <tr>
            <td>{{ $loop->parent->iteration }}</td> <!-- رقم الطلب -->
            <td>{{ $order->id }}</td>
            <td>{{ $order->user->name ?? '---' }}</td>
            <td>{{ $order->user->phone ?? '---' }}</td>
            <td>{{ $item->property->name ?? '---' }}</td>
            <td>{{ number_format($item->property->price) }} {{ $item->property->currency ?? '' }}</td>
            <td>
                <span class="badge 
                    @if($order->status == 'pending') bg-warning
                    @elseif($order->status == 'completed') bg-success
                    @elseif($order->status == 'canceled') bg-danger
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </td>
            <td>{{ $order->created_at->format('Y-m-d') }}</td>
        </tr>
    @endforeach
@endforeach
</tbody>


                </table>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
