<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $table = 'lessons';

    protected $fillable = [
        'title',
        'slug',
        'course_id'
    ];


    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function exam(){
        return $this->hasMany(Exam::class);
    }

    public function comment(){
        return $this->hasMany(Comment::class);
    }

    public function fileUpload(){
        return $this->hasMany(FileUpload::class);
    }
}
