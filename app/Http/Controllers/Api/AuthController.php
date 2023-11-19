<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    //Register
    public function register(Request $request) {
        $registrationData = $request->all();

        $validate = Validator::make($registrationData, [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'name' => 'required|max:60',
            'email'=> 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8',
            'no_telp' => 'required|regex:/^08[0-9]+$/|between:11,13',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $lastUser = DB::table('users')->orderBy('id', 'desc')->first();
        $currentYear = date('y');
        $currentMonth = date('m');

        if(is_null($lastUser)) {
            $newId = $currentYear . '.' . $currentMonth . '.' . 1; // bikin inisialisasi id pertama
        } else {
            $lastIdUser = $lastUser->id; // ambil ID terakhir di database kalau sudah ada
            $numberStrElements = explode('.', $lastIdUser); // pecah element2 nya berdasarkan pemisah tanda titik (.) => output array
            $lastStrIndex = end($numberStrElements); // ambil element terakhir dari array pecahan di atas yang berupa indexnya
            $lastIndex = intval($lastStrIndex); // ubah menjadi integer untuk jaga2 jika masih berupa string supaya bisa di increment
            $lastIndex = $lastIndex + 1; // increment index nya

            $newId = $currentYear . '.' . $currentMonth . '.' . $lastIndex; // gabungkan menjadi index baru untuk dimasukkan pada database
        }

        $registrationData['id'] = $newId;
        $registrationData['status'] = 0;
        $registrationData['password'] = bcrypt($request->password);

        $user = User::create([
            'id' => $newId,
            'image' => $registrationData['image'],
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'password' => $registrationData['password'],
            'no_telp' => $registrationData['no_telp'],
            'status' => $registrationData['status']
        ]);

        return response([
            'message'=> 'Register Success',
            'user' => $user
        ],200);
    }

    //Login
    public function login(Request $request) {
        $loginData = $request->all();

        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required',
        ]);

        if($validate->fails()) {
            return response(['message'=> $validate->errors()], 400);
        }

        if(!Auth::attempt($loginData)) {
            return response(['message'=> 'Invalid Credential'], 401);
        }

        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $token = $user->createToken('Authentication Token')->accessToken;

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token,
        ]);
    }

    //Logout
}
