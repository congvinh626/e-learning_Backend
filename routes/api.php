<?php

use App\Http\Controllers\RegisterControler;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [RegisterControler::class, 'register']);
Route::post('/login', [RegisterControler::class, 'login']);
Route::get('/user', [RegisterControler::class, 'getUser'])->middleware('auth:api');
Route::get('/getUser', [UserController::class, 'index'])->middleware('auth:api');
Route::get('/task', [TaskController::class, 'index'])->middleware('auth:api');
Route::post('/task', [TaskController::class, 'store'])->middleware('auth:api');
Route::delete('/task/{id}', [TaskController::class, 'destroy'])->middleware('auth:api');
// Route::post('/register', 'Regis@register');
// Route::post('/register', function(){
//     return 3333;
// });


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
