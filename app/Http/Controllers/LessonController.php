<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $slug)
    {
        $course = Course::where('slug', $slug)->first();
        $course->lessons;
        return $course;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LessonRequest $request)
    {
        $lesson = new Lesson();
        $lesson->fill($request->all());
        $lesson->save();

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
        $lesson = Lesson::where('slug', $slug)->first();
        return $lesson;
    }

   
    /**
     * Update the specified resource in storage.
     */
    public function update(LessonRequest $request)
    {
        $lesson = Lesson::findOrFail($request->id);
        $lesson->fill($request->all());
        $lesson->save();
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
        $course = Lesson::where('slug', $slug)->first();
        Lesson::destroy( $course->id);
        
        return response()->json([
            'statusCode' => 200,
            'message' => 'Xóa bài học thành công!'
        ], 200);
    }
}
