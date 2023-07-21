<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Models\Course;
use App\Models\FileUpload;
use App\Models\Lesson;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(string $slug)
    {
        $course = Course::where('slug', $slug)->first();
        $course->lessons;
        return $course;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $lesson = new Lesson();
        $lesson->fill($request->all());
        $lesson->save();

        if ($request->hasfile('file')) {
            $listFile =  $this->imageService->fileUpload($request->file()['file'], 'public/images/lessons');
            foreach ($listFile as $file) {
                $fileUpload = new FileUpload();
                $fileUpload->name = $file->name;
                $fileUpload->type = $file->type;
                $fileUpload->lesson_id = $lesson->id;
                $fileUpload->file_path = "public/images/lessons/" . $fileUpload->name;
                $fileUpload->name_table = "lessons";
                $fileUpload->save();
            }
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
        $lesson = Lesson::where('slug', $slug)->first();
        $file_uploads = $lesson->fileUploads;

        if ($file_uploads) {
            $this->imageService->removeFileInStorage($file_uploads);
        }

        Lesson::destroy($lesson->id);

        return response()->json([
            'statusCode' => 200,
            'message' => 'Xóa bài học thành công!'
        ], 200);
    }

    public function fileUpload(Request $request)
    {
        // $files = $request->file()['file'];
        $image =  $this->imageService->fileUpload($request->file()['file'], 'public/images/lessons');
        return $image;
    }
}
