<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promo extends Model
{
     use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',        
        'start_date',
        'end_date',
        'minimum_order_value',
        'max_discount_value',
        'status',
    ];
}
