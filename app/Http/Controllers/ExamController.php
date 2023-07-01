<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExamRequest $request)
    {
        $exam = new Exam();
        $exam->fill($request->all());
        $exam->save();

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
        $exam = Exam::where('slug', $slug)->first();
        return $exam;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExamRequest $request)
    {
        $exam = Exam::findOrFail($request->id);
        $exam->fill($request->all());
        $exam->save();
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
        $exam = Exam::where('slug', $slug)->first();
        Exam::destroy( $exam->id);
        
        return response()->json([
            'statusCode' => 200,
            'message' => 'Xóa bài kiểm tra thành công!'
        ], 200);
    }
}
