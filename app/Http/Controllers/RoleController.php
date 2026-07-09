<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Liste tous les rôles disponibles, pour peupler les formulaires
     * de création d'utilisateur côté frontend.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'roles' => Role::orderBy('libelle')->get(['id_role', 'libelle']),
        ]);
    }
}