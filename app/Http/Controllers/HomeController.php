<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; 
use App\Models\Category; 
use App\Models\News;

class HomeController extends Controller
{
    
    
    public function index()
    {
        $heroNews = News::where('is_active', 1)
                    ->where('type', 'hero')
                    ->latest()
                    ->get();
        $products = Product::where('is_featured', 1)->take(8)->get();
        $categories = Category::where('id', '!=', 1)->take(4)->get();

        return view('welcome', compact('heroNews','products','categories'));
    }
}