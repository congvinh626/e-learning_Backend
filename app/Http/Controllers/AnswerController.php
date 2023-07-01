<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
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
    public function store(AnswerRequest $request)
    {
        $question = new Answer();
        $question->fill($request->all());
        $question->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Thêm mới thành công!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnswerRequest $request)
    {
        $question = Answer::findOrFail($request->id);
        $question->fill($request->all());
        $question->save();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Cập nhật thành công!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = Answer::find($id);
        Answer::destroy( $question->id);
        
        return response()->json([
            'statusCode' => 200,
            'message' => 'Xóa câu hỏi thành công!'
        ], 200);
    }
}
