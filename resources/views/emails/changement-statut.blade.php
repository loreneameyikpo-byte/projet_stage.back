<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #F3FAFD; padding: 32px; margin: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #E2E8F0;">
        <tr>
            <td style="background-color: #003152; padding: 24px 32px;">
                <span style="color: #ffffff; font-size: 18px; font-weight: bold;">Projetis</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 32px;">
                <h1 style="font-size: 18px; color: #1F2937; margin: 0 0 16px;">
                    Bonjour {{ $projet->etudiant->prenom }},
                </h1>

                <p style="font-size: 14px; color: #1F2937; line-height: 1.6; margin: 0 0 16px;">
                    Le statut de votre projet a changé :
                </p>

                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #F3FAFD; border-radius: 6px; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 16px;">
                            <p style="margin: 4px 0; font-size: 13px; color: #64748B;">Projet</p>
                            <p style="margin: 4px 0 12px; font-size: 14px; color: #1F2937; font-weight: 600;">{{ $projet->titre }}</p>

                            <p style="margin: 4px 0; font-size: 13px; color: #64748B;">Nouveau statut</p>
                            <p style="margin: 4px 0; font-size: 14px; color: #1F2937; font-weight: 600;">
                                {{ [
                                    'en_attente' => 'En attente',
                                    'corrections' => 'Corrections demandées',
                                    'valide' => 'Validé',
                                    'presentation_planifiee' => 'Présentation planifiée',
                                    'presente' => 'Présenté',
                                ][$nouveauStatut] ?? $nouveauStatut }}
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 13px; color: #64748B; line-height: 1.6; margin: 0 0 24px;">
                    Consultez votre espace étudiant pour plus de détails.
                </p>

                <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/login"
                   style="display: inline-block; background-color: #1D5A85; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 600; padding: 10px 20px; border-radius: 6px;">
                    Accéder à la plateforme
                </a>
            </td>
        </tr>
        <tr>
            <td style="background-color: #003152; padding: 16px 32px;">
                <span style="color: #7FA8C4; font-size: 11px;">© 2026 Projetis — Tous droits réservés.</span>
            </td>
        </tr>
    </table>
</body>
</html>