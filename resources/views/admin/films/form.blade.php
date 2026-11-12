@extends('admin.layout')

@section('admin-content')
  <div class="tittle-head">
    <h4 class="latest-text">{{ $formTitle }}</h4>
  </div>

  <form action="{{ $formAction }}" method="post">
    @csrf
    @if ($film->exists)
      @method('PUT')
    @endif

    <div class="form-group">
      <label for="title">Judul Film</label>
      <input id="title" class="form-control" type="text" name="title" value="{{ old('title', $film->title) }}" required maxlength="255">
      @error('title')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label for="sinopsis">Sinopsis</label>
      <textarea id="sinopsis" class="form-control" name="sinopsis" rows="5" required maxlength="10000">{{ old('sinopsis', $film->sinopsis) }}</textarea>
      @error('sinopsis')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label for="year">Tahun Rilis</label>
      <input id="year" class="form-control" type="number" name="year" value="{{ old('year', $film->year) }}" min="1888" max="{{ now()->year + 10 }}" required>
      @error('year')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label for="poster">Path Poster</label>
      <input id="poster" class="form-control" type="text" name="poster" value="{{ old('poster', $film->poster) }}" placeholder="storage/images/m1.jpg" required maxlength="2048">
      <p class="help-block">Gunakan path gambar yang tersedia, misalnya <code>storage/images/m1.jpg</code>.</p>
      @error('poster')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label for="genre_id">Genre</label>
      <select id="genre_id" class="form-control" name="genre_id" required>
        <option value="">Pilih genre</option>
        @foreach ($genres as $genre)
          <option value="{{ $genre->id }}" @selected(old('genre_id', $film->genre_id) == $genre->id)>{{ $genre->name }}</option>
        @endforeach
      </select>
      @error('genre_id')<div class="alert alert-danger">{{ $message }}</div>@enderror
    </div>

    <button class="btn btn-warning" type="submit">Simpan</button>
    <a class="btn btn-default" href="{{ route('admin.films.index') }}">Batal</a>
  </form>
@endsection
