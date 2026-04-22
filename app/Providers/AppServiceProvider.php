<?php

namespace App\Providers;

use App\Events\UserCreatedInCentral;
use App\Listeners\ReplicateUserToTenant;
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
        Event::listen(
            UserCreatedInCentral::class,
            ReplicateUserToTenant::class,
        );
    }
}