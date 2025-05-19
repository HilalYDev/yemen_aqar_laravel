<?php

namespace App\Models;

use App\Models\OfficeDetail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    use HasFactory, Notifiable;


    protected $fillable = [
        'name', 'phone', 'verification_code', 'approved', 'admin_approved', 'token', 'type', 'password', 'expiry_date',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function details(): HasOne
    {
        return $this->hasOne(OfficeDetail::class);
    }
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
