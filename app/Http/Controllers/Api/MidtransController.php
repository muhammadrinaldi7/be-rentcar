<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    public function createTransaction(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
        Config::$curlOptions = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];


        // Buat detail transaksi
        $transaction = [
            'transaction_details' => [
                'order_id' => 121313,
                'gross_amount' => 100000, // Harga dalam IDR
            ],
            'customer_details' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '08123456789',
            ]
        ];

        // Dapatkan Snap Token
        $snapToken = Snap::getSnapToken($transaction);

        return response()->json(['token' => $snapToken]);
    }
    public function handleNotification(Request $request)
{
    $notif = new \Midtrans\Notification();

    $transactionStatus = $notif->transaction_status;
    $orderId = $notif->order_id;

    if ($transactionStatus == 'settlement') {
        // Update status transaksi jadi "lunas"
    } elseif ($transactionStatus == 'pending') {
        // Transaksi belum dibayar
    } elseif ($transactionStatus == 'expire') {
        // Transaksi kadaluarsa
    }

    return response()->json(['message' => 'Notification received']);
}

}
