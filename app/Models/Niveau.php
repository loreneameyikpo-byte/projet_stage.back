<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'niveaux';
    protected $primaryKey = 'id_niveau';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'libelle',
    ];

    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'id_niveau', 'id_niveau');
    }
}