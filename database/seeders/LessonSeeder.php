<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Lesson = new Lesson();
        $Lesson->title = 'Ngôn ngữ máy';
        $Lesson->slug = 'ngon-ngu-may4';
        $Lesson->description = '';
        $Lesson->link = '';
        $Lesson->course_id = 1;
        $Lesson->save();
    }
}
