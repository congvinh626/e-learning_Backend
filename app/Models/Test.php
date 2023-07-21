<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $table = 'test';
    protected $fillable = [
        'tl1',
        'tl2',
        'tl3',
        'tl4',
        'ch',
        'loai'
    ];

    public $timestamps = false;
}
