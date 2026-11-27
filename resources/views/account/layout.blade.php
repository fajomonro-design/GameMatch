<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — GameMatch</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/account.css') }}">
    @stack('head')
</head>
<body>
<header class="header">
    <nav class="navbar container" aria-label="Navigation principale">
        <a class="logo" href="{{ route('home') }}">Game<span>Match</span></a>
        @auth
            <a class="login-link" href="{{ route('dashboard') }}">Tableau de bord</a>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-secondary" type="submit">Se déconnecter</button>
            </form>
        @else
            <div class="nav-actions account-nav">
                <a href="{{ route('login') }}">Connexion</a>
                <a href="{{ route('register') }}">Inscription</a>
            </div>
        @endauth
    </nav>
</header>
<main class="container account-shell">
    <div class="account-panel">
        <h1>@yield('title')</h1>
        @if (session('status'))
            <p class="account-status" role="status">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <ul class="account-errors" role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        @yield('content')
    </div>
</main>
</body>
</html>
