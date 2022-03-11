@extends('home')
@section('homecontent')
<script>
    var a_fstart = "{{$fstart}}".split('-');
    //var a_fend = "{{$fend}}".split('-');
    let tokenapp = "{{ csrf_token() }}"
    //console.log(a_fstart);
    let todos_almacen = parseFloat({{count($almacen)}});
</script>

{!! Html::script('assets/js/Mod04_Materiales/ReporteEntradasSalidas.js') !!}
{!! Html::style('assets/css/customdt.css') !!}

<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header">
                Reporte de Entradas y Salidas
                <small><b>Del:</b> {{\AppHelper::instance()->getHumanDate($fstart)}} <b>al:</b>
                {{\AppHelper::instance()->getHumanDate($fend)}} (Fecha Sistema)</small>
            </h3>
            <!--
            <h5>Actualizado: {{date('d-m-Y h:i a', strtotime("now"))}}</h5>
             <h5>Fecha & hora: {{\AppHelper::instance()->getHumanDate(date('d-m-Y h:i a', strtotime("now")))}}</h5> -->
        </div>
    </div>
    <!-- /.row -->
    <div class="row" style="margin-bottom: 40px">
        <div class="form-group">
            <div class="col-md-2">
                <label><strong>
                        <font size="2">Fecha Inicial</font>
                    </strong></label>
                <input type="text" id="fstart" name="fstart" class='form-control' autocomplete="off">
            </div>
            <div class="col-md-2">
                <label><strong>
                        <font size="2">Fecha Final</font>
                    </strong></label>
                <input type="text" id="fend" name="fend" class='form-control' autocomplete="off">
            </div>
            <div class="col-md-2">
                <label><strong>
                        <font size="2">Tipo Material</font>
                    </strong></label>
                {!! Form::select("sel_tipomat[]", $tipomat, null, [
                "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                =>"sel_tipomat", "data-size" => "8", "data-style" => "btn-success btn-sm",
                "data-actions-box"=>"true", "data-deselect-all-text"=>"Desmarcar",
                "data-select-all-text"=>"Marcar", "data-max-options"=>"200",
                'data-live-search' => 'true', 'multiple'=>'multiple', 'title'=>"Selecciona..."])
                !!}
            </div>
            <div class="col-md-2">
                <label><strong>
                        <font size="2">Artículo</font>
                    </strong></label>
                {!! Form::select("sel_articulos[]", $articulos, null, [
                "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                =>"sel_articulos", "data-size" => "8", "data-style" => "btn-success btn-sm",
                "data-actions-box"=>"true", "data-deselect-all-text"=>"Desmarcar",
                "data-select-all-text"=>"Marcar", "data-max-options"=>"200",
                'data-live-search' => 'true', 'multiple'=>'multiple', 'title'=>"Selecciona..."])
                !!}
            </div>
            <div class="col-md-2">
                <label><strong>
                        <font size="2">Almacén</font>
                    </strong></label>
                
                <select data-live-search="true" class="form-control selectpicker" title="No has seleccionado nada" data-size="5"
                    data-dropup-auto="false" multiple data-actions-box="true" data-select-all-text="Marcar"
                    data-deselect-all-text="Desmarcar" data-selected-text-format="count"
                    data-count-selected-text="{0} Seleccionados" data-live-search-placeholder="Busqueda"
                    id="sel_almacen" data-max-options="200" data-size = "8" data-style="btn-success btn-sm"
                    multiple="multiple" name="sel_almacen[]">
                
                    @foreach ($almacen as $item)
                    <option value="{{$item->llave}}" selected>{{$item->valor}}</option>
                    @endforeach
                </select>
            </div>
    
            <div class="col-md-2">
                <p style="margin-bottom: 23px"></p>
                <button type="button" class="form-control btn btn-primary m-r-5 m-b-5" id="boton-mostrar"><i
                        class="fa fa-cogs"></i> Mostrar</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" style="margin-top: 0px;">
            <table id="tentradas" class="table table-striped">
                <thead class="table-condensed">
                    <tr>
                        <th># Documento</th>
                       
                        <th>Fecha</th>
                        <th>F. Sistema</th>
                        <th>Movimiento</th>

                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Valor Std</th>
                        <th>TM</th>

                        <th>Usuario</th>
                        <th>Almacén</th>
                        <th>VS</th>
                       
                        <th>Notas</th>
                       
                        <th>Hora</th>
                    </tr>
               
                </thead>
                <tbody></tbody>
                <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
               
                </tr>
                </tfoot>
            </table>
        </div> <!-- /.col-md-12 -->

    </div> <!-- /.row -->
    
</div>
<!-- /.container -->
@endsection


