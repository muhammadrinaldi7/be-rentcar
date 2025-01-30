<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Booking;

class TransactionController extends Controller
{
    public function toPayment($id) {
        $booking = Booking::find($id);

    // Cek apakah booking ditemukan
    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found',
        ], 404);
    }

    // Jika booking ditemukan, kembalikan data booking
    return new TransactionResource(true, 'Success', $booking);
    }
}
