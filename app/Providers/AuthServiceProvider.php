<?php

namespace App\Providers;

use App\Models\DoctorInfo;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // ❌ في Laravel 10/11 مفيش registerPolicies()
        // $this->registerPolicies();

       Gate::define('manage-doctor-services', function ($user, DoctorInfo $doctorInfo) {
    return $user->role === 'admin'
        || $doctorInfo->user_id === $user->id;
});

    }
}
