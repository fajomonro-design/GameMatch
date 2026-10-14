
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administration | GameMatch</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <a href="/" class="brand">
            <span class="brand-icon">G</span>
            <span>GameMatch<span class="brand-subtitle">Administration</span></span>
        </a>

        <div class="sidebar-label">MENU PRINCIPAL</div>

        <nav class="navigation">
            <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                <span class="nav-icon">▦</span>
                Tableau de bord
            </a>

            <a href="#utilisateurs" class="nav-link">
                <span class="nav-icon">♙</span>
                Utilisateurs
                <span class="nav-count">{{ $totalUsers }}</span>
            </a>

            <div class="nav-link disabled-link" title="Disponible dans une prochaine étape">
                <span class="nav-icon">◈</span>
                Jeux
                <span class="coming-soon">Bientôt</span>
            </div>

            <div class="nav-link disabled-link" title="Disponible dans une prochaine étape">
                <span class="nav-icon">⚑</span>
                Signalements
                <span class="coming-soon">Bientôt</span>
            </div>
        </nav>

        <div class="sidebar-bottom">
            <div class="security-card">
                <span class="security-icon">✓</span>
                <div>
                    <strong>Accès sécurisé</strong>
                    <p>Espace administrateur</p>
                </div>
            </div>

            <a href="/" class="back-link">← Retour au site</a>
        </div>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div>
                <p class="breadcrumb">GameMatch / Administration</p>
                <h1>Tableau de bord</h1>
            </div>

            <div class="profile">
                <div class="avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="profile-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>Administrateur</span>
                </div>
            </div>
        </header>

        <section class="welcome-section">
            <div>
                <p class="eyebrow">VUE D'ENSEMBLE</p>
                <h2>Bienvenue dans votre espace.</h2>
                <p class="welcome-text">
                    Gérez GameMatch et suivez l'activité de la plateforme.
                </p>
            </div>

            <div class="date-indicator">
                <span class="date-dot"></span>
                Administration active
            </div>
        </section>

        <section class="stats-grid">
            <article class="stat-card">
                <div class="stat-top">
                    <span class="stat-label">Utilisateurs inscrits</span>
                    <span class="stat-icon purple">♙</span>
                </div>
                <div class="stat-number">{{ number_format($totalUsers, 0, ',', ' ') }}</div>
                <p class="stat-description">Comptes enregistrés en base</p>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <span class="stat-label">Administrateurs</span>
                    <span class="stat-icon blue">⌑</span>
                </div>
                <div class="stat-number">{{ number_format($adminUsers, 0, ',', ' ') }}</div>
                <p class="stat-description">Comptes ayant les droits admin</p>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <span class="stat-label">Jeux référencés</span>
                    <span class="stat-icon orange">◈</span>
                </div>
                <div class="stat-number">—</div>
                <p class="stat-description">Module à développer</p>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <span class="stat-label">Signalements en attente</span>
                    <span class="stat-icon green">⚑</span>
                </div>
                <div class="stat-number">—</div>
                <p class="stat-description">Module à développer</p>
            </article>
        </section>

        <section class="content-panel" id="utilisateurs">
            <div class="panel-header">
                <div>
                    <h2>Utilisateurs récents</h2>
                    <p>Les cinq derniers comptes enregistrés.</p>
                </div>

                <span class="panel-badge">
                    {{ $recentUsers->count() }} résultat(s)
                </span>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>UTILISATEUR</th>
                            <th>ADRESSE E-MAIL</th>
                            <th>RÔLE</th>
                            <th>INSCRIPTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentUsers as $recentUser)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($recentUser->name, 0, 1)) }}
                                        </div>
                                        <strong>{{ $recentUser->name }}</strong>
                                    </div>
                                </td>

                                <td class="email-cell">{{ $recentUser->email }}</td>

                                <td>
                                    @if ($recentUser->is_admin)
                                        <span class="role-badge admin-role">Administrateur</span>
                                    @else
                                        <span class="role-badge user-role">Utilisateur</span>
                                    @endif
                                </td>

                                <td class="date-cell">
                                    {{ $recentUser->created_at?->format('d/m/Y') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    Aucun compte enregistré pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="panel-footer">
                <span>Les données affichées proviennent de la base de données.</span>
            </div>
        </section>

        <footer class="admin-footer">
            <span>© {{ date('Y') }} GameMatch</span>
            <span>Espace d'administration</span>
        </footer>

    </main>
</div>

</body>
</html>
