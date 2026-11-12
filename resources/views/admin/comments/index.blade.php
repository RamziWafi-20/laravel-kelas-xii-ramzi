@extends('admin.layout')

@section('admin-content')
  <div class="admin-page-heading">
    <div>
      <h1>Kelola Komentar</h1>
      <p>Daftar komentar pengguna pada film.</p>
    </div>
  </div>

  <form action="{{ route('admin.comments.index') }}" method="get" class="admin-actions" style="margin-bottom: 18px;">
    <input class="form-control" type="search" name="search" value="{{ $search }}" placeholder="Cari komentar, user, atau film">
    <button class="btn btn-warning" type="submit">Cari</button>
    @if ($search !== '')
      <a class="btn btn-default" href="{{ route('admin.comments.index') }}">Reset</a>
    @endif
  </form>

  <section class="admin-panel">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead><tr><th>User</th><th>Film</th><th>Komentar</th><th>Waktu</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse ($comments as $comment)
            <tr>
              <td>{{ $comment->user?->name ?? 'Pengguna tidak dikenal' }}</td>
              <td>{{ $comment->film?->title ?? 'Film tidak ditemukan' }}</td>
              <td>{{ $comment->comment }}</td>
              <td>{{ $comment->created_at?->diffForHumans() ?? '—' }}</td>
              <td>
                <form action="{{ route('admin.comments.destroy', $comment) }}" method="post" onsubmit="return confirm('Hapus komentar ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5">Komentar tidak ditemukan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
  {{ $comments->links() }}
@endsection
