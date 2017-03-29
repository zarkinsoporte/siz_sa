@extends('app')

@section('content')


    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">

            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#actividades"><i class="fa fa-fw fa-dashboard"></i> Actividades <i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="actividades" class="">
                    @foreach ($actividades as $a)
                    <li>
                        <a href="{!! url($a) !!}">{{ $a }}</a>
                    </li>
                    @endforeach
                        <li class="divider"></li>
                </ul>
            </li>
                @include('partials.section-navbar')
        </ul>
    </div>
    <!-- /.navbar-collapse -->
    </nav>

    <div id="page-wrapper2">

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">
                        ACTIVIDADES
                        <small>Módulos</small>
                    </h3>
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="{!! url('home') !!}">ACTIVIDADES</a>
                        </li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12 ">
                    <div class="alert alert-info">
                        <strong>Welcome  ! </strong> sin tareas para hoy.
                    </div>

                </div>
            </div>
        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->
    </div>
    </div>
    <!-- /#wrapper -->
@endsection
