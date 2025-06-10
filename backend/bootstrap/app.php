<?php

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Http\Middleware\Role;

$app = new Application($_ENV['APP_BASE_PATH'] ?? dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
*/

$app->singleton(
    Kernel::class,
    \App\Http\Kernel::class
);

$app->singleton(
    ConsoleKernel::class,
    \App\Console\Kernel::class
);

$app->singleton(
    ExceptionHandler::class,
    \App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Basic Bindings and Service Providers
|--------------------------------------------------------------------------
*/

$app->singleton('files', \Illuminate\Filesystem\Filesystem::class);
$app->singleton('filesystem', \Illuminate\Filesystem\FilesystemManager::class);

$app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(\Illuminate\Auth\AuthServiceProvider::class);
$app->register(\Illuminate\Broadcasting\BroadcastServiceProvider::class);
$app->register(\App\Providers\AppServiceProvider::class);
$app->register(\App\Providers\BroadcastServiceProvider::class);
$app->register(\App\Providers\RouteServiceProvider::class);

// Register role middleware alias
$app['router']->aliasMiddleware('role', Role::class);

return $app;
