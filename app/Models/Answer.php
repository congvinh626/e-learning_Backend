<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $table = 'answers';

    protected $fillable = [
        'title',
        'result',
        'question_id',
        'thumbnail'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    
    public function question(){
        return $this->belongsTo(Question::class);
    }

    // public function fileUpload(){
    //     return $this->belongsTo(fileUpload::class);
    // }

}
