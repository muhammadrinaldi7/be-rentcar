<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
     /**
     * index
     *
     * @return void
     */
    /**
     * Get all users.
     * 
     * @group Users
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "List Data Users",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "johndoe@me.com",
     *       "is_admin": 1,
     *       "created_at": "2020-01-01T00:00:00.000000Z",
     *       "updated_at": "2020-01-01T00:00:00.000000Z"
     *     },
     * ]
     * }
     */
    public function index()
    {
        //get all posts
        $user = User::latest()->paginate(5);

        //return collection of posts as a resource
        return new UserResource(true, 'List Data Users', $user);
    }

    public function store(Request $request){
       $validator = Validator::make($request->all(), [
           'name' => 'required',
           'email' => 'required|email|unique:users',
           'password' => 'required|min:6',
           'is_admin' => 'required'
       ]);

       if ($validator->fails()) {
           return response()->json($validator->errors(), 422);
       }
       $user = User::create([
           'name' => $request->name,
           'email' => $request->email,
           'password' => bcrypt($request->password),
           'is_admin' => $request->is_admin
       ]);

        return new UserResource(true, 'Data User Berhasil Ditambahkan', $user);
    }
    public function show($id){
        $user = User::findOrFail($id);
        return new UserResource(true, 'Detail Data User', $user);
    }
    public function update(Request $request, $id){
        $user = User::findOrFail($id);
        $user->update($request->all());
        return new UserResource(true, 'Data User Berhasil Diupdate', $user);
    }
    public function destroy($id){
        $user = User::findOrFail($id);
        $user->delete();
        return new UserResource(true, 'Data User Berhasil Dihapus', null);
    }
    public function registerUser(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),            
        ]);

         return new UserResource(true, 'Register User Berhasil!!', $user);
    }
}
