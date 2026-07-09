<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reference\ReferenceRequest;
use App\Models\Filiere;
use Illuminate\Http\JsonResponse;

class FiliereController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['filieres' => Filiere::orderBy('libelle')->get()]);
    }

    public function store(ReferenceRequest $request): JsonResponse
    {
        $filiere = Filiere::create($request->validated());

        return response()->json(['filiere' => $filiere], 201);
    }

    public function update(ReferenceRequest $request, Filiere $filiere): JsonResponse
    {
        $filiere->update($request->validated());

        return response()->json(['filiere' => $filiere->fresh()]);
    }

    public function destroy(Filiere $filiere): JsonResponse
    {
        $filiere->delete();

        return response()->json(['message' => 'Filière supprimée avec succès.']);
    }
}