<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }


    public function boot(): void
    { 
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        
        Paginator::useBootstrapFive();
        view()->composer('*', function ($view) {
            $view->with('setting', Setting::first());
        });
    }
}