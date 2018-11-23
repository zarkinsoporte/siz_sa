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
        <style>
.div{
    font-family:arial;
}
        </style>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-6.5 col-md-8 col-sm-5 ">

                @include('partials.alertas')
                <div class="alert alert-info">
                    <strong>Bienvenido  ! </strong>Usted tiene <strong>{{Count($noticias)}} </strong> tareas por realizar
                </div>
                <blockquote class="trello-board-compact">
                        <a href="https://trello.com/b/1Res600w/zarkin">Trello Zarkin</a>
                      </blockquote>
                      <script src="https://p.trellocdn.com/embed.min.js"></script>
<a href="Mod01_Produccion/Noticias" button class="btn btn-primary" type="button">
 <div class="glyphicon glyphicon-envelope">  </div> Notificaciones   <span class="badge badge-danger"> {{Count($noticias)}}</span>
</button></a>
@endsection
 