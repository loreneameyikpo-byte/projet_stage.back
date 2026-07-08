<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'permissions';
    protected $primaryKey = 'id_permission';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'libelle',
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_permission',
            'id_permission',
            'id_role'
        );
    }
}