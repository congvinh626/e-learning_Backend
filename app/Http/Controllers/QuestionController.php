<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

   
    public function store(QuestionRequest $request)
    {
        $question = new Question();
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
    public function update(Request $request)
    {
        $question = Question::findOrFail($request->id);
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
        $question = Question::find($id);
        Question::destroy( $question->id);
        
        return response()->json([
            'statusCode' => 200,
            'message' => 'Xóa câu hỏi thành công!'
        ], 200);
    }
}
