<?php

namespace App\Providers;

use App\Interfaces\PaymentGateway;
use App\Services\MockPaymentService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, MockPaymentService::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
