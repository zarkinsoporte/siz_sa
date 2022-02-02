@extends('home')

            @section('homecontent')
            <style>
                .btn{
                    border-radius: 4px;
                }
             th {
                font-size: 12px;
                }
                td {
                font-size: 11px;
                }
                th,
                td {
                white-space: nowrap;
                }
                div.container {
                min-width: 980px;
                margin: 0 auto;
                }
                th:first-child {
                    position: -webkit-sticky;
                    position: sticky;
                    left: 0;
                    z-index: 5;
                }
                .segundoth {
                    position: -webkit-sticky;
                    position: sticky;
                    left: 155px;
                    z-index: 5;
                }
                table.dataTable thead .sorting {                
                    position: sticky;
                }
                .DTFC_LeftBodyWrapper{
                    margin-top: 81px;
                }
                .DTFC_LeftHeadWrapper {
                    display:none;
                }
                .DTFC_LeftBodyLiner {
                overflow: hidden;
                overflow-y: hidden;
                }
               
                div.dt-buttons {
                    float: right;
                    margin-bottom: 6px;
                    margin-top: 0px;
                }
                .btn-group > .btn{
                float: none;
                }
                .btn{
                border-radius: 4px;
                }
                .btn-group > .btn:not(:first-child):not(:last-child):not(.dropdown-toggle) {
                border-radius: 4px;
                }
                .btn-group > .btn:first-child:not(:last-child):not(.dropdown-toggle) {
                border-top-right-radius: 4px;
                border-bottom-right-radius: 4px;
                }
                .btn-group > .btn:last-child:not(:first-child), .btn-group > .dropdown-toggle:not(:first-child) {
                border-top-left-radius: 4px;
                border-bottom-left-radius: 4px;
                }
                
                input{
                color: black;
                }
               .bootbox.modal {z-index: 9999 !important;}
                th {
                    background: #dadada;
                    color: black;
                    font-weight: bold;
                    font-style: italic; 
                    font-family: 'Helvetica';
                    font-size: 12px;
                    border: 0px;
                }
                .dataTables_wrapper .dataTables_filter {float: right;text-align: right;visibility: visible;}
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               Compras por Proveedor
                                <small></small>
                            </h3>
                                        
                        </div>
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                        
                           <!-- begin row -->
                            <div  id="btnBuscadorOrdenVenta">
   
                                            <div class="row" style="margin-bottom: 40px">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label><strong>
                                                                <font size="2">Artículo</font>
                                                            </strong></label>
                                                        {!! Form::select("sel_articulos[]", $articulos, null, [
                                                        "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                                        =>"sel_articulos", "data-size" => "8", "data-style" => "btn-success btn-sm", 
                                                        'data-live-search' => 'true', 'multiple'=>'multiple'])
                                                        !!}
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label><strong>
                                                                <font size="2">Proveedor</font>
                                                            </strong></label>
                                                        {!! Form::select("sel_proveedores[]", $proveedores, null, [
                                                        "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                                        =>"sel_proveedores", "data-size" => "8", "data-style" => "btn-success btn-sm", 
                                                        'data-live-search' => 'true', 'multiple'=>'multiple'])
                                                        !!}
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label><strong>
                                                                <font size="2">Fecha Inicial</font>
                                                            </strong></label>
                                                            <input type="text" id="fstart" class='form-control' autocomplete="off">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label><strong>
                                                                <font size="2">Fecha Final</font>
                                                            </strong></label>
                                                            <input type="text" id="fend" class='form-control' autocomplete="off">
                                                    </div>
                                                   
                                                    <div class="col-md-2">
                                                        <p style="margin-bottom: 23px"></p>
                                                        <button type="button" class="form-control btn btn-primary m-r-5 m-b-5" id="boton-mostrar"><i
                                                                class="fa fa-cogs"></i> Mostrar</button>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-scroll" id="registros-ordenes-venta">
                                                    <table id="tcompras" class="table table-striped table-bordered hover" width="100%">
                                                        <thead>
                                                            <tr>
                                                
                                                                <th>Proveedor</th>
                                                                <th># Entrada</th>
                                                                <th>Fecha</th>
                                                                <th>Material</th>
                                                                <th>UM</th>
                                                                <th>Cantidad</th>
                                                
                                                                <th>Cant. X Paq</th>
                                                                <th>Cant. Inv.</th>
                                                                <th>Precio Unitario</th>
                                                                <th>Moneda</th>
                                                                <th>Tipo Cambio</th>
                                                                <th>Importe</th>
                                                              
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                   
                                
                            </div>
                            <!-- end row -->
                                                   

                    </div>   <!-- /.container -->

@endsection
<script>
function js_iniciador() {
    $('.boot-select').selectpicker();
    $('.toggle').bootstrapSwitch();
    $('.dropdown-toggle').dropdown();
    $("#sidebarCollapse").on("click", function() {
            $("#sidebar").toggleClass("active");
            $("#page-wrapper").toggleClass("content");
            $(this).toggleClass("active");
        });
    $("#sidebar").toggleClass("active");
    $("#page-wrapper").toggleClass("content");
    $(this).toggleClass("active");
    var xhrBuscador = null;
    $('#sel_proveedores').selectpicker({
        noneSelectedText: 'Selecciona una opción',
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
    $('#sel_articulos').selectpicker({    
        noneSelectedText: 'Selecciona una opción',   
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
   

    console.log("{{$fstart}}"+' -- '+ "{{$fend}}");
    var a_fstart = "{{$fstart}}".split('-');
    var a_fend = "{{$fend}}".split('-');
    var start = new Date(a_fstart[0], a_fstart[1] - 1, a_fstart[2])
    var end = new Date(a_fend[0], a_fend[1] - 1, a_fend[2])
   
    $("#fstart").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true
    }).on("change", function() {
        var selected = $(this).val();
        var d_start = $('#fstart').datepicker('getDate');
        var d_end = $('#fend').datepicker('getDate');
        
        console.log(selected, d_end);                
        $('#fend').datepicker('setStartDate', selected);
        if (d_start > d_end) {
            $('#fend').datepicker('setDate', selected);
        }        
    });
    $("#fend").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true,  
    });
   
    $('#fstart').datepicker('setDate', start);
    $('#fend').datepicker('setStartDate', start);
    $('#fend').datepicker('setDate', end);    
   
    var wrapper = $('#page-wrapper2');
    var resizeStartHeight = wrapper.height();
    var height = (resizeStartHeight *75)/100;
    if ( height < 200 ) {
        height = 200;
    }
    console.log('height_datatable' + height)

    var options = [];         
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: { "_token": "{{ csrf_token() }}"
            },
            url: "cpp_combobox",
            beforeSend: function() {
                $.blockUI({
                message: '<h1>Cargando filtros,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
            complete: function() {
                setTimeout($.unblockUI, 1500);
                $("#tcompras").DataTable().clear().draw();
            },
            success: function(data){
                options = [];
                options.push('<option value="">Seleccionar</option>');
                $("#sel_articulos").empty();
                for (var i = 0; i < data.oitms.length; i++) { 
                    options.push('<option value="' + data.oitms[i]['codigo'] + '">' +
                    data.oitms[i]['descripcion'] + '</option>');
                    }
                $('#sel_articulos').append(options).selectpicker('refresh');                               
                options = [];
                options.push('<option value="">Seleccionar</option>');
                $("#sel_proveedores").empty();
                for (var i = 0; i < data.proveedores.length; i++) { 
                    options.push('<option value="' + data.proveedores[i]['codigo'] + '">' +
                    data.proveedores[i]['descripcion'] + '</option>');
                    }
                $('#sel_proveedores').append(options).selectpicker('refresh');                            
            }
        });

    var table = $("#tcompras").DataTable({
        language:{
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        deferRender: true,
        scrollX: true,
        scrollY: height,
        scrollCollapse: true,
        "pageLength": 100,
        "lengthMenu": [[100, 50, 25, -1], [100, 50, 25, "Todo"]],
        processing: true,
        columns: [
            
            {data: "PROVEEDOR"},
            {data: "N_ENTRADA"},
            {data: "F_COMPRA"},
            {data: "ARTICULO"},
            {data: "UM"},
            {data: "CANTIDAD",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return val;
            }},
            {data: "X_PAQ",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return val;
            }},
            {data: "Q_INV",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return val;
            }},
            {data: "PREC_UNIT",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "TIPO_CAMBIO",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return val;
            }},
            {data: "M_C"},
            {data: "IMPORTE",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
        
        ],
    });
    $('#boton-mostrar').on('click', function(e) {
        e.preventDefault();

        if(true){
            recargarTabla();
        }
    });

    function recargarTabla(){
        var registros = $('#sel_articulos').val() == null ? 0 : $('#sel_articulos').val().length;
            var cadena = "";
            for (var x = 0; x < registros; x++) {
                if (x == registros - 1) {
                    cadena += $($('#sel_articulos option:selected')[x]).val();
                } else {
                    cadena += $($('#sel_articulos option:selected')[x]).val() + "','";
                }
            }
            var articulos = cadena;

            var registros = $('#sel_proveedores').val() == null ? 0 : $('#sel_proveedores').val().length;
            var cadena = "";
            for (var x = 0; x < registros; x++) {
                if (x == registros - 1) {
                    cadena += $($('#sel_proveedores option:selected')[x]).val();
                } else {
                    cadena += $($('#sel_proveedores option:selected')[x]).val() + "','";
                }
            }
            var proveedores = cadena;

        $("#tcompras").DataTable().clear().draw();

            
        $.ajax({
            type: 'GET',
            async: true,       
            url: '{!! route('datatables_compras_proveedor') !!}',
            data: {
                articulos: articulos,
                proveedores: proveedores,
                fstart: $('#fstart').val(),
                fend: $('#fend').val()
            },
            beforeSend: function() {
                $.blockUI({
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
            complete: function() {
                setTimeout($.unblockUI, 1500);
            },
            success: function(data){            
                if(data.registros.length > 0){
                    $("#tcompras").dataTable().fnAddData(data.registros);           
                }else{
                    bootbox.dialog({
                    title: "Mensaje",
                    message: "<div class='alert alert-danger m-b-0'>No hay Compras que cumplan los parámetros.</div>",
                    buttons: {
                    success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                    }
                    }
                    }).find('.modal-content').css({'font-size': '14px'} );
                }            
            }
        });
    }
 
                                 
}                                                                                                    
                </script>
