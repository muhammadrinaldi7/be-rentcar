<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function getAllCars(){
         $cars = Car::latest()->paginate(5);

        return new CarResource(true, 'List Data Cars', $cars);
    }
    public function getReadyCars(){
        $cars = Car::latest()->where('available', 1)->paginate(5);

        return new CarResource(true, 'List Data Mobil Ready', $cars);
    }
    public function getBookedCars(){
         $cars = Car::latest()->where('available', 0)->paginate(5);
        return new CarResource(true, 'List Data Mobil Jalan', $cars);
    }

    public function index(){
        $cars = Car::latest()->paginate(5);

        return new CarResource(true, 'List Data Cars', $cars);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'brand' => 'required',
            'model' => 'required',
            'fuel' => 'required',
            'capacity' => 'required',
            'transmission' => 'required',
            'year' => 'required',
            'price_per_day' => 'required',
            'available' => 'required',
            'image_urls' => 'required|array',
            'image_urls.*' => 'url',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // $image = $request->file('image_urls');
        // $image->storeAs('public/cars', $image->hashName());
        $car = Car::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'model' => $request->model,
            'fuel' => $request->fuel,
            'capacity' => $request->capacity,
            'transmission' => $request->transmission,
            'year' => $request->year,
            'price_per_day' => $request->price_per_day,
            'available' => $request->available,
            'image_urls' => $request->image_urls,
        ]);

        return new CarResource(true, 'Data Car Berhasil Ditambahkan', $car);
    }
    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'brand' => 'required',
        'model' => 'required',
        'fuel' => 'required',
        'capacity' => 'required',
        'transmission' => 'required',
        'year' => 'required',
        'price_per_day' => 'required',
        'available' => 'required',
        'image_urls' => 'nullable|array', // Optional, bisa null atau array
        'image_urls.*' => 'nullable|url', // Validasi tiap item jika ada
    ]);

    // Cek jika validasi gagal
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Temukan data car berdasarkan ID
    $car = Car::findOrFail($id);

    // Jika ada perubahan pada image_urls
    if ($request->has('image_urls')) {
        // Update image_urls menjadi array baru
        $car->update([
            'image_urls' => $request->image_urls, // Menyimpan URL gambar dalam bentuk JSON
        ]);
    }

    // Update data mobil lainnya tanpa mengubah gambar
    $car->update([
        'name' => $request->name,
        'brand' => $request->brand,
        'model' => $request->model,
        'fuel' => 'required',
        'capacity' => 'required',
        'transmission' => 'required',
        'year' => $request->year,
        'price_per_day' => $request->price_per_day,
        'available' => $request->available,
    ]);

    return new CarResource(true, 'Data Car Berhasil Diupdate', $car);
}

     public function show($id){
        $car = Car::findOrFail($id);
        return new CarResource(true, 'Detail Data Car', $car);
    }

    public function destroy($id){
        $car = Car::findOrFail($id);
        $car->delete();
        return new CarResource(true, 'Data Car Berhasil Dihapus', null);
    }
    
}
