<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PropertyCategory;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyCategoryController extends Controller
{
    public function index()
    {
        $categories = PropertyCategory::all();
    
        if ($categories->isEmpty()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا توجد فئات عقارات'
            ], 404);
        }
    
        // تحويل البيانات إلى الشكل المطلوب
        $formattedCategories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                // 'image' => asset('public/uploads/property_categories/' . $category->image),
                // 'image' => $category->image ? asset('public/uploads/property_categories/' . $category->image) : null,
                'image' => !empty($category->image) 
                ? asset('public/uploads/property_categories/' . $category->image) 
                : null, // ❌ لا ترسل رابط فارغ
            ];
        });
    
        return response()->json([
            'status' => 'success',
            'data' => $formattedCategories
        ], 200);
    }

    public function show(Request $request)
    {
        // الحصول على id من الطلب
        $propertyCategoryId = $request->input('id');
    
        // البحث عن الفئة
        $category = PropertyCategory::find($propertyCategoryId);
    
        // إذا لم يتم العثور على الفئة
        if (!$category) {
            return response()->json([
                'status' => 'failure',
                'message' => 'الفئة المطلوبة غير موجودة'
            ], 404);
        }
    
        // جلب الأنواع التابعة لهذا التصنيف
        $propertyTypes = PropertyType::where('property_category_id', $propertyCategoryId)->get();
    
        // إذا لم توجد أنواع تابعة للفئة
        if ($propertyTypes->isEmpty()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'لا توجد أنواع عقارات لهذا التصنيف'
            ], 404);
        }
    
        // تحويل البيانات إلى الشكل المطلوب
        $formattedPropertyTypes = $propertyTypes->map(function ($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                // 'description' => $type->description,
                // 'image' => asset('public/uploads/property_categories/' . $type->image),
                'image' => asset('public/uploads/property_types/' . $type->image),

                'property_category_id' => $type->property_category_id,
            ];
        });
    
        // إرجاع البيانات
        return response()->json([
            'status' => 'success',
            'data' => $formattedPropertyTypes
        ], 200);
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $category = PropertyCategory::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $category
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_categories,id',
            'name' => 'required',
        ]);

        $category = PropertyCategory::findOrFail($request->input('id'));

        if (!$category) {
            return response()->json([
                'status' => 'failure',
                'message' => 'الفئة المطلوبة غير موجودة'
            ], 404);
        }

        $category->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $category
        ], 200);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_categories,id',
        ]);

        $category = PropertyCategory::findOrFail($request->input('id'));

        if (!$category) {
            return response()->json([
                'status' => 'failure',
                'message' => 'الفئة المطلوبة غير موجودة'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الفئة بنجاح'
        ], 200);
    }
}
