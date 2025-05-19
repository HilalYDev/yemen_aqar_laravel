<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    // ✅ التحقق من رقم الهاتف وإرسال كود التحقق
    public function checkPhone(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
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

        // البحث عن المستخدم
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failure',
                'message' => 'رقم الهاتف غير مسجل.',
            ], 404);
        }

        DB::beginTransaction(); // 🔄 بدء المعاملة لضمان عدم حدوث مشاكل

        try {
            // إنشاء كود تحقق جديد
            $verificationCode = rand(10000, 99999); // أو "22222" لأغراض الاختبار

            // تحديث كود التحقق في قاعدة البيانات
            $user->update([
                'verification_code' => $verificationCode,
            ]);

            DB::commit(); // ✅ حفظ التحديثات

            // إرسال الكود إلى المستخدم
            $this->sendVerificationCode($user->phone, $verificationCode);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إرسال رمز التحقق إلى هاتفك.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // ❌ إلغاء التحديث إذا حدث خطأ
            Log::error("خطأ أثناء تحديث كود التحقق: " . $e->getMessage());

            return response()->json([
                'status' => 'failure',
                'message' => 'حدث خطأ أثناء تحديث رمز التحقق.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ إعادة تعيين كلمة المرور
    public function resetPassword(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            // البحث عن المستخدم باستخدام رقم الهاتف
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'رقم الهاتف غير مسجل.',
                ], 404);
            }

            // تحديث كلمة المرور
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث كلمة المرور بنجاح.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error("خطأ أثناء إعادة تعيين كلمة المرور: " . $e->getMessage());

            return response()->json([
                'status' => 'failure',
                'message' => 'حدث خطأ أثناء إعادة تعيين كلمة المرور، يرجى المحاولة لاحقًا.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ دالة لإرسال كود التحقق (وهمي لأغراض الاختبار)
    private function sendVerificationCode($phoneNumber, $code)
    {
        Log::info("كود التحقق لـ $phoneNumber هو: $code");
        // يمكنك استخدام مكتبة مثل Twilio لإرسال SMS هنا
    }
}