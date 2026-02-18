<?php

namespace App\Modules\Request;

use App\Modules\Request\Repositories\RequestRepository;
use App\Modules\Request\Repositories\RequestRepositoryInterface;
use App\Modules\Request\Services\RequestService;
use Illuminate\Support\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RequestRepositoryInterface::class, RequestRepository::class);
        $this->app->singleton(RequestService::class, function ($app) {
            return new RequestService($app->make(RequestRepositoryInterface::class));
        });
    }

    public function boot(): void
    {
        //
    }
}
