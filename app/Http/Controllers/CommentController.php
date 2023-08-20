<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\CourseRequest;
use App\Models\Comment;
use App\Models\Course;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   

    public function index(Request $request)
    {
        $userCourses = $request->user()->courses();
        $userCourses = $userCourses->where('status', $request->status);

        if ($request->searchText) {
            $userCourses = $userCourses->where('title', 'like', "%$request->searchText%");
        }
        $userCourses = $userCourses->paginate($request->pageSize);

        $userCourses->each(function ($course) {
            if ($course->avatar) {
                $course->avatar ='/storage/images/course/' . $course->avatar;
            }
        });
        // return request()->server('SERVER_NAME');
        return $userCourses;
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request)
    {
        $user = Auth::user()->id;
        $comment = new Comment();
        $comment->fill($request->all());
        $comment->user_id = $user;
        $comment->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Thêm mới thành công!'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request)
    {
        $user_id = Auth::user()->id;
        $comment = Comment::findOrFail($request->id);

        if($comment->user_id == $user_id){
            $comment->fill($request->all());
            $comment->user_id = $user_id;
            $comment->save();
        }else{
            return response()->json([
                'statusCode' => 400,
                'message' => 'Bạn không có quyền thực hiện!'
            ], 400);
        }

        
        return response()->json([
            'statusCode' => 200,
            'message' => 'Cập nhật thành công!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $course = Course::where('slug', $slug)->first();
        Course::destroy($course->id);

        return response()->json([
            'statusCode' => 200,
            'message' => 'Xóa khóa học thành công!'
        ], 200);
    }

 
}
