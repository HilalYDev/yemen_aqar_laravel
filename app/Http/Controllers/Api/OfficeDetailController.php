<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use App\Models\OfficeDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class OfficeDetailController extends Controller
{
    /**
     * عرض قائمة المكاتب مع التصفّح.
     */
    public function index(Request $request)
    {
        try {
            // التحقق من صحة البيانات المدخلة
            $request->validate([
                'per_page' => 'sometimes|integer|min:1|max:100', // عدد العناصر في الصفحة (1 إلى 100)
            ]);

            // عدد العناصر في كل صفحة (القيمة الافتراضية 10)
            $perPage = $request->get('per_page', 10);

            // جلب البيانات مع دعم التصفّح
            $officeDetails = OfficeDetail::paginate($perPage);

            // تحويل البيانات إلى الشكل المطلوب
            $formattedOfficeDetails = $officeDetails->map(function ($officeDetail) {
                return [
                    'id' => $officeDetail->id,
                    'user_id' => $officeDetail->user_id,
                    'office_name' => $officeDetail->office_name,
                    'identity_number' => $officeDetail->identity_number,
                    'commercial_register_image' => $officeDetail->commercial_register_image,
                    'office_address' => $officeDetail->office_address,
                    'office_phone' => $officeDetail->office_phone,
                ];
            });

            // إرجاع البيانات مع معلومات التصفّح
            return response()->json([
                'status' => 'success',
                'data' => $formattedOfficeDetails,
                'pagination' => [
                    'current_page' => $officeDetails->currentPage(),
                    'per_page' => $officeDetails->perPage(),
                    'total' => $officeDetails->total(),
                    'last_page' => $officeDetails->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            // تسجيل الخطأ في السجلات
            Log::error("خطأ في عرض قائمة المكاتب: " . $e->getMessage());

            // إرجاع رسالة خطأ
            return response()->json([
                'status' => 'failure',
                'message' => 'حدث خطأ أثناء جلب البيانات.',
            ], 500);
        }
    }

    /**
     * عرض تفاصيل مكتب معين باستخدام user_id.
     */
    public function show(Request $request)
    {
        try {
            // التحقق من صحة البيانات المدخلة
            $request->validate([
                'user_id' => 'required|integer|exists:users,id', // يجب أن يكون user_id موجودًا في جدول users
            ]);

            // جلب user_id من الطلب
            $userId = $request->input('user_id');

            // البحث عن المكتب باستخدام user_id
            $officeDetail = OfficeDetail::where('user_id', $userId)->first();

            // التحقق من وجود المكتب
            if (!$officeDetail) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'المكتب غير موجود لهذا المستخدم',
                ], 404);
            }

            // تحويل البيانات إلى الشكل المطلوب
            $formattedOfficeDetail = [[
                'id' => $officeDetail->id,
                'user_id' => $officeDetail->user_id,
                'office_name' => $officeDetail->office_name,
                'identity_number' => $officeDetail->identity_number,
                'commercial_register_image' => $officeDetail->commercial_register_image,
                'office_address' => $officeDetail->office_address,
                'office_phone' => $officeDetail->office_phone,
             ] ];

            // إرجاع النتيجة
            return response()->json([
                'status' => 'success',
                'data' => $formattedOfficeDetail,
            ], 200);
        } catch (\Exception $e) {
            // تسجيل الخطأ في السجلات
            Log::error("خطأ في عرض تفاصيل المكتب: " . $e->getMessage());

            // إرجاع رسالة خطأ
            return response()->json([
                'status' => 'failure',
                'message' => 'حدث خطأ أثناء جلب البيانات.',
            ], 500);
        }
    }

    /**
     * جلب العقارات التابعة لمكتب معين.
     */
    public function getOfficeProperties(Request $request)
    {
        try {
            // التحقق من صحة البيانات المدخلة
            $request->validate([
                'id' => 'required|integer|exists:office_details,id', // يجب أن يكون id موجودًا في جدول office_details
                'per_page' => 'sometimes|integer|min:1|max:100', // عدد العناصر في الصفحة (1 إلى 100)
            ]);

            // جلب رقم المكتب من الطلب
            $officeId = $request->input('id');
            $perPage = $request->get('per_page', 5);

            // البحث عن المكتب
            $officeDetail = OfficeDetail::find($officeId);
            if (!$officeDetail) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'المكتب غير موجود',
                ], 404);
            }

            // التحقق من وجود عقارات تابعة للمكتب
            $propertyCount = Property::where('user_id', $officeDetail->user_id)->count();
            if ($propertyCount == 0) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'لا توجد عقارات تابعة لهذا المكتب',
                ], 200);
            }

            // جلب العقارات مع التصفّح
            $properties = Property::where('user_id', $officeDetail->user_id)
                ->with(['user', 'propertyType'])
                ->paginate($perPage);

            // تنسيق العقارات
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
                    'phone' => $property->user->phone ?? 'غير متوفر',
                    'property_type_name' => $property->propertyType->name,
                ];
            });

            // إرجاع النتيجة مع معلومات التصفّح
            return response()->json([
                'status' => 'success',
                'data' => $formattedProperties,
                'pagination' => [
                    'current_page' => $properties->currentPage(),
                    'per_page' => $properties->perPage(),
                    'total' => $properties->total(),
                    'last_page' => $properties->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            // تسجيل الخطأ في السجلات
            Log::error("خطأ في جلب عقارات المكتب: " . $e->getMessage());

            // إرجاع رسالة خطأ
            return response()->json([
                'status' => 'failure',
                'message' => 'حدث خطأ أثناء جلب البيانات.',
            ], 500);
        }
    }


    //      public function getOfficeProperties(Request $request)
    // {
    //     // جلب رقم المكتب من الـ request
    //     $officeId = $request->input('id');
    //     $perPage = $request->get('per_page', 5);

    //     // التحقق من وجود المكتب
    //     $officeDetail = OfficeDetail::find($officeId);
    //     if (!$officeDetail) {
    //         return response()->json(['message' => 'المكتب غير موجود'], 404);
    //     }

    //     // جلب العقارات التابعة للمستخدم صاحب المكتب
    //             // جلب العقارات مع التصفّح
    //             $properties = Property::where('user_id', $officeDetail->user_id)
    //             ->paginate($perPage);

    //         // التحقق من وجود عقارات
    //         if ($properties->isEmpty()) {
    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'لا توجد عقارات تابعة لهذا النوع',
    //                 'data' => [], // إرجاع مصفوفة فارغة في حالة عدم وجود عقارات
    //             ], 200);
    //         }
    //         $formattedProperties = $properties->map(function ($property) {
    //             return [
    //                 'id' => $property->id,
    //                 'name' => $property->name,
    //                 'description' => $property->description, // الوصف
    //                 'image' => asset('public/uploads/property/' . $property->image), // مسار الصورة
    //                 'price' => number_format($property->price), // السعر بتنسيق الأرقام
    //                 'currency' => $property->currency, 
    //                 'location' => $property->location, // الموقع
    //                 'property_type_id' => $property->property_type_id,
    //                 'user_id' => $property->user_id,
    //                 'phone' => $property->user->phone ?? 'غير متوفر', // جلب رقم الهاتف
    //                 'property_type_name' => $property->propertyType->name, // اسم نوع العقار
    //             ];
    //         });
    //     // $properties = Property::where('user_id', $officeDetail->user_id)
    //     //     ->get()
    //     //     ->map(function ($property) {
    //     //         return [
    //                 // 'id' => $property->id,
    //                 // 'name' => $property->name,
    //                 // 'description' => $property->description, // الوصف
    //                 // 'image' => asset('public/uploads/property/' . $property->image), // مسار الصورة
    //                 // 'price' => number_format($property->price), // السعر بتنسيق الأرقام
    //                 // 'currency' => $property->currency, 
    //                 // 'location' => $property->location, // الموقع
    //                 // 'property_type_id' => $property->property_type_id,
    //                 // 'user_id' => $property->user_id,
    //                 // 'phone' => $property->user->phone ?? 'غير متوفر', // جلب رقم الهاتف
    //                 // 'property_type_name' => $property->propertyType->name, // اسم نوع العقار
    //     //         ];
    //     //     });

    //     // // التحقق من وجود عقارات
    //     // if ($properties->isEmpty()) {
    //     //     return response()->json([
    //     //         'status' => 'failure',
    //     //         'message' => 'لا توجد عقارات تابعة لهذا المكتب',
    //     //     ], 200);
    //     // }

    //     // إرجاع النتيجة
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $formattedProperties,
    //     ], 200);
    // }


    // public function show(Request $request)
    // {
    //     $officeId = $request->input('id');

    //     // التحقق من وجود المكتب
    //     $officeDetail = OfficeDetail::find($officeId);
    //     if (!$officeDetail) {
    //         return response()->json(['message' => 'المكتب غير موجود'], 404);
    //     }

    //     // جلب العقارات التابعة للمستخدم صاحب المكتب
    //     $properties = Property::where('user_id', $officeDetail->user_id)
    //         ->get()
    //         ->map(function ($property) {
    //             return [
    //                 'id' => $property->id,
    //                 'name' => $property->name,
    //                 'description' => $property->description, // الوصف
    //                 'image' => asset('public/uploads/property/' . $property->image), // مسار الصورة
    //                 'price' => number_format($property->price), // السعر بتنسيق الأرقام
    //                 'currency' => $property->currency, 
    //                 'location' => $property->location, // الموقع
    //                 'property_type_id' => $property->property_type_id,
    //                 'user_id' => $property->user_id,
    //                 'phone' => $property->user->phone ?? 'غير متوفر', // جلب رقم الهاتف
    //                 'property_type_name' => $property->propertyType->name, // اسم نوع العقار
    //             ];
    //         });

    //     // التحقق من وجود عقارات
    //     if ($properties->isEmpty()) {
    //         return response()->json([
    //             'status' => 'failure',
    //             'message' => 'لا توجد عقارات تابعة لهذا المكتب',
    //         ], 200);
    //     }

    //     // إرجاع النتيجة
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $properties,
    //     ], 200);
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
