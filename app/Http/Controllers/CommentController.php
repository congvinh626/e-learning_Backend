<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\CourseRequest;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserInfo;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   

    public function index(string $slug)
    {   
        $lesson = Lesson::where('slug', $slug)->with('comments')->first();

        foreach ($lesson->comments as $comment) {
            $user = UserInfo::where('user_id', $comment->user_id)->first();
            $comment->avatar = $user->avatar;
            $comment->name = $user->name;
        }
        return $lesson;
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

        return statusResponse2(200, 200, 'Thêm mới thành công!', '');
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
            return statusResponse2(400, 200, 'Bạn không có quyền truy cập!', '');

        }
        return statusResponse2(200, 200, 'Cập nhật thành công!', '');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::findOrFail($id);        
        if(Auth::user()->id != $comment->id){
            return statusResponse2(400, 200, 'Bạn không có quyền truy cập!', '');
        }
        Course::destroy($comment->id);

        return statusResponse2(200, 200, 'Xóa khóa học thành công!', '');

    }

 
}
