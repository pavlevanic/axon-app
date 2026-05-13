<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['name', 'desc', 'created_by', 'updated_by'])]

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */

    use HasFactory;
    protected $fillable=['name', 'slug', 'attribute_names','desc', 'created_by', 'updated_by','attribute_groups','image'];

    protected $casts = [
        'attribute_names' => 'array',
        'attribute_groups' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    public function author() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function products(){
    return $this->hasMany(Product::class);
    }

}
