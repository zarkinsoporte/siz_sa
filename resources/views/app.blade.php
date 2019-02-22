<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ env('EMPRESA_NAME')}}</title>
    <!-- Styles -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
    <!-- Material Design fonts -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">      
    <link rel="shortcut icon" href="images/favicons/IconZrk.ico" type="image/x-icon" >
                                                         
    <script data-require="jquery" data-semver="3.3.1" src=" https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src=" https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>

    <![endif]-->
{!! Html::style('assets/css/bootstrap.css') !!}
{!! Html::style('assets/css/bootstrap-switch.min.css') !!}
{!! Html::style('assets/css/bootstrap-switch.css') !!}
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css" type="text/css">
{!! Html::style('assets/css/sb-admin.css') !!}
{!! Html::style('assets/css/responsive.css') !!}



    <style>
        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .side-nav>li>ul>li>ul>li>a {
            display: block;
            color: #e8e8e8;
            padding: 8px 26px 0% 25%;
            text-decoration: none;
        }

        /* Change the link color on hover */
        .side-nav>li>ul>li>ul>li>a:hover {
            background-color: #555;
            color: white;
        }
        thead input {
        width: 100%;
    }
    .dataTables_processing {
        top: 64px !important;
        z-index: 11000 !important;
    }
    .dataTables_wrapper .dataTables_filter {
float: right;
text-align: right;
visibility: hidden;
}
    </style>


    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>


</head>
<body>
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

                    <a class="navbar-brand" href="{!! url('home') !!}" style="color: white">
                      <div  style=" display: inline-block;
                    
  position: absolute;
  top:  10px; 
  left: 10px;
    ">
    <img src="{{ asset('/images/lZRK.png') }}" width="160px" height="35px"></div>
                    
                    </a>
                </div>
                <!-- Top Menu Items -->
                <ul class="nav navbar-right top-nav hidden-xs">
                    <li>
                    <a href="{!! url('Mod01_Produccion/Noticias') !!}"><i class="fa fa-bell"></i> <span class="badge badge-danger"> {{Auth::user()->getCountNotificacion()}}</span></a>
                    </li>  
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


            </nav>
        </div>
           



</body>
       
 {!! Html::script('assets/js/bootstrap-datepicker.js') !!}
 {!! Html::script('assets/js/bootstrap-datepicker.es.min.js') !!}
 

{!! Html::script('assets/js/bootstrap-switch.js') !!}

<!--<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>-->

{!! Html::script('assets/js/bootstrap.min.js') !!}
<!--<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
{!! Html::script('assets/js/moment.min.js') !!}
{!! Html::script('assets/js/shortcut.js') !!}
<!-- Include Date Range Picker -->


<script>

    $(document).ready(function (event) {

            $('.toggle').bootstrapSwitch();
$('.dropdown-toggle').dropdown();


        @yield('script')

    });


</script>
  
</html>
