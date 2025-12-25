<?php

namespace App\Providers;

use App\Domain\Customer\CustomerRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\CustomerRepository;
use App\Infrastructure\Persistence\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(
            CustomerRepositoryInterface::class,
            CustomerRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
