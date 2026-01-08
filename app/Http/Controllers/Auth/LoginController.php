<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * معالجة محاولة تسجيل الدخول
     */
    public function login(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // محاولة تسجيل الدخول
        $credentials = $request->only('phone', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // // التحقق من موافقة المسؤول إذا كان المستخدم مكتب
            // if ($user->type === 'office' && !$user->admin_approved) {
            //     Auth::logout();
            //     return back()->withErrors([
            //         'phone' => 'حسابك قيد انتظار موافقة المسؤول',
            //     ]);
            // }

            // // التحقق من انتهاء الصلاحية إذا كان هناك تاريخ انتهاء
            // if ($user->expiry_date && now()->gt($user->expiry_date)) {
            //     Auth::logout();
            //     return back()->withErrors([
            //         'phone' => 'انتهت صلاحية حسابك',
            //     ]);
            // }

            // إنشاء توكن جديد للجلسة
            $request->session()->regenerate();

            // توجيه المستخدم حسب نوعه
            return $user->type === 'admin' 
                ? redirect()->intended('/dashboard')
                : redirect()->intended('/user/dashboard');
        }

        // في حالة فشل تسجيل الدخول
        return back()->withErrors([
            'phone' => 'بيانات الدخول غير صحيحة',
        ]);
    }
    // public function login(Request $request)
    // {
    //     // التحقق من صحة البيانات المدخلة
    //     $request->validate([
    //         'phone' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     // محاولة تسجيل الدخول
    //     $credentials = $request->only('phone', 'password');

    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();

    //         // التحقق من موافقة المسؤول إذا كان المستخدم مكتب
    //         if ($user->type === 'office' && !$user->admin_approved) {
    //             Auth::logout();
    //             return back()->withErrors([
    //                 'phone' => 'حسابك قيد انتظار موافقة المسؤول',
    //             ]);
    //         }

    //         // التحقق من انتهاء الصلاحية إذا كان هناك تاريخ انتهاء
    //         if ($user->expiry_date && now()->gt($user->expiry_date)) {
    //             Auth::logout();
    //             return back()->withErrors([
    //                 'phone' => 'انتهت صلاحية حسابك',
    //             ]);
    //         }

    //         // إنشاء توكن جديد للجلسة
    //         $request->session()->regenerate();

    //         // توجيه المستخدم حسب نوعه
    //         return $user->type === 'office' 
    //             ? redirect()->intended('/office/dashboard')
    //             : redirect()->intended('/user/dashboard');
    //     }

    //     // في حالة فشل تسجيل الدخول
    //     return back()->withErrors([
    //         'phone' => 'بيانات الدخول غير صحيحة',
    //     ]);
    // }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        // Auth::logout();
                Auth::guard('web')->logout();


        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}