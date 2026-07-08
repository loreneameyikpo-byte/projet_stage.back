<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VersionProjet extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'version_projets';
    protected $primaryKey = 'id_version';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'numero_version',
        'rapport_pdf',
        'depot_github',
        'date_depot',
        'statut_version',
        'id_projet',
    ];

    protected function casts(): array
    {
        return [
            'date_depot' => 'datetime',
        ];
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'id_projet', 'id_projet');
    }
}