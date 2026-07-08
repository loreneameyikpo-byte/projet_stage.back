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
        'annee',
        'id_niveau',
    ];

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
            get: fn () => trim("{$this->niveau?->libelle} {$this->annee}")
        );
    }
}