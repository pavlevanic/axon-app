<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    Schema::defaultStringLength(191);
    Paginator::useBootstrapFive();

    view()->composer('layouts.app', function ($view) {
        $view->with('regularNews', \App\Models\News::where('type', 'normal')
            ->where('is_active', 1)
            ->latest()
            ->take(5)
            ->get());
    });
    
    if (app()->environment('production')) {
        URL::forceRootUrl(config('app.url'));
        URL::forceScheme('https');
    }
}
}
