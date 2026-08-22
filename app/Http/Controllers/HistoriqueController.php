<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class HistoriqueController extends Controller
{
    /**
     * Liste paginée de l'historique d'activité de toute la plateforme,
     * la plus récente en premier. Réservé au super administrateur.
     *
     * Filtres optionnels via query string :
     *   ?type=utilisateur|projet|presentation  (log_name)
     *   ?recherche=texte                        (dans la description)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Activity::with('causer')->latest();

        if ($request->filled('type')) {
            $query->where('log_name', $request->query('type'));
        }

        if ($request->filled('recherche')) {
            $query->where('description', 'like', '%'.$request->query('recherche').'%');
        }

        $activites = $query->paginate(20);

        return response()->json([
            'historique' => collect($activites->items())->map(fn ($a) => [
                'id' => $a->id,
                'type' => $a->log_name,
                'description' => $a->description,
                'sujet_type' => class_basename($a->subject_type ?? ''),
                'sujet_id' => $a->subject_id,
                'auteur' => $a->causer ? trim("{$a->causer->prenom} {$a->causer->nom}") : 'Système',
                'modifications' => $a->properties,
                'date' => $a->created_at->format('Y-m-d H:i:s'),
            ]),
            'pagination' => [
                'page_actuelle' => $activites->currentPage(),
                'nb_pages' => $activites->lastPage(),
                'total' => $activites->total(),
            ],
        ]);
    }
}