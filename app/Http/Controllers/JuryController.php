<?php

namespace App\Http\Controllers;

use App\Http\Requests\Jury\SaisirNoteRequest;
use App\Http\Resources\JuryResource;
use App\Models\Jury;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JuryController extends Controller
{
    /**
     * Saisie de la note par un membre du jury (interne ou externe),
     * pour sa propre participation uniquement.
     */
    public function saisirNote(SaisirNoteRequest $request, Jury $jury): JsonResponse
    {
        $validated = $request->validated();
        $utilisateur = $request->user();

        DB::transaction(function () use ($jury, $utilisateur, $validated) {
            $jury->membres()->updateExistingPivot($utilisateur->id_utilisateur, [
                'note_saisie' => $validated['note'],
            ]);

            $noteFinale = $jury->calculerNoteFinale();

            if ($noteFinale !== null) {
                $jury->presentation->update(['note_finale' => $noteFinale]);
            }
        });

        return response()->json([
            'message' => 'Note enregistrée avec succès.',
            'jury' => new JuryResource($jury->fresh(['membres'])),
        ]);
    }
}