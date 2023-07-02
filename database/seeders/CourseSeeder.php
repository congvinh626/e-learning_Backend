<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Course = new Course();
        $Course->title = 'Ngôn ngữ máy';
        $Course->slug = 'ngon-ngu-may4';
        $Course->code = 'sdfgsde4';
        $Course->status = 1;
        $Course->save();
    }
}
