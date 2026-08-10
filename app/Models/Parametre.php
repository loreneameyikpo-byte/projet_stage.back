<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $table = 'parametres';
    protected $primaryKey = 'cle';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['cle', 'valeur'];

    public static function obtenir(string $cle, $defaut = null)
    {
        return static::find($cle)?->valeur ?? $defaut;
    }
}