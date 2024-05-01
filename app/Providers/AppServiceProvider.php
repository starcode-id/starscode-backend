<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
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
        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            if (preg_match('/^data:image\/(\w+);base64,/', $value)) {
                return true;
            }
        });
    }
}
