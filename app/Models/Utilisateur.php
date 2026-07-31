<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    use HasFactory, HasApiTokens, HasUuids;

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
        'contacts',
        'adresse',
        'id_role',
        'id_promotion',
        'id_filiere',
        'id_specialite',
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
        )->withPivot('role_jury', 'note_saisie');
    }

    public function hasPermission(string $code): bool
    {
        return $this->role?->permissions()->where('code', $code)->exists() ?? false;
    }
}