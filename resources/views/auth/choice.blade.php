<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rejoindre GameMatch</title>
    <link rel="stylesheet" href="{{ asset('css/join.css') }}">
</head>
<body>

    <main class="join-page">
        <a href="{{ url('/') }}" class="brand">
            <span class="brand-icon">G</span>
            <span>GameMatch</span>
        </a>

        <section class="join-content">
            <span class="eyebrow">BIENVENUE SUR GAMEMATCH</span>

            <h1>Prêt à trouver<br>ton prochain <span>coéquipier ?</span></h1>

            <p class="intro">
                Rejoins une communauté de joueurs, trouve des personnes
                qui partagent tes jeux préférés et forme ta prochaine équipe.
            </p>

            <div class="choice-grid">
                <article class="choice-card">
                    <div class="choice-icon">↗</div>

                    <h2>Tu as déjà un compte ?</h2>

                    <p>
                        Retrouve ton compte et reprends ta recherche
                        de coéquipiers.
                    </p>

                    <a href="{{ url('/login') }}" class="choice-button secondary">
                        Se connecter
                        <span>→</span>
                    </a>
                </article>

                <article class="choice-card featured">
                    <div class="choice-icon">✦</div>

                    <span class="new-label">NOUVEAU JOUEUR</span>

                    <h2>Tu nous rejoins ?</h2>

                    <p>
                        Crée ton compte gratuitement et commence
                        à trouver des joueurs compatibles.
                    </p>

                    <a href="{{ url('/register') }}" class="choice-button primary">
                        Créer un compte
                        <span>→</span>
                    </a>
                </article>
            </div>

            <a href="{{ url('/') }}" class="back-link">
                ← Retour à l'accueil
            </a>
        </section>

        <footer>
            © {{ date('Y') }} GameMatch · Joue ensemble, progresse ensemble.
        </footer>
    </main>

</body>
</html>