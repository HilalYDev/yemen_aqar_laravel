<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{


    public function checkout(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    $userId = $request->user_id;

    // 🟢 جلب عناصر السلة غير المباعة فقط
    $cartItems = Cart::with('property')
        ->where('user_id', $userId)
        ->whereHas('property', function ($q) {
            $q->where('is_sold', false);
        })
        ->get();

    // ❌ إذا لم يتبق أي عنصر صالح
    if ($cartItems->isEmpty()) {
        return response()->json([
            'status' => 'failure',
            'message' => 'جميع العقارات في السلة تم بيعها مسبقًا.',
        ], 200);
    }

    DB::beginTransaction();

    try {
        // 🧮 حساب المجموع (عقارات غير مباعة فقط)
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->property->price * $item->quantity;
        });

        // 🧾 إنشاء الطلب
        $order = Order::create([
            'user_id' => $userId,
            'total_price' => $totalPrice,
            'status' => 'completed',
        ]);

        // 🧱 إنشاء عناصر الطلب + بيع العقار
        foreach ($cartItems as $item) {

            // حماية إضافية
            if ($item->property->is_sold) {
                continue;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'property_id' => $item->property_id,
                'quantity' => $item->quantity,
                'price' => $item->property->price,
                'currency' => $item->property->currency,
            ]);

            // 🔒 تحديث حالة العقار
            // $item->property->update([
            //     'is_sold' => true
            // ]);
        }

        // 🧹 حذف عناصر السلة للمشتري
        // Cart::where('user_id', $userId)->delete();

        // 🧹 تنظيف السلات الأخرى من العقارات المباعة
        Cart::whereHas('property', function ($q) {
            $q->where('is_sold', true);
        })->delete();

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'تم إتمام الطلب بنجاح.',
            'order_id' => $order->id,
            'total_price' => $totalPrice,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Checkout Error: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في إتمام الطلب، يرجى المحاولة مرة أخرى.',
        ], 500);
    }
}

// public function checkout(Request $request)
// {
//     $request->validate([
//         'user_id' => 'required|exists:users,id',
//     ]);

//     $userId = $request->user_id;

//     // جلب جميع عناصر السلة للمستخدم
//     $cartItems = Cart::with('property')
//                      ->where('user_id', $userId)
//                      ->get();

//     if ($cartItems->isEmpty()) {
//         return response()->json([
//             'status' => 'failure',
//             'message' => 'السلة فارغة، لا يمكن إتمام الطلب.',
//         ], 200);
//     }

//     DB::beginTransaction();

//     try {
//         // حساب المجموع الكلي
//         $totalPrice = $cartItems->sum(function ($item) {
//             return $item->property->price * $item->quantity;
//         });

//         // إنشاء الطلب
//         $order = Order::create([
//             'user_id' => $userId,
//             'total_price' => $totalPrice,
//             'status' => 'completed', // أو pending حسب رغبتك
//         ]);

//         // إنشاء عناصر الطلب
//         foreach ($cartItems as $item) {
//             OrderItem::create([
//                 'order_id' => $order->id,
//                 'property_id' => $item->property_id,
//                 'quantity' => $item->quantity,
//                 'price' => $item->property->price,
//                   'currency' => $item->property->currency,
//             ]);
            
//             // ✅ تحديث حالة العقار إلى مباع
//             $item->property->update([
//                 'is_sold' => true
//             ]);
//         }

//         // حذف كل عناصر السلة بعد الشراء
//         Cart::where('user_id', $userId)->delete();

//         DB::commit();

//         return response()->json([
//             'status' => 'success',
//             'message' => 'تم إتمام الطلب بنجاح.',
//             'order_id' => $order->id,
//             'total_price' => $totalPrice
//         ], 201);

//     } catch (\Exception $e) {
//         DB::rollBack();
//         Log::error("خطأ أثناء إتمام الطلب: " . $e->getMessage());

//         return response()->json([
//             'status' => 'failure',
//             'message' => 'فشل في إتمام الطلب، يرجى المحاولة مرة أخرى.',
//         ], 500);
//     }}




    // ✅ عرض كل الطلبات (مثالي لواجهة الادمن)
    public function index()
    {
        $orders = Order::with('user')->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا توجد طلبات',
                'data' => [],
            ], 200);
        }

        $formattedOrders = $orders->map(function ($order) {
            return [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'user_name' => $order->user->name ?? 'غير متوفر',
                'total_price' => number_format($order->total_price),
                'status' => $order->status,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedOrders,
        ], 200);
    }

    // ✅ عرض كل الطلبات لمستخدم محدد
    public function show(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $orders = Order::with('items.property')
                ->where('user_id', $request->user_id)
                ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'لا توجد طلبات لهذا المستخدم',
                    'data' => [],
                ], 200);
            }

            $formattedOrders = $orders->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'total_price' => number_format($order->total_price),
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'items_count' => $order->items->count(),
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedOrders,
            ], 200);

        } catch (\Exception $e) {
            Log::error("خطأ أثناء جلب الطلبات: " . $e->getMessage());
            return response()->json([
                'status' => 'failure',
                'message' => 'فشل في جلب الطلبات، يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }

public function userOrders(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    try {
        // جلب الطلبات مع عناصرها والعقارات
        $orders = Order::with(['items.property'])
            ->where('user_id', $request->user_id)
            ->latest()
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا توجد طلبات لهذا المستخدم',
                'data' => [],
            ], 200);
        }

        // تنسيق البيانات (قابل للاستخدام مباشرة في Flutter)
        $formattedOrders = $orders->map(function ($order) {
            return [
                'order_id' => $order->id,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'created_at' => $order->created_at->toDateTimeString(),

                'items' => $order->items
                    ->filter(fn ($item) => $item->property) // حماية لو عقار محذوف
                    ->map(function ($item) {
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
                    }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedOrders,
        ], 200);

    } catch (\Exception $e) {
        Log::error('خطأ في جلب الطلبات: ' . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'حدث خطأ أثناء جلب الطلبات',
        ], 500);
    }
}



}
