@extends('home')

            @section('homecontent')


                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="page-header">
                                Salotto SA DE CV
                                <small>Reporte de Rechazos</small>
                            </h3>
                           
                                                      
                          
                        </div>
                    </div>
                    <!-- /.row -->
                    <div class="row">

                        <div class="col-md-12 ">
                            @include('partials.alertas')                        
                        </div>

<style>
    .options {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 40%;
}
</style>
                        <!-- Modal -->

  <div class="modal fade" id="pass" role="dialog" >
      <div class="modal-dialog modal-lg" role="document">
           <div class="modal-content" >
              <div class="modal-header">

                   <h4 class="modal-title" id="pwModalLabel">Reporte de Rechazos</h4>
              </div>
              {!! Form::open(['url' => 'pdfRechazo', 'method' => 'POST']) !!}

              <div class="modal-body">
                   <input type="text" hidden name="send" value="send">
                  <div class="form-group">
                       @include('partials.alertas')
                  </div>
                  <div class="form-group">
                      <label for="date_range" class="control-label">Rango de Fechas:</label><br>
                       Desde:<input type="date" id="FechIn" name="FechIn" class="form-control" >
                      Hasta:<input type="date" id="FechaFa" name="FechaFa" class="form-control" >
                      </div>
                                     
                      <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
        Filtros  de Busqueda
        </a>
        <span class="glyphicon glyphicon-chevron-down"></span>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
      <div class="form-group">
      <label for="date_range" class="control-label">Proveedor:</label><br>
 <select id="prov" name="prov">
                     <option type="text" selected disabled hidden value="" >Selecciona</option>
                        @foreach($Proveedores as $proveedor)
                     <option type="text"  class="form-control" value="{{$proveedor->proveedorId}}">{{$proveedor->proveedorId}} - {{$proveedor->proveedorNombre}}</option>
                         @endforeach
                         </select><br>
                         <label for="date_range" class="control-label">Articulo:</label><br>
<select  id="arti" name="arti">
                         <option type="text" maxlength="30" selected disabled hidden max value="">Selecciona</option>
                         @foreach($Articulos as $Articulo)
                         <div class="options">
                     <option  type="text" class="form-control" maxlength="30" value="{{$Articulo->materialCodigo}}">{{$Articulo->materialCodigo}} - {{$Articulo->materialDescripcion}}</option>
                     </div>
                         @endforeach
                      </select>
                      </div>

<div>

<label for="date_range" class="control-label">Registros :</label><br>
<form>
    <div class="radio">
      <label><input type="radio" name="registro"value="0" checked>Todos</label>
    </div>
    <div class="radio">
      <label><input type="radio" name="registro" value="1">Solo Rechazados</label>
    </div>
    <div class="radio">
      <label><input type="radio" name="registro"  value="2" >Solo Aceptados</label>
    </div>
  </form>

  </div>
      </div>
    </div>

                         </div>


                         <div class="modal-footer">
                          <input formtarget="_blank" id="submit" name="submit" type="submit" value="Generar" onclick="mostrar();"  class="btn btn-primary"/>

                             <a type="button" class="btn btn-default"  href="{!!url('home')!!}">Cancelar</a>
                            
                            </div>
                    {!! Form::close() !!}
                    </div>
                            </div>
            </div><!-- /modal -->

                     
                        <!-- /cantidadModal-->

                    </div>
                    <!-- /.container -->

                    @endsection

                    @section('homescript')

                        var myuser = $('#login').data("field-id");
                       
                        $('#pass').modal(
                        {
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                        }
                        );

                    @endsection

                    <script>

                        function mostrar(){
                            $("#hiddendiv").show();
                        };

                    </script>