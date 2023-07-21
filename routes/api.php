<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\RegisterControler;
use App\Http\Controllers\UserController;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Console\Output\AnsiColorMode;

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
    Route::post('/course/changeStatus/{slug}', [CourseController::class, 'changeStatus']);

    Route::get('/course/lesson/{slug}', [LessonController::class, 'index']);
    Route::get('/lesson/{slug}', [LessonController::class, 'show']);
    Route::post('/lesson', [LessonController::class, 'store']);
    Route::post('/lesson/update', [LessonController::class, 'update']);
    Route::delete('/lesson/{slug}', [LessonController::class, 'destroy']);

    // Route::get('/exam/{slug}', [LessonController::class, 'index']);
    Route::get('/exam/{slug}', [ExamController::class, 'show']);
    Route::post('/exam', [ExamController::class, 'store']);
    Route::post('/exam/update', [ExamController::class, 'update']);
    Route::delete('/exam/{slug}', [ExamController::class, 'destroy']);
    Route::get('/getExam/{slug}', [ExamController::class, 'getExam']);

    // Route::get('/exam/{slug}', [ExamController::class, 'show']);
    Route::post('/question', [QuestionController::class, 'store']);
    Route::post('/question/update', [QuestionController::class, 'update']);
    Route::delete('/question/{id}', [QuestionController::class, 'destroy']);

    Route::post('/answer', [AnswerController::class, 'store']);
    Route::post('/answer/update', [AnswerController::class, 'update']);
    Route::delete('/answer/{id}', [AnswerController::class, 'destroy']);

    Route::get('/history/{id}', [HistoryController::class, 'show']);
    Route::post('/history', [HistoryController::class, 'store']);

    Route::get('/user', [UserController::class, 'show']);
    Route::post('/user/update', [UserController::class, 'update']);
    Route::post('/avatar', [UserController::class, 'avatar']);

    Route::post('/importExam', [ExamController::class, 'importExam']);


    // Route::post('/fileUpload', [LessonController::class, 'fileUpload']);
    Route::delete('/fileUpload/{id}', [FileUploadController::class, 'destroy']);

});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
