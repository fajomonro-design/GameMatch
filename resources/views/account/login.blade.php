@extends('account.layout')
@section('title', 'Connexion')
@section('content')
<form class="account-form" method="post" action="{{ route('login') }}">
    @csrf
    <label>Adresse e-mail<input type="email" name="email" value="{{ old('email') }}" required maxlength="254" autocomplete="username"></label>
    <label>Mot de passe<input type="password" name="password" required maxlength="72" autocomplete="current-password"></label>
    <button type="submit" class="btn btn-primary">Se connecter</button>
</form>
<p>Première visite ? <a href="{{ route('register') }}">Créer un compte</a></p>
@endsection
