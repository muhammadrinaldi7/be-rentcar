<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class transaction extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'payment_method',
        'payment_status',
    ];
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
