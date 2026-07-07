<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jury extends Model
{
    use HasFactory;

    protected $table = 'jury';
    protected $primaryKey = 'id_jury';

    protected $fillable = [
        'id_presentation',
    ];

    public function presentation()
    {
        return $this->belongsTo(Presentation::class, 'id_presentation', 'id_presentation');
    }

    public function membres()
    {
        return $this->belongsToMany(
            Utilisateur::class,
            'jury_utilisateur',
            'id_jury',
            'id_utilisateur'
        )->withPivot('role_jury', 'note_saisie');
    }

    public function toutesLesNotesSaisies(): bool
    {
        return $this->membres()->wherePivotNull('note_saisie')->doesntExist()
            && $this->membres()->exists();
    }

    public function calculerNoteFinale(): ?float
    {
        if (! $this->toutesLesNotesSaisies()) {
            return null;
        }

        return round($this->membres()->avg('jury_utilisateur.note_saisie'), 2);
    }
}