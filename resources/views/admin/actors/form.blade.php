@extends('admin.layout')

@section('admin-content')
  <div class="admin-page-heading">
    <div>
      <h1>{{ $formTitle }}</h1>
      <p>Hubungkan cast dengan film dan nama karakter.</p>
    </div>
  </div>

  <form action="{{ $formAction }}" method="post">
    @csrf
    @if ($peran->exists)
      @method('PUT')
    @endif
    <div class="form-group">
      <label for="cast_id">Cast</label>
      <select id="cast_id" class="form-control" name="cast_id" required>
        <option value="">Pilih cast</option>
        @foreach ($casts as $cast)
          <option value="{{ $cast->id }}" @selected(old('cast_id', $peran->cast_id) == $cast->id)>{{ $cast->name }}</option>
        @endforeach
      </select>
      @error('cast_id')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <label for="film_id">Film</label>
      <select id="film_id" class="form-control" name="film_id" required>
        <option value="">Pilih film</option>
        @foreach ($films as $film)
          <option value="{{ $film->id }}" @selected(old('film_id', $peran->film_id) == $film->id)>{{ $film->title }}</option>
        @endforeach
      </select>
      @error('film_id')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <label for="actor">Karakter / Nama Peran</label>
      <input id="actor" class="form-control" name="actor" value="{{ old('actor', $peran->actor) }}" maxlength="255" required>
      @error('actor')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-warning" type="submit">Simpan</button>
    <a class="btn btn-default" href="{{ route('admin.casts.index') }}">Batal</a>
  </form>
@endsection
