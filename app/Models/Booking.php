<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'car_id',
        'promo_code',
        'start_date',
        'end_date',
        'total_price',
        'discount_applied',
        'external_id',
        'final_price',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function car()
    {
        return $this->belongsTo(Car::class);
    }
    public function transactions()
    {
        return $this->hasOne(Transaction::class);
    }
}
