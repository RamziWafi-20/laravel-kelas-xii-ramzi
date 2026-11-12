<!DOCTYPE html>
<html lang="en">
@include('templates.component.head')
<style>
  body.admin-body { background: #0f172a; color: #e5e7eb; font-family: Arial, sans-serif; }
  .admin-topbar { min-height: 76px; display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 14px 28px; background: #1e293b; border-bottom: 1px solid #334155; }
  .admin-brand { color: #fff; font-size: 23px; font-weight: 700; text-decoration: none; white-space: nowrap; }
  .admin-brand span { color: #ff8d1b; }
  .admin-topbar-actions, .admin-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
  .admin-topbar-actions form, .admin-actions form { margin: 0; }
  .admin-shell { display: grid; grid-template-columns: 250px minmax(0, 1fr); min-height: calc(100vh - 76px); }
  .admin-sidebar { background: #1e293b; border-right: 1px solid #334155; padding: 24px 14px; }
  .admin-sidebar h4 { padding: 0 12px; margin: 0 0 16px; color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase; }
  .admin-sidebar a { display: block; padding: 12px; margin-bottom: 5px; border-radius: 8px; color: #cbd5e1; text-decoration: none; }
  .admin-sidebar a:hover, .admin-sidebar a.active { color: #fff; background: #f58212; }
  .admin-content { min-width: 0; padding: 30px; }
  .admin-page-heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 25px; }
  .admin-page-heading h1 { margin: 0 0 6px; color: #fff; font-size: 26px; font-weight: 700; }
  .admin-page-heading p { margin: 0; color: #94a3b8; }
  .admin-stat { display: flex; align-items: center; gap: 16px; min-height: 112px; padding: 20px; margin-bottom: 20px; border: 1px solid #334155; border-radius: 14px; background: #1e293b; }
  .admin-stat-icon { display: grid; place-items: center; width: 58px; height: 58px; flex: 0 0 58px; border-radius: 12px; background: #334155; color: #ff8d1b; font-size: 23px; }
  .admin-stat strong { display: block; color: #fff; font-size: 30px; line-height: 1.1; }
  .admin-stat span { display: block; margin-top: 5px; color: #94a3b8; }
  .admin-panel { overflow: hidden; margin-bottom: 20px; border: 1px solid #334155; border-radius: 14px; background: #1e293b; }
  .admin-panel-heading { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 18px 22px; border-bottom: 1px solid #334155; }
  .admin-panel-heading h2 { margin: 0; color: #fff; font-size: 18px; font-weight: 700; }
  .admin-content .table-responsive { margin: 0; }
  .admin-content .table { margin: 0; color: #e5e7eb; }
  .admin-content .table > thead > tr > th { color: #94a3b8; background: #172235; border-color: #334155; }
  .admin-content .table > tbody > tr > td { vertical-align: middle; border-color: #334155; }
  .admin-content .table-striped > tbody > tr:nth-of-type(odd) { background: #202d40; }
  .admin-content .form-control { color: #e5e7eb; background: #172235; border-color: #475569; }
  .admin-content .pagination > li > a, .admin-content .pagination > li > span { color: #e5e7eb; background: #1e293b; border-color: #334155; }
  .admin-comment { padding: 18px 22px; border-bottom: 1px solid #334155; }
  .admin-comment:last-child { border-bottom: 0; }
  .admin-comment h5 { margin: 0 0 8px; color: #fff; font-weight: 700; }
  .admin-comment p { color: #cbd5e1; }
  .admin-comment small, .admin-muted { color: #94a3b8; }
  .admin-content label { color: #cbd5e1; }
  .admin-content .help-block { color: #94a3b8; }
  @media (max-width: 767px) {
    .admin-topbar { align-items: flex-start; flex-direction: column; padding: 14px 18px; }
    .admin-shell { grid-template-columns: 1fr; }
    .admin-sidebar { display: flex; flex-wrap: wrap; gap: 4px; padding: 12px; border-right: 0; border-bottom: 1px solid #334155; }
    .admin-sidebar h4 { width: 100%; }
    .admin-sidebar a { padding: 9px 10px; }
    .admin-content { padding: 20px 14px; }
    .admin-page-heading { align-items: flex-start; flex-direction: column; }
  }
</style>
<body class="admin-body">
  <header class="admin-topbar">
    <a class="admin-brand" href="{{ route('home') }}">One<span>Movies</span> <small class="label label-warning">ADMIN</small></a>
    <div class="admin-topbar-actions">
      <a class="btn btn-warning" href="{{ route('admin.films.create') }}"><i class="fa fa-plus"></i> Tambah Film</a>
      <a class="btn btn-default" href="{{ route('home') }}"><i class="fa fa-globe"></i> Buka Website</a>
      <form action="{{ route('logout') }}" method="post">
        @csrf
        <button class="btn btn-danger" type="submit">Logout</button>
      </form>
    </div>
  </header>
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <h4>Menu Utama</h4>
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa fa-dashboard"></i> &nbsp; Dashboard</a>
      <a href="{{ route('admin.films.index') }}" class="{{ request()->routeIs('admin.films.*') ? 'active' : '' }}"><i class="fa fa-film"></i> &nbsp; Kelola Film</a>
      <h4>Interaksi &amp; User</h4>
      <a href="{{ route('admin.comments.index') }}" class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}"><i class="fa fa-comments"></i> &nbsp; Komentar</a>
      <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fa fa-user"></i> &nbsp; Kelola User</a>
      <a href="{{ route('admin.casts.index') }}" class="{{ request()->routeIs('admin.casts.*', 'admin.actors.*') ? 'active' : '' }}"><i class="fa fa-users"></i> &nbsp; Cast &amp; Aktor</a>
      <h4>Aksi Cepat</h4>
      <a href="{{ route('home') }}"><i class="fa fa-external-link"></i> &nbsp; Lihat Website</a>
    </aside>
    <main class="admin-content">
      @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
      @endif
      @yield('admin-content')
    </main>
  </div>
</body>
</html>
