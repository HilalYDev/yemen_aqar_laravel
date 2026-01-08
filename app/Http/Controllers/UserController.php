<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\OfficeDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function index()
{
    $users = User::where('type', 'user')->latest()->paginate(10);
    return view('pages.users.index', compact('users'));
}



// public function propertyOwners(Request $request)
// {
//     $query = User::where('type', 'property_owner')
//         ->with('details')
//         ->latest();

    // // تصفية مالكي العقارات غير الموافق عليهم إذا كان هناك فلتر
    // if ($request->has('filter') && $request->filter == 'unapproved') {
    //     $query->where('admin_approved', false);
    // }

//     $owners = $query->paginate(10);

//     // عدّ غير الموافق عليهم للفلتر
//     $unapprovedCount = User::where('type', 'property_owner')
//                            ->where('admin_approved', false)
//                            ->count();

//     return view('pages.owners.index', compact('owners', 'unapprovedCount'));
// }

public function propertyOwners(Request $request)
{
    $query = User::where('type', 'property_owner')->latest();

    // 🔴 فلتر: منتهي الصلاحية فقط
    if ($request->filter === 'expired') {
        $query->whereNotNull('expiry_date')
              ->whereDate('expiry_date', '<', now());
    }

    $owners = $query->paginate(10);

    // 🔢 عدّ المنتهية
    $expiredCount = User::where('type', 'property_owner')
                        ->whereNotNull('expiry_date')
                        ->whereDate('expiry_date', '<', now())
                        ->count();

    // 🔢 عدّ الكل
    $totalCount = User::where('type', 'property_owner')->count();

    return view('pages.owners.index', compact(
        'owners',
        'expiredCount',
        'totalCount'
    ));
}


    
public function toggleApproval($id)
{
    $user = User::findOrFail($id);
    
    $user->update([
        'admin_approved' => !$user->admin_approved,
        // إذا تمت الموافقة لأول مرة، يتم تعيين تاريخ انتهاء سنة من الآن
        'expiry_date' => $user->admin_approved ? $user->expiry_date : Carbon::now()->addYear()
    ]);
    
    return redirect()->back()->with('success', 'تم تحديث حالة الموافقة بنجاح');
}

   public function renewSubscription($id)
{
    $user = User::findOrFail($id);

    // تجديد الاشتراك لمدة سنة كاملة من تاريخ اليوم
    $newExpiryDate = Carbon::now()->addYear();

    $user->update([
        'expiry_date' => $newExpiryDate,
        // 'admin_approved' => true // تأكيد الموافقة عند التجديد
    ]);

    return redirect()->back()->with('success', 'تم تجديد الاشتراك بنجاح حتى ' . $newExpiryDate->format('Y-m-d'));
}
}
