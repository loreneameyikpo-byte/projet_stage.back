<?php

namespace App\Providers;

use App\Models\Projet;
use App\Policies\ProjetPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\ResetPassword;


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
        Gate::policy(Projet::class, ProjetPolicy::class);
        ResetPassword::createUrlUsing(function ($utilisateur, string $token) {
        return config('app.frontend_url', 'http://localhost:3000') . '/reinitialiser-mot-de-passe?token=' . $token . '&email=' . urlencode($utilisateur->email);
    });
    }
}
