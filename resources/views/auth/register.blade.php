

<!doctype html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="icon" type="image/png" href="https://cloud.serverxox.my.id/public/images/favicon.png" />
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="fonts/icomoon/style.css">
<link rel="stylesheet" href="https://cloud.serverxox.my.id//public/css/owl.carousel.min.css">
<link rel="stylesheet" href="https://cloud.serverxox.my.id//public/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cloud.serverxox.my.id//public/css/style2.css">
<title>Register</title>
</head>
<body>
<div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-image: url('https://cloud.serverxox.my.id/public/images/bg_3.jpg');"></div>
        <div class="contents order-2 order-md-1">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-7">
                    <h3>Register to<img src="https://cloud.serverxox.my.id/public/images/logo.png" width="150px" height="50px"></h3>
                    <p class="mb-4">Program Kasir, Mudah digunakan.</p>
                    <div id="hasil"></div>
                    <form method="POST" class="form-signup" autocomplete="off" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
							<div class="col-md-12">
                                <input id="name" type="text" placeholder="{{ _lang('Name') }}" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-md-12">
                                <input id="email" type="email" placeholder="{{ _lang('E-Mail Address') }}" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-md-12">
                                <input id="password" type="password" placeholder="{{ _lang('Password') }}" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-md-12">
                                <input id="password-confirm" type="password" class="form-control" placeholder="{{ _lang('Confirm Password') }}" name="password_confirmation" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-12">
                                <select class="form-control" name="jenis_langganan" id="extend_type" required>
                                    <option value="" disabled selected>Layanan</option>
                                    <option value="POS">
                                       POS
                                    </option>
                                    <option value="TRADING">
                                        TRADING
                                    </option>
                                    <option value="INTEGRATED">
                                        INTEGRATED
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-12">
                                <select class="form-control" name="cabang" id="extend_type" required>
                                    <option value="" disabled selected>Lisensi/Cabang</option>
                                    @for ($i = 1; $i <= 500; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
						<div class="form-group row">
							<div class="col-md-12 text-center">
								<button type="submit" class="btn btn-block btn-primary">
								{{ _lang('Create My Account') }}
                                </button>
							</div>
						</div>
                        <div class="form-group row mt-5">
							<div class="col-md-12 text-center">
							   {{ _lang('Already Have An Account?') }} 
                               <a href="{{ route('login') }}">{{ _lang('Log In Here') }}</a>
							</div>
						</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cloud.serverxox.my.id//public/js/jquery-3.3.1.min.js"></script>
<script src="https://cloud.serverxox.my.id//public/js/popper.min.js"></script>
<script src="https://cloud.serverxox.my.id//public/js/bootstrap.min.js"></script>
<script src="https://cloud.serverxox.my.id//public/js/main.js"></script>

</body>
</html>