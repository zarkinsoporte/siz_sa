 {{-- cambiar id's --}}   
<div class="container">                                                                                                                  
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
    
                <div class="panel-body">
                    <div>
                        @if (false)
                        <div class="alert alert-info" role="alert">
                            si hay alerta personalizada
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom: 5px">
                            <div class="form-group">
                                <div class="col-md-3 col-md-offset-1">
                                    <label><strong>
                                            <font size="2">Estado</font>
                                        </strong></label>
                                    {!! Form::select("cboestadoprogramar", $estado, null, [
                                    "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                    =>"cbo_estadoprogramar", "data-size" => "8", "data-style"=>"btn-success"])
                                    !!}
                                </div>
                                <div class="col-md-3">
                                    <label><strong>
                                            <font size="2">Tipo OP</font>
                                        </strong></label>
                                    {!! Form::select("cbotipocompleto", $tipocompleto, null, [
                                    "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                    =>"cbo_tipoOPcompleto", "data-size" => "8", "data-style"=>"btn-success"])
                                    !!}
                                </div>
                                <div class="col-md-2">
                                    <p style="margin-bottom: 23px"></p>
                                    <button type="button" class="form-control btn btn-primary m-r-5 m-b-5" id="boton-mostrar_programar"><i
                                            class="fa fa-cogs"></i> Filtrar</button>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-md-12">
                            <table id="tabla_programar" class="table table-striped table-bordered hover" width="100%">
                                <thead>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>OP</th>
                                        <th>Prioridad</th>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>F. Venta</th>
                                        <th>F. Compra</th>
                                        <th>F. Producción</th>
                                        <th>Estatus</th>
                                        <th>Sec. OT</th>
                                        <th>Sec. Compra</th>
                                        <th>Prog. Corte</th>

                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
          
        </div>
    </div>                                                                                                 
</div> 

<script>
   
</script>