<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadImageController extends Controller
{
    //
     public function upload(Request $request)
    {
          $request->validate([
            'images[].*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk multiple files
        ]);

        $urls = [];

        // Iterasi dan upload setiap gambar
        foreach ($request->file('images') as $file) {
            $fileName = time() . '-' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            $filePath = $file->storeAs('images', $fileName, 'public');
            
            // Generate URL untuk file yang diupload
            $url = url('api/images/' . $fileName);
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
}
