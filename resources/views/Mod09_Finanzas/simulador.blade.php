@extends('home')

@section('homecontent')
<style>
    th,
    td {
        white-space: nowrap;
        vertical-align: middle;
    }
    table.dataTable.nowrap td {
    white-space: nowrap;
    vertical-align: middle;
}
    .btn {
        border-radius: 4px;
    }

    th {
        background: #dadada;
        color: black;
        font-weight: bold;
        font-style: italic;
        font-family: 'Helvetica';
        font-size: 12px;
        border: 0px;
    }

    td {
        font-family: 'Helvetica';
        font-size: 11px;
        border: 0px;
        line-height: 1;
    }

    

    .dataTables_wrapper.no-footer .dataTables_scrollBody {
        border-bottom: 1px solid #111;
        max-height: 250px;
    }

    .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
        visibility: visible;
    }

    .dataTables_scrollHeadInner th:first-child {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        z-index: 5;
    }

    .segundoth {
        position: -webkit-sticky;
        position: sticky !important;
        left: 0px;
        z-index: 5;
    }

    table.dataTable thead .sorting {
        position: sticky;
    }

    
    .DTFC_LeftHeadWrapper {
        overflow-x: hidden;
    }
    .DTFC_LeftBodyLiner{
        overflow-x: hidden;
    }
    .blue{
        background-color: #87cefad3;
    }
    .ignoreme{
                    background-color: rgba(235, 0, 0, 0.288) !important;       
    }
</style>

<div class="container">
    <hr>
    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                SIMULADOR EN BASE AL PIEL 0301
                <small><b>SIMULADOR COSTOS PT</b></small>

            </h3>
            <h4><b>MODELO {{$modelo. ' ' . $modelo_descr}}</b></h4>
        </div>

        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
    </div> <!-- /.row -->
    <div class="row">
        <div  style="margin-bottom: 5px">
            
            <div class="form-group">
                <div class="col-md-3">
                    <h4><b>TIPOS DE CAMBIO</b> </h4>
                </div>
                <div class="col-md-3">
                    <label><strong>
                            <font size="2">USD</font>
                        </strong></label>
                    <input type="number" id="tc_usd" class="form-control" value="{{$tc[0]->usd}}">
                </div>
                <div class="col-md-3">
                    <label><strong>
                            <font size="2">CAN</font>
                        </strong></label>
                    <input type="number" id="tc_can" class="form-control" value="{{$tc[0]->can}}">
                </div>
                <div class="col-md-3">
                    <label><strong>
                            <font size="2">EUR</font>
                        </strong></label>
                    <input type="number" id="tc_eur" class="form-control" value="{{$tc[0]->eur}}">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <h4><b>DATOS PARAMETRIZABLES</b> </h4>
            <div class="table-responsive">
                <table id="tparametros" class="table table-striped table-bordered nowrap" width="100%">
                    <thead>
                        <tr>
                           <th>Err</th>
                           <th>Código</th>
                           <th>Descripcion</th>
                           <th>UM</th>
                        
                           <th>Precio</th>
                           <th>Moneda</th>
                           <th>Activar</th>
                           <th>PrecioMXP</th>
                           
                        </tr>
                    </thead>
                    
                </table>
            </div>
        </div>
        <div class="col-md-2">
            <p style="margin-bottom: 23px"></p>
            <button type="button" class="form-control btn btn-primary " id="btn_mostrar"> Calcular</button>
            
        </div>
        <div class="col-md-1">
            <p style="margin-bottom: 23px"></p>
            <a onclick="window.history.back();" class=" btn btn-primary " style="margin-left: -10px">Atras</a>
            
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="table_detalle_modelos" class="table table-striped table-bordered nowrap" width="100%">
                    <thead>
                        <tr>
                           <th>Código</th>
                           <th>Composición</th>
                           <th>Total</th>
                           <th>Margen %</th>
                           <th>Venta</th>
                           <th class="blue">DCM/MT</th>
                           <th class="blue">1 Piel/Tela</th>
                           <th class="blue">% Piel</th>
                           <th>USD Hule</th>
                           <th>2 Hule</th>
                           <th>% Hule</th>
                           <th class="blue">3 Pluma/Acojin</th>
                           <th class="blue">% Cojín</th>
                           <th>4 Casco</th>
                           <th>% Casco</th>
                           <th class="blue">USD Herrajes</th>
                           <th class="blue">5 Herrajes</th>
                           <th class="blue">% Herrajes</th>
                           <th>USD Patas</th>
                           <th>6 Patas</th>
                           <th>% Patas</th>
                           <th class="blue">7 Empaque</th>
                           <th class="blue">% Empaque</th>
                           <th>8 Otros</th>
                           <th>% Otros</th>
                           <th class="blue">9 Cuotas</th>
                           <th class="blue">% Cuotas</th>
                           
                        </tr>
                    </thead>
                    <tfoot>
                        <tr></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_price" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Detalle de Costo <codigo id='text_categoria'></codigo></h4>
                </div>
                <input id="modal_composicion" type="hidden">
                <input id="modal_categoria" type="hidden">
                <div class="modal-body" style='padding:16px'>
    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-scroll" id="registros-provisionar">
                                <table id="table_precios" class="table table-striped table-bordered hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th>HIDE CódigoComposicion</th>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>UM</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Importe</th>
                                            <th>Moneda</th>
                                            
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <a id='btn_aplicar_cambios' class="btn btn-success"> Aplicar cambios</a>
                </div>
    
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_update" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" > Actualizar Precios</h4>
                </div>

                <div class="modal-body" style='padding:16px'>
                    <input id="origindb" type="hidden">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input class="form-check-input" type="radio" name="r1" id="ch1" value="1" checked>
                                <label for="fecha_provision">Nuevo Precio</label>
                                <input  type="number" id="precio_nuevo" name="precio_nuevo" min=".0001" step=".0001" class='form-control' autocomplete="off">
                            </div>
                        </div>
                        <input type="button" id="check" hidden>
                    </div><!-- /.row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input class="form-check-input" type="radio" name="r1" id="ch2" value="2" >
                                <label for="fecha_provision">Incrementar /decrementar %</label>
                                <input  type="number" id="precio_porcentaje" name="precio_porcentaje" min="-99" max="100" class='form-control' autocomplete="off">
                            </div>
                        </div>
                        
                    </div><!-- /.row -->                                         
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button id='btn_actualiza_precio'class="btn btn-primary"> Actualizar</button>
                </div>
    
            </div>
        </div>
    </div>

<input type="hidden" id="insert" value="1">
</div> <!-- /.container -->

@endsection

<script>
    function js_iniciador() {
        $('.toggle').bootstrapSwitch();
        $('[data-toggle="tooltip"]').tooltip();
        $('.boot-select').selectpicker();
        $('.dropdown-toggle').dropdown();
        setTimeout(function() {
            $('#infoMessage').fadeOut('fast');
        }, 5000); // <-- time in milliseconds
        $("#sidebarCollapse").on("click", function() {
            $("#sidebar").toggleClass("active");
            $("#page-wrapper").toggleClass("content");
            $(this).toggleClass("active");
        });
   var data,
                tableName = '#table_detalle_modelos',
                table_modelos, tparametros, createparametros = 0,
                datosTParametros = new Array(); 
                var keepenablecheckbox = [''];
        $(window).on('load', function() {
            var xhrBuscador = null;

            
                createTable();
               
            
            function createTable(){
                
                if (createparametros > 0) {
                    datosTParametros = getTParametros();
                    keepenablecheckbox = datosTParametros.filter(function (item) { return item.codigo == "99999" });                   
                } 

                datosTParametros = JSON.stringify(datosTParametros);
                   
                console.log('**** LEYENDO PARAMETROS ******************')
                console.log(datosTParametros)
                table_modelos = $(tableName).DataTable({
                    deferRender: true,
                    "paging": false,
                    dom: 'frti',
                    scrollX: true,
                    scrollCollapse: true,
                    scrollY: "230px",
                    fixedColumns:   {
                        leftColumns: 5,
                    },
                    ajax: {
                        url: '{!! route('datatables_simulador') !!}',
                        data: function (d) {
                            d.modelo = '{{$modelo}}',
                            d.tc_usd = $('#tc_usd').val(),
                            d.tc_can = $('#tc_can').val(),
                            d.tc_eur = $('#tc_eur').val(),            
                            d.insert = $('#insert').val(),
                            d.tparametros = datosTParametros           
                        }              
                    },
                    processing: true,
                    columns: [   
                        {"data" : "composicionCodigoCorto", "name" : "Código"},
                        {"data" : "composicion", "name" : "Composición"},
                        {"data" : "total", "name" : "Total",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "margen"},
                        {"data" : "venta"},
                        {"data" : "pieles",  "name" : "DCM/MT"},
                        {"data" : "pieles_precio_detalle", "name" : "1 Piel/Tela"},
                        {"data" : "pg_piel_tela", "name" : "% Piel",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);                            
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "g_hule_detalle", "name" : "USD Hule"},
                        {"data" : "g_hule", "name" : "2 Hule",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "pg_hule", "name" : "% Hule",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "g_cojineria_detalle", "name" : "3 Pluma/Acojin"},
                        {"data" : "pg_cojineria", "name" : "% Cojín",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "g_casco_detalle", "name" : "4 Casco"},
                        {"data" : "pg_casco", "name" : "% Casco",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "g_herrajesUSD", "name" : "Dólares Herrajes",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "g_herrajes_detalle", "name" : "5 Herrajes y Mecanismos"},
                        {"data" : "pg_herrajes", "name" : "% Herrajes",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "g_metalesUSD", "name" : "Dólares Patas",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "g_metales_detalle", "name" : "6 Patas"},
                        {"data" : "pg_metales", "name" : "% Patas",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "g_empaques_detalle", "name" : "7 Empaque"},
                        {"data" : "pg_empaques", "name" : "% Empaque",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "g_otros_detalle", "name" : "8 Otros"},
                        {"data" : "pg_otros", "name" : "% Otros",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }},
                        {"data" : "g_cuotas_detalle", "name" : "9 Cuotas"},
                        {"data" : "pg_cuotas", "name" : "% Cuotas",
                        render: function(data){
                            //var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            var val = Math.round(data);
                            return val;
                        }}
                        
                    ],
                    "language": {
                        "url": "{{ asset('assets/lang/Spanish.json') }}",
                    },
                    columnDefs: [
                        {
                            "targets": [ 4 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                    return '<input  readonly id= "inputventa" style="width: 100px" class="form-control input-sm inputvta" value="' + number_format(row['venta'],0,'.','') + '" type="number" min="0.1" >'

                            }

                        }
                    ],
                    "initComplete": function(settings, json) {
                        if (createparametros == 0) {
                            createparametros = 1;
                            createTableParametros();
                        }
                        if (keepenablecheckbox.length == 0) {
                            $('.inputvta').prop('readonly', false);
                        }
                    }
                });
                
            
            }
            $('#table_detalle_modelos').on( 'change', 'input', function (e) {
                e.preventDefault();
                var table_modelos = $('#table_detalle_modelos').DataTable();

                var fila = $(this).closest('tr');
                var datos = table_modelos.row(fila).data();
               
                var total = parseFloat(datos['total']);
                var  cantInput = parseFloat($(this).val());
                var margen = 50;
                if (cantInput < total || $(this).val() == '') {
                    bootbox.dialog({
                        title: "Mensaje",
                        message: "<div class='alert alert-danger m-b-0'>El valor de Venta no puede ser menor al Total.</div>",
                        buttons: {
                            success: {
                                label: "Ok",
                                className: "btn-primary m-r-5 m-b-5"
                            }
                        }
                    }).find('.modal-content').css({ 'font-size': '14px' });
                   
                    
                    datos['venta']  =  total / (1 - (margen * .01));
                } else {
                  
                    //var margen = total / (cantInput * .01);
                    margen = ((cantInput - total) / (cantInput)) * 100;
                    
                    /*
                        precioVta = total/(margen*.01)
                        percioVta * 

                    */
                    datos['venta'] = cantInput+'';
                    datos['margen'] = number_format(margen ,1,'.','');
                    
                    console.log('total ' + total)
                    console.log('cantcantInput vta ' +cantInput)
                    console.log('margen ' + margen)
                }
                    table_modelos.row(fila).data(datos); 
                    $('.inputvta').prop('readonly', false);
                    table_modelos.fixedColumns().update();
                    
               
            });
            function number_format(number, decimals, dec_point, thousands_sep) 
                {
                    var n = !isFinite(+number) ? 0 : +number,
                        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                        toFixedFix = function (n, prec) {
                            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                            var k = Math.pow(10, prec);
                            return Math.round(n * k) / k;
                        },
                        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
                    if (s[0].length > 3) {
                        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                    }
                    if ((s[1] || '').length < prec) {
                        s[1] = s[1] || '';
                        s[1] += new Array(prec - s[1].length + 1).join('0');
                    }
                    return s.join(dec);
                }
            function createTableParametros(){
               tparametros = $('#tparametros').DataTable({

                    deferRender: true,
                    "paging": false,
                    dom: 't',
                    scrollX: false,
                    "order": [[0, "asc"],[ 1, "asc" ]],
                    scrollCollapse: false,
                    ajax: {
                        url: '{!! route('datatables_tparametros') !!}',
                        data: function (d) {
                               
                        }              
                    },
                    processing: true,
                    columns: [   
                        {"data" : "err"},
                        {"data" : "codigo"},
                        {"data" : "descripcion"},
                        {"data" : "um"},
                        {"data" : "precio"},
                        {"data" : "moneda"},
                        {"data" : "activar"},
                        {"data" : "precioMXP"}
                        
                    ],
                    "language": {
                        "url": "{{ asset('assets/lang/Spanish.json') }}",
                    },
                    "rowCallback": function( row, data, index ) {
                        
                        if ( data['err'] > 0)
                        {
                       
                            $('td',row).addClass("ignoreme");
                            $(row).attr({
                                'data-toggle': 'tooltip',
                                'data-placement': 'right',
                                'title': 'Falta capturar peso a Hule.',
                                'container': 'body'
                            });
                        
                        }
                        
                    },
                    columnDefs:  [
                        {
                            "targets": 0,
                            "visible": false
                        },    
                         
                        {
                            'targets': 6,
                            'searchable': false,
                            'orderable': false,
                            'className': 'dt-body-center',
                            'render': function (data, type, full, meta){
                            return '<input  type="checkbox" id="selectCheck" name="selectCheck" class="checkboxes" value="' + $('<div/>').text(data).html() + '">';
                            }
                        },
                        {
                            "targets": 7,
                            "visible": false
                        }  
                    ],
                    "initComplete": function(settings, json) {
                            $('.checkboxes').prop('checked', true);
                    }
                   
                });

            }

            $('#tparametros').on( 'change', 'input#selectCheck', function (e) {
                e.preventDefault();
                // var tblBancos = $('#tableBancos').DataTable();
                var fila = $(this).closest('tr');
                var datos = tparametros.row(fila).data();
                var check = datos['activar'];
                console.log('check A val- ' + datos['activar'])
               
                if($(this).is(':checked')){
                    datos['activar'] = 1; 
                    if (datos['codigo'] == '99999') {
                        $('.inputvta').prop('readonly', true);
                    }
                }else{
                    datos['activar'] = 0; 
                    if (datos['codigo'] == '99999') {
                        $('.inputvta').prop('readonly', false);
                    }
                }

                if (check == 0) {
                    
                   
                } else {
                    
                   
                }
                console.log('check val- ' + datos['activar'])
                
            });

            function getTParametros(){

                //var tabla = $('#tparametros').DataTable();
                var fila = $('#tparametros tbody tr').length;
                var datos_Tabla = tparametros.rows().data();
                
                var tblParametros = new Array();
                console.log('filas - '+ fila)
                console.log('filasTabla - '+ datos_Tabla.length)
                if (datos_Tabla.length != 0){

                    var siguiente = 0;
                    for (var i = 0; i < fila; i++) {
                        console.log('row - '+ datos_Tabla[i])
                        console.log('row activar - '+ datos_Tabla[i]["activar"])
                        

                            tblParametros[siguiente]={

                                "codigo" : datos_Tabla[i]["codigo"]
                                ,"precio" : datos_Tabla[i]["precio"]
                                ,"moneda" : datos_Tabla[i]["moneda"]
                                ,"precioMXP" : datos_Tabla[i]["precioMXP"]
                                ,"checkbox" : datos_Tabla[i]["activar"] == 1 ? true : false

                            }
                           
                            siguiente++;

                        

                    }
                    console.log(tblParametros)
                    return tblParametros;

                }
                else{

                    return tblParametros;

                }

            }

            $('#btn_mostrar').on('click', function(e) {
                // boton calcular
                e.preventDefault();
                reloadTable();
            });
            function reloadTable(){
                $('#insert').val(0)
                table_modelos.destroy();
               // $('#table_detalle_modelos').empty();
                createTable();
            }
            function reloadTablePrecios(){
                $.ajax({
                        type: 'GET',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {
                            "_token": "{{ csrf_token() }}",
                            categoria: $('#modal_categoria').val(),
                            id : $('#modal_composicion').val()
                        },
                        url: '{!! route('datatables_simulador_precios') !!}',
                        success: function(data){
                            $("#table_precios").DataTable().clear().draw();
                            if((data.material).length > 0){
                                $("#table_precios").dataTable().fnAddData(data.material);
                            }   
                        }
                        });
            }
            function reloadTparametros(){
                tparametros.ajax.reload();
            }

            $('#table_detalle_modelos tbody').on( 'click', 'a', function (event) {
                var rowdata = table_modelos.row( $(this).parents('tr') ).data();
                
                console.log(rowdata['composicionCodigo'])
                console.log(event.currentTarget.id);
                var categoria = event.currentTarget.id;
                var textCategoria = categoria.split("_")[1];
                var grupo_costo = rowdata['g_'+textCategoria]
                console.log(grupo_costo)
                if ( parseFloat(grupo_costo) == 0 ) {
                        bootbox.dialog({
                        title: "Mensaje",
                        message: "<div class='alert alert-danger m-b-0'>No hay detalle para mostrar.</div>",
                        buttons: {
                            success: {
                                label: "Ok",
                                className: "btn-success m-r-5 m-b-5"
                            }
                        }
                        }).find('.modal-content').css({ 'font-size': '14px' });
                    }else{
                       $('#modal_composicion').val( rowdata['composicionCodigo'])
                        $('#text_categoria').text(capitalizeFirstLetter(textCategoria))
                        $('#modal_categoria').val(textCategoria)
                        reloadTablePrecios();
                        $('#modal_price').modal('show'); 
                    }
                
            });
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }
            $('#tparametros tbody').on( 'click', 'a', function (event) {
               
                var fila = tparametros.row( $(this).parents('tr') ).data();
                console.log(fila)
                //var code_composicion = fila[0]['composicionCodigo'];
                //var code = fila[0]['codigo'];
                var num = parseFloat(fila[0]['precio']).toFixed(4);
                var activar = (fila[0]['activar']);
                $('#check').val(activar)
                $('#precio_nuevo').val(num)
                $('#origindb').val(0)
                $("#ch1").prop("checked", true);
                $("#ch2").prop("checked", false);
                $('#modal_update').modal('show');
                
            });

            $('#tparametros').on('dblclick', 'tr', function () {
                //modal que muestra el modal de actualizar precio
                tparametros.rows().every( function ( rowIdx, tableLoop, rowLoop ) {                   
                        var node=this.node();
                        if ( $(node).hasClass("selected")) {
                            $(node).toggleClass('selected');
                        }
                });
                var fila = tparametros.rows(this).data()
                var num = parseFloat(fila[0]['precio']).toFixed(4);
                var code = fila[0]['codigo'];
                var activar = (fila[0]['activar']);
                $('#check').val(activar)
                $('#precio_nuevo').val(num)
                $("#ch1").prop("checked", true);
                $("#ch2").prop("checked", false);
                $('#origindb').val(0)
                $('#modal_update').modal("show");

                tparametros.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                    if(this.data().codigo === code){
                        var node=this.node();
                    // console.log($(node).hasClass("selected"))
                        if ( $(node).hasClass("selected")) {

                        } else {
                            $(node).toggleClass('selected');
                        }
                    }
                });
            });
        var table_precios = $("#table_precios").DataTable(
            {
                language:{
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },
                "aaSorting": [],
                dom: 'T<"clear">lfrtip',
                processing: true,
                columns: [
                    {data: "composicionCodigo"},
                    {data: "codigo"},
                    {data: "descripcion"},
                    {data: "um"},
                    {data: "cantidad",
                        render: function(data){
                        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                        return val;
                    }},
                    {data: "precio_moneda",
                        render: function(data){
                        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                        return val;
                    }},
                    {data: "importe_moneda",
                        render: function(data){
                        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                        return val;
                    }},
                    {data: "moneda"}
                ],
                "columnDefs": [
                    {
                    "targets": [ 0 ],
                    "visible": false
                    },
                ],
            });

            $('#table_precios tbody').on('dblclick','tr',function(e){
                e.preventDefault();
                if ($(e.target).hasClass("selected")) {

                } else {
                    $(this).toggleClass('selected');
                }
                var fila = table_precios.rows(this).data()
                console.log(fila)
                //var code_composicion = fila[0]['composicionCodigo'];
                //var code = fila[0]['codigo'];
                var moneda = fila[0]['moneda'], precio_moneda = 0;
                switch (moneda) {
                    case 'USD':                        
                        precio_moneda = fila[0]['precioUSD'];
                        break;
                                  
                    default:
                        precio_moneda = fila[0]['precioMXP'];
                      
                        break;
                }
                var num = parseFloat(precio_moneda).toFixed(4);
                $('#precio_nuevo').val(num)
                $("#ch1").prop("checked", true);
                $("#ch2").prop("checked", false);
                $('#origindb').val(1)
                $('#modal_update').modal('show');
               
            });
           
            $('#btn_actualiza_precio').on('click', function (e) {
                if ($("#ch1").is(":checked")) {
                    if ($('#precio_nuevo').val() <= 0 || $('#precio_nuevo').val() == '' ) {
                        bootbox.dialog({
                        title: "Mensaje",
                        message: "<div class='alert alert-danger m-b-0'>Introduzca Precio válido.</div>",
                        buttons: {
                            success: {
                                label: "Ok",
                                className: "btn-success m-r-5 m-b-5"
                            }
                        }
                        }).find('.modal-content').css({ 'font-size': '14px' });
                    }else{
                        if ($('#origindb').val() == 0) {                            
                            click_programar(1);
                        } else {
                            click_programardb(1);                            
                        }
                    }
                } else {
                    if ($('#precio_porcentaje').val() < -100 || $('#precio_porcentaje').val() == '' ) {
                        bootbox.dialog({
                        title: "Mensaje",
                        message: "<div class='alert alert-danger m-b-0'>Introduzca Porcentaje válido.</div>",
                        buttons: {
                            success: {
                                label: "Ok",
                                className: "btn-success m-r-5 m-b-5"
                            }
                        }
                        }).find('.modal-content').css({ 'font-size': '14px' });
                    }else{
                        if ($('#origindb').val() == 0) {                            
                            click_programar(2);
                        } else {
                            click_programardb(2);                            
                        }
                    }
                }
            });

            function click_programar(option) {
                var row = tparametros.row('.selected');
                var mifila = row.data();

                console.log(mifila['codigo'])
                var
                codigo = mifila.codigo,
                precio = mifila.precio,
                precio_nuevo = $('#precio_nuevo').val(),
                precio_porcentaje = $('#precio_porcentaje').val(),
                option = option,
                moneda = mifila.moneda, 
                tc_usd = $('#tc_usd').val(),
                tc_can = $('#tc_can').val(),
                tc_eur = $('#tc_eur').val(),
                check = $('#check').val(),
                precioMXP = mifila.precioMXP
                console.log('estoy actualizando precio articulo ' + codigo + ' - '+ precio)
                if (option == '1') { 
                    precio = precio_nuevo;
                } else if (option == '2') { 
                    precio += precio * ( precio_porcentaje / 100 );
                }
                mifila.precio = precio
                
                switch (moneda) {
                    case 'USD':
                        precioMXP = precio * tc_usd;
                        break;
                    case 'CAN':
                        precioMXP = precio * tc_can;
                        break;
                    case 'EUR':
                        precioMXP = precio * tc_eur;
                        break;                
                    default:
                        precioMXP = precio;
                        break;
                }
                
                mifila.precioMXP = precioMXP
                var tr = $(row.node());
                var checkbox = tr.find('td:first-child input[type="checkbox"]')
                if(checkbox.is(':checked')){
                    mifila.validar = 1;
                   
                } else {
                    mifila.validar = 0;
                   
                }
                console.log('checkbox ' + checkbox.is(':checked') )
                console.log('validar ' + mifila.validar )
                console.log('precio nuevo ' + precio + ' - '+ precioMXP)
                console.log(mifila )

                tparametros.row('.selected').data(mifila);
                console.log('ok' )

                var tr = $(row.node());
                var checkbox = tr.find('td:first-child input[type="checkbox"]')
                if(checkbox.is(':checked')){
                  
                    checkbox.prop('checked', true);
                } else {
                   
                    checkbox.prop('checked', false);
                }

                    $('#modal_update').modal('hide');
                    $('#precio_nuevo').val('');
                    $('#check').val('')
                    $('#precio_porcentaje').val('');
                
            }
            function click_programardb(option) {
                var ordvta = table_precios.rows('.selected').data();
                //var ordvtac = table.rows('.selected').node();
                //console.log(ordvtac[0])
                var code_composicion = '';
                var ops = '';
                var registros = ordvta == null ? 0 : ordvta.length;
                for (var i = 0; i < registros; i++) {
                    if (i == registros - 1) {
                        code_composicion = ordvta[i].composicionCodigo;    
                        moneda = ordvta[i].moneda;    
                        ops += ordvta[i].codigo + "&" + parseFloat(ordvta[i].precio).toFixed(4);
                    } else {
                        ops += ordvta[i].codigo + "&" + parseFloat(ordvta[i].precio).toFixed(4) + ",";
                    }
                    //console.log(ordvta[i]);         
                }
                if (registros > 0) {
                    
                    $.ajax({
                        type: 'GET',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: {
                            "_token": "{{ csrf_token() }}",
                            articulos: ops,
                            precio_nuevo: $('#precio_nuevo').val(),
                            precio_porcentaje: $('#precio_porcentaje').val(),
                            option: option,
                            code_composicion: code_composicion,
                            moneda: moneda, 
                            tc_usd :$('#tc_usd').val(),
                            tc_can : $('#tc_can').val(),
                            tc_eur : $('#tc_eur').val()
                        },
                        url: '{!! route('simulador_actualizarPrecios') !!}',
                        beforeSend: function () {
                            $.blockUI({
                                baseZ: 2000,
                                message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                                css: {
                                    border: 'none',
                                    padding: '16px',
                                    width: '50%',
                                    top: '40%',
                                    left: '30%',
                                    backgroundColor: '#fefefe',
                                    '-webkit-border-radius': '10px',
                                    '-moz-border-radius': '10px',
                                    opacity: .7,
                                    color: '#000000'
                                }
                            });
                        },
                        complete: function () {
                            setTimeout($.unblockUI, 1500);
                        },
                        success: function (data) {
                            setTimeout(function () {
                                        var respuesta = JSON.parse(JSON.stringify(data));
                                        console.log(respuesta)
                                        if(respuesta.codigo == 302){
                                            window.location = '{{ url("auth/login") }}';
                                        }
                                    }, 2000);
                            reloadTablePrecios();
                           
                            $('#modal_update').modal('hide');
                            $('#precio_nuevo').val('');
                            $('#precio_porcentaje').val('');
                            
                        }
                    });
                } else {
                    bootbox.dialog({
                        title: "Mensaje",
                        message: "<div class='alert alert-danger m-b-0'>No hay registros seleccionados.</div>",
                        buttons: {
                            success: {
                                label: "Ok",
                                className: "btn-success m-r-5 m-b-5"
                            }
                        }
                    }).find('.modal-content').css({ 'font-size': '14px' });
                }
            }

            $('#btn_aplicar_cambios').on('click', function (e) {
                reloadTable();
                $('#modal_price').modal('hide');
            });

            
        }); //fin on load
    } //fin js_iniciador               
</script>