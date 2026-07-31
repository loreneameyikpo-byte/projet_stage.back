<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reference\SalleRequest;
use App\Models\Salle;
use Illuminate\Http\JsonResponse;

class SalleController extends Controller
{
    public function index(): JsonResponse
    {
        $salles = Salle::orderBy('numero')->get();

        return response()->json([
            'salles' => $salles,
            'stats' => [
                'total' => $salles->count(),
                'places_totales' => $salles->sum('capacite'),
                'moyenne_places' => $salles->count() > 0 ? round($salles->avg('capacite')) : 0,
            ],
        ]);
    }

    public function store(SalleRequest $request): JsonResponse
    {
        $salle = Salle::create($request->validated());

        return response()->json(['salle' => $salle], 201);
    }

    public function update(SalleRequest $request, Salle $salle): JsonResponse
    {
        $salle->update($request->validated());

        return response()->json(['salle' => $salle->fresh()]);
    }

    public function destroy(Salle $salle): JsonResponse
    {
    $nbPresentations = $salle->presentations()->count();

        if ($nbPresentations > 0) {
            return response()->json([
                'message' => "Impossible de supprimer cette salle : elle est utilisée par {$nbPresentations} présentation(s).",
            ], 422);
        }

        $salle->delete();

        return response()->json(['message' => 'Salle supprimée avec succès.']);
    }
}