<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user()->id;
        $course = new Course();

        $course->fill($request->all());

        if ($request->file()) {
            $image =  $this->imageService->storeImage($request->file('avatar'), 'public/images/course', '', $request->slug);
            $course->avatar = $image;
        }

        $course->save();
        $course->users()->attach($user);

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

        $image =  $this->imageService->storeImage($request->file('avatar'), 'public/images/course', $course->avatar, $request->slug);

        $course->avatar = $image;
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
}
