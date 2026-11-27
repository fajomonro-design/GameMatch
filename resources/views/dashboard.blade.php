<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tableau de bord — GameMatch</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="{{ asset('js/dashboard.js') }}" defer></script>
</head>
<body>
@php
    $nav = [
        ['Tableau de bord', '#accueil', 'M3 10l9-7 9 7v10h-6v-7H9v7H3z'],
        ['Rechercher', '#recherche', 'M21 21l-5-5M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0z'],
        ['Mes jeux', route('profile').'#jeux', 'M8 12h4M10 10v4M16 11h.01M19 14h.01M7 6h10a3 3 0 0 1 3 2l2 7a3 3 0 0 1-5 3l-2-2H9l-2 2a3 3 0 0 1-5-3l2-7a3 3 0 0 1 3-2z'],
        ['Contacts', '#contacts', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM22 21v-2a4 4 0 0 0-3-4m-3-12a4 4 0 0 1 0 8'],
        ['Notifications', '#notifications', 'M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4'],
        ['Mon profil', route('profile'), 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z'],
        ['Paramètres', route('profile').'#parametres', 'M4 7h16M4 12h16M4 17h16M8 5v4m8 1v4m-6 1v4'],
    ];
@endphp
<a class="skip-link" href="#recherche">Aller à la recherche</a>
<nav class="rail" aria-label="Navigation joueur">
    <a class="logo" href="{{ route('home') }}" aria-label="GameMatch, accueil public">GM</a>
    @foreach ($nav as [$label, $href, $icon])
        <a href="{{ $href }}" title="{{ $label }}" aria-label="{{ $label }}" @if ($loop->first) aria-current="location" @endif>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="{{ $icon }}"/></svg>
            <span>{{ $label }}</span>
        </a>
    @endforeach
    <form method="post" action="{{ route('logout') }}">@csrf
        <button type="submit" title="Déconnexion" aria-label="Déconnexion"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/></svg><span>Déconnexion</span></button>
    </form>
</nav>
<div class="layout" id="accueil">
<main class="workspace">
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}">Game<span>Match</span></a>
        <div class="top-actions"><a href="#notifications">Notifications @if ($notifications->isNotEmpty()) <strong>{{ $notifications->count() }}</strong> @endif</a><a class="identity" href="{{ route('profile') }}"><span class="avatar">{{ mb_strtoupper(mb_substr($player->name, 0, 1)) }}</span><span>{{ $player->name }}</span></a></div>
    </header>
    @if(session('status')) <p role="status" class="notice">{{ session('status') }}</p> @endif
    <section class="hero">
        <div>
            <p class="eyebrow">TON ESPACE JOUEUR</p>
            <h1>Prêt à jouer, <span>{{ $player->name }}</span> ?</h1>
            <p>Trouve des joueurs qui partagent ton niveau, ton style et ta passion.</p>
            <a class="button primary" href="#recherche">Trouver mon équipe</a>
        </div>
    </section>
    <section class="library" aria-labelledby="library-title">
        <div class="heading"><h2 id="library-title">Bibliothèque de jeux</h2><button class="link-button" type="button" data-game="" aria-pressed="true">Tous les jeux</button></div>
        <div class="game-strip">
            @foreach ($games as $game => $cover)
                <button class="game-cover" type="button" data-game="{{ $game }}" aria-pressed="false">
                    <img src="{{ $cover }}" alt="" loading="lazy" referrerpolicy="no-referrer">
                    <span>{{ $game }}</span>
                </button>
            @endforeach
        </div>
    </section>
    <section class="search" id="recherche" aria-labelledby="search-title">
        <div class="heading"><h2 id="search-title">Trouve tes coéquipiers</h2><a href="{{ route('profile') }}">Mettre à jour mon profil</a></div>
        @if (empty($player->games) || empty($player->availability_slots)) <p class="hint">Ajoute tes jeux et tes créneaux dans ton profil pour obtenir des scores plus précis.</p> @endif
        <form id="player-filters" data-endpoint="{{ route('players.index') }}">
            <div class="search-line"><label class="search-field">Pseudo<input name="search" type="search" maxlength="80" placeholder="Rechercher un joueur"></label><button type="submit" class="button">Rechercher</button><button id="show-filters" type="button" class="button" aria-expanded="false" aria-controls="advanced">Filtres</button></div>
            <div id="advanced" hidden>
                <label>Jeu<select name="game"><option value="">Tous les jeux</option>@foreach(array_keys($games) as $game)<option>{{ $game }}</option>@endforeach</select></label>
                @foreach ($filters as $field => [$label, $values])
                    <label>{{ $label }}<select name="{{ $field }}"><option value="">Tous</option>@foreach ($values as $value)<option>{{ $value }}</option>@endforeach</select></label>
                @endforeach
                <button class="link-button" type="reset">Réinitialiser les filtres</button>
            </div>
        </form>
        <p id="player-status" role="status" class="hint">Chargement des joueurs…</p>
        <div id="player-list" class="player-list"></div>
        <button id="load-more" class="button" type="button" hidden>Afficher plus de joueurs</button>
        <noscript><p>Active JavaScript pour rechercher et comparer les profils.</p></noscript>
    </section>
    <section class="activity" id="contacts" aria-labelledby="contacts-title">
        <h2 id="contacts-title">Mes contacts</h2>
        @forelse ($contacts as $contact)
            <article class="activity-row">
                <strong>{{ $contact->name }}</strong>
                <span>{{ match($contact->status) { 'accepted' => 'Accepté', 'declined' => 'Refusé', default => $contact->sender_id === $player->id ? 'Demande envoyée' : 'Demande reçue' } }}</span>
                @if ($contact->status === 'pending' && $contact->sender_id !== $player->id)
                    <form action="{{ route('contacts.respond', $contact->id) }}" method="post">@csrf @method('PATCH')
                        <button class="button primary" name="status" value="accepted">Accepter</button><button class="button" name="status" value="declined">Refuser</button>
                    </form>
                @endif
            </article>
        @empty <p class="hint">Aucune demande pour le moment.</p> @endforelse
    </section>
    <section class="activity" id="notifications" aria-labelledby="notifications-title">
        <h2 id="notifications-title">Notifications</h2>
        @forelse ($notifications as $notification)
            <p class="activity-row">{{ $notification->status === 'pending' ? $notification->name.' souhaite jouer avec toi.' : 'Demande '.($notification->status === 'accepted' ? 'acceptée' : 'refusée').' par '.$notification->name.'.' }} <a href="#contacts">Mes contacts</a></p>
        @empty <p class="hint">Tu n’as aucune notification de contact.</p> @endforelse
    </section>
</main>
<aside id="profile-panel" class="profile-panel" aria-labelledby="panel-title" tabindex="-1">
    <button id="close-profile" class="button" type="button" aria-label="Fermer le profil">Fermer</button>
    <h2 id="panel-title">Profil joueur</h2>
    <p id="no-selection" class="hint">Sélectionne un joueur pour consulter son profil.</p>
    <div id="selected-profile" hidden>
        <div class="selected-head"><span id="selected-avatar" class="avatar"></span><h3 id="selected-name"></h3></div>
        <div class="score"><label for="selected-score">Compatibilité <strong id="score-value"></strong></label><progress id="selected-score" max="100" value="0"></progress></div>
        <div class="tabs" role="group" aria-label="Informations du profil">
            <button type="button" data-tab="about" aria-pressed="true">À propos</button>
            <button type="button" data-tab="games" aria-pressed="false">Jeux</button>
            <button type="button" data-tab="times" aria-pressed="false">Disponibilités</button>
        </div>
        <section data-pane="about"><dl id="selected-info"></dl><p id="selected-bio"></p></section>
        <section data-pane="games" hidden><ul id="selected-games" class="tag-list"></ul></section>
        <section data-pane="times" hidden><ul id="selected-slots" class="tag-list"></ul><p id="selected-availability"></p></section>
        <div class="profile-actions"><button type="button" id="contact-player" class="button primary">Demander à jouer</button><button id="report-player" type="button" class="link-button">Signaler</button></div>
        <p id="action-status" role="status" class="hint"></p>
    </div>
</aside>
</div>
<dialog id="report-dialog" aria-labelledby="report-title">
    <form id="report-form">
        <h2 id="report-title">Signaler un profil</h2>
        <label>Motif<select name="reason" required><option value="">Sélectionner</option>@foreach ($reasons as $reason)<option>{{ $reason }}</option>@endforeach</select></label>
        <label>Précisions (facultatif)<textarea name="details" rows="3" maxlength="500"></textarea></label>
        <p id="report-status" role="status"></p>
        <div class="profile-actions"><button class="button primary" type="submit">Envoyer</button><button id="cancel-report" class="button" type="button">Fermer</button></div>
    </form>
</dialog>
</body>
</html>
