<?php

namespace App\Models;

use App\Mail\NotificationChangementStatut;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Projet extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'projets';
    protected $primaryKey = 'id_projet';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'titre',
        'description',
        'statut',
        'id_utilisateur',
        'id_encadreur',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function encadreur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_encadreur', 'id_utilisateur');
    }

    public function versions()
    {
        return $this->hasMany(VersionProjet::class, 'id_projet', 'id_projet')
            ->orderByDesc('numero_version');
    }

    public function derniereVersion()
    {
        return $this->hasOne(VersionProjet::class, 'id_projet', 'id_projet')
            ->latestOfMany('numero_version');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class, 'id_projet', 'id_projet');
    }

    public function presentation()
    {
        return $this->hasOne(Presentation::class, 'id_projet', 'id_projet');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'id_projet', 'id_projet');
    }

    public function paiementReussi()
    {
        return $this->hasOne(Paiement::class, 'id_projet', 'id_projet')
            ->where('statut', 'reussi')
            ->latestOfMany();
    }
    protected static function booted(): void
{
    static::updated(function (Projet $projet) {
        if ($projet->wasChanged('statut')) {
            $projet->loadMissing('etudiant');

            if ($projet->etudiant) {
                Mail::to($projet->etudiant->email)->send(
                    new NotificationChangementStatut(
                        $projet,
                        $projet->getOriginal('statut'),
                        $projet->statut
                    )
                );
            }
        }
    });
}

    /**
     * Configuration du journal d'activité : suit les changements de titre,
     * description, statut et affectation d'encadreur.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['titre', 'description', 'statut', 'id_encadreur'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('projet');
    }
}