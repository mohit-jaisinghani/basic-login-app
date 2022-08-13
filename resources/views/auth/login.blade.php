@extends('layout')
  
@section('content')
<main class="login-form">
  <div class="cotainer">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header">Login</div>
                  <div class="card-body">
  
                      <form action="{{ route('login.post') }}" method="POST" id="login_form">
                          @if(Session::has('message'))
                            <div>
                              <p class="alert {{ Session::get('alert-class') }}">{{ Session::get('message') }}</p>
                            </div>
                          @endif
                          @csrf
                          <div class="form-group row">
                              <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                              <div class="col-md-6">
                                  <input type="text" id="email_address" class="form-control" name="email" required autofocus>
                                  @if ($errors->has('email'))
                                      <span class="text-danger">{{ $errors->first('email') }}</span>
                                  @endif
                              </div>
                          </div>
  
                          <div class="form-group row">
                              <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                              <div class="col-md-6">
                                  <input type="password" id="password" class="form-control" name="password" required>
                                  @if ($errors->has('password'))
                                      <span class="text-danger">{{ $errors->first('password') }}</span>
                                  @endif
                              </div>
                          </div>
  
                          <div class="col-md-6 offset-md-4">
                              @if($data['remainingAttempts'] <= 0)
                                <button type="submit" disabled class="btn btn-primary login_btn">
                                    Login
                                </button>
                              @else
                                <button type="submit" class="btn btn-primary login_btn">
                                  Login
                                </button>
                              @endif
                          </div>
                      </form>
                        
                  </div>
              </div>
          </div>
      </div>
  </div>
</main>
<script type="text/javascript">
  $(document).ready(function() {
    if({{$data['remainingAttempts']}} == 0 && $(".login_btn").is(":disabled") == true) {
      setTimeout(function(){  
          $(".login_btn").prop('disabled', false);
      }, 30000);
    }
  })
</script>
@endsection