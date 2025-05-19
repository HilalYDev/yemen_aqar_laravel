<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    // public function index()
    // {
    //     return Property::all(); // جلب العقارات فقط بدون أي علاقات
    // }

    public function index()
    {
        // جلب جميع بيانات OfficeDetail
        $properties = Property::all(); 
    
        // التحقق من وجود البيانات
        if ($properties->isEmpty()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا توجد تفاصيل مكاتب'
            ], 404);
        }
    
        // تحويل البيانات إلى الشكل المطلوب
        $formattedProperties = $properties->map(function ($property) {
            return [
                'id' => $property->id,
                'name' => $property->name,
                'description' => $property->description, // الوصف
                'image' => asset('public/uploads/property/' . $property->image),
                'price' => number_format($property->price), // السعر الأصلي
                'currency' => $property->currency, 
                'location' => $property->location, // الموقع
                'property_type_id' => $property->property_type_id,
                'user_id' => $property->user_id, // معرف المستخدم
            ];
        });

        // إرجاع النتيجة
        return response()->json([
            'status' => 'success',
            'data' => $formattedProperties,
        ], 200);
    }
    // ✅ دالة العرض (بالصيغة المطلوبة)
    public function show(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'property_type_id' => 'required|exists:property_types,id',
        ]);
    
        // جلب عدد العناصر في الصفحة (الافتراضي 5)
        $perPage = $request->get('per_page', 5);
    
        try {
            // ✅ التحقق أولًا مما إذا كانت هناك عقارات قبل تنفيذ `paginate`
            $propertyCount = Property::where('user_id', $request->input('user_id'))
                ->where('property_type_id', $request->input('property_type_id'))
                ->count();
    
            if ($propertyCount == 0) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'لا توجد عقارات متاحة لهذا النوع.',
                    // 'data' => [],
                ], 200);
            }
    
            // ✅ جلب العقارات بعد التأكد من وجودها
            $properties = Property::where('user_id', $request->input('user_id'))
                ->where('property_type_id', $request->input('property_type_id'))
                ->paginate($perPage);
    
            // تحويل البيانات إلى الصيغة المطلوبة
            $formattedProperties = $properties->map(function ($property) {
                return [
                    'id' => $property->id,
                    'name' => $property->name,
                    'description' => $property->description,
                    'image' => asset('public/uploads/property/' . $property->image),
                    'price' => number_format($property->price),
                    'currency' => $property->currency,
                    'location' => $property->location,
                    'property_type_id' => $property->property_type_id,
                    'user_id' => $property->user_id,
                ];
            });
    
            // إرجاع النتيجة مع البيانات
            return response()->json([
                'status' => 'success',
                'data' => $formattedProperties,
            ], 200);
    
        } catch (\Exception $e) {
            Log::error("خطأ أثناء جلب العقارات: " . $e->getMessage());
    
            return response()->json([
                'status' => 'failure',
                'message' => 'فشل في جلب العقارات، يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }
    

 

    public function store(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // التحقق من أن الملف صورة
        'price' => 'required|string',
        'currency' => 'required|string',

        'location' => 'required|string',
        'property_type_id' => 'required|exists:property_types,id',
        'user_id' => 'required|exists:users,id',
    ]);

    DB::beginTransaction(); // 🔄 بدء المعاملة لضمان عدم حدوث مشاكل

    try {
    
        $imagePath = $request->file('image')->getClientOriginalName();
    
        // حفظ الصورة في المجلد public/uploads/property
        $request->file('image')->storeAs('uploads/property', $imagePath, 'public');

        // إنشاء عقار جديد
        $property = Property::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath, // حفظ مسار الصورة
            'price' => $request->price,
            'currency' => $request->currency,
            'location' => $request->location,
            'property_type_id' => $request->property_type_id,
            'user_id' => $request->user_id,
        ]);

        DB::commit(); // ✅ تأكيد حفظ البيانات
        $responseData = [
      
            'id' => $property->id,
                    'name' => $property->name,
                    'description' => $property->description,
                    'image' => asset('public/uploads/property/' . $property->image),
                    'price' => number_format($property->price),
                    'currency' => $property->currency,
                    'location' => $property->location,
                    'property_type_id' => $property->property_type_id,
                    'user_id' => $property->user_id,
        ];
        // إرجاع رسالة نجاح مع بيانات العقار
        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة العقار بنجاح.',
            // 'data' => $property,
            'data' => $responseData,
          
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack(); // ❌ إلغاء العملية إذا حدث خطأ
        Log::error("خطأ أثناء إضافة العقار: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في إضافة العقار، يرجى المحاولة مرة أخرى.',
        ], 500);
    }
}
//     public function store(Request $request)
// {
//     // التحقق من صحة البيانات المدخلة
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'description' => 'required|string',
//         'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // التحقق من أن الملف صورة
//         'price' => 'required|string',
//         'currency' => 'required|string',

//         'location' => 'required|string',
//         'property_type_id' => 'required|exists:property_types,id',
//         'user_id' => 'required|exists:users,id',
//     ]);

//     DB::beginTransaction(); // 🔄 بدء المعاملة لضمان عدم حدوث مشاكل

//     try {
    
//         $imagePath = $request->file('image')->getClientOriginalName();
    
//         // حفظ الصورة في المجلد public/uploads/property
//         $request->file('image')->storeAs('uploads/property', $imagePath, 'public');

//         // إنشاء عقار جديد
//         $property = Property::create([
//             'name' => $request->name,
//             'description' => $request->description,
//             'image' => $imagePath, // حفظ مسار الصورة
//             'price' => $request->price,
//             'currency' => $request->currency,
//             'location' => $request->location,
//             'property_type_id' => $request->property_type_id,
//             'user_id' => $request->user_id,
//         ]);

//         DB::commit(); // ✅ تأكيد حفظ البيانات

//         // إرجاع رسالة نجاح مع بيانات العقار
//         return response()->json([
//             'status' => 'success',
//             'message' => 'تم إضافة العقار بنجاح.',
//             'data' => $property,
          
//         ], 201);
//     } catch (\Exception $e) {
//         DB::rollBack(); // ❌ إلغاء العملية إذا حدث خطأ
//         Log::error("خطأ أثناء إضافة العقار: " . $e->getMessage());

//         return response()->json([
//             'status' => 'failure',
//             'message' => 'فشل في إضافة العقار، يرجى المحاولة مرة أخرى.',
//         ], 500);
//     }
// }

    // ✅ دالة التعديل
    public function update(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'id' => 'required|exists:properties,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // التحقق من أن الملف صورة
            'price' => 'required|string',
            'currency' => 'required|string',
            'location' => 'required|string',
            'property_type_id' => 'required|exists:property_types,id',
            'user_id' => 'nullable|exists:users,id',
        ]);
    
        DB::beginTransaction(); // 🔄 بدء المعاملة لضمان عدم حدوث مشاكل
    
        try {
            // جلب العقار المطلوب
            $property = Property::findOrFail($request->id);
    
            // التأكد من أن العقار يتبع نفس المستخدم
            if ($property->user_id != $request->user_id) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'هذا العقار لا يتبع للمستخدم الحالي.',
                ], 403); // 🚫 Unauthorized
            }
    
            // إذا كانت هناك صورة جديدة
            if ($request->hasFile('image')) {
                // 🔴 حذف الصورة القديمة إذا كانت موجودة
                if ($property->image) {
                    $oldImagePath =   Storage::disk('public')->delete('uploads/property/' . $property->image);

                    // $oldImagePath = storage_path('app/public/uploads/property/' . $property->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // حذف الصورة القديمة
                    }
                }
    
                // 🟢 حفظ الصورة الجديدة
                $imagePath = $request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('uploads/property', $imagePath, 'public');
                $property->image = $imagePath; // تحديث مسار الصورة الجديدة
            }
    
            // تحديث بيانات العقار
            $property->update([
                'name' => $request->name ?? $property->name,
                'description' => $request->description ?? $property->description,
                'price' => $request->price ?? $property->price,
                'currency' => $request->currency ?? $property->currency,
                'location' => $request->location ?? $property->location,
                'property_type_id' => $request->property_type_id ?? $property->property_type_id,
                'user_id' => $request->user_id ?? $property->user_id,
            ]);
    
            DB::commit(); // ✅ تأكيد حفظ البيانات
            $responseData = [
      
                'id' => $property->id,
                        'name' => $property->name,
                        'description' => $property->description,
                        'image' => asset('public/uploads/property/' . $property->image),
                        'price' => number_format($property->price),
                        'currency' => $property->currency,
                        'location' => $property->location,
                        'property_type_id' => $property->property_type_id,
                        'user_id' => $property->user_id,
            ];
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث العقار بنجاح.',
                // 'property' => $property,
                'property' => $responseData,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // ❌ إلغاء العملية إذا حدث خطأ
            Log::error("خطأ أثناء تحديث العقار: " . $e->getMessage());
    
            return response()->json([
                'status' => 'failure',
                'message' => 'فشل في تحديث العقار، يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }
    
    
    
    // ✅ دالة الحذف
    // public function destroy(Request $request)
    // {
    //     // التحقق من صحة البيانات المدخلة
        // $request->validate([
        //     'id' => 'required|exists:properties,id',
        // ]);

    //     DB::beginTransaction(); // 🔄 بدء المعاملة لضمان عدم حدوث مشاكل

    //     try {
    //         // جلب العقار المطلوب
    //         $property = Property::findOrFail($request->id);

    //         // حذف العقار
    //         $property->delete();

    //         DB::commit(); // ✅ تأكيد حفظ البيانات

    //         // إرجاع رسالة نجاح
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'تم حذف العقار بنجاح.',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack(); // ❌ إلغاء العملية إذا حدث خطأ
    //         Log::error("خطأ أثناء حذف العقار: " . $e->getMessage());

    //         return response()->json([
    //             'status' => 'failure',
    //             'message' => 'فشل في حذف العقار، يرجى المحاولة مرة أخرى.',
    //         ], 500);
    //     }
    // }

    public function destroy(Request $request)
    {
            //     // التحقق من صحة البيانات المدخلة

        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);
        DB::beginTransaction(); // بداية المعاملة

        try {
            $property = Property::findOrFail($request->id); // العثور على العقار باستخدام ID

       
            if ($property->image) {
                Storage::disk('public')->delete('uploads/property/' . $property->image);
            }
            // حذف العقار من قاعدة البيانات
            $property->delete();

            DB::commit(); // تأكيد الحذف

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف العقار بنجاح.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // إلغاء العملية في حال حدوث خطأ

            Log::error("خطأ أثناء حذف العقار: " . $e->getMessage());

            return response()->json([
                'status' => 'failure',
                'message' => 'فشل في حذف العقار، يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }
}


