<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Presentation extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'presentations';
    protected $primaryKey = 'id_presentation';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date_presentation',
        'heure_presentation',
        'libelle',
        'note_finale',
        'id_utilisateur',
        'id_projet',
        'id_salle',
    ];

    protected function casts(): array
    {
        return [
            'date_presentation' => 'date',
            'note_finale' => 'decimal:2',
        ];
    }

    public function etudiant()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'id_projet', 'id_projet');
    }

    public function salle()
    {
        return $this->belongsTo(Salle::class, 'id_salle', 'id_salle');
    }

    public function jury()
    {
        return $this->hasOne(Jury::class, 'id_presentation', 'id_presentation');
    }

    /**
     * Configuration du journal d'activité : suit la planification et les
     * modifications de date/heure/salle d'une soutenance.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['date_presentation', 'heure_presentation', 'libelle', 'note_finale', 'id_salle'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('presentation');
    }
}