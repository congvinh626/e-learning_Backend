<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterControler extends Controller
{
    //
    public function register(RegisterRequest $request){
        $user = new User;
        $user->fill($request->all());
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json($user);
    }

    public function login(LoginRequest $request){
        if(Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])){
            $user = User::whereEmail($request->email)->first();
            $user->token = $user->createToken('App')->accessToken;
            $user->statusCode = 200;
            return response()->json($user);
        }

        return response()->json(['email' => 'Sai ten dang nhap hoac mat khau'], 400);
    }

    public function getUser(Request $request){
        return response()->json($request->user('api'));
    }
}
