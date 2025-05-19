<?php

namespace App\Http\Controllers\Api;

use App\Models\OfficeDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{

    public function searchOffice(Request $request)
    {
        // الحصول على الكلمة المفتاحية من الطلب
        $searchKeyword = $request->input('search_keyword');
    
        // إذا لم يتم إرسال الكلمة المفتاحية، نرجع رسالة خطأ
        if (empty($searchKeyword)) {
            return response()->json([
                'status' => 'error',
                'message' => 'يجب إرسال كلمة مفتاحية للبحث'
            ], 400);
        }
        // // البحث عن المكاتب التي تحتوي على العنوان (بحث جزئي)
        // $offices = OfficeDetail::where('office_address', 'LIKE', '%' . $searchKeyword . '%')->get();
    
    
        //// البحث في الحقول office_address و office_name
    
        $offices = OfficeDetail::where('office_address', 'LIKE', '%' . $searchKeyword . '%')
        ->orWhere('office_name', 'LIKE', '%' . $searchKeyword . '%')
        // ->orWhere('office_phone', 'LIKE', '%' . $searchKeyword . '%')
        ->get();
     
        // // يمكنك ترتيب النتائج باستخدام orderBy:
        // $offices = OfficeDetail::where('office_address', 'LIKE', '%' . $searchKeyword . '%')
        // ->orWhere('office_name', 'LIKE', '%' . $searchKeyword . '%')
        // ->orderBy('office_name', 'asc')
        // ->get();
        // تحقق إذا كانت نتائج البحث فارغة
        if ($offices->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا توجد مكاتب تطابق الكلمة المفتاحية'
            ], 404);
        }
        // تحويل البيانات إلى الشكل المطلوب
        $formattedOffices = $offices->map(function ($office) {
            return [
                'id' => $office->id,
                'user_id' => $office->user_id,
                'office_name' => $office->office_name,
                'identity_number' => $office->identity_number,
                'commercial_register_image' => $office->commercial_register_image,
                'office_address' => $office->office_address,
                'office_phone' => $office->office_phone,
            ];
        });
        // إذا تم العثور على مكاتب تطابق البحث، نرجع البيانات
        return response()->json([
            'status' => 'success',
            'data' =>$formattedOffices

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
