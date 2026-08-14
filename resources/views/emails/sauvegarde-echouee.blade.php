<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background-color: #F3FAFD; padding: 32px 0; margin: 0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden;">
                    <tr>
                        <td style="background-color: #003152; padding: 24px; text-align: center;">
                            <span style="color: #ffffff; font-size: 18px; font-weight: bold;">Projetis</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px;">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background-color: #FEE2E2; text-align: center; line-height: 48px; font-size: 24px; margin-bottom: 16px;">⚠</div>
                            <h1 style="font-size: 18px; color: #0F2A3D; margin: 0 0 12px;">Échec de la sauvegarde automatique</h1>
                            <p style="font-size: 14px; color: #5B7C93; line-height: 1.6; margin: 0 0 8px;">
                                La sauvegarde automatique quotidienne de la plateforme n'a pas pu se terminer correctement. Une vérification manuelle est recommandée.
                            </p>
                            <p style="font-size: 13px; color: #5B7C93; margin: 16px 0 8px;">
                                Date et heure : <strong>{{ $dateHeure }}</strong>
                            </p>
                            <div style="background-color: #FEF2F2; border: 1px solid #FECACA; border-radius: 8px; padding: 12px 14px; margin-top: 8px;">
                                <p style="font-size: 12px; color: #B91C1C; margin: 0; font-family: monospace; word-break: break-word;">
                                    {{ $messageErreur }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 16px 32px 24px; border-top: 1px solid #F3FAFD;">
                            <p style="font-size: 11px; color: #94a3b8; margin: 0;">
                                Ceci est un message automatique envoyé aux comptes super administrateur de Projetis.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>