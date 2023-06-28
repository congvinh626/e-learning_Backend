<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = [
        'title',
        'level',
        'exam_id',
        'fileUpload_id'
    ];

    public function exam(){
        return $this->belongsTo(Exam::class);
    }

    public function fileUpload(){
        return $this->belongsTo(FileUpload::class);
    }

    public function answer(){
        return $this->hasMany(Answer::class);
    }

}
