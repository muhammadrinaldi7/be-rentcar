<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
          $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials from request
        $credentials = $request->only('email', 'password');
        if(User::all()->where('email', $credentials['email'])->isEmpty()){
             return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar'
            ], 404);
        }
        //if auth failed
        if(!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Password Anda salah'
            ], 401);
        }
         $user = Auth::user();
        if ($user->is_admin == 1) {
             $is_admin = "admin";
         }else{
             $is_admin = "user";
         };

         $customClaims = ['role' => $is_admin, 'name' => $user->name, 'email' => $user->email, 'id' => $user->id];
        $token = JWTAuth::claims($customClaims)->attempt($credentials);

        //if auth success
        return response()->json([
            'success' => true,   
            'token'   => $token   
        ], 200);
    }
}
