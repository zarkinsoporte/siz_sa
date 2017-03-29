<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'Fusion Confort' }}</title>

    <!-- Styles -->
    <!-- Material Design fonts -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

{!! Html::style('assets/css/bootstrap.css') !!}
{!! Html::style('assets/css/font-awesome.css') !!}
{!! Html::style('assets/css/sb-admin.css') !!}
{!! Html::style('assets/css/zarkin.css') !!}
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-inverse navbar-fixed-top"  style="background-color: #3a3327;" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <a class="navbar-brand" href="#" style="color: white">
                      <div  style=" display: inline-block;
    padding: 0px 8px;
    height: 28px;
    font-size: 18px;
    line-height: 28px;
    border-radius: 35px;
    background-color: #f1f1f1;" ><img src="{{ asset('/images/zain_b.png') }}" width="12px" height="23px" ></div>
                       &nbsp; SIZ
                    </a>
                </div>
                <!-- Top Menu Items -->
                <ul class="nav navbar-right top-nav hidden-xs">

                    <li class="dropdown">

                    @if (Auth::guest())
                     <a href="{{ url('/auth/login') }}" style="color: white">Login</a>
                        <!--  <li><a href="url('/register') ">Register</a></li>  -->
                    @else
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color: white"><i class="fa fa-user"></i>
                                &nbsp;{{ Auth::user()->firstName.' '.Auth::user()->lastName }} &nbsp;
                                <b class="caret"></b></a>





                        <ul class="dropdown-menu">
                            <li>
                                <a href="#"><i class="fa fa-fw fa-gear"></i> Configuración</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="{!! url('/auth/logout') !!}"><i class="fa fa-fw fa-power-off"></i> Cerrar Sesión</a>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
            @yield('content')

        <!-- jQuery -->
        <script src="js/jquery.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="js/bootstrap.min.js"></script>



</body>

<!-- Scripts -->
{!! Html::script('assets/js/jquery.min.js') !!}
{!! Html::script('assets/js/bootstrap.js') !!}
{!! Html::script('assets/js/material.js') !!}
{!! Html::script('assets/js/ripples.js') !!}
<script>
    $(document).ready(function (event) {

        $.material.init();
    });


</script>
</html>
