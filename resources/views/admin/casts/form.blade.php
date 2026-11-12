@extends('admin.layout')

@section('admin-content')
  <div class="admin-page-heading">
    <div>
      <h1>{{ $formTitle }}</h1>
      <p>Isi data cast dengan benar.</p>
    </div>
  </div>

  <form action="{{ $formAction }}" method="post">
    @csrf
    @if ($cast->exists)
      @method('PUT')
    @endif
    <div class="form-group">
      <label for="name">Nama</label>
      <input id="name" class="form-control" name="name" value="{{ old('name', $cast->name) }}" maxlength="45" required>
      @error('name')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <label for="age">Umur</label>
      <input id="age" class="form-control" type="number" name="age" value="{{ old('age', $cast->age) }}" min="1" max="150" required>
      @error('age')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <label for="bio">Bio</label>
      <textarea id="bio" class="form-control" name="bio" rows="5" maxlength="10000" required>{{ old('bio', $cast->bio) }}</textarea>
      @error('bio')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-warning" type="submit">Simpan</button>
    <a class="btn btn-default" href="{{ route('admin.casts.index') }}">Batal</a>
  </form>
@endsection
