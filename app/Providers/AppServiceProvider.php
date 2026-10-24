<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        Gate::define('manage-products', fn (User $user): bool => $user->is_admin);

        RateLimiter::for('auth', function (Request $request): Limit {
            $inputEmail = $request->input('email');
            $email = is_string($inputEmail) ? Str::lower(trim($inputEmail)) : '';

            return Limit::perMinute(10)->by($email.'|'.$request->ip());
        });
    }
}
