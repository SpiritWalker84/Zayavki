<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    protected array $modules = [
        'Auth',
        'User',
        'Request',
    ];

    public function register(): void
    {
        foreach ($this->modules as $name) {
            $class = "App\\Modules\\{$name}\\{$name}ServiceProvider";
            if (class_exists($class)) {
                $this->app->register($class);
            }
        }
    }

    public function boot(): void
    {
        foreach ($this->modules as $name) {
            $routesPath = app_path("Modules/{$name}/Routes");
            if (is_dir($routesPath)) {
                Route::middleware('web')->group(function () use ($routesPath) {
                    foreach (glob($routesPath . '/*.php') ?: [] as $file) {
                        require $file;
                    }
                });
            }
        }
    }
}
