<?php

namespace Basit\FastpayPayment\Providers;

use Illuminate\Support\ServiceProvider;

class FastpayServiceProvider extends ServiceProvider
{

    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/fastpay.php' => config_path('fastpay.php'),
        ], ["fastpay"]);
    }
}
