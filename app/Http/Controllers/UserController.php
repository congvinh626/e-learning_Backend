<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(string $id)
    {
        $user = User::user()->id;
        $user = UserInfo::find($user);

        return $user;
    }

    public function store(UserRequest $request)
    {
        if ($request->id) {
            $user = User::findOrFail($request->id);
        } else {
            $user = new User();
        }
        $user->fill($request->all());
        $user->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Cập nhật thành công!'
        ], 200);
    }
}
