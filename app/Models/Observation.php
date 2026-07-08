<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'observations';
    protected $primaryKey = 'id_observation';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'contenu',
        'date',
        'id_utilisateur',
        'id_projet',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function auteur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'id_projet', 'id_projet');
    }
}