<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }


    public function boot(): void
    { 
        Paginator::useBootstrapFive();
        view()->composer('*', function ($view) {
        $view->with('setting', Setting::first());
    });
    }
}
