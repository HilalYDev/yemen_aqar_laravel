<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'office_name', 'identity_number', 'image', 'office_address', 'office_phone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
