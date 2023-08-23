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
        $userCourses = $request->user()->courses();
        $userCourses = $userCourses->where('status', $request->status);

        if ($request->searchText) {
            $userCourses = $userCourses->where('title', 'like', "%$request->searchText%");
        }
        $userCourses = $userCourses->paginate($request->pageSize);

        $userCourses->each(function ($course) {

            $course_user = $course->users();
            $idTeacher = $course_user->first()->id;
            $course->nameTeacher = UserInfo::find($idTeacher)->name;
            $course->numberOfMember = $course_user->count();
            $course->numberOfLesson = $course->lessons()->count();
            // $course->rwsst = 1;
            if ($course->avatar) {
                $course->avatar ='/storage/images/course/' . $course->avatar;
            }
        });
        // return request()->server('SERVER_NAME');
        return $userCourses;
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request)
    {
        if ($request->user()->can('create-course')) {
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
        return statusResponse(401,"Bạn không có quyền tạo");

    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $course = Course::where('slug', $slug)->first();
        $course->avatar = '/storage/images/course/' . $course->avatar;

        return $course;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseRequest $request)
    {
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
        $course = Course::where('slug', $slug)->first();
        Course::destroy($course->id);

        return response()->json([
            'statusCode' => 200,
            'message' => 'Xóa khóa học thành công!'
        ], 200);
    }

    public function changeStatus(string $slug)
    {
        $course = Course::where('slug', $slug)->first();

        $course->status = $course->status == 1 ? 0 : 1;
        $course->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Thay đổi trạng thái thành công!'
        ], 200);
    }

    public function register(Request $request)
    {
        $user_id = Auth::user()->id;

        $course = Course::find($request->course_id);
        $course->users()->attach( $user_id, [
            'user_create' => false,
            'confirm' => false
        ]);
        return statusResponse(200, "Đăng ký thành công!");
    }

    public function addMember(Request $request)
    {
        // return 1;
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

    public function removeMember(Request $request)
    {
        $course = Course::findOrFail($request->course_id);
        $course->users()->detach($request->user_id);

        return statusResponse(200, "Xóa thành viên thành công!");

    }

    public function waitConfirmMember(string $id)
    {
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
    
    
}
