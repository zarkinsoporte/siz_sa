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
                                    {!! Form::select("cboestado", $estado, null, [
                                    "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                    =>"cbo_estadoOP", "data-size" => "8", "data-style"=>"btn-success"])
                                    !!}
                                </div>
                                <div class="col-md-3">
                                    <label><strong>
                                            <font size="2">Tipo OP</font>
                                        </strong></label>
                                    {!! Form::select("cbotipo", $tipo, null, [
                                    "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                    =>"cbo_tipoOP", "data-size" => "8", "data-style"=>"btn-success"])
                                    !!}
                                </div>
                                <div class="col-md-2">
                                    <p style="margin-bottom: 23px"></p>
                                    <button type="button" class="form-control btn btn-primary m-r-5 m-b-5" id="boton-mostrar"><i
                                            class="fa fa-cogs"></i> Filtrar</button>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-scroll" id="registros-liberacion">
                                <table id="tabla_liberacion" class="table table-striped table-bordered hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Estado</th>
                                            <th>Pedido</th>
                                            <th>OP</th>
                                            <th>Codigo</th>
                                            <th>Descripción</th>
                                            <th>Cliente</th>

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
</div>

<script>

</script>