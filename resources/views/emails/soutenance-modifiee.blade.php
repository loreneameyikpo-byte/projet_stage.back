<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #F8FAFC; padding: 32px; margin: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #E2E8F0;">
        <tr>
            <td style="background-color: #1E3A8A; padding: 24px 32px;">
                <span style="color: #ffffff; font-size: 18px; font-weight: bold;">Projetis</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 32px;">
                <h1 style="font-size: 18px; color: #1F2937; margin: 0 0 16px;">
                    Bonjour {{ $destinataireNom }},
                </h1>

                <p style="font-size: 14px; color: #1F2937; line-height: 1.6; margin: 0 0 20px;">
                    Les informations de la soutenance suivante ont été modifiées :
                </p>

                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #F8FAFC; border-radius: 6px; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 16px;">
                            <p style="font-size: 13px; color: #64748B; margin: 0 0 4px;">Projet</p>
                            <p style="font-size: 14px; color: #1F2937; margin: 0 0 12px; font-weight: 600;">{{ $presentation->projet->titre }}</p>

                            <p style="font-size: 13px; color: #64748B; margin: 0 0 4px;">Nouvelle date</p>
                            <p style="font-size: 14px; color: #1F2937; margin: 0 0 12px; font-weight: 600;">
                                {{ $presentation->date_presentation?->format('d/m/Y') }} à {{ $presentation->heure_presentation }}
                            </p>

                            <p style="font-size: 13px; color: #64748B; margin: 0 0 4px;">Salle</p>
                            <p style="font-size: 14px; color: #1F2937; margin: 0; font-weight: 600;">
                                {{ $presentation->salle?->numero }}{{ $presentation->salle?->libelle ? ' — '.$presentation->salle->libelle : '' }}
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 13px; color: #64748B; line-height: 1.6;">
                    Merci de prendre note de ces changements. Connectez-vous à la plateforme pour consulter le détail complet.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #1E3A8A; padding: 16px 32px;">
                <span style="color: #93C5FD; font-size: 11px;">© 2026 Projetis — Tous droits réservés.</span>
            </td>
        </tr>
    </table>
</body>
</html>