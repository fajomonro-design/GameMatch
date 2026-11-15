
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | GameMatch</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-glow"></div>

    <main class="auth-container">
        <a href="{{ route('home') }}" class="logo">
            Game<span>Match</span>
        </a>

        <section class="auth-card">
            <div class="auth-heading">
                <span class="eyebrow">⚡ PRÊT À JOUER ?</span>
                <h1>Content de te revoir</h1>
                <p>Connecte-toi pour retrouver tes coéquipiers.</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="toi@exemple.fr"
                        autocomplete="email"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Ton mot de passe"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <label class="remember-option">
                    <input type="checkbox" name="remember" value="1">
                    <span>Se souvenir de moi</span>
                </label>

                <button type="submit" class="btn btn-primary">
                    Se connecter <span>→</span>
                </button>
            </form>

            <p class="auth-switch">
                Pas encore de compte ?
                <a href="{{ route('register') }}">Inscris-toi</a>
            </p>

            <a href="{{ route('home') }}" class="back-link">
                ← Retour à l'accueil
            </a>
        </section>

        <p class="auth-footer">
            © 2026 GameMatch · Joue ensemble. Progresse ensemble.
        </p>
    </main>
</body>
</html>
