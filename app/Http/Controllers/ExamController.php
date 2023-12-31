<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Imports\ExamImport;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\History;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
        if ($request->user()->can('exam-create')) {

            $exam = new Exam();
            $exam->fill($request->all());
            if($request->classify){
                $exam->classify = explode(",", $request->classify);
            }
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                $validExtensions = ['xlsx', 'xls'];
        
                if (!in_array($extension, $validExtensions)) {
                    return response()->json([
                        'statusCode' => 400,
                        'message' => 'Tệp không đúng định dạng Excel.'
                    ], 400);
                }
        
                $exam->save();
                Excel::import(new ExamImport($exam->id), $file);
            }
            else if($request->importQuestion){
                $exam->save();

                $jsonlistQuestion = json_decode($request->importQuestion);
                foreach($jsonlistQuestion as $questionItem) {
                    $question = new Question([
                        'title' => $questionItem->question,
                        'level' => $questionItem->type,
                        'exam_id' => $exam->id,
                        // 'file_upload_id' => $row[5]
                    ]);
                    $question->save();

                    foreach($questionItem->answers as $answerItem) {
                        $answer = new Answer([
                            'title' => $answerItem->title,
                            'result' => $answerItem->correct,
                            'question_id' => $question->id,
                        ]);
                        $answer->save();
                    }
                }
            } else {
                $exam->save();
            }
            return statusResponse(200,"Thêm mới thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $user_id = Auth::user()->id;
        $exam = Exam::where('slug', $slug)->first();
        $history = History::where('exam_id', $exam->id)->where('user_id', $user_id)->count();
        $exam->history = $history;
        return $exam;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExamRequest $request)
    {
        if ($request->user()->can('exam-update')) {

            $exam = Exam::findOrFail($request->id);
            $exam->fill($request->all());
            $exam->save();
            
            return statusResponse(200,"Cập nhật thành công!");

        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $slug)
    {
        if ($request->user()->can('exam-delete')) {
            $exam = Exam::where('slug', $slug)->first();
            Exam::destroy($exam->id);
            return statusResponse(200,"Cập nhật thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    public function getExam(string $slug)
    {
        $exam = Exam::where('slug', $slug)->first();
        $classify = $exam->classify;
        $arr = [];

        // lấy ra danh sách câu hỏi và câu trả lời
        if ($classify) {
            for ($i = 0; $i < count($classify); $i++) {
                $questionsWithAnswers = Question::where('exam_id', $exam->id)->where('level', $i + 1)
                    ->with(['answers' => function ($query) {
                        $query->select('id');
                        $query->inRandomOrder();
                    }])->take($classify[$i])->get()->toArray();

                $arr = array_merge($arr, $questionsWithAnswers);
                shuffle($arr);
            }
        } else {
            $questionsWithAnswers = Question::where('exam_id', $exam->id)
                ->with(['answers' => function ($query) {
                    $query->select('id','title', 'question_id')->inRandomOrder();
                }])->inRandomOrder();
            if ($exam->numberOfQuestion) {
                $arr = $questionsWithAnswers->take($exam->numberOfQuestion)->get();
            }else{
                $arr = $questionsWithAnswers->get();
            }
        }

        $exam->question = $arr;

        return $exam;
    }

    public function importExam(Request $request){
        if ($request->user()->can('exam-import-excel')) {
            Excel::import(new ExamImport($request->examId), $request->file('file'));
            return statusResponse(200,"Import thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    public function uploadExam(Request $request){
        if ($request->user()->can('exam-upload')) {

            $convRequest = collect($request->listItem);

            // lấy danh sách các answer_id của request
            $filtered = $convRequest->pluck('answer_id')->filter(); 
            $ids = $filtered->values()->all();

            // lấy ra các result của những id trên
            $resultOfAnswer = Answer::whereIn('id', $ids)->pluck('result');
            $numberOfCorrectAnswers = $resultOfAnswer->sum();
            $sumRequest = count($convRequest);
            $scores = round(10 / $sumRequest * $numberOfCorrectAnswers, 2);

            $exam = Exam::where('slug', $request->exam_slug)->first();

            $history = new History();
            $history->history = $request->listItem;
            $history->scores = $scores;
            $history->exam_id = $exam->id;
            $history->user_id = Auth::user()->id;
            $history->save();

            return response()->json([
                'statusCode' => 200,
                'data' => [
                    'history' => $history->id,
                    'numberOfCorrectAnswers' => $numberOfCorrectAnswers.' / '.$sumRequest,
                    'scores' => $scores,
                    'showResult' => $exam->showResult
                ]
            ], 200); 
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }
}
