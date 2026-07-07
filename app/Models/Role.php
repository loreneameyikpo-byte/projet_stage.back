<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';
    protected $primaryKey = 'id_role';

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