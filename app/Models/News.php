<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title', 
        'slug', 
        'summary', 
        'content', 
        'image', 
        'image_mobile',
        'type', 
        'is_active'
    ];
    use HasFactory;
}
