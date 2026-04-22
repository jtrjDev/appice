<?php

namespace App\Providers;

use App\Events\UserCreatedInCentral;
use App\Listeners\ReplicateUserToTenant;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
{
    \Illuminate\Support\Facades\Request::setTrustedProxies(
        ['*'],
        \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
    );

    if (app()->environment('production') || str_contains(config('app.url'), 'github.dev')) {
        URL::forceScheme('https');
        $this->app['url']->forceRootUrl(config('app.url'));
    }

    Event::listen(
        UserCreatedInCentral::class,
        ReplicateUserToTenant::class,
    );
}
}