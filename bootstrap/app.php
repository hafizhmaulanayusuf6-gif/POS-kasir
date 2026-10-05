<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        /* Middleware ini berjalan di setiap request halaman. 
        Karyawan yang dinonaktifkan saat sedang login akan otomatis keluar pada klik berikutnya, 
        termasuk jika ia memakai "Remember me".*/
        $middleware->web(append: [                                   
            \App\Http\Middleware\EnsureUserIsActive::class,          
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
