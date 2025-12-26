<?php

namespace App\Providers;

use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Transfer\AuthorizerInterface;
use App\Domain\Transfer\NotifyerInterface;
use App\Domain\Transfer\TransactionMangerInterface;
use App\Domain\Transfer\TransferRepositoryInterface;
use App\Infrastructure\Authorizer\AuthorizerAdapter;
use App\Infrastructure\Notifier\NotifierAdapter;
use App\Infrastructure\Persistence\Eloquent\CustomerRepository;
use App\Infrastructure\Persistence\Eloquent\TransferRepository;
use App\Infrastructure\Persistence\Eloquent\UserRepository;
use App\Infrastructure\Persistence\LaravelManagerTransaction;
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
        $this->app->bind(
            AuthorizerInterface::class,
            AuthorizerAdapter::class
        );
        $this->app->bind(
            NotifyerInterface::class,
            NotifierAdapter::class
        );
        $this->app->bind(
            TransferRepositoryInterface::class,
            TransferRepository::class
        );
        $this->app->bind(
            TransactionMangerInterface::class,
            LaravelManagerTransaction::class
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
