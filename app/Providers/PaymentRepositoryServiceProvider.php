<?php

namespace App\Providers;

use App\Repositories\PaymentRepository;
use App\Repositories\PaymentRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class PaymentRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
    }
}
