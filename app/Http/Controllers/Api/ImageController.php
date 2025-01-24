<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Upload multiple images and return accessible URLs.
     */
    public function upload(Request $request)
    {
          $request->validate([
            'images[].*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk multiple files
        ]);

        $urls = [];

        // Iterasi dan upload setiap gambar
        foreach ($request->file('images') as $file) {
            $fileName = time() . '-' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

             $file->storeAs('images', $fileName, 'public');

            // Generate URL untuk file yang diupload
            $url =  route('images.show', ['filename' => $fileName]);
            $urls[] = asset($url);  // Menyimpan URL ke dalam array
        }

        // Response JSON dengan array URL
        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'message' => 'Upload images success',
            'urls' => $urls,  // Mengembalikan array URL
        ]);
    }

    /**
     * Serve image files from storage.
     */
    public function show($filename)
    {
        // Path to the file in storage/app/public/images
        $path = storage_path('app/public/images/' . $filename);

        // If file doesn't exist, return 404 error
        if (!File::exists($path)) {
            abort(404, 'Image not found');
        }

        // Get file contents and MIME type
        $file = File::get($path);
        $type = File::mimeType($path);

        // Return the file as a response with the correct MIME type
        return response($file, 200)->header('Content-Type', $type);
    }
}
