<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\RegisterControler;
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


// Route::post('/register', 'Regis@register');
// Route::post('/register', function(){
//     return 3333;
// });
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/course', [CourseController::class, 'index']);
    Route::get('/course/{slug}', [CourseController::class, 'show']);
    Route::post('/course', [CourseController::class, 'store']);
    Route::post('/course/update', [CourseController::class, 'update']);
    Route::delete('/course/{slug}', [CourseController::class, 'destroy']);
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
