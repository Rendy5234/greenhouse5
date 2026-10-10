<?php

namespace App\Providers;

use App\Models\Configuration;
use App\Models\SensorReading;
use App\Policies\ConfigurationPolicy;
use App\Policies\SensorReadingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        $this->registerPolicies();
    }

    /**
     * Daftarkan policy untuk model yang diperlukan.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Configuration::class, ConfigurationPolicy::class);
        Gate::policy(SensorReading::class, SensorReadingPolicy::class);
    }
}
