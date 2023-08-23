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
        if ($request->user()->can('question-create')) {
            $question = new Question();
            $question->fill($request->all());
            $question->save();

            return statusResponse(200,"Thêm mới thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if ($request->user()->can('question-update')) {
            $question = Question::findOrFail($request->id);
            $question->fill($request->all());
            $question->save();
            return statusResponse(200,"Cập nhật thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        if ($request->user()->can('question-delete')) {
            Question::destroy( $id);
            return statusResponse(200,"Xóa câu hỏi thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }
}
