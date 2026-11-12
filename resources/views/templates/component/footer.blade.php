<div class="footer">
  <div class="container">
    <div class="w3ls_footer_grid">
      <div class="col-md-6 w3ls_footer_grid_left">
        <div class="w3ls_footer_grid_left1">
          <h2>Subscribe to us</h2>
          <div class="w3ls_footer_grid_left1_pos">
            <form action="#" method="post">
              <input type="email" name="email" placeholder="Your email..." required="">
              <input type="submit" value="Send">
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-6 w3ls_footer_grid_right">
        <a href="{{ route('home') }}"><h2>One<span>Movies</span></h2></a>
      </div>
      <div class="clearfix"> </div>
    </div>
    <div class="col-md-5 w3ls_footer_grid1_left">
      <p>&copy; 2016 One Movies. All rights reserved | Design by <a href="http://w3layouts.com/">W3layouts</a></p>
    </div>
    <div class="col-md-7 w3ls_footer_grid1_right">
      <ul>
        <li>
          <a href="{{ route('movies') }}">Movies</a>
        </li>
        <li>
          <a href="{{ route('genre', 'action') }}">Action</a>
        </li>
        <li>
          <a href="{{ route('genre', 'adventure') }}">Adventure</a>
        </li>
        <li>
          <a href="{{ route('genre', 'comedy') }}">Comedy</a>
        </li>
        <li>
          <a href="{{ route('faq') }}">FAQ</a>
        </li>
        <li>
        </li>
        <li>
        </li>
      </ul>
    </div>
    <div class="clearfix"> </div>
  </div>
</div>