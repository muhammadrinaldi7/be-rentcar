<?php

namespace App\Http\Controllers;


use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
     public function index()
    {
        // Ambil semua pesan dengan data pengguna terkait
        return Message::with('user:id,name')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'user_id' => 'required|exists:users,id',
        ]);

        // Simpan pesan baru ke database
        $message = Message::create([
            'content' => $request->content,
            'user_id' => $request->user_id,
        ]);

        // Broadcast pesan baru
        broadcast(new MessageSent($message->load('user:id,name')))->toOthers();

        return response()->json($message, 201);
    }
}
