<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $table = 'exams';

    protected $fillable = [
        'title',
        'showResult',
        'lesson_id'
    ];

    public function course(){
        return $this->belongsTo(Lesson::class);
    }

    public function history(){
        return $this->hasMany(Lesson::class);
    }

    public function question(){
        return $this->hasMany(Question::class);
    }

}
