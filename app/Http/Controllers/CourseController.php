<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Models\User;
use App\Models\UserInfo;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $course = DB::table('course_user')->where('user_id', Auth::user()->id);

        if($request->confirm == 'true'){
            $course->where('confirm', true);
        }else{
            $course->where('confirm', false);
        }
        $arrCourse = $course->pluck("course_id");

        $getCourse = Course::whereIn('id', $arrCourse)->where('status', $request->status);
        if ($request->searchText) {
            $getCourse = $getCourse->where('title', 'like', "%$request->searchText%");
        }

        $getCourse = $getCourse->paginate($request->pageSize);
        
        $getCourse->each(function ($course) {

            $course_user = $course->users();
            $idTeacher = $course_user->first()->id;
            $course->nameTeacher = UserInfo::where('user_id', $idTeacher)->first()->name;
            $course->numberOfMember = $course_user->count();
            $course->numberOfLesson = $course->lessons()->count();
            if ($course->avatar) {
                $course->avatar ='/storage/images/course/' . $course->avatar;
            }
        });
        return $getCourse;
    }

    public function suggest(Request $request)
    {
        if($request->courseCode){
            $courseSuggest = Course::where('code', 'like', "$request->courseCode")->get();
        }else{
            $userCourses = $request->user()->courses->pluck('id');
            $courseSuggest = Course::whereNotIn('id', $userCourses);
            if ($request->searchText) {
                $courseSuggest = $courseSuggest->where('title', 'like', "%$request->searchText%");
            }
            $courseSuggest = $courseSuggest->inRandomOrder()->take(10)->get();
        }
       
        $courseSuggest->each(function ($course) {

            $course_user = $course->users();
            $idTeacher = $course_user->first()->id;

            $course->nameTeacher = UserInfo::where('user_id', $idTeacher)->first()->name;

            $course->numberOfMember = $course_user->count();
            $course->numberOfLesson = $course->lessons()->count();
            if ($course->avatar) {
                $course->avatar ='/storage/images/course/' . $course->avatar;
            }
        });
        return $courseSuggest;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request)
    {
        if ($request->user()->can('course-create')) {
            $user = Auth::user()->id;
            $course = new Course();

            $course->fill($request->all());

            if ($request->file()) {
                $image =  $this->imageService->storeImage($request->file('avatar'), 'public/images/course', '', $request->slug);
                $course->avatar = $image;
            }

            $course->save();
            $course->users()->attach($user, [
                'user_create' => true,
                'confirm' => true
            ]);

            return statusResponse(200,"Thêm mới thành công");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");

    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $course = Course::where('slug', $slug)->first();
        $course->avatar = '/storage/images/course/' . $course->avatar;
        $course->numberOfLesson = $course->lessons()->count();
        $course->numberOfMember = $course->users()->count();
        $idTeacher = $course->users()->first()->id;
        $course->nameTeacher = UserInfo::where('user_id', $idTeacher)->first()->name;
        return $course;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseRequest $request)
    {
        if ($request->user()->can('course-edit')) {
            
            $course = Course::findOrFail($request->id);
            // return $course->avatar;
            $course->fill($request->all());

            if ($request->file()) {
                $image =  $this->imageService->storeImage($request->file('avatar'), 'public/images/course', $course->avatar, $request->slug);
                $course->avatar = $image;
            }
            // else{
            //     $course->avatar = $course->avatar;
            // }
            $course->save();
            return statusResponse(200,"Cập nhật thành công!");

        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $slug)
    {
        if ($request->user()->can('course-delete')) {

            $course = Course::where('slug', $slug)->first();
            Course::destroy($course->id);

            return response()->json([
                'statusCode' => 200,
                'message' => 'Xóa khóa học thành công!'
            ], 200);
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    
    }

    public function changeStatus(Request $request, string $slug)
    {
        if ($request->user()->can('course-change-status')) {

            $course = Course::where('slug', $slug)->first();

            $course->status = $course->status == 1 ? 0 : 1;
            $course->save();

            return statusResponse(200, "Thay đổi trạng thái thành công!");

        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    public function register(Request $request, string $id)
    {
        if ($request->user()->can('course-register')) {

            $user_id = Auth::user()->id;

            $course = Course::find($id);
            $course->users()->attach( $user_id, [
                'user_create' => false,
                'confirm' => false
            ]);
            return statusResponse(200, "Đăng ký thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }
    

    public function member(Request $request, string $id)
    {
        $course_user = DB::table('course_user')->where('course_id', $id);
        $users_id = $course_user->where('confirm', true)->pluck('user_id');
        $users = UserInfo::whereIn('user_id', $users_id)->select('id', 'avatar', 'name', 'user_id')->get();
        $teacher_id =  $course_user->where('user_create', true)->first()->user_id;
        return response()->json([
            'teacher_id' => $teacher_id,
            'users' => $users
        ], 200);
    }


    public function addMember(Request $request)
    {
        if ($request->user()->can('course-add-member')) {

            $user_id = Auth::user()->id;
        
        
            $course = Course::findOrFail($request->course_id);

            $userCreate = $course->users->find($user_id)->pivot->user_create;

            if($userCreate == 0){
                return statusResponse(401, "Không có quyền thêm thành viên!");
            }
            
            $course = Course::find($request->course_id);
            $course->users()->updateExistingPivot($request->user_id, ['confirm' => true]);

            return statusResponse(200, "Thêm thành viên thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    public function removeMember(Request $request)
    {
        if ($request->user()->can('course-remove-member')) {

            $course = Course::findOrFail($request->course_id);
            $course->users()->detach($request->user_id);

            return statusResponse(200, "Xóa thành viên thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }

    public function waitConfirmMember(Request $request, string $id)
    {
        if ($request->user()->can('course-wait-confirm-member')) {
            $users = DB::table('course_user')
            ->where('course_id', $id)
            ->where('confirm', false)
            ->join('user_infos', 'course_user.user_id', '=', 'user_infos.user_id')
            ->select('user_infos.user_id', 'user_infos.avatar', 'user_infos.name')
            ->get();
            return response()->json([
                'statusCode' => 200,
                'data' => $users
            ], 200);
        }
        return statusResponse(401,"Bạn không có quyền truy cập");

    }
    
    public function getOff(Request $request, string $id)
    {
        if ($request->user()->can('course-get-off')) {
            $course = Course::findOrFail($id);
            $course->users()->detach(Auth::user()->id);

            return statusResponse(200, "Thoát khóa học thành công!");
        }
        return statusResponse(401,"Bạn không có quyền truy cập");
    }
    
}
