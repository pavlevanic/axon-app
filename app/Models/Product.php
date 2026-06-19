<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'name', 'slug', 'short_desc', 'desc', 'price', 
        'discount_price', 'stock', 'is_featured', 
        'image', 'category_id', 'created_by', 'updated_by', 
        'specs','is_custom_build', 'build_components'
    ];

    protected $casts = [
        'specs' => 'array',
        'build_components' => 'array',
        'is_custom_build'  => 'boolean',
    ];
    
    public function getRouteKeyName() {
        return 'slug';
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

  

    public function images()  {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

  
    public function author() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function carts()
    {
    return $this->hasMany(Cart::class);
    }
}