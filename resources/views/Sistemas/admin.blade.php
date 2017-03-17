@extends('app')

@section('content')
<div class="container-fluid col-md-offset-1 col-md-10">
    <div>


        <div id="wrapper">

            <!-- /. NAV TOP  -->
            <nav class="navbar-default navbar-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav" id="main-menu">

                        <li class="active-link">
                            <a href="{!! url('home') !!}"><i class="fa fa-circle-o-notch"></i>&nbsp; Administrador &nbsp;<span class="badge">1</span></a>
                        </li>

                    </ul>
                </div>

            </nav>
            <!-- /. NAV SIDE  -->
            <div id="page-wrapper" >
                <div id="page-inner">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>MODULO ADMINISTRADOR</h4>
                        </div>
                    </div>
                    <!-- /. ROW
                     <hr />
                   <div class="row">
                       <div class="col-lg-12 ">
                           <div class="alert alert-info">
                                <strong>Welcome Jhon Doe ! </strong> You Have No pending Task For Today.
                           </div>

                       </div>
                       </div>
                     <!-- /. ROW  -->
                    <div class="row text-center pad-top">





                        <div class="col-xs-6 col-md-2">

                            <a href="{!! url('users') !!}" class="thumbnail" style="color: darkgray">
                                <i class="fa fa-users fa-3x" style="margin-top: 10px" ></i>
                                <h4>Usuarios</h4>
                            </a>

                        </div>


                    </div>
                    <!-- /. ROW  -->


                    <!-- /. PAGE INNER  -->
                </div>
                <!-- /. PAGE WRAPPER  -->
            </div>
            <div class="footer">


                <div class="row">
                    <div class="col-lg-12" >

                    </div>
                </div>
            </div>


</div> <!-- cierra row-->
@endsection
