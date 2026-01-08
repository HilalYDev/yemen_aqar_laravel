<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // ✅ عرض كل تفاصيل كل عناصر الطلبات (للادمن)
    public function index()
    {
        $items = OrderItem::with(['order.user', 'property'])->get();

        if ($items->isEmpty()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا توجد عناصر في الطلبات',
                'data' => [],
            ], 200);
        }

        $formattedItems = $items->map(function ($item) {
            return [
                'order_item_id' => $item->id,
                'order_id' => $item->order_id,
                'user_id' => $item->order->user_id ?? null,
                'user_name' => $item->order->user->name ?? 'غير متوفر',
                'property_id' => $item->property_id,
                'property_name' => $item->property->name ?? 'غير متوفر',
                'quantity' => $item->quantity,
                'price' => number_format($item->price),
                'currency' => $item->currency,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedItems,
        ], 200);
    }

 

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }





    /**
     * Display the specified resource.
     */
     // ✅ عرض تفاصيل عناصر طلب محدد

public function show(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $request->validate([
        'order_id' => 'required|exists:orders,id',
    ]);

    try {
        // جلب عناصر الطلب مع بيانات العقار المرتبطة
        $orderItems = OrderItem::with('property')
            ->where('order_id', $request->order_id)
            ->get();

        if ($orderItems->isEmpty()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا توجد عناصر لهذا الطلب',
                'data' => [],
            ], 200);
        }

        // تحويل البيانات للشكل المطلوب مثل CartController
        $formattedItems = $orderItems->map(function ($item) {
            return [
                'order_item_id' => $item->id,
                'quantity' => $item->quantity,
                'id' => $item->property->id,
                'name' => $item->property->name,
                'description' => $item->property->description,
                'image' => asset('public/uploads/property/' . $item->property->image),
                'price' => number_format($item->price),
                'currency' => $item->currency,
                'location' => $item->property->location,
                'property_type_id' => $item->property->property_type_id,
                'user_id' => $item->property->user_id,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedItems,
        ], 200);

    } catch (\Exception $e) {
        Log::error("خطأ أثناء عرض عناصر الطلب: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في جلب عناصر الطلب، يرجى المحاولة مرة أخرى.',
        ], 500);
    }
}


    // public function show(Request $request)
    // {
    //     $request->validate([
    //         'order_id' => 'required|exists:orders,id',
    //     ]);

    //     try {
    //         $items = OrderItem::with('property')
    //             ->where('order_id', $request->order_id)
    //             ->get();

    //         if ($items->isEmpty()) {
    //             return response()->json([
    //                 'status' => 'failure',
    //                 'message' => 'لا توجد عناصر لهذا الطلب',
    //                 'data' => [],
    //             ], 200);
    //         }

    //         $formattedItems = $items->map(function ($item) {
    //             return [
    //                 'order_item_id' => $item->id,
    //                 'property_id' => $item->property_id,
    //                 'property_name' => $item->property->name ?? 'غير متوفر',
    //                 'quantity' => $item->quantity,
    //                 'price' => number_format($item->price),
    //                 'currency' => $item->currency,


                    
    //             ];
    //         });

    //         return response()->json([
    //             'status' => 'success',
    //             'data' => $formattedItems,
    //         ], 200);

    //     } catch (\Exception $e) {
    //         Log::error("خطأ أثناء جلب تفاصيل الطلب: " . $e->getMessage());
    //         return response()->json([
    //             'status' => 'failure',
    //             'message' => 'فشل في جلب تفاصيل الطلب، يرجى المحاولة مرة أخرى.',
    //         ], 500);
    //     }
    // }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
