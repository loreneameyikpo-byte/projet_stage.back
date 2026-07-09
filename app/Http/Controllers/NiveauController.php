<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reference\ReferenceRequest;
use App\Models\Niveau;
use Illuminate\Http\JsonResponse;

class NiveauController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['niveaux' => Niveau::orderBy('libelle')->get()]);
    }

    public function store(ReferenceRequest $request): JsonResponse
    {
        $niveau = Niveau::create($request->validated());

        return response()->json(['niveau' => $niveau], 201);
    }

    public function update(ReferenceRequest $request, Niveau $niveau): JsonResponse
    {
        $niveau->update($request->validated());

        return response()->json(['niveau' => $niveau->fresh()]);
    }

    public function destroy(Niveau $niveau): JsonResponse
    {
        $niveau->delete();

        return response()->json(['message' => 'Niveau supprimé avec succès.']);
    }
}