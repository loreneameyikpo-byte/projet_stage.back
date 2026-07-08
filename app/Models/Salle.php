<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'salles';
    protected $primaryKey = 'id_salle';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'numero',
        'libelle',
    ];

    public function presentations()
    {
        return $this->hasMany(Presentation::class, 'id_salle', 'id_salle');
    }
}