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
        $specialite->delete();

        return response()->json(['message' => 'Spécialité supprimée avec succès.']);
    }
}