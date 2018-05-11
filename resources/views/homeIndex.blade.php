@extends('home')

@section('homecontent')

    <div class="container" >

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-6.5 col-md-8 col-sm-5">
            <div class="hidden-lg"><br><br></div>
                <h3 class="page-header">
                    Inicio
                    <small>Sistema Informático Zarkin</small>
                </h3>

            </div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-6.5 col-md-8 col-sm-5 ">
                @include('partials.alertas')
                <div class="alert alert-info">
                    <strong>Bienvenido  ! </strong> sin tareas para hoy.
                </div>



            </div>
        </div>
    </div>
@endsection
