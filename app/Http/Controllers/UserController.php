<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserInfo;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ImageIntervention;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function show()
    {
        $userId = Auth::user()->id;
        $userInfo = UserInfo::where('user_id', $userId)->first();
        $userInfo->avatar = '/storage/images/avatar/'.$userInfo->avatar;
        return $userInfo;
    }

    public function update(Request $request)
    {
        $userInfo = UserInfo::findOrFail($request->id);        
        $userInfo->name = $request->name;
        $userInfo->phone = $request->phone;
        $userInfo->dob = $request->dob;
        $userInfo->adress = $request->adress;
        $userInfo->save();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Cập nhật thành công!'
        ], 200);
    }

    public function avatar(request $request)
    {
        
        $user = Auth::user();

        $userInfo = UserInfo::where('user_id', $user->id)->first();

        $image =  $this->imageService->storeImage($request->file('avatar'), 'public/images/avatar', $userInfo->avatar, $user->username);

        $userInfo->avatar = $image;
        $userInfo->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Cập nhật thành công!'
        ], 200);
    }

    
}
