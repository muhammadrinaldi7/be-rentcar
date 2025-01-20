<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    public function show($filename)
    {
        // Path file di storage/public
        $path = storage_path('app/public/images/' . $filename);

        // Jika file tidak ditemukan, kirimkan error 404
        if (!File::exists($path)) {
            abort(404, 'Image not found');
        }

        // Ambil konten file
        $file = File::get($path);
        $type = File::mimeType($path);

        // Kembalikan respons dengan file dan MIME type
        return response($file, 200)->header('Content-Type', $type);
    }
}
