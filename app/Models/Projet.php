<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $table = 'projet';
    protected $primaryKey = 'id_projet';

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
}