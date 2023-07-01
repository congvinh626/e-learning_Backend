<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'avatar',
        'title',
        'slug',
        'description',
        'code',
        'status'
    ];

    public function users(){
        return $this->belongsToMany(User::class, 'course_user');
        // return $this->belongsToMany(Category::class, 'categoriy_post')->withPivot('value');
    }

    public function lessons(){
        return $this->hasMany(Lesson::class);
        // return $this->belongsToMany(Category::class, 'categoriy_post')->withPivot('value');
    }
}
