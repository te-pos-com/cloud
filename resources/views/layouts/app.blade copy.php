<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ get_option('site_title', config('app.name')) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ get_favicon() }}">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DataTables -->
    <link href="{{ asset('public/backend/plugins/datatable/datatables.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('public/backend/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/backend/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet"
        type="text/css">
    <link href="{{ asset('public/backend/plugins/animate/animate.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/backend/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/backend/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" />
    <link href="{{ asset('public/backend/plugins/jquery-toast-plugin/jquery.toast.min.css') }}" rel="stylesheet" />

    <!-- App Css -->
    <link rel="stylesheet" href="{{ asset('public/backend/assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('public/backend/assets/css/styles.css?v=1.1') }}">

    <!-- Modernizr -->
    <script src="{{ asset('public/backend/assets/js/modernizr-2.8.3.min.js') }}"></script>


    @if(get_company_option('backend_direction',get_option('backend_direction')) == "rtl")
    <link rel="stylesheet" href="{{ asset('public/backend/assets/css/rtl/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/backend/assets/css/rtl/style.css') }}">
    @endif

    @include('layouts.others.languages')
    <style>
        div.scrollmenu {
            overflow: auto;
            white-space: nowrap;
            height:500px;
            float: left;
        }
        
        div.scrollmenu a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px;
            text-decoration: none;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Main Modal -->
    <div id="main_modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none m-3"></div>
                <div class="alert alert-primary d-none m-3"></div>
                <div class="modal-body overflow-hidden"></div>

            </div>
        </div>
    </div>

    <!-- Secondary Modal -->
    <div id="secondary_modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title mt-0 text-dark"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none m-3"></div>
                <div class="alert alert-primary d-none m-3"></div>
                <div class="modal-body overflow-hidden"></div>
            </div>
        </div>
    </div>

    <!-- Preloader area start -->
    <div id="preloader"></div>
    <!-- Preloader area end -->

    <!--Header Nav-->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand text-md-center"
                href="{{ route('dashboard') }}">{{ get_option('site_title', config('app.name')) }}</a>
            <button class="btn btn-link btn-sm mr-auto" id="sidebarToggle" href="#">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown animate-dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false"><i class="ti-user"></i> {{ Auth::user()->name }}</a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        @if(Auth::user()->user_type == 'user' && get_option('membership_system') == 'enabled')
							<a class="dropdown-item" href="{{ route('membership.extend') }}"><i class="ti-id-badge"></i> {{ _lang('Extend Membership') }}</a>
						@endif
                        <a class="dropdown-item" href="{{ route('profile.index') }}"><i class="ti-user"></i>
                            {{ _lang('My Profile') }}</a>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="ti-settings"></i>
                            {{ _lang('Profile Settings') }}</a>
                        <a class="dropdown-item" href="{{ route('profile.change_password') }}"><i
                                class="ti-exchange-vertical"></i> {{ _lang('Change Password') }}</a>
                        @if(Auth::user()->user_type == 'admin')      
                        <a class="dropdown-item" href="{{ route('settings.update_settings') }}"><i
                                class="ti-panel"></i> {{ _lang('System Settings') }}</a>
                        @elseif(Auth::user()->user_type == 'user')   
                        <a class="dropdown-item" href="{{ route('company.change_settings') }}"><i
                                class="ti-settings"></i> {{ _lang('Company Settings') }}</a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ url('logout') }}"><i class="ti-power-off"></i>
                            {{ _lang('Logout') }}</a>
                    </div>
                </li>
            </ul>

        </div>
    </nav>
    <!--End Header Nav-->

    <div id="layoutSidenav" class="container-fluid d-flex align-items-stretch">
        <div id="layoutSidenav_nav">
            <span class="close-mobile-nav"><i class="ti-close"></i></span>
            <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">

                <div class="sidebar-user">
                    <a href="javascript: void(0);">
                        <img src="{{ profile_picture() }}" alt="user-image" height="42"
                            class="rounded-circle shadow-sm">
                        <span class="sidebar-user-name">{{ Auth::user()->name }}</span>
                    </a>
                </div>

                <div class="sb-sidenav-menu">
                    <div class="nav">
                        @if (jenis_langganan()=="POS")
                            @include('layouts.menus.pos.'.Auth::user()->user_type)
                        @elseif (jenis_langganan()=="TRADING")
                            @include('layouts.menus.trading.'.Auth::user()->user_type)
                        @else
                            @include('layouts.menus.'.Auth::user()->user_type)
                        @endif
                    </div>
                </div>
            </nav>
        </div>
        <!--ENd layoutSidenav_nav-->

        <div id="layoutSidenav_content">
            <main>
                @if(get_option('membership_system') == 'enabled' && Auth::user()->user_type == 'user')
                    @if( Auth::user()->valid_to < date('Y-m-d'))
                        <div class="alert alert-danger">
                            <b>{{ _lang('Your membership has expired. Please renew your membership !') }}</b>
                            <a href="{{ route('membership.extend') }}">{{ _lang('Extend Membership') }}</a>
                        </div>
                    @endif
				@endif

                <div class="alert alert-success alert-dismissible" id="main_alert" role="alert">
                    <button type="button" id="close_alert" class="close">
						<span aria-hidden="true"><i class="ti-close"></i></span>
					</button>
                    <span class="msg"></span>
                </div>

                @yield('content')
            </main>

        </div>
        <!--End layoutSidenav_content-->

    </div>
    <!--End layoutSidenav-->


    <!-- Core Js  -->
    <script src="{{ asset('public/backend/assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('public/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('public/backend/assets/js/print.js') }}"></script>
    <script src="{{ asset('public/backend/assets/js/pace.min.js') }}"></script>
    <script src="{{ asset('public/backend/assets/js/clipboard.min.js') }}"></script>
    <script src="{{ asset('public/backend/plugins/moment/moment.js') }}"></script>

    <!-- Datatable js -->
    <script src="{{ asset('public/backend/plugins/datatable/datatables.min.js') }}"></script>

    <script src="{{ asset('public/backend/plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('public/backend/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('public/backend/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('public/backend/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('public/backend/plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('public/backend/plugins/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ asset('public/backend/plugins/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
    <script src="{{ asset('public/backend/plugins/number/jquery.number.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('public/backend/assets/js/scripts.js?v=1.1') }}"></script>

    <script type="text/javascript">
    (function($) {

        "use strict";

        //Show Success Message
        @if(Session::has('success'))
			$("#main_alert > span.msg").html(" {{ session('success') }} ");
			$("#main_alert").addClass("alert-success").removeClass("alert-danger");
			$("#main_alert").css('display', 'block');
        @endif

        //Show Single Error Message
        @if(Session::has('error'))
			$("#main_alert > span.msg").html(" {{ session('error') }} ");
			$("#main_alert").addClass("alert-danger").removeClass("alert-success");
			$("#main_alert").css('display', 'block');
        @endif

        @php $i = 0; @endphp

        @foreach($errors->all() as $error)
			@if($loop-> first)
				$("#main_alert > span.msg").html("<i class='ti-info-alt'></i> {{ $error }} ");
				$("#main_alert").addClass("alert-danger").removeClass("alert-success");
			@else
				$("#main_alert > span.msg").append("<br><i class='ti-info-alt'></i> {{ $error }} ");
			@endif

			@if($loop-> last)
				$("#main_alert").css('display', 'block');
			@endif

        	@if(isset($errors-> keys()[$i]))
				var name = "{{ $errors->keys()[$i] }}";

				$("input[name='" + name + "']").addClass('error is-invalid');
				$("select[name='" + name + "'] + span").addClass('error is-invalid');

				$("input[name='" + name + "'], select[name='" + name + "']").parent().append(
					"<span class='v-error'>{{$error}}</span>");
        	@endif

        	@php $i++; @endphp

        @endforeach

    })(jQuery);
    $(function(){				
        $('.price').on('change',function(){
            console.log('Change event.');
            var val = $('.price').val();
            console.log( val !== '' ? val : '(empty)' );
        });
    });    
    </script>
   
    <!-- Custom JS -->
    @yield('js-script')

</body>

</html>