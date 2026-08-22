<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Utilisateur extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, HasApiTokens, HasUuids, CanResetPassword, Notifiable, LogsActivity;

    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_utilisateur';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'actif',
        'mot_de_passe_a_changer',
        'contacts',
        'adresse',
        'id_role',
        'id_promotion',
        'id_filiere',
        'id_specialite',
        'cree_par',
    ];
    

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'actif' => 'boolean',
            'mot_de_passe_a_changer' => 'boolean',

        ];
    }

    // Laravel cherche par défaut une colonne "password" pour l'authentification ;
    // notre colonne s'appelle "mot_de_passe", il faut donc l'indiquer explicitement.
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'id_promotion', 'id_promotion');
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'id_filiere', 'id_filiere');
    }

    public function specialite()
    {
        return $this->belongsTo(Specialite::class, 'id_specialite', 'id_specialite');
    }

    // Projets soumis par cet utilisateur en tant qu'étudiant
    public function projets()
    {
        return $this->hasMany(Projet::class, 'id_utilisateur', 'id_utilisateur');
    }

    // Projets encadrés par cet utilisateur en tant qu'encadreur
    public function projetsEncadres()
    {
        return $this->hasMany(Projet::class, 'id_encadreur', 'id_utilisateur');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class, 'id_utilisateur', 'id_utilisateur');
    }

    // Présentation à laquelle cet utilisateur est associé en tant qu'étudiant
    public function presentation()
    {
        return $this->hasOne(Presentation::class, 'id_utilisateur', 'id_utilisateur');
    }

    // Jurys auxquels cet utilisateur participe (encadreur interne ou jury externe)
    public function jurys()
    {
        return $this->belongsToMany(
            Jury::class,
            'jury_utilisateur',
            'id_utilisateur',
            'id_jury'
        )->withPivot('role_jury', 'note_saisie', 'commentaire');
    }

    // L'administrateur qui a créé ce compte (null pour les comptes créés
    // autrement, ex. le tout premier super admin).
    public function createur()
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par', 'id_utilisateur');
    }

    // Tous les comptes que CET utilisateur (généralement un admin) a créés.
    public function comptesCrees()
    {
        return $this->hasMany(Utilisateur::class, 'cree_par', 'id_utilisateur');
    }

    public function hasPermission(string $code): bool
    {
        return $this->role?->permissions()->where('code', $code)->exists() ?? false;
    }

    /**
     * Configuration du journal d'activité (spatie/laravel-activitylog) :
     * seuls les champs listés sont suivis, et seulement quand ils changent
     * réellement (logOnlyDirty), pour éviter de polluer l'historique.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom', 'prenom', 'email', 'actif', 'id_role', 'id_promotion', 'id_filiere', 'id_specialite'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('utilisateur');
    }
}