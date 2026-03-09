<?php

namespace App\Providers;

use App\Interfaces\PaymentGateway;
use App\Services\MockPaymentService;
use App\Services\PayDunyaService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, function () {
            return config('paydunya.master_key')
                ? new PayDunyaService()
                : new MockPaymentService();
        });
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
