<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SemoaService;

class PaiementController extends Controller
{
    public function initiate(Request $request)
{
    $validator = Validator::make($request->all(), [
        'etudiant_id' => 'required|exists:etudiants,id',
        'montant' => 'required|numeric|min:100',
        'lastname' => 'required|string',
        'firstname' => 'required|string',
        'phone' => 'required|string',
        'nature_paiement' => 'nullable|string|in:scolarite,inscription',
        'payable_id' => 'nullable|integer',
        'payable_type' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    try {
        $paiement = $this->paiementService->creerPaiementEnAttente(
            $request->etudiant_id,
            $request->montant,
            'semoa',
            $request->get('nature_paiement', 'scolarite'),
            $request->payable_id,
            $request->payable_type,
            "Initialisation paiement SEMOA"
        );

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
        $semoaResponse = $this->semoaService->initializePayment([
            'amount' => (float) $request->montant,
            'description' => "Paiement Scolarité - " . $request->lastname . " " . $request->firstname,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'phone' => $request->phone,
            'gateway_reference' => $request->payment_method,
            'success_url' => $frontendUrl . '/etudiant/mes-paiements?status=success',
            'cancel_url' => $frontendUrl . '/etudiant/mes-paiements?status=cancel',
        ]);

        $orderReference = $semoaResponse['order_reference'] ?? null;
        if ($orderReference) {
            $paiement->update(['reference' => $orderReference]);
        }

        return response()->json([
            'success' => true,
            'payment_url' => $semoaResponse['long_bill_url'] ?? $semoaResponse['bill_url'] ?? null,
            'order_reference' => $orderReference
        ]);

    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
    }
}






}