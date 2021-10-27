@extends('home')

@section('homecontent')
<style>
    th,
    td {
        white-space: nowrap;
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
        <div class="col-md-12" style="margin-bottom: 5px">
            <h4><b>TIPOS DE CAMBIO</b> </h4>
            <div class="form-group">
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
                <div class="col-md-2">
                    <p style="margin-bottom: 23px"></p>
                    <button type="button" class="form-control btn btn-primary m-r-5 m-b-5" id="btn_mostrar"> Calcular</button>
                    
                </div>
                <div class="col-md-1">
                    <p style="margin-bottom: 23px"></p>
                    <a onclick="window.history.back();" class="btn btn-primary">Atras</a>
                    
                </div>

            </div>
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
                           <th>6 Patas"</th>
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
                    <h4 class="modal-title">Cambio de precio</h4>
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
                                            <th>CódigoComposicion</th>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>UM</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Moneda</th>
                                            <th>Total MXP</th>
                                            
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
                    
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input class="form-check-input" type="radio" name="r1" id="ch1" value="1" checked>
                                        <label for="fecha_provision">Nuevo Precio</label>
                                        <input  type="number" id="precio_nuevo" name="precio_nuevo" min=".0001" step=".0001" class='form-control' autocomplete="off">
                                    </div>
                                </div>
                                
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
   
        $(window).on('load', function() {
            var xhrBuscador = null;

            var data,
                tableName = '#table_detalle_modelos',
                table_modelos; 
                createTable();
            
            
            function createTable(){
               table_modelos = $(tableName).DataTable({

                    deferRender: true,
                    "paging": false,
                    dom: 'frti',
                    scrollX: true,
                    scrollCollapse: true,
                    scrollY: "230px",
                    fixedColumns:   {
                        leftColumns: 3,
                    },
                    ajax: {
                        url: '{!! route('datatables_simulador') !!}',
                        data: function (d) {
                            d.modelo = '{{$modelo}}',
                            d.tc_usd = $('#tc_usd').val(),
                            d.tc_can = $('#tc_can').val(),
                            d.tc_eur = $('#tc_eur').val(),            
                            d.insert = $('#insert').val()            
                        }              
                    },
                    processing: true,
                    columns: [   
                        {"data" : "composicionCodigoCorto", "name" : "Código"},
                        {"data" : "composicion", "name" : "Composición"},
                        {"data" : "total", "name" : "Total",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "pieles", "name" : "DCM/MT"},
                        {"data" : "pieles_precio", "name" : "1 Piel/Tela"},
                        {"data" : "pg_piel_tela", "name" : "% Piel",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_huleUSD", "name" : "USD Hule",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_hule", "name" : "2 Hule",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "pg_hule", "name" : "% Hule",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_cojineria", "name" : "3 Pluma/Acojin",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "pg_cojineria", "name" : "% Cojín",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_casco", "name" : "4 Casco",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "pg_casco", "name" : "% Casco",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_herrajesUSD", "name" : "Dólares Herrajes",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_herrajes", "name" : "5 Herrajes y Mecanismos",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "pg_herrajes", "name" : "% Herrajes",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_metalesUSD", "name" : "Dólares Patas",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_metales", "name" : "6 Patas",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "pg_metales", "name" : "% Patas",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_empaques", "name" : "7 Empaque",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "pg_empaques", "name" : "% Empaque",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_otros", "name" : "8 Otros",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "pg_metales", "name" : "% Otros",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "g_cuotas", "name" : "9 Cuotas",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }},
                        {"data" : "pg_cuotas", "name" : "% Cuotas",
                        render: function(data){
                            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                            return val;
                        }}
                        
                    ],
                    "language": {
                        "url": "{{ asset('assets/lang/Spanish.json') }}",
                    },
                    columnDefs: [
                            
                    ],

                });

            }
            $('#btn_mostrar').on('click', function(e) {
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
            $('#table_detalle_modelos tbody').on( 'click', 'a', function (event) {
                var rowdata = table_modelos.row( $(this).parents('tr') ).data();
                
                console.log(rowdata['composicionCodigo'])
                console.log(event.currentTarget.id);
                
                var categoria = event.currentTarget.id;
                $('#modal_composicion').val( rowdata['composicionCodigo'])
                $('#modal_categoria').val(categoria)
                reloadTablePrecios();
                $('#modal_price').modal('show');
                
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
                    {data: "precio",
                        render: function(data){
                        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                        return val;
                    }},
                    {data: "moneda"},
                    {data: "precio_pesos",
                        render: function(data){
                        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                        return val;
                    }}
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
                //var code_composicion = fila[0]['composicionCodigo'];
                //var code = fila[0]['codigo'];
                var num = parseFloat(fila[0]['precio']).toFixed(4);
                $('#precio_nuevo').val(num)
                $("#ch1").prop("checked", true);
                $("#ch2").prop("checked", false);
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
                        click_programar(1);
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
                        click_programar(2);
                    }
                }
            });

            function click_programar(option) {
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