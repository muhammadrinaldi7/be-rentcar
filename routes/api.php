<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\UploadImageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\InvoiceCallback;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:api'])->group(function () {
  Route::apiResource('/users', UserController::class);
    Route::apiResource('/cars', CarController::class);  
    Route::apiResource('/bookings', BookingController::class);
    Route::get('/my-booking', [BookingController::class, 'myBooking']);
    Route::apiResource('/promos', PromoController::class);
    Route::post('/apply-promo', [BookingController::class, 'applyPromo']);
    Route::apiResource('/payment-methods', PaymentMethodController::class);
    Route::get('/booktopay/{id}', [BookingController::class, 'toPayment']);

    // Midtrans
    Route::post('/midtrans',[MidtransController::class, 'createTransaction']);
    Route::post('/midtrans/notification', [MidtransController::class, 'handleNotification']);

  });
 Route::post('/xendit/create-invoice', function (Request $request) {
    // Set API Key
    Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));

    // Inisialisasi InvoiceApi
    $invoiceApi = new InvoiceApi();
    $price = Booking::find($request->id)->final_price;
    // Parameter untuk membuat invoice
    $params = [
        'external_id' => 'invoice-' . time(),
        'description' => 'Pembayaran Rental Mobil',
        'amount' => $price,
    ];

    try {
        // Membuat invoice
        $invoice = $invoiceApi->createInvoice($params);
        Booking::where('id', $request->id)->update(['external_id' => $invoice['external_id']]);
        return response()->json($invoice);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
Route::post('/xendit/webhook', function (Request $request) {
    // Ambil payload dari webhook
    $payload = $request->all();
    
    // Anda bisa menggunakan InvoiceCallback untuk memproses callback
    $invoiceCallback = new InvoiceCallback($payload);

    // Pastikan status pembayaran berhasil (atau Anda bisa sesuaikan logika ini)
    if ($invoiceCallback->getStatus() == 'PAID') {
        // Update status pembayaran di database
        $externalId = $invoiceCallback->getExternalId();
        //  Booking::where('external_id', $externalId)->update(['status' => 'paid']);
        $booking = Booking::where('external_id', $externalId)->first();

    if ($booking) {
        // Ubah status booking menjadi "paid"
        $booking->update(['status' => 'paid']);

        // Update status ketersediaan mobil (car) menjadi tidak tersedia (available = 0)
        Car::where('id', $booking->car_id)->update(['available' => 0]);
    }
    }

    // Kirim respons ke Xendit
    return response()->json(['message' => 'Webhook processed successfully']);
});
  // Cars
  Route::get('/get-all-cars', [CarController::class, 'getAllCars']);
  Route::get('/get-ready-cars', [CarController::class, 'getReadyCars']);
  Route::get('/get-booked-cars', [CarController::class, 'getBookedCars']);
  // Promos
  Route::get('/get-active-promos', [PromoController::class, 'getPromosActive']);
  Route::post('/register', RegisterController::class)->name('register');
  Route::post('/login', LoginController::class)->name('login');
  Route::post('/upload-image', [UploadImageController::class, 'upload']);
  Route::get('/messages', [ChatController::class, 'index']);
  Route::post('/messages', [ChatController::class, 'store']);
  Route::post('/images/upload', [ImageController::class, 'upload']);
  Route::get('/images/{filename}', [ImageController::class, 'show'])->name('images.show');
//   Route::get('images/{filename}', [ImageController::class, 'show'])->where('filename', '.*');