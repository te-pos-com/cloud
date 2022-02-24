

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
<title>Lupa password</title>
</head>
<body>
<div class="d-lg-flex half">
<div class="bg order-1 order-md-2" style="background-image: url('{{url('')}}/public/backend/images/bg_2.webp');"></div>
        <div class="contents order-2 order-md-1">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-7">
                    <div id="hasil"></div>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="{{ _lang('E-Mail Address') }}" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ _lang('Password') }}" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ _lang('Confirm Password') }}" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-block btn-primary">
                                    {{ _lang('Reset Password') }}
                                </button>
                            </div>
                        </div>
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