<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialite extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'specialites';
    protected $primaryKey = 'id_specialite';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'libelle',
    ];

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'id_specialite', 'id_specialite');
    }
}