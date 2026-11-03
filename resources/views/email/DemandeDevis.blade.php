<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande de devis - MadWork</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background: #f4f1f5; margin: 0; padding: 40px 20px;">

    <div style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.08); overflow: hidden;">

        <!-- En-tête -->
        <div style="background: linear-gradient(135deg, #c10e46 0%, #d81b60 50%, #1e002f 100%); padding: 40px 20px; text-align: center;">

            <div style="background: rgba(255, 255, 255, 0.603);
                        border-radius: 16px;
                        padding: 20px;
                        display: inline-block;">

                <img src="{{ asset('logo.png') }}"
                     alt="Logo MadWork"
                     style="height: 100px; width: auto;">
            </div>

        </div>

        <!-- Contenu -->
        <div style="padding: 50px 40px;">

            <!-- Icône -->
            <div style="text-align: center; margin-bottom: 30px;">

                <div style="background: linear-gradient(135deg, #c10e46 0%, #1e002f 100%);
                            width: 80px;
                            height: 80px;
                            border-radius: 50%;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            margin-bottom: 20px;">

                    <span style="color: white; font-size: 32px;">📋</span>

                </div>

                <h2 style="color: #1e002f;
                           font-size: 28px;
                           font-weight: bold;
                           margin: 0 0 10px 0;">
                    Nouvelle demande de devis
                </h2>

                <p style="color: #6b7280;
                          font-size: 16px;
                          line-height: 1.6;
                          margin: 0;">
                    Un client a soumis une nouvelle demande de devis.
                </p>

            </div>

            <!-- Informations client -->
            <div style="background: #faf7f8;
                        border-radius: 12px;
                        padding: 25px;
                        margin: 30px 0;
                        border-left: 5px solid #c10e46;">

                <h3 style="color: #1e002f;
                           font-size: 18px;
                           font-weight: bold;
                           margin: 0 0 20px 0;
                           text-align: center;">
                    Coordonnées du client
                </h3>

                <p style="color: #374151; margin: 12px 0;">
                    <strong style="color: #c10e46;">👤 Nom :</strong>
                    {{ $customer->name }}
                </p>

                <p style="color: #374151; margin: 12px 0;">
                    <strong style="color: #c10e46;">📧 Email :</strong>
                    {{ $customer->email }}
                </p>

                <p style="color: #374151; margin: 12px 0;">
                    <strong style="color: #c10e46;">📞 Téléphone :</strong>
                    {{ $customer->phone }}
                </p>

            </div>

            <!-- Bouton d'action -->
            <div style="text-align: center; margin: 40px 0;">

                <a href="mailto:{{ $customer->email }}"
                   style="background: linear-gradient(135deg, #c10e46 0%, #1e002f 100%);
                          color: #ffffff;
                          text-decoration: none;
                          padding: 16px 40px;
                          border-radius: 50px;
                          font-weight: bold;
                          font-size: 16px;
                          display: inline-block;
                          box-shadow: 0 8px 25px rgba(193,14,70,0.30);">
                    Répondre au client
                </a>

            </div>

        </div>

        <!-- Footer -->
        <div style="background: #1e002f;
                    color: white;
                    padding: 30px 40px;
                    text-align: center;">

            <img src="{{ asset('logo.png') }}"
                 alt="MadWork"
                 style="height: 40px;
                        width: auto;
                        margin-bottom: 15px;">

            <div style="border-top: 1px solid rgba(255,255,255,0.15);
                        padding-top: 20px;">

                <p style="color: #c9c9c9;
                          font-size: 12px;
                          margin: 0;">
                    © {{ date('Y') }} MadWork. Tous droits réservés.
                    <br>
                    <span style="font-size: 11px;">
                        Notification automatique - Demande de devis
                    </span>
                </p>

            </div>

        </div>

        <!-- Bannière -->
        <div style="background: #faf7f8;
                    padding: 15px;
                    text-align: center;
                    border-top: 1px solid #ececec;">

            <p style="color: #6b7280;
                      font-size: 12px;
                      margin: 0;">
                ⚠️ Ceci est une notification automatique. Veuillez ne pas répondre directement à cet e-mail.
            </p>

        </div>

    </div>

</body>
</html>
