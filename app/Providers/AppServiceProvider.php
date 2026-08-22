<?php

namespace App\Providers;

use App\Models\Projet;
use App\Policies\ProjetPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Notifications\ResetPassword;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;


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

        // Enregistre le transport "brevo" utilisé par config/mail.php.
        // Sans cet appel, Laravel connaît le nom "brevo" dans mail.php mais
        // ne sait pas comment l'instancier -> erreur "Mailer [brevo] is
        // not defined".
        Mail::extend('brevo', function () {
            return (new BrevoTransportFactory)->create(
                new Dsn(
                    'brevo+api',
                    'default',
                    config('services.brevo.key')
                )
            );
        });
    }
}