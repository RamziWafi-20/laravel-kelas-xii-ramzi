<div class="header">
  <div class="container">
    <div class="w3layouts_logo">
      <a href="{{ route('home') }}"><h1>One<span>Movies</span></h1></a>
    </div>
    <div class="w3_search">
      <form action="{{ route('home') }}" method="get">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search" required>
        <input type="submit" value="Go">
      </form>
    </div>
    <div class="w3l_sign_in_register">
      <ul>
        @auth
          <li>{{ Auth::user()->name }}</li>
          @if (Auth::user()->role === 'admin')
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          @endif
          <li>
            <form action="{{ route('logout') }}" method="post" style="display:inline">
              @csrf
              <button type="submit" class="auth-logout" style="border:0;background:none;color:inherit;font:inherit;cursor:pointer;padding:0">Logout</button>
            </form>
          </li>
        @else
          <li><a href="#myModal" data-toggle="modal" data-target="#myModal">Login</a></li>
        @endauth
      </ul>
    </div>
    <div class="clearfix"> </div>
    @if (session('status'))
      <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif
  </div>
</div>