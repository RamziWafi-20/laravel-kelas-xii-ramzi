@extends('admin.layout')

@section('admin-content')
  <div class="tittle-head">
    <h4 class="latest-text">Kelola Film</h4>
  </div>

  <div class="admin-actions">
    <a class="btn btn-warning" href="{{ route('admin.films.create') }}">Tambah Film</a>
    <form action="{{ route('admin.films.index') }}" method="get" class="form-inline">
      <input class="form-control" type="search" name="search" value="{{ $search }}" placeholder="Cari judul atau tahun">
      <button class="btn btn-default" type="submit">Cari</button>
      @if ($search !== '')
        <a class="btn btn-link" href="{{ route('admin.films.index') }}">Reset</a>
      @endif
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>Poster</th>
          <th>Judul</th>
          <th>Tahun</th>
          <th>Genre</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($films as $film)
          <tr>
            <td><img src="{{ $film->poster_url }}" alt="{{ $film->title }}" width="55"></td>
            <td>{{ $film->title }}</td>
            <td>{{ $film->year }}</td>
            <td>{{ $film->genre?->name ?? '—' }}</td>
            <td>
              <div class="admin-actions">
                <a class="btn btn-primary btn-xs" href="{{ route('admin.films.edit', $film) }}">Edit</a>
                <a class="btn btn-default btn-xs" href="{{ route('movies.show', $film) }}">Lihat</a>
                <form action="{{ route('admin.films.destroy', $film) }}" method="post" onsubmit="return confirm('Hapus film ini beserta komentar dan daftar cast-nya?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5">Film tidak ditemukan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $films->links() }}
@endsection
