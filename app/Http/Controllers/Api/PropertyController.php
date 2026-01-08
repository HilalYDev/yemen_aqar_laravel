<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use Illuminate\Support\Str;
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
    try {
        // ✅ التحقق أولًا من وجود عقارات
        $propertyCount = Property::count();

        if ($propertyCount == 0) {
            return response()->json([
                'status'  => 'failure',
                'message' => 'لا توجد عقارات متاحة حاليًا.',
            ], 200);
        }

        // ✅ جلب أحدث 10 عقارات (بدون أي شرط)
        $properties = Property::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // ✅ تنسيق البيانات مع إضافة صورة الملكية
        $formattedProperties = $properties->map(function ($property) {
            // ✅ بناء مسار صورة الملكية (تأكد من وجودها أولاً)
            $ownershipImageUrl = null;
            if ($property->ownership_image) {
                // تحقق من وجود الملف فعلياً
                if (file_exists(public_path('uploads/ownership_image/' . $property->ownership_image))) {
                    $ownershipImageUrl = asset('public/uploads/ownership_image/' . $property->ownership_image);
                } else {
                    // إذا لم توجد صورة الملكية، استخدم صورة العقار كبديل
                    $ownershipImageUrl = asset('public/uploads/property/' . $property->image);
                }
            } else {
                // إذا لم يكن هناك صورة ملكية، استخدم صورة العقار
                $ownershipImageUrl = asset('public/uploads/property/' . $property->image);
            }

            return [
                'id'               => $property->id,
                'name'             => $property->name,
                'description'      => $property->description,
                'image'            => asset('public/uploads/property/' . $property->image), // ✅ إصلاح المسار (إزالة public)
                'ownership_image'  => $ownershipImageUrl, // ✅ إضافة صورة الملكية
                'price'            => number_format($property->price),
                'currency'         => $property->currency,
                'location'         => $property->location,
                'property_type_id' => $property->property_type_id, // ✅ إضافة هذا الحقل إذا كنت تريده
                'user_id'          => $property->user_id, // ✅ إضافة هذا الحقل إذا كنت تريده
            ];
        });

        // ✅ إرجاع الاستجابة
        return response()->json([
            'status' => 'success',
            'data'   => $formattedProperties,
        ], 200);

    } catch (\Exception $e) {
        Log::error('خطأ أثناء جلب العقارات (index): ' . $e->getMessage());

        return response()->json([
            'status'  => 'failure',
            'message' => 'فشل في جلب العقارات، يرجى المحاولة لاحقًا.',
        ], 500);
    }
}

    // ✅ دالة العرض (بالصيغة المطلوبة)
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
        $propertyCount = Property::where('user_id', $request->input('user_id'))
            ->where('property_type_id', $request->input('property_type_id'))
            ->where('is_sold', false)
            ->count();

        if ($propertyCount == 0) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا توجد عقارات متاحة لهذا النوع.',
            ], 200);
        }

        $properties = Property::where('user_id', $request->input('user_id'))
            ->where('property_type_id', $request->input('property_type_id'))
            ->where('is_sold', false)
            ->paginate($perPage);

        // ✅ تحويل البيانات إلى الصيغة المطلوبة مع إضافة صورة الملكية
        $formattedProperties = $properties->map(function ($property) {
            // ✅ بناء مسار صورة الملكية
            $ownershipImageUrl = null;
            if ($property->ownership_image) {
                // تحقق من وجود الملف
                if (file_exists(public_path('uploads/ownership_image/' . $property->ownership_image))) {
                    $ownershipImageUrl = asset('public/uploads/ownership_image/' . $property->ownership_image);
                } else {
                    // بديل إذا لم توجد الصورة
                    $ownershipImageUrl = asset('public/uploads/property/' . $property->image);
                }
            } else {
                // إذا لم توجد صورة ملكية، استخدم صورة العقار
                $ownershipImageUrl = asset('public/uploads/property/' . $property->image);
            }

            return [
                'id' => $property->id,
                'name' => $property->name,
                'description' => $property->description,
                'image' => asset('public/uploads/property/' . $property->image), // ✅ إصلاح المسار
                'ownership_image' => $ownershipImageUrl, // ✅ إضافة صورة الملكية
                'price' => number_format($property->price),
                'currency' => $property->currency,
                'location' => $property->location,
                'property_type_id' => $property->property_type_id,
                'user_id' => $property->user_id,
                'is_sold' => $property->is_sold, // ✅ إضافة حالة البيع
                'created_at' => $property->created_at->format('Y-m-d H:i:s'), // ✅ إضافة تاريخ الإنشاء
                'updated_at' => $property->updated_at->format('Y-m-d H:i:s'), // ✅ إضافة تاريخ التحديث
            ];
        });

        // ✅ إرجاع النتيجة مع البيانات والصفحة
        return response()->json([
            'status' => 'success',
            'data' => [
                'properties' => $formattedProperties,
                'pagination' => [
                    'current_page' => $properties->currentPage(),
                    'last_page' => $properties->lastPage(),
                    'per_page' => $properties->perPage(),
                    'total' => $properties->total(),
                    'from' => $properties->firstItem(),
                    'to' => $properties->lastItem(),
                ]
            ],
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
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'ownership_image' => 'required|image|mimes:jpeg,png,jpg,gif,pdf|max:2048', // ✅ إضافة validation لصورة الملكية
        'price' => 'required|string',
        'currency' => 'required|string',
        'location' => 'required|string',
        'property_type_id' => 'required|exists:property_types,id',
        'user_id' => 'required|exists:users,id',
    ]);

    DB::beginTransaction();

    try {
        // ✅ 1. حفظ صورة العقار الأساسية
        $imagePath = $request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('uploads/property', $imagePath, 'public');
        
        // ✅ 2. حفظ صورة الملكية في المجلد المخصص
        $ownershipImagePath = $this->generateUniqueFileName($request->file('ownership_image'), 'ownership_');
        $request->file('ownership_image')->storeAs('uploads/ownership_image', $ownershipImagePath, 'public');
        
        // ✅ 3. إنشاء العقار مع صورة الملكية
        $property = Property::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'ownership_image' => $ownershipImagePath, // ✅ حفظ اسم ملف صورة الملكية
            'price' => $request->price,
            'currency' => $request->currency,
            'location' => $request->location,
            'property_type_id' => $request->property_type_id,
            'user_id' => $request->user_id,
            'is_sold' => false,
        ]);

        DB::commit();
        
        // ✅ 4. تحضير البيانات للرد
        $responseData = [
            'id' => $property->id,
            'name' => $property->name,
            'description' => $property->description,
            'image' => asset('public/uploads/property/' . $property->image),
            'ownership_image' => asset('public/uploads/ownership_image/' . $property->ownership_image), // ✅ إضافة رابط صورة الملكية
            'price' => number_format($property->price),
            'currency' => $property->currency,
            'location' => $property->location,
            'property_type_id' => $property->property_type_id,
            'user_id' => $property->user_id,
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة العقار بنجاح.',
            'data' => $responseData,
        ], 201);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("خطأ أثناء إضافة العقار: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في إضافة العقار، يرجى المحاولة مرة أخرى.',
            'error' => env('APP_DEBUG') ? $e->getMessage() : null, // إظهار الخطأ في وضع التطوير فقط
        ], 500);
    }
}
    // ✅ دالة التعديل
public function update(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $request->validate([
        'id' => 'required|exists:properties,id',
        'name' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        'ownership_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:2048', // ✅ دعم صورة الملكية
        'price' => 'required|string',
        'currency' => 'required|string',
        'location' => 'required|string',
        'property_type_id' => 'required|exists:property_types,id',
        'user_id' => 'nullable|exists:users,id',
    ]);

    DB::beginTransaction();

    try {
        // جلب العقار المطلوب
        $property = Property::findOrFail($request->id);

        if ($property->is_sold) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا يمكن تعديل عقار تم بيعه.',
            ], 403);
        }

        // التأكد من أن العقار يتبع نفس المستخدم
        if ($property->user_id != $request->user_id) {
            return response()->json([
                'status' => 'failure',
                'message' => 'هذا العقار لا يتبع للمستخدم الحالي.',
            ], 403);
        }

        // تحديث صورة العقار إذا كانت موجودة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($property->image && Storage::disk('public')->exists('uploads/property/' . $property->image)) {
                Storage::disk('public')->delete('uploads/property/' . $property->image);
            }

            // حفظ الصورة الجديدة
            $imagePath = $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('uploads/property', $imagePath, 'public');
            $property->image = $imagePath;
        }

        // تحديث صورة الملكية إذا تم رفعها
        if ($request->hasFile('ownership_image')) {
            // حذف الصورة القديمة
            if ($property->ownership_image && Storage::disk('public')->exists('uploads/ownership_image/' . $property->ownership_image)) {
                Storage::disk('public')->delete('uploads/ownership_image/' . $property->ownership_image);
            }

            // حفظ الصورة الجديدة مع اسم فريد
            $ownershipImagePath = $this->generateUniqueFileName($request->file('ownership_image'), 'ownership_');
            $request->file('ownership_image')->storeAs('uploads/ownership_image', $ownershipImagePath, 'public');
            $property->ownership_image = $ownershipImagePath;
        }

        // تحديث باقي بيانات العقار
        $property->update([
            'name' => $request->name ?? $property->name,
            'description' => $request->description ?? $property->description,
            'price' => $request->price ?? $property->price,
            'currency' => $request->currency ?? $property->currency,
            'location' => $request->location ?? $property->location,
            'property_type_id' => $request->property_type_id ?? $property->property_type_id,
            'user_id' => $request->user_id ?? $property->user_id,
        ]);

        DB::commit();

        $responseData = [
            'id' => $property->id,
            'name' => $property->name,
            'description' => $property->description,
            'image' => asset('public/uploads/property/' . $property->image),
            'ownership_image' => asset('public/uploads/ownership_image/' . $property->ownership_image),
            'price' => number_format($property->price),
            'currency' => $property->currency,
            'location' => $property->location,
            'property_type_id' => $property->property_type_id,
            'user_id' => $property->user_id,
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث العقار بنجاح.',
            'property' => $responseData,
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("خطأ أثناء تحديث العقار: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في تحديث العقار، يرجى المحاولة مرة أخرى.',
            'error' => env('APP_DEBUG') ? $e->getMessage() : null,
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

//        if ($property->is_sold) {
//     return response()->json([
//         'status' => 'failure',
//         'message' => 'لا يمكن حذف عقار تم بيعه.',
//     ], 403);
// }

        // حذف صورة العقار الأساسية إذا كانت موجودة
        if ($property->image && Storage::disk('public')->exists('uploads/property/' . $property->image)) {
            Storage::disk('public')->delete('uploads/property/' . $property->image);
        }

        // حذف صورة الملكية إذا كانت موجودة
        if ($property->ownership_image && Storage::disk('public')->exists('uploads/ownership_image/' . $property->ownership_image)) {
            Storage::disk('public')->delete('uploads/ownership_image/' . $property->ownership_image);
        }

        // حذف العقار من قاعدة البيانات
        $property->delete();

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف العقار بنجاح.',
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("خطأ أثناء حذف العقار: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في حذف العقار، يرجى المحاولة مرة أخرى.',
            'error' => env('APP_DEBUG') ? $e->getMessage() : null,
        ], 500);
    }
}
    private function generateUniqueFileName($file, $prefix = '')
{
    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $extension = $file->getClientOriginalExtension();
    $timestamp = time();
    $random = Str::random(10);
    
    return $prefix . $originalName . '_' . $timestamp . '_' . $random . '.' . $extension;
}
}


