<?php

namespace App\Http\Controllers;
use App\Models\Product;  
use App\Models\Category; 
use App\Models\User;
use App\Models\News;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $productCount = Product::count();
        $userCount = User::count()-1;
        $categoryCount = Category::count();
        
        $products = \App\Models\Product::latest()->take(5)->get()->map(function($item) {
            $item->activity_type = 'proizvod';
            $item->activity_title = "Dodat novi proizvod: <strong>{$item->name}</strong>";
            return $item;
        });
    
        $categories = \App\Models\Category::latest()->take(5)->get()->map(function($item) {
            $item->activity_type = 'kategorija';
            $item->activity_title = "Kreirana nova kategorija: <strong>{$item->name}</strong>";
            return $item;
        });
    
        $news = \App\Models\News::latest()->take(5)->get()->map(function($item) {
            $item->activity_type = 'vest';
            $item->activity_title = "Objavljena nova vest: <strong>{$item->title}</strong>";
            return $item;
        });

        $recentActivities = $products->concat($categories)->concat($news)
            ->sortByDesc('created_at')
            ->take(5);
        return view('admin.index', compact('productCount', 'userCount', 'categoryCount','recentActivities')); 
    }
}
