<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'paiements';
    protected $primaryKey = 'id_paiement';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'montant',
        'methode',
        'numero_telephone',
        'reference_transaction',
        'statut',
        'date_paiement',
        'id_projet',
    ];

    protected function casts(): array
    {
        return [
            'date_paiement' => 'datetime',
            'montant' => 'decimal:2',
        ];
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'id_projet', 'id_projet');
    }
}