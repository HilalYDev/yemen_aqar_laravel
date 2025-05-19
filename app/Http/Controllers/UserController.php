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
        $users = User::with('details')->latest()->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    public function offices()
    {
        // $offices = OfficeDetail::with('user')->latest()->paginate(10);
        $offices = OfficeDetail::with('user')
    ->whereHas('user', function($q) {
        $q->where('type', 'office');
    })
    ->latest()
    ->paginate(10);
        return view('pages.offices.index', compact('offices'));
    }
        public function toggleApproval($id)
    {
        $user = User::findOrFail($id);
        $user->update(['admin_approved' => !$user->admin_approved]);
                        return redirect()->route('admin.offices.index')->with('success', 'تم حفظ القسم بنجاح');


      
    }

    // ... الدوال الحالية ...

public function renewSubscription($id)
{
    $user = User::findOrFail($id);
    
    // تجديد الاشتراك لمدة سنة كاملة من تاريخ اليوم
    $newExpiryDate = Carbon::now()->addYear(); // إضافة سنة كاملة من الآن
    
    $user->update([
        'expiry_date' => $newExpiryDate,
        'admin_approved' => true
    ]);

    return redirect()->back()->with('success', 'تم تجديد الاشتراك بنجاح حتى ' . $newExpiryDate->format('Y-m-d'));
}

}