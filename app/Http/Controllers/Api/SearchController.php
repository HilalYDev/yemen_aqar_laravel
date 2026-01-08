<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{

  public function searchProperty(Request $request)
{
    // الحصول على الكلمة المفتاحية
    $searchKeyword = $request->input('search_keyword');

    // التحقق من وجود الكلمة المفتاحية
    if (empty($searchKeyword)) {
        return response()->json([
            'status' => 'error',
            'message' => 'يجب إرسال كلمة مفتاحية للبحث'
        ], 400);
    }

    // البحث في العقارات (بحث جزئي)
    $properties = Property::where('name', 'LIKE', '%' . $searchKeyword . '%')
        ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%')
        ->orWhere('location', 'LIKE', '%' . $searchKeyword . '%')
        ->get();

    // في حال عدم وجود نتائج
    if ($properties->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'لا توجد عقارات تطابق كلمة البحث'
        ], 404);
    }

    // تنسيق البيانات
    $formattedProperties = $properties->map(function ($property) {
        return [
            'id' => $property->id,
            'name' => $property->name,
            'description' => $property->description,
            'image' => asset('public/uploads/property/' . $property->image),
                  'ownership_image' =>  asset('public/uploads/ownership_image/' . $property->ownership_image),
            'price' => number_format($property->price),
            'currency' => $property->currency,
            'location' => $property->location,
            'property_type_id' => $property->property_type_id,
            'user_id' => $property->user_id,
            'is_sold' => $property->is_sold,
        ];
    });

    // إرجاع النتائج
    return response()->json([
        'status' => 'success',
        'data' => $formattedProperties
    ], 200);
}


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}
