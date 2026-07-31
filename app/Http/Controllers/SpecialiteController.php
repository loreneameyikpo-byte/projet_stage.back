<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reference\ReferenceRequest;
use App\Models\Specialite;
use Illuminate\Http\JsonResponse;

class SpecialiteController extends Controller
{
    public function index():JsonResponse
    {
        return response()->json(['specialites' => Specialite::orderBy('libelle')->get()]);
    }

    public function store(ReferenceRequest $request): JsonResponse
    {
        $specialite = Specialite::create($request->validated());

        return response()->json(['specialite' => $specialite], 201);
    }

    public function update(ReferenceRequest $request, Specialite $specialite): JsonResponse
    {
        $specialite->update($request->validated());

        return response()->json(['specialite' => $specialite->fresh()]);
    }

    public function destroy(Specialite $specialite): JsonResponse
    {
        $nbUtilisateurs = $specialite->utilisateurs()->count();

        if ($nbUtilisateurs > 0) {
            return response()->json([
                'message' => "Impossible de supprimer cette spécialité : elle est utilisée par {$nbUtilisateurs} utilisateur(s).",
            ], 422);
        }
        $specialite->delete();

        return response()->json(['message' => 'Spécialité supprimée avec succès.']);
    }
}