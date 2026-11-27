@extends('account.layout')
@section('title', 'Mon profil joueur')
@section('content')
<form class="account-form" method="post" action="{{ route('profile') }}">
    @csrf
    @method('PATCH')
    <label>Pseudo<input name="name" value="{{ old('name', $player->name) }}" required minlength="2" maxlength="24" autocomplete="nickname"></label>
    <p id="parametres">Adresse e-mail du compte <strong>{{ $player->email }}</strong></p>
    @foreach ($options as $field => [$label, $values])
        <label>{{ $label }}<select name="{{ $field }}" @required(in_array($field, ['age_group', 'platform'], true))>
            <option value="">Choisir</option>
            @foreach ($values as $value)
                <option @selected(old($field, $player->$field) === $value)>{{ $value }}</option>
            @endforeach
        </select></label>
    @endforeach
    <fieldset id="jeux">
        <legend>Jeux pratiqués</legend>
        @foreach ($games as $game)
            <label class="account-check"><input type="checkbox" name="games[]" value="{{ $game }}" @checked(in_array($game, (array) old('games', session()->hasOldInput() ? [] : $player->games), true))>{{ $game }}</label>
        @endforeach
    </fieldset>
    <label>Biographie<textarea name="bio" rows="3" maxlength="500">{{ old('bio', $player->bio) }}</textarea></label>
    <fieldset>
        <legend>Créneaux de jeu</legend>
        @foreach ($slots as $slot)
            <label class="account-check"><input type="checkbox" name="availability_slots[]" value="{{ $slot }}" @checked(in_array($slot, (array) old('availability_slots', session()->hasOldInput() ? [] : $player->availability_slots), true))>{{ $slot }}</label>
        @endforeach
    </fieldset>
    <label>Précisions sur tes disponibilités<textarea name="availability" rows="3" maxlength="240" placeholder="Ex. mardi et jeudi après 19 h">{{ old('availability', $player->availability) }}</textarea></label>
    <button type="submit" class="btn btn-primary">Enregistrer mon profil</button>
</form>
@endsection
