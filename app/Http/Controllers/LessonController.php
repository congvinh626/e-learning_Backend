<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Models\Course;
use App\Models\FileUpload;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserInfo;
use App\Notifications\TestNotification;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use stdClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $course_user = $course->users();
        $idTeacher = $course_user->first()->id;

        $course->nameTeacher = UserInfo::where('user_id', $idTeacher)->first()->name;
        $course->numberOfMember = $course_user->count();

        $course->numberOfMemberWaiting = $course_user->where('confirm', false)->count();
        $lessons = $course->lessons()->get(['id', 'title' , 'slug']);
        $course->numberOfLesson = $lessons->count();

        $now = Carbon::now();

        foreach ($lessons as $lesson) {
            // Lấy ra danh sách exams liên kết với từng lesson
            foreach($lesson->exams as $exam){
                $startTime = Carbon::parse($exam->startTime);
                $endTime = Carbon::parse($exam->endTime);

                // So sánh
                if ($now->lessThan($startTime)) {
                    $exam->checkTime = "Chưa mở";
                } elseif ($now->greaterThan($startTime) && $now->lessThan($endTime)) {
                    $exam->checkTime = "Đã mở";
                } elseif ($now->greaterThan($endTime) ) {
                    $exam->checkTime = "Hết hạn";
                }
            }
            $lesson->exams;
            $file = $lesson->fileUploads()->get(['id', 'name', 'type']);
            $lesson->files = $file;

        }
        $course->lessons = $lessons;
        return $course;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(LessonRequest $request)
    {
        if ($request->user()->can('lesson-create')) {

            $course = Course::where('slug', $request->course_slug)->first();
            $lesson = new Lesson();
            $lesson->fill($request->all());
            $lesson->link = 'https://www.youtube.com/embed/'. basename($request->link);
            $lesson->course_id = $course->id;
            $lesson->save();

            if ($request->hasfile('file')) {
                $username = Auth::user()->username;

                $listFile =  $this->imageService->fileUpload($request->file()['file'], 'public/docs/' . $username);
                foreach ($listFile as $file) {
                    $fileUpload = new FileUpload();
                    $fileUpload->name = $file->name;
                    $fileUpload->type = $file->type;
                    $fileUpload->lesson_id = $lesson->id;
                    $fileUpload->file_path = "public/docs/" . $username . '/' . $fileUpload->name;
                    $fileUpload->name_table = "lessons";
                    $fileUpload->save();
                }
            }
            return statusResponse(200 ,"Thêm mới thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $lesson = Lesson::where('slug', $slug)->first();
        $fileUploads = FileUpload::where('lesson_id', $lesson->id)->get();
        if(count($fileUploads) > 0){
            $username = Auth::user()->username;

            foreach ($fileUploads as $file) {
                $temp = new stdClass;
                $temp->id = $file->id;
                $temp->name = $file->name;
                $temp->path = '/storage/docs/'. $username. '/'. $file->name;
                $files[] = $temp;
            }
            $lesson->files =  $files;
        }
        
        return $lesson;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if ($request->user()->can('lesson-update')) {

            $lesson = Lesson::findOrFail($request->id);
            $lesson->fill($request->all());
            $lesson->save();
            return statusResponse(200 ,"Cập nhật thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $slug)
    {
        if ($request->user()->can('lesson-delete')) {

            $lesson = Lesson::where('slug', $slug)->first();
            $file_uploads = $lesson->fileUploads;

            if ($file_uploads) {
                $this->imageService->removeFileInStorage($file_uploads);
            }

            Lesson::destroy($lesson->id);

            return statusResponse(200 ,"Xóa bài học thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    public function fileUpload(Request $request)
    {
        // $files = $request->file()['file'];
        $image =  $this->imageService->fileUpload($request->file()['file'], 'public/images/lessons');
        return $image;
    }

    

    public function pushNotification(Request $request)
    {
        $user = User::find(1); // id của user mình đã đăng kí ở trên, user này sẻ nhận được thông báo
        $data = $request->only([
            'title',
            'content',
        ]);
        $user->notify(new TestNotification($data));

    }

    public function getNotification()
    {
        $user = Auth::user();
        $notifications = $user->notifications;
        return $notifications;
    }

}
