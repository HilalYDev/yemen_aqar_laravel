<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use App\Models\OfficeDetail;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PropertyTypeController extends Controller
{
    public function index()
    {
        $propertyTypes = PropertyType::all();

        if ($propertyTypes->isEmpty()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا توجد أنواع عقارات متاحة'
            ], 404);
        }
                // تحويل البيانات إلى الشكل المطلوب
                $formattedCategories = $propertyTypes->map(function ($propertyTypes) {
                    return [
                        'id' => $propertyTypes->id,
                        'name' => $propertyTypes->name,
                        // 'image' => asset('public/uploads/property_categories/' . $category->image),
                        // 'image' => $category->image ? asset('public/uploads/property_categories/' . $category->image) : null,
                        'image' => !empty($propertyTypes->image) 
                        ? asset('public/uploads/property_types/' . $propertyTypes->image) 
                        : null, 
                        'property_category_id' => $propertyTypes->property_category_id,
                        // 'image' => asset('public/uploads/property_types/' . $type->image),


                    ];
                });

        return response()->json([
            'status' => 'success',
            'data' => $formattedCategories
        ], 200);
    }



 
    
    public function show(Request $request)
    {
        // جلب رقم نوع العقار من الـ request
        $propertyTypeId = $request->input('id');
        $perPage = $request->get('per_page', 5);
    
        // التحقق من وجود نوع العقار
        $propertyType = PropertyType::find($propertyTypeId);
        if (!$propertyType) {
            return response()->json([
                'status' => 'failure',
                'message' => 'نوع العقار غير موجود',
            ], 404);
        }
    
        // ✅ التحقق من وجود عقارات لهذا النوع قبل تنفيذ `paginate`
        $propertyCount = Property::where('property_type_id', $propertyTypeId)->count();
        if ($propertyCount == 0) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا توجد عقارات متاحة لهذا النوع.',
                'data' => [],
            ], 200);
        }
    
        // ✅ جلب العقارات بعد التأكد من وجودها
        $properties = Property::where('property_type_id', $propertyTypeId)
            ->with('user')
            ->paginate($perPage);
    
        // تنسيق العقارات
        $formattedProperties = $properties->map(function ($property) use ($propertyType) {
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
                'property_type_name' => $propertyType->name,
            ];
        });
    
        return response()->json([
            'status' => 'success',
            'data' => $formattedProperties,
        ], 200);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'property_category_id' => 'required|exists:property_categories,id',
        ]);

        $type = PropertyType::create($request->all());

        if ($type) {
            return response()->json([
                'status' => 'success',
                'message' => 'تمت إضافة نوع العقار بنجاح',
                'data' => $type
            ], 201);
        }

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في إضافة نوع العقار'
        ], 500);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_types,id',
        ]);

        $type = PropertyType::findOrFail($request->input('id'));

        if ($type->update($request->all())) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث نوع العقار بنجاح',
                'data' => $type
            ], 200);
        }

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في تحديث نوع العقار'
        ], 500);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_types,id',
        ]);

        $deleted = PropertyType::destroy($request->input('id'));

        if ($deleted) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف نوع العقار بنجاح'
            ], 200);
        }

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في حذف نوع العقار'
        ], 500);
    }
}
