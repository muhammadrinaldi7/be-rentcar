<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Promo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
     public function index(){
        $Booking = Booking::latest()->paginate(5);

        return new BookingResource(true, 'List Data Booking', $Booking);
    }
    public function myBooking(){
        $Booking = Booking::with('car')->where('user_id', Auth::user()->id)->get();
        return new BookingResource(true, 'List Data Booking', $Booking);
    }
    public function show($id){
          $booking = Booking::find($id);

    // Cek apakah booking ditemukan
    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found',
        ], 404);
    }
    

    // Jika booking ditemukan, kembalikan data booking
    return new BookingResource(true, 'Success', $booking);
    }
    public function toPayment($id) {
        $booking = Booking::with('car')->find($id);
        $booking->makeHidden(['created_at', 'updated_at', 'user_id', 'car_id']);        

        // Cek apakah booking ditemukan
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
            ], 404);
        }
         $startDate = Carbon::parse($booking->start_date);
        $endDate = Carbon::parse($booking->end_date);
        $duration = $startDate->diffInDays($endDate); // Menghitung selisih hari

    // Menyembunyikan field yang tidak diinginkan
    $booking->makeHidden(['created_at', 'updated_at', 'user_id', 'car_id']);

    // Membuat array untuk respons
    $responseData = $booking->toArray(); // Mengonversi model ke array
    $responseData['duration'] = $duration; // Menambahkan durasi ke array


    // Jika booking ditemukan, kembalikan data booking
    return new BookingResource(true, 'Success', $responseData);
    }
   public function store(Request $request){
    // Validasi Input
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:cars,id',
            'promo_code' => 'nullable|exists:promos,code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

    if ($validator->fails()) {
    // Mengambil semua pesan kesalahan
    $errors = $validator->errors()->all();
    
    // Menggabungkan pesan kesalahan menjadi satu string
    $errorMessage = implode(', ', $errors);
    
    // Mengembalikan respons JSON dengan format yang diinginkan
    return response()->json(['message' => $errorMessage], 422);
    }
    $car = Car::findOrFail($request->car_id);
    // Default nilai untuk diskon dan harga final
     $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);
    $days = $startDate->diffInDays($endDate); 

    $totalPrice = $car->price_per_day * $days;
    $discountApplied = 0;
    
    $finalPrice = $totalPrice;
    // Jika ada promo code, validasi dan hitung diskon
    if ($request->has('promo_code') && $request->promo_code) {
        // Cari promo code di database
        $promo = Promo::where('code', $request->promo_code)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$promo) {
            return response()->json(['message' => 'Promo code is invalid or expired.'], 400);
        }

        // Hitung diskon (misalnya dalam persen atau nominal langsung)
        if ($promo->discount_type === '%') {
            $discountApplied = ($promo->discount_value / 100) * $totalPrice;
        } elseif ($promo->discount_type === 'fixed') {
            $discountApplied = $promo->discount_value;
        }

        // Pastikan diskon tidak lebih besar dari total harga
        $discountApplied = min($discountApplied, $totalPrice );
        $finalPrice = $totalPrice- $discountApplied;
    }

    // Simpan booking
    $booking = Booking::with('car')->create([
        'user_id' => Auth::user()->id,
        'car_id' => $request->car_id,
        'promo_code' => $request->promo_code,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'total_price' => $totalPrice,
        'discount_applied' => $discountApplied,
        'final_price' => $finalPrice,
        'status' => 'pending',
    ]);
   
    $booking->load('car')->makeHidden(['created_at', 'updated_at', 'user_id', 'car_id']);
    
    return new BookingResource(true, 'Data Booking Berhasil Ditambahkan', $booking);
}

    public function applyPromo(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'promo_code' => 'required|exists:promos,code',
        'total_price' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Invalid promo code or price'], 422);
    }

    // Cari promo code
    $promo = Promo::where('code', $request->promo_code)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->first();

    if (!$promo) {
        return response()->json(['message' => 'Promo code is invalid or expired.'], 400);
    }

    // Hitung diskon
    $discount = 0;
    if ($promo->discount_type === '%') {
        $discount = ($promo->discount_value / 100) * $request->total_price;
    } elseif ($promo->discount_type === 'fixed') {
        $discount = $promo->discount_value;
    }

    // Pastikan diskon tidak lebih besar dari total harga
    $discount = min($discount, $request->total_price);

    // Hitung harga akhir
    $finalPrice = $request->total_price - $discount;

    // Return hasil ke frontend
    return response()->json([
        'success' => true,
        'promo' => $promo->code,
        'discount' => $discount,
        'final_price' => $finalPrice,
        'message' => 'Promo code applied successfully',
    ]);
}

}
