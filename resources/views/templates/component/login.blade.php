<div class="modal video-modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        Sign In & Sign Up
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
      </div>
      <section>
        <div class="modal-body">
          <div class="w3_login_module">
            <div class="module form-module">
              <div class="toggle"><i class="fa fa-times fa-pencil"></i>
              <div class="tooltip">Click Me</div>
              </div>
              <div class="form">
              <h3>Login to your account</h3>
              <form action="{{ route('login.store') }}" method="post">
                @csrf
                @if ($errors->has('credentials'))
                  <div class="alert alert-danger" role="alert">{{ $errors->first('credentials') }}</div>
                @endif
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" autocomplete="email" required="">
                @error('email')
                  <div class="alert alert-danger" role="alert">{{ $message }}</div>
                @enderror
                <input type="password" name="password" placeholder="Password" autocomplete="current-password" required="">
                @error('password')
                  <div class="alert alert-danger" role="alert">{{ $message }}</div>
                @enderror
                <input type="submit" value="Login">
              </form>
              </div>
              <div class="form">
              <h3>Create an account</h3>
              <form action="{{ route('register') }}" method="post">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Name / Username" autocomplete="name" required="">
                @error('name')
                  <div class="alert alert-danger" role="alert">{{ $message }}</div>
                @enderror
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" autocomplete="email" required="">
                @error('email')
                  <div class="alert alert-danger" role="alert">{{ $message }}</div>
                @enderror
                <input type="password" name="password" placeholder="Password (minimum 8 characters)" autocomplete="new-password" required="">
                @error('password')
                  <div class="alert alert-danger" role="alert">{{ $message }}</div>
                @enderror
                <input type="password" name="password_confirmation" placeholder="Confirm Password" autocomplete="new-password" required="">
                <input type="submit" value="Register">
              </form>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
<script>
  $('.toggle').click(function(){
    var $module = $(this).closest('.form-module');
    // Switches the Icon
    $(this).children('i').toggleClass('fa-pencil');
    // Switches the forms  
    $module.find('.form').animate({
    height: "toggle",
    'padding-top': 'toggle',
    'padding-bottom': 'toggle',
    opacity: "toggle"
    }, "slow");
  });
  @if (session('auth_form'))
    $(function () {
      @if (session('auth_form') === 'register')
        $('#myModal .toggle').trigger('click');
      @endif
      $('#myModal').modal('show');
    });
  @endif
</script>