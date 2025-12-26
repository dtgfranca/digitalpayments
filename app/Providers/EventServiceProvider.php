<?php

namespace App\Providers;

use App\Events\MoneyTransferred;
use App\Listeners\SendPaymentNotification;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MoneyTransferred::class => [SendPaymentNotification::class],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
