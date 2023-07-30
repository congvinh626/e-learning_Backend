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
        'slug',
        'numberOfQuestion',
        'time',
        'startTime',
        'endTime',
        'classify',
        'showResult',
        'lesson_id'
    ];

    protected $casts = [
        'classify' => 'array',
        'startTime' => 'datetime',
        'endTime' => 'datetime',
        'showResult' => 'boolean'
   ];

    public function course(){
        return $this->belongsTo(Lesson::class);
    }

    public function histories(){
        return $this->hasMany(Lesson::class);
    }

    public function questions(){
        return $this->hasMany(Question::class);
    }

}
