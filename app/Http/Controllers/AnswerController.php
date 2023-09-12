<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
  
    public function store(AnswerRequest $request)
    {
        if ($request->user()->can('answer-create')) {

            $question = new Answer();
            $question->fill($request->all());
            $question->save();

            return statusResponse(200,"Thêm mới thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnswerRequest $request)
    {
        if ($request->user()->can('answer-edit')) {

            $question = Answer::findOrFail($request->id);
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
        if ($request->user()->can('answer-delete')) {
            $question = Answer::find($id);
            Answer::destroy($question->id);
            return statusResponse(200,"Xóa đáp án thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }
}
