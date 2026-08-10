<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index(): JsonResponse
    {
        $parametres = Parametre::all()->pluck('valeur', 'cle');

        return response()->json(['parametres' => $parametres]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'parametres' => ['required', 'array'],
        ]);

        foreach ($validated['parametres'] as $cle => $valeur) {
            Parametre::updateOrCreate(['cle' => $cle], ['valeur' => (string) $valeur]);
        }

        return response()->json(['message' => 'Paramètres enregistrés avec succès.']);
    }
}