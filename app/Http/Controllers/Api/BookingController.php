<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
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
        $Booking = Booking::where('user_id', Auth::user()->id)->get();
        return new BookingResource(true, 'List Data Booking', $Booking);
    }
    public function show($id){
        $Booking = Booking::findOrFail($id);
        return new BookingResource(true, 'Detail Data Booking', $Booking);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'promo_code' => 'nullable|exists:promos,code',
            'start_date' => 'required',
            'end_date' => 'required',
            'total_price' => 'required|numeric',
            'discount_applied' => 'nullable|numeric',
            'final_price' => 'required|numeric',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $booking = Booking::create([
            'user_id' => $request->user_id,
            'car_id' => $request->car_id,
            'promo_code' => $request->promo_code,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $request->total_price,
            'discount_applied' => $request->discount_applied,
            'final_price' => $request->final_price,
            'status' => $request->status,
        ]);

        return new BookingResource(true, 'Data Booking Berhasil Ditambahkan', $booking);
    }
}
