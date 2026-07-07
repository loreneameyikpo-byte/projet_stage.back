<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    use HasFactory;

    protected $table = 'niveau';
    protected $primaryKey = 'id_niveau';

    protected $fillable = [
        'libelle',
    ];

    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'id_niveau', 'id_niveau');
    }
}