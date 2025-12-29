<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\DoctorMiddleware;
use App\Http\Middleware\RedirectIfNotAuth;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
  ->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware) {

    // aliases
    $middleware->alias([
        'auth'        => \App\Http\Middleware\RedirectIfNotAuth::class,
        'admin.area'  => \App\Http\Middleware\AdminMiddleware::class,
        'doctor.area' => \App\Http\Middleware\DoctorMiddleware::class,
        'patient.area' => \App\Http\Middleware\PatientMiddleware::class,
        'admin_or_doctor' => \App\Http\Middleware\AdminOrDoctor::class,
    ]);

    // append to web group
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,
    ]);

})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
