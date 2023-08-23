<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Permission;
use App\Models\Question;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      
        $developer_create = new User();
        $developer_create->username = 'teacher';
        $developer_create->email = 'teacher@test.com';
        $developer_create->password = Hash::make(123456);
        $developer_create->type = 1;
        $developer_create->save();
       
        $developer_delete = new User();
        $developer_delete->username = 'student';
        $developer_delete->email = 'student@test.com';
        $developer_delete->password = Hash::make(123456);
        $developer_delete->type = 2;
        $developer_delete->save();


        

        

        $Exam = new Exam();
        $Exam->title = 'bai-kiem-tra';
        $Exam->time = 5;
        $Exam->showResult = true;
        $Exam->lesson_id = 1;
        $Exam->slug = 'bai-kiem-tra-12';
        $Exam->save();

        $Question = new Question();
        $Question->title = '2 + 2';
        $Question->exam_id = 1;
        $Question->save();

        $Answer = new Answer();
        $Answer->title = '1';
        $Answer->result = 0;
        $Answer->question_id = 1;
        $Answer->save();

        $Answer = new Answer();
        $Answer->title = '2';
        $Answer->result = 0;
        $Answer->question_id = 1;
        $Answer->save();

        $Answer = new Answer();
        $Answer->title = '4';
        $Answer->result = 1;
        $Answer->question_id = 1;
        $Answer->save();


        $admin_role = new Role();
        $admin_role->slug = 'admin';
        $admin_role->name = 'Front-end Developer';
        $admin_role->save();

        $teacher_role = new Role();
        $teacher_role->slug = 'teacher';
        $teacher_role->name = 'teacher';
        $teacher_role->save();

        $student_role = new Role();
        $student_role->slug = 'student';
        $student_role->name = 'student';
        $student_role->save();

        $createCourse = new Permission();
        $createCourse->slug = 'create-course';
        $createCourse->name = 'Thêm mới bài học';
        $createCourse->save();

        $editCourse = new Permission();
        $editCourse->slug = 'edit-course';
        $editCourse->name = 'Sửa bài học';
        $editCourse->save();

        $deleteCourse = new Permission();
        $deleteCourse->slug = 'delete-course';
        $deleteCourse->name = 'Xóa bài học';
        $deleteCourse->save();
    }
}
