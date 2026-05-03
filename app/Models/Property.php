<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'location',
        'type',      // Land | Apartment
        'user_id',   // صاحب العقار
    ];

    /**
     * علاقة العقار مع المستخدم (المالك)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
