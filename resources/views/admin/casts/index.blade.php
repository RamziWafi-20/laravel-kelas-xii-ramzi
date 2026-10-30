@extends('admin.layout')

@section('admin-content')
  <div class="admin-page-heading">
    <div>
      <h1>Kelola Cast &amp; Aktor</h1>
      <p>Kelola data pemeran dan karakter film dalam satu halaman.</p>
    </div>
    <div class="admin-actions">
      <a class="btn btn-warning" href="{{ route('admin.casts.create') }}">Tambah Cast</a>
      <a class="btn btn-primary" href="{{ route('admin.actors.create') }}">Tambah Aktor</a>
    </div>
  </div>

  <form action="{{ route('admin.casts.index') }}" method="get" class="admin-actions" style="margin-bottom: 18px;">
    <input class="form-control" type="search" name="search" value="{{ $search }}" placeholder="Cari cast, karakter, atau film">
    <button class="btn btn-warning" type="submit">Cari</button>
    @if ($search !== '')
      <a class="btn btn-default" href="{{ route('admin.casts.index') }}">Reset</a>
    @endif
  </form>

  <section class="admin-panel">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead><tr><th>Nama Cast</th><th>Umur</th><th>Bio</th><th>Aktor / Karakter &amp; Film</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse ($casts as $cast)
            <tr>
              <td>{{ $cast->name }}</td>
              <td>{{ $cast->age }}</td>
              <td>{{ \Illuminate\Support\Str::limit($cast->bio, 100) }}</td>
              <td>
                @forelse ($cast->perans as $peran)
                  <div style="margin-bottom: 8px;">
                    <strong>{{ $peran->actor }}</strong>
                    <span class="admin-muted">— {{ $peran->film?->title ?? 'Film tidak ditemukan' }}</span>
                    <div class="admin-actions" style="margin-top: 5px;">
                      <a class="btn btn-primary btn-xs" href="{{ route('admin.actors.edit', $peran) }}">Edit Aktor</a>
                      <form action="{{ route('admin.actors.destroy', $peran) }}" method="post" onsubmit="return confirm('Hapus data aktor/peran ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-xs">Hapus Aktor</button>
                      </form>
                    </div>
                  </div>
                @empty
                  <span class="admin-muted">Belum ada data aktor.</span>
                @endforelse
                <a class="btn btn-default btn-xs" href="{{ route('admin.actors.create', ['cast_id' => $cast->id]) }}">Tambah Aktor</a>
              </td>
              <td>
                <div class="admin-actions">
                  <a class="btn btn-primary btn-xs" href="{{ route('admin.casts.edit', $cast) }}">Edit Cast</a>
                  <form action="{{ route('admin.casts.destroy', $cast) }}" method="post" onsubmit="return confirm('Hapus cast ini beserta data actor/perannya?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">Hapus Cast</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5">Data cast atau aktor tidak ditemukan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
  {{ $casts->links() }}
@endsection
