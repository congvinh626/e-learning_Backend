<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userCourses = $request->users()->courses();
        if($request->search){
            $userCourses = $userCourses->where('title', 'like', "%$request->search%");
        }
        $userCourses = $userCourses->paginate($request->pageSize);
        // $userCourses = $userCourses->paginate()->toArray();
        return $userCourses;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request)
    {
        $user = Auth::user()->id;
        $course = new Course();
        $course->fill($request->all());
        $course->save();
        $course->users()->attach($user);

        return response()->json([
            'statusCode' => 200,
            'message' => 'Thêm mới thành công!'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $course = Course::where('slug', $slug)->first();
        return $course;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseRequest $request)
    {
        $course = Course::findOrFail($request->id);
        $course->fill($request->all());
        $course->save();
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
        Course::destroy( $course->id);
        
        return response()->json([
            'statusCode' => 200,
            'message' => 'Xóa khóa học thành công!'
        ], 200);
    }

    public function changeStatus(string $slug)
    {
        $course = Course::where('slug', $slug)->first();

        $course->status = $course->status == 1 ? 0 : 1;
        $course->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Thay đổi trạng thái thành công!'
        ], 200);
    }
    
}
