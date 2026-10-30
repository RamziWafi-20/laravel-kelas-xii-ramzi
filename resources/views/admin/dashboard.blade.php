@extends('admin.layout')

@section('admin-content')
    <div class="admin-page-heading">
      <div>
        <h1>Dashboard Ringkasan</h1>
        <p>Pantau statistik film, cast, komentar, dan aktivitas terkini.</p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4 col-sm-6">
        <div class="admin-stat"><div class="admin-stat-icon"><i class="fa fa-film"></i></div><div><strong>{{ $filmCount }}</strong><span>Total Film</span></div></div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="admin-stat"><div class="admin-stat-icon"><i class="fa fa-users"></i></div><div><strong>{{ $castCount }}</strong><span>Total Cast / Aktor</span></div></div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="admin-stat"><div class="admin-stat-icon"><i class="fa fa-tags"></i></div><div><strong>{{ $genreCount }}</strong><span>Total Genre</span></div></div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="admin-stat"><div class="admin-stat-icon"><i class="fa fa-comments"></i></div><div><strong>{{ $commentCount }}</strong><span>Total Ulasan / Komentar</span></div></div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="admin-stat"><div class="admin-stat-icon"><i class="fa fa-user"></i></div><div><strong>{{ $userCount }}</strong><span>Pengguna Terdaftar</span></div></div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-7">
        <section class="admin-panel">
          <div class="admin-panel-heading">
            <h2><i class="fa fa-film text-warning"></i> Film Terbaru</h2>
            <a class="btn btn-default btn-sm" href="{{ route('admin.films.index') }}">Lihat Semua ({{ $filmCount }})</a>
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead><tr><th>Poster</th><th>Judul</th><th>Genre</th><th>Tahun</th><th>Aksi</th></tr></thead>
              <tbody>
                @forelse ($recentFilms as $film)
                  <tr>
                    <td><img src="{{ $film->poster_url }}" alt="{{ $film->title }}" width="45"></td>
                    <td>{{ $film->title }}</td>
                    <td>{{ $film->genre?->name ?? '—' }}</td>
                    <td>{{ $film->year }}</td>
                    <td><a class="btn btn-primary btn-xs" href="{{ route('admin.films.edit', $film) }}" aria-label="Edit {{ $film->title }}"><i class="fa fa-edit"></i></a></td>
                  </tr>
                @empty
                  <tr><td colspan="5">Belum ada film.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>
      </div>
      <div class="col-lg-5">
        <section class="admin-panel" id="comments">
          <div class="admin-panel-heading">
            <h2><i class="fa fa-comments text-info"></i> Komentar Terbaru</h2>
            <a class="btn btn-default btn-sm" href="{{ route('admin.comments.index') }}">Lihat Semua</a>
          </div>
          @forelse ($comments as $comment)
            <div class="admin-comment">
              <h5>{{ $comment->user?->name ?? 'Pengguna tidak dikenal' }}</h5>
              <p class="admin-muted">pada film: <a href="{{ route('movies.show', $comment->film_id) }}">{{ $comment->film?->title ?? 'Film tidak ditemukan' }}</a></p>
              <p>{{ \Illuminate\Support\Str::limit($comment->comment, 150) }}</p>
              <small>{{ $comment->created_at?->diffForHumans() }}</small>
            </div>
          @empty
            <p class="admin-comment">Belum ada komentar.</p>
          @endforelse
        </section>
      </div>
    </div>
@endsection
