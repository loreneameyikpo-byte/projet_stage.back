<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'promotions';
    protected $primaryKey = 'id_promotion';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'libelle',
        'annee_debut',
        'annee_fin',
        'id_niveau',
    ];

    protected function casts(): array
    {
        return [
            'annee_debut' => 'integer',
            'annee_fin' => 'integer',
        ];
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'id_promotion', 'id_promotion');
    }

    protected function intitule(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->niveau?->libelle} {$this->libelle}")
        );
    }

    /**
     * Durée de la promotion en années (simple soustraction, puisqu'on
     * travaille désormais avec des années entières et non des dates).
     */
    protected function dureeAnnees(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->annee_debut !== null && $this->annee_fin !== null)
                ? $this->annee_fin - $this->annee_debut
                : null
        );
    }
}