<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Car extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'brand',
        'model',
        'fuel',
        'capacity',
        'transmission',
        'year',
        'price_per_day',
        'available',
        'image_urls',
    ];
    protected $casts = [
        'image_urls' => 'array', // Konversi JSON ke array
    ];
    /**
     * Accessor for Image URLs
     *
     * @return Attribute
     */
    // protected function imageUrls(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($image) => url('/storage/cars/' . $image) , // Decode JSON to array
    //     );
    // }

    /**
     * Relasi ke Bookings
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
