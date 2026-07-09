<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reference\SalleRequest;
use App\Models\Salle;
use Illuminate\Http\JsonResponse;

class SalleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['salles' => Salle::orderBy('numero')->get()]);
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
        $salle->delete();

        return response()->json(['message' => 'Salle supprimée avec succès.']);
    }
}