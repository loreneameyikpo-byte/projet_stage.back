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
                    La soutenance suivante a été <strong style="color: #EF4444;">annulée</strong> :
                </p>

                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #F8FAFC; border-radius: 6px; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 16px;">
                            <p style="font-size: 13px; color: #64748B; margin: 0 0 4px;">Projet</p>
                            <p style="font-size: 14px; color: #1F2937; margin: 0 0 12px; font-weight: 600;">{{ $donnees['titre_projet'] }}</p>

                            <p style="font-size: 13px; color: #64748B; margin: 0 0 4px;">Étudiant</p>
                            <p style="font-size: 14px; color: #1F2937; margin: 0 0 12px; font-weight: 600;">{{ $donnees['etudiant'] }}</p>

                            <p style="font-size: 13px; color: #64748B; margin: 0 0 4px;">Créneau annulé</p>
                            <p style="font-size: 14px; color: #1F2937; margin: 0; font-weight: 600;">
                                {{ $donnees['date_presentation'] }} à {{ $donnees['heure_presentation'] }}
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 13px; color: #64748B; line-height: 1.6;">
                    Une nouvelle soutenance sera planifiée ultérieurement. Vous serez notifié dès qu'une nouvelle date sera fixée.
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