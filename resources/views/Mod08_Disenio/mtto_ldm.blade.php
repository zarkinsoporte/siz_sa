@extends('home')
@section('homecontent')
<script>   
    let tokenapp = "{{ csrf_token() }}"
</script>

{!! Html::script('assets/js/Mod08_Disenio/mtto_ldm.js') !!}
{!! Html::style('assets/css/customdt.css') !!}

<style>
    /* ajusta el encabezado de las columnas de la tabla al ocultar la barra de Menu */
    .dataTables_scrollHeadInner, .table{ width:100%!important; }
    .col-md-2 {
    width: auto;
    }
</style>
          
                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="">
                              Mantenimiento Lista de Materiales 
                            </h3>                                 
                        </div>
                        <div  style="margin-bottom: 40px">
                            <div class="form-group">
                                
                                <div class="col-md-2">
                                    <label><strong>
                                            <font size="2">Tipo Material</font>
                                        </strong></label>
                                    {!! Form::select("sel_tipomat", $tipomat, null, [
                                    "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                    =>"sel_tipomat", "data-size" => "8", "data-style" => "btn-success btn-sm",
                                    'data-live-search' => 'true',  'title'=>"Selecciona..."])
                                    !!}
                                </div>
                                <div class="col-md-6">
                                    <label><strong>
                                            <font size="2">Artículo</font>
                                        </strong></label>
                                    {!! Form::select("sel_articulos", $articulos, null, [
                                    "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                    =>"sel_articulos", "data-size" => "5", "data-style" => "btn-success btn-sm",
                                    'data-live-search' => 'true', 'title'=>"Selecciona..."])
                                    !!}
                                </div>

                                <div class="col-md-2">
                                    <p style="margin-bottom: 23px"></p>
                                    <a style="" id="btn_enviar" class="btn btn-success btn-sm" data-operacion='1'><i
                                        class="fa fa-send"></i> Programar Cambios <span class="badge"></span></a>
                                </div>
                                <div class="col-md-2">
                                    <p style="margin-bottom: 23px"></p>
                                    <a style="" id="btn_pendientes" class="btn btn-success btn-sm"><i
                                        class="fa fa-check"></i> Cambios Pendientes </a>
                                </div>
                            </div>
                        </div>  
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                            
                            <div class="col-md-12">
                                <div class="table-scroll" id="registros-ordenes-venta">
                                    <table id="tabla_arts" class="table table-striped table-bordered hover" width="100%">
                                        <thead>
                                            <tr>              
                                                <th>Origen</th>
                                                <th>Descripción Origen</th>
                                                                                  
                                                <th>UM</th>         
                                                <th>Cantidad</th>         
                                                <th>Precio</th>         
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                    </div>                     
                </div>   <!-- /.container -->
                    <div class="modal fade" id="updateprogramar" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" > Modificación LDM</h4>
                                </div>
    
                                <div class="modal-body" style='padding:16px'>
                                    
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input class="form-check-input" type="radio" name="r1" id="ch1" value="1" checked>
                                                        <label for="fecha_provision">Nueva Cantidad:</label>
                                                        <input  type="number" id="input_update" name="input_update" min=".0001" step=".0001" class='form-control' autocomplete="off">
                                                    </div>
                                                </div>
                                                
                                            </div><!-- /.row -->
                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input class="form-check-input" type="radio" name="r1" id="ch2" value="2" >
                                                        <label for="fecha_provision">Incrementar /decrementar %</label>
                                                        <input  type="number" id="input_modificacion" name="input_modificacion" min="-99" max="100" class='form-control' autocomplete="off">
                                                    </div>
                                                </div>
                                               
                                            </div><!-- /.row -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <input class="form-check-input" type="radio" name="r1" id="ch3" value="3">
                                                    <label for="fecha_provision">Borrar de LDM</label>
                                                </div>
                                            </div>
                                                                                       
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                    <button id='btn_modificacion'class="btn btn-primary"> Guardar</button>
                                </div>
                    
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="cambios_pendientes" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" > Cambios Pendientes</h4>
                                </div>
    
                                <div class="modal-body" style='padding:16px'>
                                     
                                                                                       
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-succes" data-dismiss="modal">Cerrar</button>
                                    
                                </div>
                    
                            </div>
                        </div>
                    </div>

                    @endsection

                  
