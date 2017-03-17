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


{!! Html::style('assets/css/bootstrap.css') !!}
{!! Html::style('assets/css/bootstrap-material-design.css') !!}
{!! Html::style('assets/css/ripples.css') !!}
{!! Html::style('assets/css/font-awesome.css') !!}
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
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container-fluid  col-md-offset-1 col-md-10">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">

                        <div >
                            <img src="{{ asset('/images/zain.jpg') }}" width="25" height="25">
                            {{'Fusion Confort'}}
                        </div>

                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/auth/login') }}">Login</a></li>
                         <!--  <li><a href="url('/register') ">Register</a></li>  -->
                        @else

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Usuario: {{ Auth::user()->firstName }} &nbsp; <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{!! url('/auth/logout') !!}">Cerrar Sesión</a></li>
                                </ul>
                            </li>

                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

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

</body>
</html>
