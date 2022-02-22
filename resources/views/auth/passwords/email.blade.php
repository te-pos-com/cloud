

<!doctype html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="https://cloud.serverxox.my.id/public/images/favicon.png" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="fonts/icomoon/style.css">
<link rel="stylesheet" href="https://cloud.serverxox.my.id//public/css/owl.carousel.min.css">
<link rel="stylesheet" href="https://cloud.serverxox.my.id//public/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cloud.serverxox.my.id//public/css/style2.css">
<title>Lupa password</title>
</head>
<body>
<div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-image: url('https://cloud.serverxox.my.id/public/images/bg_2.JPG');"></div>
        <div class="contents order-2 order-md-1">
            <div class="container">
                <div class="row align-items-center justify-content-center">

                    <div class="col-md-7">
                    <div class="login-form-head">
					    <h4>{{ _lang('Reset Password') }}</h4>
				    </div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" class="form-signin" action="{{ route('password.email') }}" autocomplete="off">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" placeholder="{{ _lang('Enter your Email') }}" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-block btn-primary">
                                    {{ _lang('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group row mt-5">
							<div class="col-md-12 text-center">
							   Kembali Ke halaman Login ?
                               <a href="{{ route('login') }}">Klik Disini</a>
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