<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromoResource;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoController extends Controller
{
    //
    public function index(){
        $promos = Promo::latest()->paginate(5);

        return new PromoResource(true, 'List Data Promos', $promos);
    }
    public function getPromosActive(){
        $promo = Promo::all()->where('status', '=', 'active');

        return new PromoResource(true, 'List Data Promo Active', $promo);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'description' => 'required',
            'discount_type' => 'required',
            'discount_value' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'minimum_order_value' => 'required',
            'maximum_order_value' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $car = Promo::create([
            'code' => $request->code,
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'minimum_order_value' => $request->minimum_order_value,
            'maximum_order_value' => $request->maximum_order_value,
        ]);

        return new PromoResource(true, 'Data Car Berhasil Ditambahkan', $car);
    }
}
