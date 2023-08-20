<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Imports\ExamImport;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
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
        Exam::destroy($exam->id);

        return response()->json([
            'statusCode' => 200,
            'message' => 'Xóa bài kiểm tra thành công!'
        ], 200);
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
        Excel::import(new ExamImport($request->examId), $request->file('file'));

        return response()->json([
            'statusCode' => 200,
            'message' => 'Import thành công!'
        ], 200); 
    }

    
}
