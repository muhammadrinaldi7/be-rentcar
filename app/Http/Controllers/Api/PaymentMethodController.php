<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodsResource;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $paymentMethods = PaymentMethod::all();
        return new PaymentMethodsResource(true, 'List Data Payment Methods', $paymentMethods);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'image_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $paymentMethod = PaymentMethod::create([
            'name' => $request->name,
            'description' => $request->description,
            'image_url' => $request->image_url,
        ]);

        return new PaymentMethodsResource(true, 'Data Payment Method Berhasil Ditambahkan', $paymentMethod);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
