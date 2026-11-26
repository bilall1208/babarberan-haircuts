<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['role' => \App\Http\Middleware\RoleMiddleware::class]);
    $middleware->redirectGuestsTo(fn () => route('login'));
    $middleware->redirectUsersTo(fn () => Auth::user()?->isAdmin()
        ? route('admin.dashboard')
        : route('admin.transaksi.index'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, \Illuminate\Http\Request $request) {
        if ($response->getStatusCode() === 419) {
            if ($request->is('admin/login')) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Halaman kedaluwarsa. Silakan masuk lagi.']);
            }

            return redirect()->back()->with('error', 'Sesi halaman habis. Muat ulang halaman lalu coba lagi.');
        }

        return $response;
    });
    })->create();
