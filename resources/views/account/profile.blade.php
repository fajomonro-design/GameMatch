@extends('account.layout')
@section('title', 'Mon profil joueur')
@section('content')
<form class="account-form" method="post" action="{{ route('profile') }}">
    @csrf
    @method('PATCH')
    <label>Pseudo<input name="name" value="{{ old('name', $player->name) }}" required minlength="2" maxlength="24" autocomplete="nickname"></label>
    <p>Adresse e-mail du compte <strong>{{ $player->email }}</strong></p>
    @foreach ($options as $field => [$label, $values])
        <label>{{ $label }}<select name="{{ $field }}" @required(in_array($field, ['age_group', 'platform'], true))>
            <option value="">Choisir</option>
            @foreach ($values as $value)
                <option @selected(old($field, $player->$field) === $value)>{{ $value }}</option>
            @endforeach
        </select></label>
    @endforeach
    <fieldset>
        <legend>Jeux pratiqués</legend>
        @foreach ($games as $game)
            <label class="account-check"><input type="checkbox" name="games[]" value="{{ $game }}" @checked(in_array($game, (array) old('games', session()->hasOldInput() ? [] : $player->games), true))>{{ $game }}</label>
        @endforeach
    </fieldset>
    <label>Disponibilités<textarea name="availability" rows="3" maxlength="240" placeholder="Ex. mardi et jeudi après 19 h">{{ old('availability', $player->availability) }}</textarea></label>
    <button type="submit" class="btn btn-primary">Enregistrer mon profil</button>
</form>
@endsection
