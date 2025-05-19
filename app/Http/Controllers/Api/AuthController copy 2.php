<?php

// namespace App\Http\Controllers\Api;

// use App\Models\User;
// use Illuminate\Support\Str;
// use App\Models\OfficeDetail;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Hash;

// class AuthController extends Controller
// {


//     public function register(Request $request)
//     {
//         // ✅ التحقق من صحة البيانات المدخلة
//         $request->validate([
//             'name' => 'required|string|max:255',
//             // 'phone' => 'required|string|unique:users,phone',
//             'phone' => 'required|string',
//             'password' => 'required|string|min:6',
//             'type' => 'required|in:office,user',

//             // ✅ التحقق من البيانات الإضافية (اختيارية)
//             'office_name' => 'nullable|string|max:255',
//             'identity_number' => 'nullable|string|unique:office_details,identity_number',
//             'commercial_register_image' => 'nullable|string',
//             'office_address' => 'nullable|string|max:255',
//             'office_phone' => 'nullable|string|max:20',
//         ]);

//         // ✅ إنشاء كود تحقق رقمي عشوائي
//         $verificationCode = rand(10000, 99999);
//         // $verificationCode = "11111";

//         DB::beginTransaction(); // 🔄 بدء المعاملة لضمان عدم حدوث مشاكل

//         try {
//             // ✅ إنشاء حساب جديد
//             $user = User::create([
//                 'name' => $request->name,
//                 'phone' => $request->phone,
//                 'verification_code' => $verificationCode,
//                 'approved' => false, // الحساب غير مفعل حتى يتم التحقق
//                 'token' => $request->token ?? null, // ✅ تجنب الأخطاء إذا لم يتم إرسال `token`
//                 'type' => $request->type,
//                 'password' => Hash::make($request->password),
//             ]);

//             // ✅ إضافة بيانات المكتب إذا كان هناك رقم هوية
//             if (!empty($request->identity_number)) {
//                 OfficeDetail::create([
//                     'user_id' => $user->id,
//                     'office_name' => $request->office_name ?? '',
//                     'identity_number' => $request->identity_number,
//                     'commercial_register_image' => $request->commercial_register_image ?? '',
//                     'office_address' => $request->office_address ?? '',
//                     'office_phone' => $request->office_phone ?? '',
//                 ]);
//             }

//             DB::commit(); // ✅ تأكيد حفظ البيانات

//             // ✅ إرسال كود التحقق إلى المستخدم
//             try {
//                 $this->sendVerificationCode($user->phone, $verificationCode);
//             } catch (\Exception $e) {
//                 Log::error("فشل في إرسال كود التحقق: " . $e->getMessage());
//                 return response()->json([
//                     'status' => 'failure',
//                     'message' => 'تم تسجيل الحساب، لكن فشل إرسال كود التحقق. يرجى المحاولة لاحقًا.',
//                 ], 500);
//             }

//             // ✅ إرجاع رسالة نجاح بدون بيانات المستخدم
//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'تم تسجيل الحساب بنجاح. يرجى إدخال كود التحقق.',
//             ], 201);
//         } catch (\Exception $e) {
//             DB::rollBack(); // ❌ إلغاء العملية إذا حدث خطأ
//             Log::error("خطأ أثناء التسجيل: " . $e->getMessage());

//             return response()->json([
//                 'status' => 'failure',
//                 'message' => 'فشل في تسجيل الحساب، يرجى المحاولة مرة أخرى.',
//             ], 500);
//         }
//     }


//     // تسجيل الدخول

//     public function login(Request $request)
//     {
//         // ✅ التحقق من البيانات المدخلة
//         $request->validate([
//             'phone' => 'required|string',
//             'password' => 'required|string',
//         ]);
    
//         try {
//             // ✅ البحث عن المستخدم باستخدام رقم الهاتف
//             $user = User::where('phone', $request->phone)->first();
    
//             // ❌ إذا لم يتم العثور على المستخدم
//             if (!$user) {
//                 return response()->json([
//                     'status' => 'failure',
//                     'message' => 'رقم الهاتف غير مسجل.',
//                 ], 404);
//             }
    
//             // ❌ التحقق من كلمة المرور
//             if (!Hash::check($request->password, $user->password)) {
//                 return response()->json([
//                     'status' => 'failure',
//                     'message' => 'كلمة المرور غير صحيحة.',
//                 ], 400);
//             }
    
//             // ✅ التحقق من حالة الحساب
//             if (!$user->approved) {
//                 return response()->json([
//                     'status' => 'success',
//                     'message' => 'الحساب غير مفعل، يرجى التحقق من رقم الهاتف.',
//                     'data' => [
//                         'approved' => 0, // 🔹 إرجاع حالة الحساب فقط
//                     ],
//                 ], 403);
//             }
    
//             // ✅ إنشاء توكن جديد وتحديث بيانات المستخدم
//             $token = $request->device_token;
//             $user->update([
//                 'token' => $token,
//             ]);
    
//             // ✅ إرجاع جميع بيانات المستخدم إذا كان الحساب مفعلًا
//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'تم تسجيل الدخول بنجاح.',
//                 'data' => [
//                     'id' => $user->id,
//                     'name' => $user->name,
//                     'phone' => $user->phone,
//                     'approved' => $user->approved ,
//                     'type' => $user->type,
//                     'details' => $user->details ?? null,
//                 ],
//             ], 200);
    
//         } catch (\Exception $e) {
//             Log::error("خطأ أثناء تسجيل الدخول: " . $e->getMessage());
//             return response()->json([
//                 'status' => 'failure',
//                 'message' => 'حدث خطأ أثناء تسجيل الدخول، يرجى المحاولة لاحقًا.',
//             ], 500);
//         }
//     }
    
    

//     // تسجيل الخروج
//     public function logout(Request $request)
//     {
//         $request->user()->currentAccessToken()->delete();

//         return response()->json([
//             'message' => 'تم تسجيل الخروج بنجاح',
//         ], 200);
//     }

//     // التحقق من رقم الهاتف


//     public function verifyPhone(Request $request)
//     {
//         // ✅ التحقق من صحة البيانات المدخلة
//         $request->validate([
//             'phone' => 'required|string',
//             'verification_code' => 'required|string',
//         ]);

//         DB::beginTransaction(); // 🔄 بدء المعاملة لضمان عدم حدوث مشاكل

//         try {
//             // ✅ البحث عن المستخدم بالهاتف وكود التحقق
//             $user = User::where('phone', $request->phone)
//                 ->where('verification_code', $request->verification_code)
//                 ->first();

//             // ❌ إذا لم يتم العثور على المستخدم
//             if (!$user) {
//                 return response()->json([
//                     'status' => 'failure',
//                     'message' => 'رمز التحقق غير صحيح أو المستخدم غير موجود.',
//                 ], 400);
//             }

//             // ✅ تحديث حالة المستخدم إلى "مُوافق عليه"
//             $user->update([
//                 'approved' => true,
//                 // 'verification_code' => null, // 🔄 مسح رمز التحقق بعد التحقق
//             ]);

//             DB::commit(); // ✅ تأكيد التحديث في قاعدة البيانات

//             // ✅ إرجاع رسالة نجاح فقط بدون بيانات المستخدم
//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'تم التحقق من الحساب بنجاح!',
//                 'data' => [
//                     'id' => $user->id,
//                     'name' => $user->name,
//                     'phone' => $user->phone,
//                     'approved' => $user->approved,
//                     'type' => $user->type,
//                     'details' => $user->details ?? null, // ✅ تضمين بيانات المكتب إن وجدت
//                 ],
//             ], 200);
//         } catch (\Exception $e) {
//             DB::rollBack(); // ❌ إلغاء التحديث إذا حدث خطأ
//             Log::error("خطأ أثناء التحقق من رقم الهاتف: " . $e->getMessage());

//             return response()->json([
//                 'status' => 'failure',
//                 'message' => 'حدث خطأ أثناء التحقق، يرجى المحاولة مرة أخرى.',
//             ], 500);
//         }
//     }

//     public function resendVerificationCode(Request $request)
//     {
//         // ✅ التحقق من صحة البيانات المدخلة
//         $request->validate([
//             'phone' => 'required|string',
//         ]);
    
//         DB::beginTransaction(); // 🔄 بدء المعاملة لضمان عدم حدوث مشاكل
    
//         try {
//             // ✅ البحث عن المستخدم باستخدام رقم الهاتف
//             $user = User::where('phone', $request->phone)->first();
    
//             // ❌ إذا لم يتم العثور على المستخدم
//             if (!$user) {
//                 return response()->json([
//                     'status' => 'failure',
//                     'message' => 'رقم الهاتف غير مسجل.',
//                 ], 404);
//             }
    
//             // ✅ إنشاء كود تحقق جديد
//             $verificationCode = rand(10000, 99999);
//             // $verificationCode = "11111"; // لأغراض الاختبار
    
//             // ✅ تحديث رمز التحقق في قاعدة البيانات
//             $user->update([
//                 'verification_code' => $verificationCode,
//             ]);
    
//             DB::commit(); // ✅ تأكيد التحديث في قاعدة البيانات
    
//             // ✅ إرسال كود التحقق الجديد إلى المستخدم
//             try {
//                 $this->sendVerificationCode($user->phone, $verificationCode);
//             } catch (\Exception $e) {
//                 Log::error("فشل في إرسال كود التحقق: " . $e->getMessage());
//                 return response()->json([
//                     'status' => 'failure',
//                     'message' => 'فشل في إرسال كود التحقق. يرجى المحاولة لاحقًا.',
//                 ], 500);
//             }
    
//             // ✅ إرجاع رسالة نجاح
//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'تم إعادة إرسال كود التحقق بنجاح.',
//             ], 200);
//         } catch (\Exception $e) {
//             DB::rollBack(); // ❌ إلغاء العملية إذا حدث خطأ
//             Log::error("خطأ أثناء إعادة إرسال كود التحقق: " . $e->getMessage());
    
//             return response()->json([
//                 'status' => 'failure',
//                 'message' => 'حدث خطأ أثناء إعادة إرسال كود التحقق، يرجى المحاولة مرة أخرى.',
//             ], 500);
//         }
//     }
//     private function sendVerificationCode($phoneNumber, $code)
//     {
//         // هنا يمكنك استخدام مكتبة مثل Twilio لإرسال SMS
//         // مثال:
//         // Twilio::sendSMS($phoneNumber, "كود التحقق الخاص بك هو: $code");

//         // لأغراض الاختبار، سنطبع الكود في الـ Log
//         Log::info("كود التحقق لـ $phoneNumber هو: $code");
//     }
// }
