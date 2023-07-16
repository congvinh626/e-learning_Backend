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
    public function show(string $id)
    {
        $user = User::user()->id;
        $user = UserInfo::find($user);

        return $user;
    }

    public function store(request $request)
    {
       
        $fileName = $request->get('name') . '.' . $request->file('photo')->extension();
        $request->file('photo')->storeAs('profile', $fileName);
        $pathToFile = $request->file('image')->store('images', 'public');

        // ImageIntervention::make(storage_path($path))->resize(150,150)->save();
        return $fileName;
    }

    public function avatar(request $request)
    {
        $user = Auth::user();
        $userInfo = UserInfo::where('user_id', $user->id)->first();

        $userInfo->avatar = $this->storeImage($request);
        $userInfo->save();

        return $userInfo;
    }

    protected function storeImage(Request $request) {
        
        $user = Auth::user();
        $avatar = UserInfo::where('user_id',$user->id)->first()->avatar;
       
        $file_path = storage_path().'/app/public/images/avatar/'. $avatar;
        if (File::exists($file_path)) {
            
            unlink($file_path);
        }

        $fileName = $user->username . '_' .  time().'.' . $request->file('avatar')->extension();        

        $path = $request->file('avatar')->storeAs('public/images/avatar', $fileName);
        return substr($path, strlen('public/images/avatar/'));

      }
}
