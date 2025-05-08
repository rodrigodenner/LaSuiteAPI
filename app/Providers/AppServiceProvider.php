<?php

namespace App\Providers;

use App\Payments\Contracts\PaymentProcessorInterface;
use App\Payments\Payment;
use Illuminate\Support\ServiceProvider;
use App\Payments\Contracts\PaymentInterface;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
      $this->app->bind(PaymentInterface::class, Payment::class);

      $this->app->bind(
        PaymentProcessorInterface::class,
        Config::get('payment.current_processor')
      );
    }
}
