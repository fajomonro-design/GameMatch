
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GameMatch - Trouve ton équipe</title>
    <meta name="description" content="Trouve des coéquipiers compatibles avec tes jeux et ton niveau sur GameMatch.">

    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<body>

    <div class="background-glow" aria-hidden="true"></div>

    <header class="header">
        <nav class="navbar container" aria-label="Navigation principale">

            <a href="{{ url('/') }}" class="logo">
                Game<span>Match</span>
            </a>

            <div class="nav-links">
                <a href="{{ url('/') }}">Accueil</a>
                <a href="#fonctionnalites">Fonctionnalités</a>
                <a href="#concept">Le concept</a>
            </div>

            <div class="nav-actions">
                <a href="#connexion" class="login-link">Connexion</a>
                <a href="#inscription" class="btn btn-primary">
                    Nous rejoindre <span aria-hidden="true">↗</span>
                </a>
            </div>

        </nav>
    </header>

    <main>

        <section class="hero container">

            <div class="hero-content">

                <div class="eyebrow">
                    <span class="status-dot" aria-hidden="true"></span>
                    La communauté des joueurs
                </div>

                <h1>
                    Trouve ton prochain
                    <span class="gradient-text">coéquipier.</span>
                </h1>

                <p class="hero-description">
                    Marre de jouer seul ? Rencontre des joueurs qui
                    partagent tes jeux, ton niveau et ton envie de
                    progresser. La prochaine victoire commence ici.
                </p>

                <div class="hero-buttons">
                    <a href="#inscription" class="btn btn-primary">
                        Trouver mon équipe <span aria-hidden="true">→</span>
                    </a>

                    <a href="#fonctionnalites" class="btn btn-secondary">
                        Découvrir le concept
                    </a>
                </div>

                <p class="hero-note">
                    🎮 Des joueurs compatibles. Des parties plus fun.
                </p>

            </div>

            <div class="hero-visual">

                <div class="visual-orb" aria-hidden="true"></div>

                <article class="player-card">

                    <div class="card-top">
                        <strong>Profil joueur</strong>
                        <span class="online-label">
                            <span aria-hidden="true">●</span> Disponible
                        </span>
                    </div>

                    <div class="avatar" aria-hidden="true">🎮</div>

                    <h2 class="player-name">NovaPulse</h2>

                    <p class="player-subtitle">
                        À la recherche d'une équipe
                    </p>

                    <div class="tags">
                        <span class="tag">VALORANT</span>
                        <span class="tag">LEAGUE OF LEGENDS</span>
                        <span class="tag">COOP</span>
                    </div>

                    <div class="player-divider"></div>

                    <div class="player-info">
                        <span>Style de jeu</span>
                        <strong>Compétitif</strong>
                    </div>

                    <div class="player-divider"></div>

                    <div class="player-info">
                        <span>Objectif</span>
                        <strong>Progresser ensemble</strong>
                    </div>

                </article>

            </div>

        </section>

        <section class="section container" id="fonctionnalites">

            <div class="section-heading">

                <span class="eyebrow">Pourquoi GameMatch ?</span>

                <h2>
                    Le bon joueur change
                    <span class="gradient-text">la partie.</span>
                </h2>

                <p>
                    Trouve des partenaires de jeu qui te correspondent
                    et construis une équipe à ton image.
                </p>

            </div>

            <div class="features">

                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">🎯</div>

                    <h3>Des joueurs compatibles</h3>

                    <p>
                        Recherche des coéquipiers selon leur niveau,
                        leur style de jeu et leurs disponibilités.
                    </p>
                </article>

                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">🎮</div>

                    <h3>Tes jeux préférés</h3>

                    <p>
                        Retrouve des joueurs qui partagent tes jeux
                        favoris et tes objectifs.
                    </p>
                </article>

                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">🤝</div>

                    <h3>Une équipe motivée</h3>

                    <p>
                        Échange avec de nouveaux joueurs, développe
                        ton esprit d'équipe et amuse-toi.
                    </p>
                </article>

            </div>

        </section>

        <section class="cta container" id="inscription">

            <h2>Prêt à trouver ton équipe ?</h2>

            <p>
                Tes prochains coéquipiers t'attendent.
                L'aventure GameMatch commence ici.
            </p>

            <a href="#connexion" class="btn btn-primary">
                Découvrir GameMatch <span aria-hidden="true">↗</span>
            </a>

        </section>

    </main>

    <footer class="footer" id="concept">

        <div class="footer-content container">

            <a href="{{ url('/') }}" class="logo">
                Game<span>Match</span>
            </a>

            <p>© 2026 GameMatch · Projet étudiant BTS SIO SLAM</p>

            <p>Joue ensemble. Progresse ensemble.</p>

        </div>

    </footer>

</body>
</html>
