<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Liste des notifications de l'utilisateur connecté (les 30 plus récentes),
     * accompagnées du nombre total de notifications non lues.
     */
    public function index(Request $request): JsonResponse
    {
        $utilisateur = $request->user();

        $notifications = $utilisateur->notifications()
            ->latest()
            ->take(30)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->data['type'] ?? null,
                'message' => $n->data['message'] ?? '',
                'lien' => $n->data['lien'] ?? null,
                'lu' => $n->read_at !== null,
                'date' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'non_lues' => $utilisateur->unreadNotifications()->count(),
        ]);
    }

    public function marquerLu(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marquée comme lue.']);
    }

    public function marquerToutLu(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
    }
}