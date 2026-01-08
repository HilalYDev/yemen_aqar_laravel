<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\OfficeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ✅ تسجيل مستخدم جديد
  public function register(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'phone' => 'required|string|unique:users,phone',
        'password' => 'required|string|min:6',
        'type' => 'required|in:user,property_owner', // تحديث النوع
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'failure',
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 400);
    }

    DB::beginTransaction();

    try {
        // إنشاء كود تحقق
        // $verificationCode = rand(10000, 99999);
        $verificationCode=11111;

        // تحديد تاريخ انتهاء الصلاحية (يمكن تغييره حسب المنطق)
$expiryDate = $request->type === 'property_owner' ? now()->addMonth() : null;

        // تحديد حالة الموافقة من قبل المسؤول
        $adminApproved = true; // حسب المنطق الجديد

        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'verification_code' => $verificationCode,
            'approved' => false,
            'admin_approved' => $adminApproved,
            'token' => $request->token ?? null,
            'type' => $request->type,
            'password' => Hash::make($request->password),
            'expiry_date' => $expiryDate,
        ]);

        DB::commit();

        // إرسال كود التحقق
        $this->sendVerificationCode($user->phone, $verificationCode);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الحساب بنجاح. يرجى إدخال كود التحقق.',
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("خطأ أثناء التسجيل: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'فشل في تسجيل الحساب، يرجى المحاولة مرة أخرى.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function getUserData(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $validator = Validator::make($request->all(), [
        'id' => 'nullable|integer|exists:users,id', // يمكن أن يكون id فارغًا أو عددًا صحيحًا
        'phone' => 'nullable|string|exists:users,phone', // يمكن أن يكون phone فارغًا أو نصًا
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'failure',
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 400);
    }

    try {
        // البحث عن المستخدم باستخدام id أو phone
        $user = null;
        if ($request->has('id')) {
            $user = User::find($request->id);
        } elseif ($request->has('phone')) {
            $user = User::where('phone', $request->phone)->first();
        }

        // إذا لم يتم العثور على المستخدم
        if (!$user) {
            return response()->json([
                'status' => 'failure',
                'message' => 'المستخدم غير موجود.',
            ], 404);
        }

        // إرجاع بيانات المستخدم
        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب بيانات المستخدم بنجاح.',
            'data' => [
                'id' => $user->id,
                // 'name' => $user->name,
                // 'phone' => $user->phone,
                // 'approved' => $user->approved,
                'admin_approved' => $user->admin_approved,
                // 'token' => $user->token,
                'type' => $user->type,
                'expiry_date' => $user->expiry_date,
                // 'details' => $user->details ?? null,
            ],
        ], 200);
    } catch (\Exception $e) {
        Log::error("خطأ أثناء جلب بيانات المستخدم: " . $e->getMessage());

        return response()->json([
            'status' => 'failure',
            'message' => 'حدث خطأ أثناء جلب بيانات المستخدم، يرجى المحاولة لاحقًا.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
    // ✅ الموافقة على المكاتب
//     public function approveOffice(Request $request, $userId)
// {
//     $user = User::findOrFail($userId);

//     if ($user->type !== 'office') {
//         return response()->json([
//             'status' => 'failure',
//             'message' => 'هذا المستخدم ليس مكتبًا.',
//         ], 400);
//     }

//     // التحقق من بيانات المكتب (يمكنك إضافة المزيد من الشروط هنا)
//     $officeDetails = OfficeDetail::where('user_id', $userId)->first();

//     if (!$officeDetails || !$officeDetails->identity_number || !$officeDetails->commercial_register_image) {
//         return response()->json([
//             'status' => 'failure',
//             'message' => 'بيانات المكتب غير مكتملة.',
//         ], 400);
//     }

//     // الموافقة على المكتب
//     $user->admin_approved = true;
//     $user->save();

//     return response()->json([
//         'status' => 'success',
//         'message' => 'تمت الموافقة على المكتب بنجاح.',
//     ], 200);
// }

    // ✅ تسجيل الدخول
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'رقم الهاتف غير مسجل.',
                ], 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'كلمة المرور غير صحيحة.',
                ], 400);
            }

            if (!$user->approved) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'الحساب غير مفعل، يرجى التحقق من رقم الهاتف.',
                    'data' => ['approved' => 0],
                ], 403);
            }

            // تحديث التوكن
            $user->update(['token' => $request->device_token]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الدخول بنجاح.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'approved' => $user->approved,
                    'admin_approved' => $user->admin_approved,
                    'token' => $user->token,
                    'type' => $user->type,
                    'expiry_date' => $user->expiry_date,

                   
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error("خطأ أثناء تسجيل الدخول: " . $e->getMessage());

            return response()->json([
                'status' => 'failure',
                'message' => 'حدث خطأ أثناء تسجيل الدخول، يرجى المحاولة لاحقًا.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ تسجيل الخروج
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'تم تسجيل الخروج بنجاح',
            ], 200);
        } catch (\Exception $e) {
            Log::error("خطأ أثناء تسجيل الخروج: " . $e->getMessage());

            return response()->json([
                'status' => 'failure',
                'message' => 'حدث خطأ أثناء تسجيل الخروج.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ التحقق من رقم الهاتف
    public function verifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'verification_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        DB::beginTransaction();

        try {
            $user = User::where('phone', $request->phone)
                ->where('verification_code', $request->verification_code)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'رمز التحقق غير صحيح أو المستخدم غير موجود.',
                ], 400);
            }

            $user->update(['approved' => true]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم التحقق من الحساب بنجاح!',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'approved' => $user->approved,
                    'admin_approved' => $user->admin_approved,
                    'token' => $user->token,

                    'type' => $user->type,
                    'expiry_date' => $user->expiry_date,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("خطأ أثناء التحقق من رقم الهاتف: " . $e->getMessage());

            return response()->json([
                'status' => 'failure',
                'message' => 'حدث خطأ أثناء التحقق، يرجى المحاولة مرة أخرى.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ إعادة إرسال رمز التحقق
    public function resendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        DB::beginTransaction();

        try {
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'رقم الهاتف غير مسجل.',
                ], 404);
            }

            $verificationCode = rand(10000, 99999);
            $user->update(['verification_code' => $verificationCode]);

            DB::commit();

            $this->sendVerificationCode($user->phone, $verificationCode);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إعادة إرسال كود التحقق بنجاح.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("خطأ أثناء إعادة إرسال كود التحقق: " . $e->getMessage());

            return response()->json([
                'status' => 'failure',
                'message' => 'حدث خطأ أثناء إعادة إرسال كود التحقق، يرجى المحاولة مرة أخرى.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ إرسال كود التحقق (وهمي لأغراض الاختبار)
    private function sendVerificationCode($phoneNumber, $code)
    {
        Log::info("كود التحقق لـ $phoneNumber هو: $code");
        // يمكنك استخدام مكتبة مثل Twilio لإرسال SMS هنا
    }
}