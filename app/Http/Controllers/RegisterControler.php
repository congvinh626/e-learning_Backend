<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use App\Notifications\EmailVerificationNotification;
use Carbon\Carbon;
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

        $user->notify(new EmailVerificationNotification());

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
        return response()->json($user);
    }

    public function sendOtp(){
        $email = Auth::user()->email;

        $user = User::where('email', $email)->first();
        $user->notify(new EmailVerificationNotification());
       
        return statusResponse(200, 'Gửi OTP thành công!');

    }

    public function verifyOtp(string $otp, Request $request){
        $email = Auth::user()->email;

        $otps = DB::table('otps')->where('identifier', $email)->first();

        // $user_checkCreate = ;
        $created_at = Carbon::parse($otps->created_at);
        $two_minutes_later = $created_at->addMinutes(2);
        $current_time = Carbon::now();

        if ($current_time->greaterThan($two_minutes_later)) {
            return statusResponse(400, 'Otp sai hoặc đã hết hạn!');
        }

        $user_otp = $otps->token;
        if ($otp != $user_otp) {
            return statusResponse(400, 'Otp sai hoặc đã hết hạn!');
        }

        $user = User::where('email', $email)->first();
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

            $data = [
                'token' => $token,
                'role' => $role,
                'permission' => $permission,
                'user_id' => $user->id
            ];

            if(!$user->email_verified_at){
                $this->sendOtp();
                return statusResponse2('ACC013', 200, 'Bạn chưa xác thực!', $data);
            }

            $name_user = UserInfo::where('user_id', $user->id)->first()->name;
            if(!$name_user){
                return statusResponse2('ACC017', 200, 'Bạn chưa cập nhật thông tin!', $data);
            }

            return statusResponse2(200, 200, 'Đăng nhập thành công!', $data);
          
        }

        return statusResponse2(200, 400, 'Tên đăng nhập hoặc mật khẩu không chính xác', '');
    }

    public function getUser(Request $request){
        return response()->json($request->user('api'));
    }
}
