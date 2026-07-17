<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tarifs';
    protected $primaryKey = 'id_tarif';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'montant',
        'id_niveau',
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau', 'id_niveau');
    }
}