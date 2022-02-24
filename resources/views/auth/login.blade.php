

<!doctype html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="icon" type="image/png" href="{{url('')}}/public/uploads/icon/favicon.png" />
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="fonts/icomoon/style.css">
<link rel="stylesheet" href="{{url('')}}/public/backend/assets/css/owl.carousel.min.css">
<link rel="stylesheet" href="{{url('')}}/public/backend/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="{{url('')}}/public/backend/assets/css/style2.css">
<title>Login</title>
</head>
<body>
<div class="d-lg-flex half">
<div class="bg order-1 order-md-2" style="background-image: url('{{url('')}}/public/backend/images/bg_1.webp');"></div>
        <div class="contents order-2 order-md-1">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-7">
                    <h3>Login to<img src="{{url('')}}/public/backend/images/logo.png" width="150px" height="50px"></h3>
                    <p class="mb-4">Aplikasi Kasir, Mudah digunakan.</p>
                    <div id="hasil"></div>
                        @if(Session::has('error'))
                            <div class="alert alert-danger text-center">
                                <strong>{{ session('error') }}</strong>
                            </div>
                        @endif
    					
    					@if(Session::has('registration_success'))
                            <div class="alert alert-success text-center">
                                <strong>{{ session('registration_success') }}</strong>
                            </div>
                        @endif
        		<form method="POST" class="form-signin" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="{{ _lang('Email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
						    <div class="col-md-12">	

								<input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ _lang('Password') }}" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="text-left">
							<div class="custom-control custom-checkbox mb-3">
								<input type="checkbox" name="remember" class="custom-control-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
								<label class="custom-control-label" for="remember">{{ _lang('Remember Me') }}</label>
							</div>
						</div>
                    
                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-block">
                                    {{ _lang('Login') }}
                                </button>
        
                                @if(get_option('google_login') == 'enabled')
                                    <a href="{{ url('/login/google') }}" class="btn btn-google btn-block"> {{ _lang('Continue With Google') }}</a>
        						@endif
        
                                @if(get_option('facebook_login') == 'enabled')
                                    <a href="{{ url('/login/facebook') }}" class="btn btn-facebook btn-block"> {{ _lang('Continue With Facebook') }}</a>
                                @endif
                            </div>
                        </div>
        				
        				<p class="cl-grey text-center">
                            <br/>
                            <br/>
                            Lupa akun te-pos ? <a href="{{ route('password.request') }}">Klik Disini</a>
                            <br/>
                            Belum Punya Akun te-pos ? <a href="{{ url('register') }}">Daftar Disini</a>
                        </p>
        				
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{url('')}}/public/backend/assets/js/jquery-3.3.1.min.js"></script>
<script src="{{url('')}}/public/backend/assets/js/popper.min.js"></script>
<script src="{{url('')}}/public/backend/assets/js/bootstrap.min.js"></script>
<script src="{{url('')}}/public/backend/assets/js/main.js"></script>

</body>
</html>