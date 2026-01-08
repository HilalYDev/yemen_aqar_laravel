<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function index()
    {
        // إحصائيات المستخدمين
        $userCount = User::where('type', 'user')->count();
        $officeCount = User::where('type', 'office')->count();
        
        // المكاتب بدون موافقة
        $unapprovedOffices = User::where('type', 'office')
            ->where('admin_approved', false)
            ->count();
            
        // المكاتب منتهية الصلاحية
        $expiredOffices = User::where('type', 'office')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', Carbon::today())
            ->count();
            
        // المكاتب الجديدة (آخر 7 أيام)
        $newOffices = User::where('type', 'office')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
            
        // المستخدمين الجدد (آخر 7 أيام)
        $newUsers = User::where('type', 'user')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

          // حساب المكاتب غير الموافق عليها
    $unapprovedCount = User::where('type', 'office')
        ->where('admin_approved', false)
        ->count();
    
    return view('dashboard', compact(
        'userCount',
        'officeCount',
        'unapprovedOffices',
        'expiredOffices',
        'newOffices',
        'newUsers',
        'unapprovedCount'
    ));
    

        // return response()->json([
        //     'userCount' => $userCount,
        //     'officeCount' => $officeCount,
        //     'officesWithoutAttachment' => $officesWithoutAttachment,
        //     'expiredOffices' => $expiredOffices,
        //     'newOffices' => $newOffices,
        //     'newUsers' => $newUsers
        // ]);
    }
}
