<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant' => \App\Http\Middleware\IdentifyTenant::class,
            'tenant.public' => \App\Http\Middleware\IdentifySchoolByDomain::class,
            'super_admin' => \App\Http\Middleware\CheckSuperAdmin::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'module' => \App\Http\Middleware\VerifyModuleEnabled::class,
            'api.key' => \App\Http\Middleware\AuthenticateApiKey::class,
            'website.unlocked' => \App\Http\Middleware\EnsureWebsiteIsUnlocked::class,
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\VerifySessionHardening::class,
        ]);

        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'logout',
            'super-admin/logout', // In case platform admin logout is used
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
