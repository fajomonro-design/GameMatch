@extends('account.layout')
@section('title', 'Créer un compte')
@if (config('services.turnstile.site_key'))
    @push('head')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endpush
@endif
@section('content')
<form class="account-form" method="post" action="{{ route('register') }}">
    @csrf
    <label>Pseudo<input name="name" value="{{ old('name') }}" required minlength="2" maxlength="24" autocomplete="nickname"></label>
    <label>Adresse e-mail<input type="email" name="email" value="{{ old('email') }}" required maxlength="254" autocomplete="email"></label>
    <label>Mot de passe<input type="password" name="password" required minlength="12" maxlength="72" autocomplete="new-password" aria-describedby="password-help"></label>
    <p id="password-help">Utilise au moins 12 caractères.</p>
    <label>Confirme ton mot de passe<input type="password" name="password_confirmation" required autocomplete="new-password"></label>
    <label>Tranche d’âge<select name="age_group" required>
        <option value="">Choisir une tranche d’âge</option>
        @foreach ($ages as $age)
            <option @selected(old('age_group') === $age)>{{ $age }}</option>
        @endforeach
    </select></label>
    <label>Plateforme principale<select name="platform" required>
        <option value="">Choisir une plateforme</option>
        @foreach ($platforms as $platform)
            <option @selected(old('platform') === $platform)>{{ $platform }}</option>
        @endforeach
    </select></label>
    @if (config('services.turnstile.site_key'))
        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-language="fr" data-size="flexible"></div>
    @endif
    <button type="submit" class="btn btn-primary">Créer mon compte</button>
</form>
<p>Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a></p>
@endsection
