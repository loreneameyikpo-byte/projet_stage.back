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
                    Bonjour,
                </h1>

                @if ($roleDestinataire)
                    <p style="font-size: 14px; color: #1F2937; line-height: 1.6; margin: 0 0 16px;">
                        Vous avez été désigné(e) <strong>{{ ['president' => 'président', 'rapporteur' => 'rapporteur', 'membre' => 'membre'][$roleDestinataire] ?? $roleDestinataire }}</strong> du jury pour la soutenance suivante :
                    </p>
                @else
                    <p style="font-size: 14px; color: #1F2937; line-height: 1.6; margin: 0 0 16px;">
                        Une soutenance a été planifiée pour le projet suivant :
                    </p>
                @endif

                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #F3FAFD; border-radius: 6px; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 16px;">
                            <p style="margin: 4px 0; font-size: 13px; color: #64748B;">Projet</p>
                            <p style="margin: 4px 0 12px; font-size: 14px; color: #1F2937; font-weight: 600;">{{ $presentation->projet->titre }}</p>

                            <p style="margin: 4px 0; font-size: 13px; color: #64748B;">Étudiant</p>
                            <p style="margin: 4px 0 12px; font-size: 14px; color: #1F2937; font-weight: 600;">{{ $presentation->projet->etudiant->prenom }} {{ $presentation->projet->etudiant->nom }}</p>

                            <p style="margin: 4px 0; font-size: 13px; color: #64748B;">Date</p>
                            <p style="margin: 4px 0 12px; font-size: 14px; color: #1F2937; font-weight: 600;">{{ $presentation->date_presentation }}</p>

                            <p style="margin: 4px 0; font-size: 13px; color: #64748B;">Heure</p>
                            <p style="margin: 4px 0 12px; font-size: 14px; color: #1F2937; font-weight: 600;">{{ $presentation->heure_presentation }}</p>

                            <p style="margin: 4px 0; font-size: 13px; color: #64748B;">Salle</p>
                            <p style="margin: 4px 0; font-size: 14px; color: #1F2937; font-weight: 600;">{{ $presentation->salle->numero }}{{ $presentation->salle->libelle ? ' — ' . $presentation->salle->libelle : '' }}</p>
                        </td>
                    </tr>
                </table>

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