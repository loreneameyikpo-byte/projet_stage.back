<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'filieres';
    protected $primaryKey = 'id_filiere';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'libelle',
    ];

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'id_filiere', 'id_filiere');
    }
}