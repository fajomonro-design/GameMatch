
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | GameMatch</title>
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
                <span class="eyebrow">🎮 REJOINS LA COMMUNAUTÉ</span>
                <h1>Crée ton compte</h1>
                <p>Trouve tes prochains coéquipiers sur GameMatch.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Pseudo</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ton pseudo de joueur"
                        maxlength="50"
                        autocomplete="username"
                        required
                    >
                </div>

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
                    >
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="8 caractères minimum"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        Confirmer le mot de passe
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Retape ton mot de passe"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Créer mon compte <span>→</span>
                </button>
            </form>

            <p class="auth-switch">
                Tu as déjà un compte ?
                <a href="{{ route('login') }}">Connecte-toi</a>
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
