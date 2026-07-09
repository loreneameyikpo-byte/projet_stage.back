<?php
 
namespace App\Http\Controllers;

use App\Http\Requests\Reference\PromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;

class PromotionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'promotions' => Promotion::with('niveau')->orderByDesc('annee')->get()
                ->map(fn ($p) => [
                    'id' => $p->id_promotion,
                    'annee' => $p->annee,
                    'niveau' => $p->niveau?->libelle,
                    'intitule' => $p->intitule,
                ]),
        ]);
    }

    public function store(PromotionRequest $request): JsonResponse
    {
        $promotion = Promotion::create($request->validated());

        return response()->json(['promotion' => $promotion], 201);
    }

    public function update(PromotionRequest $request, Promotion $promotion): JsonResponse
    {
        $promotion->update($request->validated());

        return response()->json(['promotion' => $promotion->fresh()]);
    }

    public function destroy(Promotion $promotion): JsonResponse
    {
        $promotion->delete();

        return response()->json(['message' => 'Promotion supprimée avec succès.']);
    }
}