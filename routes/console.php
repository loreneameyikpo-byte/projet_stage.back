<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Nettoie les anciennes sauvegardes avant d'en créer une nouvelle,
// pour ne pas cumuler des sauvegardes obsolètes inutilement.
    Schedule::command('backup:clean')->daily()->at('00:30');
 
// Sauvegarde complète (base de données + fichiers) tous les jours à 1h.
    Schedule::command('backup:run')->daily()->at('01:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
