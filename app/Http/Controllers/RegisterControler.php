<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Otp;
use DB;
use Illuminate\Support\Str;
use stdClass;

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
        
        if($request->type == 1){
            $user->roles()->attach(2);
            $role = Role::findOrFail(2); 
        }else{
            $user->roles()->attach(3);
            $role = Role::findOrFail(3); 
        }
        $permissions_role = $role->permissions->pluck('id');
        $user->permissions()->attach($permissions_role);
        // $user->givePermissionsTo($permissions_role);


        $user->notify(new EmailVerificationNotification());

        return response()->json($user);
    }

    public function resend(Request $request){
        $user = User::where('email', $request->email)->first();
        $user->notify(new EmailVerificationNotification());
       
        return statusResponse(200, 'Gửi OTP thành công!');

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
        
        return statusResponse(200, 'Thành công');
    }

    

    public function login(LoginRequest $request){
        if(Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ])){
            

            $user = User::where('username', $request->username)->first();

            $token = $user->createToken('App')->accessToken;
            $role = $user->roles->pluck('slug');
            $permission = $user->permissions->pluck('slug');

            return response()->json([
                'statusCode' => 200,
                'data' => [
                    'token' => $token,
                    'role' => $role,
                    'permission' => $permission
                ]
            ], 200);
        }

        return statusResponse(400, 'Tên đăng nhập hoặc mật khẩu không chính xác');
    }

    public function getUser(Request $request){
        return response()->json($request->user('api'));
    }
}
