<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\AdminController;
use App\Models\Product; 
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PCBuilderController;
use App\Http\Controllers\BuilderProductController;

Auth::routes();
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/komponente', [ShopController::class, 'components'])->name('shop.components');
Route::get('/gotove-konfiguracije', [ShopController::class, 'prebuilts'])->name('shop.prebuilts');
Route::get('/pc-builder', [PCBuilderController::class, 'index'])->name('builder.index');
Route::get('/api/builder/components/{type}', [PCBuilderController::class, 'getComponents'])->name('builder.components');


Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/pc-builder/add-to-cart', [PcBuilderController::class, 'addBuildToCart'])->name('builder.add-to-cart');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::resource('category', CategoryController::class);

    
    Route::get('/admin/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/admin/news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/admin/news', [NewsController::class, 'store'])->name('news.store');
    Route::get('/admin/news/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/admin/news/{id}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/admin/news/{id}', [NewsController::class, 'destroy'])->name('news.destroy');
    Route::delete('/admin/product-image/{id}', [ProductController::class, 'destroyImage'])->name('product.image.destroy');
    Route::post('/news/upload-image', [NewsController::class, 'uploadImage'])->name('news.upload.image');
    
    
    Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::resource('product', ProductController::class)->except(['show']);
    Route::resource('builder-products', BuilderProductController::class)->except(['show']);
});

Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');