<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\UserInfo;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Otp;
use DB;
class RegisterControler extends Controller
{

    public function register(RegisterRequest $request){
        $user = new User;
        $user->fill($request->all());
        $user->password = Hash::make($request->password);
        $user->save();

        $userInfo = new UserInfo();
        $userInfo->user_id = $user->id;
        $userInfo->save();

        $user->notify(new EmailVerificationNotification());

        return response()->json($user);
    }

    public function resend(Request $request){
        $user = User::where('email', $request->email)->first();
        $user->notify(new EmailVerificationNotification());
        return response()->json([
            'statusCode' => 200,
            'message' =>  'Gửi OTP thành công!'
        ], 200);
    }

    public function verifyOtp(Request $request){
        $otp = DB::table('otps')->where('identifier', $request->email)->first()->token;
        if ($request->otp != $otp) {
            return response()->json([
                'statusCode' => 400,
                'message' =>  'Otp sai hoặc đã hết hạn!'
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = time();
        $user->save();
        
        return response()->json([
            'statusCode' => 200,
            'message' =>  'Thành công!'
        ]);
    }

    

    public function login(LoginRequest $request){
        if(Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ])){
            $user = User::where('username', $request->username)->first();
            $user->token = $user->createToken('App')->accessToken;
            $user->statusCode = 200;
            return response()->json($user);
        }

        return response()->json([
            'statusCode' => 400,
            'message' => [
                'incorrect' => 'Tên đăng nhập hoặc mật khẩu không chính xác'
            ]
        ], 200);
    }

    public function getUser(Request $request){
        return response()->json($request->user('api'));
    }
}
