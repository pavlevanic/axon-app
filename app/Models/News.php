<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title', 
        'slug', 
        'custom_url',
        'summary', 
        'content', 
        'image', 
        'image_mobile',
        'dark_image',
        'type', 
        'is_active'
    ];
    use HasFactory;
}
