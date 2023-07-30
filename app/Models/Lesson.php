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
        'description',
        'link',
        // 'course_id'
    ];

    public $timestamps = false;
    
    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function exams(){
        return $this->hasMany(Exam::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function fileUploads(){
        return $this->hasMany(FileUpload::class);
    }
}
