<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
// ✅ عرض كل عناصر السلة
public function index()
{
    // جلب جميع عناصر السلة مع بيانات العقار فقط
    // $cartItems = Cart::with('property')->get();
    $cartItems = Cart::with('property')
    ->whereHas('property', function ($q) {
        $q->where('is_sold', false);
    })
    ->get();


    // التحقق من وجود عناصر في السلة
    if ($cartItems->isEmpty()) {
        return response()->json([
            'status' => 'failure',
            'message' => 'لا توجد منتجات في السلة.',
            'data' => [],
        ], 200);
    }

    // تحويل البيانات للشكل المطلوب
    $formattedItems = $cartItems->map(function ($item) {
        return [
            'cart_id' => $item->id,
            'quantity' => $item->quantity,
               'id' => $item->property->id,
                'name' => $item->property->name,
                'description' => $item->property->description,
                'image' => asset('public/uploads/property/' . $item->property->image),
                'price' => number_format($item->property->price),
                'currency' => $item->property->currency,
                'location' => $item->property->location,
                'property_type_id' => $item->property->property_type_id,
                'user_id' => $item->property->user_id,
          
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
    // التحقق من صحة البيانات المدخلة
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'property_id' => 'required|exists:properties,id',
    ]);
    $property = \App\Models\Property::find($request->property_id);

if ($property->is_sold) {
    return response()->json([
        'status' => 'failure',
        'message' => 'هذا العقار تم بيعه ولا يمكن إضافته إلى السلة.',
    ], 403);
}

    DB::beginTransaction(); // بدء المعاملة

    try {
        // تحقق إذا كان العنصر موجود مسبقًا في السلة لنفس المستخدم
        $existingCartItem = Cart::where('user_id', $request->user_id)
            ->where('property_id', $request->property_id)
            ->first();

        if ($existingCartItem) {
            // العنصر موجود مسبقًا
            return response()->json([
                'status' => 'failure',
                'message' => 'العنصر موجود مسبقًا في السلة.',
            ], 200);
        }

        // إنشاء عنصر جديد في السلة
        $cartItem = Cart::create([
            'user_id' => $request->user_id,
            'property_id' => $request->property_id,
            'quantity' => 1, // الكمية دائمًا 1
        ]);

        DB::commit(); // تأكيد العملية

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة العنصر إلى السلة بنجاح.',
            // 'data' => $responseData,
             'cart_item_id' => $cartItem->id
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack(); // إلغاء العملية في حال حدوث خطأ
        Log::error("خطأ أثناء إضافة العنصر إلى السلة: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في إضافة العنصر إلى السلة، يرجى المحاولة مرة أخرى.',
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
public function show(Request $request)
{
    // تحقق من صحة المدخلات
    $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    try {
         // 🧹 حذف العقارات المباعة من سلة المستخدم
        Cart::where('user_id', $request->user_id)
            ->whereHas('property', function ($q) {
                $q->where('is_sold', true);
            })
            ->delete();

        // جلب السلة بعد التنظيف
        $cartItems = Cart::with('property')
            ->where('user_id', $request->user_id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'السلة فارغة',
            ], 200);
        }

        // تحويل البيانات للشكل المطلوب
        $formattedItems = $cartItems->map(function ($item) {
            return [
                'cart_id' => $item->id,
                'quantity' => $item->quantity,
                'id' => $item->property->id,
                'name' => $item->property->name,
                'description' => $item->property->description,
                'image' => asset('public/uploads/property/' . $item->property->image),
                'price' => number_format($item->property->price),
                'currency' => $item->property->currency,
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
        Log::error("خطأ أثناء عرض عناصر السلة: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في جلب عناصر السلة، يرجى المحاولة مرة أخرى.',
        ], 500);
    }
}


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
  public function destroy(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $request->validate([
        'cart_id' => 'required|exists:carts,id',
        'user_id' => 'required|exists:users,id',
    ]);

    try {
        // البحث عن عنصر السلة الذي يخص هذا المستخدم فقط
        $cartItem = Cart::where('id', $request->cart_id)
                        ->where('user_id', $request->user_id)
                        ->first();

        if (!$cartItem) {
            return response()->json([
                'status' => 'failure',
                'message' => 'العنصر غير موجود أو لا يخص المستخدم.',
            ], 404);
        }

        // حذف العنصر
        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف العنصر من السلة بنجاح.',
        ], 200);

    } catch (\Exception $e) {
        Log::error("خطأ أثناء حذف عنصر السلة: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في حذف العنصر، يرجى المحاولة مرة أخرى.',
        ], 500);
    }
}

}
