@extends('home') 
@section('homecontent')
   
    <div class="container">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-6.5 col-md-9 col-sm-8" style="margin-bottom: -20px;">
                    <div class="visible-xs visible-sm"><br><br></div>               
                <h3 class="page-header">
                   Picking de Artículos<small> Solicitud de Material #{{$id}}</small>
                </h3>
            </div>
        </div>
        <div class="col-lg-5.5 col-md-8 col-sm-7">
                @if (count($errors) > 0)
                <div class="alert alert-danger text-center" role="alert">
                    @foreach($errors->getMessages() as $this_error)
                    <strong>¡Lo sentimos!  &nbsp; {{$this_error[0]}}</strong><br> @endforeach
                </div>
                @elseif(Session::has('mensaje'))
                <div class="row">
                    <div class="alert alert-success text-center" role="alert">
                        {{ Session::get('mensaje') }}
                    </div>
                </div>
                @endif

            </div>
            <style>       
            td {
                font-family: 'Helvetica';
                font-size: 80%;
            }

            th {
                font-family: 'Helvetica';
                font-size: 90%;
            }
        </style>
      @if (count($articulos_validos)>0)
            <div class="row">
                     <div class="col-md-4 col-md-offset-9">
                            <a class="btn btn-danger btn-sm" href="{{'PDF/'.$id}}" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>                                                              
                            <a class="btn btn-success btn-sm" href="{{'update/'.$id}}"><i class="fa fa-thumbs-o-up"></i> Finalizar</a>
                    </div>  
            </div>          
        <!-- /.row -->
 <div class="row">
  <div class="col-md-11">
 <h4>Material a Surtir</h4>
    <table>
      <thead>
        <tr>
          
          <th>Código</th>
          <th>Descripción</th>
          <th>UM</th>
          <th>Cant. Requerida</th>
          <th>APG-PA</th>
          <th>AMP-ST</th>
          <th>Total Disponible</th>
          <th>Quitar</th>
        </tr>
      </thead>
      <tbody>

        @foreach ($articulos_validos as $art)
        <tr <?php ?>>
                   
          <td>{{$art->ItemCode}}</td>
          <td>{{$art->ItemName}}</td>
          <td>{{$art->UM}}</td>
          <td>{{$art->Cant}}</td>
          <td>{{number_format($art->APGPA, 2)}}</td>
          <td>{{number_format($art->AMPST, 2)}}</td>
          <td>{{number_format($art->Disponible, 2)}}</td>
          <td><a role="button" data-toggle="modal" data-target="#remove" data-id="{{$art->Id}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-down fa-lg" style="color:red"></i></a></td>
        </tr>
        @endforeach

      </tbody>
    </table>
  </div>
</div> <!-- /.row -->   
@endif
@if (count($articulos_novalidos)>0)
    <div class="row">
  <div class="col-md-11">
    <h4>Material que NO se surtirá</h4>
    <table>
      <thead>
        <tr>

          <th>Código</th>
          <th>Descripción</th>
          <th>Estación</th>
          <th>Cant. Solicitada</th>
          <th>Cant. Disponible</th>
          <th>Regresar</th>
        </tr>
      </thead>
      <tbody>

        @foreach ($articulos_novalidos as $art)
        <tr>
          
          <td>{{$art->ItemCode}}</td>
          <td>{{$art->ItemName}}</td>
          <td>{{$art->Destino}}</td>
          <td>{{$art->Cant}}</td>
          <td>{{number_format($art->Disponible, 2)}}</td>          
          <td><a @if ($art->Disponible < $art->Cant)
                    {{'disabled'}}
                @endif
            role="button" href="{{'articulos/return/'.$art->Id}}" class="btn btn-default"><i class="fa fa-arrow-circle-o-up fa-lg" style="color:royalblue"></i></a></td>
        </tr>
        @endforeach     

      </tbody>
    </table>
  </div>
</div> <!-- /.row -->
@endif

<!-- .Model quitar -->

<div class="modal fade" id="remove" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">

        {!! Form::open(['url' => 'home/PICKING ARTICULOS/solicitud/articulos/remove', 'method' => 'POST']) !!}

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Quitar Articulo</h4>
        <div class="modal-body">

          <input type="hidden" id="articulo-id" name="articulo" >
          <h4>¿Cuàl es la razón por la que no surtirá este artículo?</h4>
          <input type="radio" name="reason" value="Error de Captura" required>
          Error de Captura en Solicitud<br>

          <input type="radio" name="reason" value="Material Dañado">
          Material Dañado<br>

          <input type="radio" name="reason" value="El Material no se encuentra">
          El Material no se encuentra<br>

          <input type="radio" name="reason" value="Material Incompleto">
          Material Incompleto (Si lleva varias PZAS)<br>

          <input type="radio" name="reason" value="Material No Disponible">
          Material No Disponible / Apartado<br>

          <input type="radio" name="reason" value="Otro">
          Otro…<br>

        </div>
        <div class="modal-footer">

          <button class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Quitar</button>
        </div>
      </div>
    </div>
  </div>
</div>
{!! Form::close() !!}
@endsection
 
@section('homescript')
$('#remove').on('show.bs.modal', function (event) {
var button = $(event.relatedTarget) // Button that triggered the modal
var id = button.data('id') // Extract info from data-* attributes

// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
var modal = $(this)
modal.find('#articulo-id').val(id)
});
@endsection 
<script>
document.onkeyup = function(e) {
   if (e.shiftKey && e.which == 112) {
    window.open("ayudas_pdf/AyM00_00.pdf","_blank");
  }
  } 
  
</script>

























