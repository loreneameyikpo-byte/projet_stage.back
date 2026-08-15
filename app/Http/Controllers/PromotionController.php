<?php
 
namespace App\Http\Controllers;

use App\Http\Requests\Reference\PromotionRequest;
use App\Models\Promotion;
use App\Models\Niveau;
use Illuminate\Http\JsonResponse;

class PromotionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'promotions' => Promotion::with('niveau')->orderByDesc('annee_debut')->get()
                ->map(fn ($p) => [
                    'id' => $p->id_promotion,
                    'libelle' => $p->libelle,
                    'annee_debut' => $p->annee_debut,
                    'annee_fin' => $p->annee_fin,
                    'niveau' => $p->niveau?->libelle,
                    'id_niveau' => $p->id_niveau,
                    'intitule' => $p->intitule,
                    'nb_etudiants' => $p->utilisateurs()->count(),
                ]),
                'niveaux' => Niveau::orderBy('libelle')->get()
                    ->map(fn ($n) => [
                        'id_niveau' => $n->id_niveau,
                        'libelle' => $n->libelle,
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
        $nbEtudiants = $promotion->utilisateurs()->count();

        if ($nbEtudiants > 0) {
            return response()->json([
                'message' => "Impossible de supprimer cette promotion : elle est utilisée par {$nbEtudiants} étudiant(s).",
            ], 422);
        }
        $promotion->delete();

        return response()->json(['message' => 'Promotion supprimée avec succès.']);
    }
}