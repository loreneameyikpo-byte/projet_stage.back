<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reference\TarifRequest;
use App\Models\Tarif;
use Illuminate\Http\JsonResponse;

class TarifController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['tarifs' => Tarif::with('niveau')->get()]);
    }

    public function store(TarifRequest $request): JsonResponse
    {
        $tarif = Tarif::updateOrCreate(
            ['id_niveau' => $request->validated('id_niveau')],
            $request->validated()
        );

        return response()->json(['tarif' => $tarif], 201);
    }

    public function destroy(Tarif $tarif): JsonResponse
    {
        $tarif->delete();

        return response()->json(['message' => 'Tarif supprimé avec succès.']);
    }
}