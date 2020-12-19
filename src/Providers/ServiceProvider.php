<?php

declare(strict_types=1);

namespace Lifhold\Users\Rest\Providers;

use Lifhold\Users\Contracts\UsersService;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            UsersService::class,
            fn() => new \Lifhold\Users\Rest\Services\UsersService(env("USERS_API_BASE_URL"))
        );
    }
}
