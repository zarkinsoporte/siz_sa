

@extends('home')

@section('homecontent')

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-8 col-md-9 col-xs-10">
                <div class="hidden-lg"><br><br></div>
                    <h3 class="page-header">
                       Notificaciones
                        <small>Retrocesos</small>
                    </h3>
                    <div class="visible-lg">
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard">  <a href="{!! url('home') !!}">Inicio</a></i>
                        </li>
                        <li>
                            <i class= "fa fa-archive"> <a href="traslados">Traslados</a></i>
                    </ol>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            @foreach ($noticias as $noticia)
           
            

    <div class="alert alert-info alert-dismissible fade in">  
             <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
             <strong>• Retroceso:</strong> Se esta llevando a cabo el reproceso de la orden <strong>"{{$noticia->No_Orden}}"</strong> de la estación {{$noticia->Estacion_Act}} a la estacion de destino{{$noticia->Estacion_Destino}} 
             por motivo de <strong> {{$noticia->Descripcion}}</strong>.
             <br>            
             <strong> Nota:</strong>  {{$noticia->Nota}}

             <div align="center">
             <strong> Autorizo :</strong>  {{$noticia->Reproceso_Autorizado}}
            </div>
<div align="right"><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#{{$noticia->Id}}">Aceptar y Retroceder</button></div>

                           <!-- Modal -->
  <div class="modal fade" id="{{$noticia->Id}}" role="dialog">
    <div class="modal-dialog modal-sm">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body">
            <p>¿Esta seguro de realizar el reproceso?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <a href="../leido/{{$noticia->Id}}" class="btn btn-primary btn-sm">Aceptar</a>
      </div>
      </div>   
      </div>   
    </div>
  </div>
@endforeach
        <!-- /.container-fluid -->

@endsection
