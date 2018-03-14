@extends('home')

@section('homecontent')

    <div class="container" >

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header">
                    Inicio
                    <small>Sistema Informatico Zarkin</small>
                </h3>

            </div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12 ">
                @include('partials.alertas')
                <div class="alert alert-info">
                    <strong>Bienvenido  ! </strong> sin tareas para hoy.
                </div>



            </div>
        </div>
    </div>
@endsection
