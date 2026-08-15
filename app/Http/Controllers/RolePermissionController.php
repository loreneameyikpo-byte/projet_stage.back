<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    /**
     * Matrice complète : toutes les permissions, avec pour chaque rôle de
     * la plateforme la liste des codes activés.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::orderBy('id_permission')->get(['id_permission', 'code', 'libelle', 'description']);

        $rolesGeres = Role::with('permissions:id_permission')->get();

        $matrice = $rolesGeres->mapWithKeys(fn ($role) => [
            $role->libelle => $role->permissions->pluck('id_permission'),
        ]);

        return response()->json([
            'permissions' => $permissions,
            'roles' => $rolesGeres->map(fn ($r) => ['id' => $r->id_role, 'libelle' => $r->libelle]),
            'matrice' => $matrice,
        ]);
    }

    /**
     * Enregistre la liste des permissions activées pour un rôle donné.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['uuid', 'exists:permissions,id_permission'],
        ]);

        $role->permissions()->sync($validated['permission_ids']);

        return response()->json([
            'message' => 'Permissions mises à jour avec succès.',
            'nb_permissions' => count($validated['permission_ids']),
        ]);
    }
}