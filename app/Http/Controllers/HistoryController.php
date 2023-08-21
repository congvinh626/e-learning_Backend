<?php

namespace App\Http\Controllers;

use App\Http\Requests\HistoryRequest;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\History;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function show(string $id)
    {
        $user_id = Auth::user()->id;
        $history = History::findOrFail($id);
        if($history->user_id != $user_id){
            return  statusResponse(400, 'Bạn không có quyền truy cập!');
        }

        $showResult = Exam::where('id', $history->exam_id)->first();
        if($showResult->showResult == 0){
            return  statusResponse(400, 'Bạn không có quyền truy cập!');
        }



        $array = collect();

        foreach ($history->history as $item) {
            $question = Question::findOrFail($item['question_id']);
            $question->selected = $item['answer_id'];
            $question->answers;
            $array->push($question);
        }

        return response()->json([
            'statusCode' => 200,
            'data' => [
                "title" => $showResult->title,
                "question" => $array
            ]
        ], 200); 
    }
}
