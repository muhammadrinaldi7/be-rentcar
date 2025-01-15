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
        $cars = Promo::latest()->paginate(5);

        return new PromoResource(true, 'List Data Cars', $cars);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'description' => 'required',
            'discount_type' => 'required',
            'value' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'minimum_order_value' => 'required',
            'maximum_order_value' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $car = Promo::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'price_per_day' => $request->price_per_day,
            'available' => $request->available,
            'image_urls' => $request->image_urls,
        ]);

        return new PromoResource(true, 'Data Car Berhasil Ditambahkan', $car);
    }
}
