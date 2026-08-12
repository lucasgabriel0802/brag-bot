<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brag extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'context',
        'impact',
        'technologies',
    ];

    protected $casts = [
        'technologies' => 'array',
    ];
}
