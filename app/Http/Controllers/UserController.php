<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }
    
    public function index(Request $request) {
        // dd($request->user());
        if ($request->user()->can('view-users')) {
            return response()->json(User::all()); 
        } 

        return response([
            'status' => false,
            'message' => 'You don\'t have permission to view users!' 
        ], 200);
    }
}
