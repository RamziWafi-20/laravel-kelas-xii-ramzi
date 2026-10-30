@extends('admin.layout')

@section('admin-content')
  <div class="admin-page-heading">
    <div>
      <h1>Kelola User</h1>
      <p>Lihat akun terdaftar dan hapus akun yang tidak diperlukan.</p>
    </div>
  </div>

  @if ($errors->has('user'))
    <div class="alert alert-danger" role="alert">{{ $errors->first('user') }}</div>
  @endif
  <form action="{{ route('admin.users.index') }}" method="get" class="admin-actions" style="margin-bottom: 18px;">
    <input class="form-control" type="search" name="search" value="{{ $search }}" placeholder="Cari nama atau email">
    <button class="btn btn-warning" type="submit">Cari</button>
    @if ($search !== '')
      <a class="btn btn-default" href="{{ route('admin.users.index') }}">Reset</a>
    @endif
  </form>

  <section class="admin-panel">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Bergabung</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse ($users as $user)
            <tr>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ ucfirst($user->role ?? 'user') }}</td>
              <td>{{ $user->created_at?->format('d M Y') ?? '—' }}</td>
              <td>
                @if ($user->id !== auth()->id())
                  <form action="{{ route('admin.users.destroy', $user) }}" method="post" onsubmit="return confirm('Hapus user ini beserta komentarnya?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                  </form>
                @else
                  <span class="admin-muted">Akun aktif</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="5">User tidak ditemukan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
  {{ $users->links() }}
@endsection
