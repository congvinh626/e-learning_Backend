<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CommentController;
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
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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




Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/sendOtp', [RegisterControler::class, 'sendOtp']);
    Route::post('/verifyOtp/{otp}', [RegisterControler::class, 'verifyOtp']);

    Route::get('/course', [CourseController::class, 'index']);
    Route::get('/course/suggest', [CourseController::class, 'suggest']);
    Route::get('/course/{slug}', [CourseController::class, 'show']);
    Route::post('/course', [CourseController::class, 'store']);
    Route::post('/course/update', [CourseController::class, 'update']);
    Route::delete('/course/{slug}', [CourseController::class, 'destroy']);
    Route::post('/course/changeStatus/{slug}', [CourseController::class, 'changeStatus']);
    Route::post('/course/register/{id}', [CourseController::class, 'register']);
    Route::post('/course/member/{id}', [CourseController::class, 'member']);
    Route::post('/course/addMember', [CourseController::class, 'addMember']);
    Route::post('/course/removeMember', [CourseController::class, 'removeMember']);
    Route::get('/course/waitConfirmMember/{id}', [CourseController::class, 'waitConfirmMember']);
    Route::post('/course/getOff/{id}', [CourseController::class, 'getOff']);
    
    Route::get('/course/lesson/{slug}', [LessonController::class, 'index']);
    Route::get('/lesson/{slug}', [LessonController::class, 'show']);
    Route::post('/lesson', [LessonController::class, 'store']);
    Route::post('/lesson/update', [LessonController::class, 'update']);
    Route::delete('/lesson/{slug}', [LessonController::class, 'destroy']);


    Route::post('/notification', [LessonController::class, 'pushNotification']);
    Route::get('/notification', [LessonController::class, 'getNotification']);

    // Route::get('/exam/{slug}', [LessonController::class, 'index']);
    Route::get('/exam/{slug}', [ExamController::class, 'show']);
    Route::post('/exam', [ExamController::class, 'store']);
    Route::post('/exam/update', [ExamController::class, 'update']);
    Route::delete('/exam/{slug}', [ExamController::class, 'destroy']);
    Route::get('/getExam/{slug}', [ExamController::class, 'getExam']);
    Route::put('/uploadExam', [ExamController::class, 'uploadExam']);
    Route::post('/importExam', [ExamController::class, 'importExam']);
    
    // Route::get('/exam/{slug}', [ExamController::class, 'show']);
    Route::post('/question', [QuestionController::class, 'store']);
    Route::post('/question/update', [QuestionController::class, 'update']);
    Route::delete('/question/{id}', [QuestionController::class, 'destroy']);

    Route::get('/comment/{slug}', [CommentController::class, 'index']);
    Route::post('/comment', [CommentController::class, 'store']);
    Route::post('/comment/update', [CommentController::class, 'update']);
    Route::delete('/comment/{id}', [CommentController::class, 'destroy']);

    Route::post('/answer', [AnswerController::class, 'store']);
    Route::post('/answer/update', [AnswerController::class, 'update']);
    Route::delete('/answer/{id}', [AnswerController::class, 'destroy']);

    Route::get('/historyExam/{id}', [HistoryController::class, 'show']);

    Route::get('/user', [UserController::class, 'show']);
    Route::post('/user/update', [UserController::class, 'update']);
    Route::post('/avatar', [UserController::class, 'avatar']);



    // Route::post('/fileUpload', [LessonController::class, 'fileUpload']);
    Route::delete('/fileUpload/{id}', [FileUploadController::class, 'destroy']);
    Route::post('/upload/avatar', [UserController::class, 'uploadAvatar']);

    Route::post('/addRoleTo', [UserController::class, 'addRoleTo']);
    Route::post('/addPermissonsTo', [UserController::class, 'addPermissonsTo']);
    Route::post('/addManyPermissonsTo', [UserController::class, 'addManyPermissonsTo']);
    
    Route::post('/addPermissonsToRole', [UserController::class, 'addPermissonsToRole']);

    Route::post('/upload-excel-create-role-permission', [UserController::class, 'createRolePermission']);
    
});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
