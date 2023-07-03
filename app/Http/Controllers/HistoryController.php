<?php

namespace App\Http\Controllers;

use App\Http\Requests\HistoryRequest;
use App\Models\Answer;
use App\Models\History;
use App\Models\Question;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function show(string $id)
    {
        $exam = History::find($id);
        $arrHistory = array();
       
        foreach ($exam->history as $item) {
            $question = Question::where('id', $item['question_id']);
            $question = $question->with('answers')->get()
            ->map(function ($question) use ($item) {
                $answers = $question->answers->map(function ($answer) use ($item) {
                    $answer->checked = $answer->id == $item['answer_id'];
                    return $answer;
                });
                $question->answers = $answers;
                return $question;
            })->toArray();
           
            $arrHistory = array_merge($arrHistory, $question);
        }
       
        return $arrHistory;
    }

    public function store(HistoryRequest $request)
    {
        // return 12312;
        $exam = new History();
        $exam->fill($request->all());
        $exam->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Thêm mới thành công!'
        ], 200);
    }
}
