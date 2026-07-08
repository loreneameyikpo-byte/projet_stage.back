<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'roles';
    protected $primaryKey = 'id_role';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'libelle',
    ];

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'id_role', 'id_role');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permission',
            'id_role',
            'id_permission'
        );
    }
}