@extends('app')

@section('content')
    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">

            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo">{{Route::current()->getName()}}<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo" class="">

                        <li>
                            <a href="{!! url('USUARIOS') !!}"><i class="fa fa-fw fa-users"></i> Usuarios</a>
                        </li>

                </ul>
            </li>
            @include('partials.section-navbar')
        </ul>
    </div>
    <!-- /.navbar-collapse -->
    </nav>

    <div id="page-wrapper2">

        <div class="container-fluid" >

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">
                        {{Route::current()->getName()}}
                    </h3>
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="{!! url('home') !!}">ACTIVIDADES</a>
                        </li>
                        <li class="active">
                            <i class="fa fa-file"></i> {{Route::current()->getName()}}
                        </li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->
         <div class="container">
             @yield('subcontent-01')
         </div>



        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->
    </div>
    </div>
    <!-- /#wrapper -->
@endsection
