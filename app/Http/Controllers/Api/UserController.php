<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::all();

        if(is_null($user)){
            return response(['message' => 'User Data Empty', 'data' => null], 400);
        }

        return response([
            'message' => 'Retrieve All User Data Success',
            'data' => $user
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Sudah di method register pada AuthController
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if(is_null($user)){
            return response(['message' => "User Doesn't Exists", 'data' => null], 400);
        }

        return response([
            'message' => 'Retrieve User Data Success',
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updateData = $request->all();

        $userNow = Auth::user();

        $validate = Validator::make($updateData, [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'name' => 'required|max:60',
            'email'=> ['required', 'email:rfc,dns', Rule::unique('users')->ignore($id)],
            'password' => 'required|min:8',
            'no_telp' => 'required|regex:/^08[0-9]+$/|between:11,13',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $user = User::find($id);

        if(is_null($user)){
            return response(['message' => 'User Not Found'], 400);
        }

        $user->image = $updateData['image'];
        $user->name = $updateData['name'];
        $user->email = $updateData['email'];
        $user->password = $updateData['password'];
        $user->no_telp = $updateData['no_telp'];

        if($user->save()){
            return response([
                'message' => 'Update data user success',
                'data' => $user
            ], 200);
        }

        return response([
            'message' => 'Update data user failed',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if(is_null($user)){
            return response(['message' => 'User not Found'], 400);
        }

        if($user->delete()){
            return response([
                'message' => 'Delete User Data Success',
                'data' => $user
            ], 200);
        }

        return response([
            'message' => 'Delete USer Data Failed',
            'data' => null
        ], 400);
    }
}
