@extends('home')

            @section('homecontent')
            <style>
                /* ajusta el encabezado de las columnas de la tabla al ocultar la barra de Menu */
                .dataTables_scrollHeadInner,
                .table {
                    width: 100% !important;
                }
            
                .col-md-2 {
                    width: auto;
                }

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
    .primerth {
        position: -webkit-sticky;
        position: sticky;
        left: 0px;
        z-index: 5;
    }


   
    .DTFC_LeftBodyLiner {
        overflow-x: hidden;
        left: -2px !important;
    }

               
                div.dt-buttons {
                   
                    margin-bottom: 6px;
                    margin-top: 0px;
                }
                .btn-group {
                //cuando es datatables y custom buttons
                margin-bottom: 0px;
                
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
               /* .dataTables_wrapper .dataTables_filter {float: right;text-align: right;visibility: visible;}
                 remove bs select all btn 
                select[data-max-options="20"] ~ .dropdown-menu .bs-actionsbox .bs-select-all {
                display: none;
                }

                select[data-max-options="20"] ~ .dropdown-menu .bs-actionsbox .bs-deselect-all {
                width: 100%;
                }

                .bootstrap-select .dropdown-toggle .filter-option-inner-inner {
                overflow: hidden;
                font-size: 16px;
                }*/
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               Ultimos Precios
                                <small></small>
                            </h3>
                                        
                        </div>
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                        
                           <!-- begin row -->
                            <div  id="btnBuscadorOrdenVenta">
   
                                            <div class="row" style="margin-bottom: 20px">
                                                <div class="form-group">
                                                    <div class="col-md-2">
                                                        <label><strong>
                                                                <font size="2">Fecha Inicial</font>
                                                            </strong></label>
                                                        <input type="text" id="fstart" class='form-control' autocomplete="off">
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <label><strong>
                                                                <font size="2">Artículo</font>
                                                            </strong></label>
                                                        {!! Form::select("sel_articulos", $articulos, null, [
                                                        "class" => "form-control selectpicker","id"
                                                        =>"sel_articulos", "data-size" => "8", "data-style" => "btn-success btn-sm",
                                                        'data-live-search' => 'true', 'title'=>"Selecciona..."])
                                                        !!}
                                                    </div>
                                                    
                                                    
                                                    <div class="col-md-2">
                                                        <p style="margin-bottom: 23px"></p>
                                                        <button type="button" class="form-control btn btn-primary m-r-5 m-b-5" id="boton-mostrar"><i class="fa fa-cogs"></i>
                                                            Mostrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 40px">
                                                <div class="form-group">
                                                    <div class="col-md-2">
                                                        <label><strong>
                                                                <font size="2">Código</font>
                                                            </strong></label>
                                                        <input type="text" id="mat_code" class='form-control' autocomplete="off" readonly>
                                                    </div>
                                                    
                                                    <div class="col-md-8 col-sm-12">
                                                        <label><strong>
                                                                <font size="2">Artículo</font>
                                                            </strong></label>
                                                        <input type="text" id="mat_descr" class='form-control' autocomplete="off" readonly>
                                                    </div>
                                                    
                                                    <div class="col-md-1">
                                                        <label><strong>
                                                                <font size="2">UDM</font>
                                                        </strong></label>
                                                        <input type="text" id="mat_udm" class='form-control' autocomplete="off" readonly>
                                                        <input type="text" id="mat_udm2" class='form-control' style="display: none">
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                
                                                <div class="table-scroll" id="registros-ordenes-venta">
                                                    <table id="tprecios" class="table table-striped table-bordered hover" width="100%">
                                                        <thead>
                                                            <tr>                                                
                                                                <th>OC</th>
                                                                <th>Entrada</th>
                                                                <th>FechaF</th>
                                                                <th>Proveedor</th>
                                                                <th>PrecioF</th>
                                                                <th>Moneda</th>                                                              
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
    let codestr = '';
     var table = $("#tprecios").DataTable({
        language:{
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        dom: "Brtip",
        scrollX: true,
        scrollY: height,
        fixedHeader : true,
        processing: true,
        columns: [
            
            {data: "ORDEN_C"},
            {data: "COMPRA"},
            {data: "FECHAF"},
            {data: "PROVEEDOR"},
            {data: "PRECIOF"},
            {data: "MONEDA"},
            
        ],
        buttons: [
        
         {
            text: '<i class="fa fa-file-excel-o"></i> Excel',
            className: "btn-success",
            action: function ( e, dt, node, config ) {                                   
                       
                        $( "button:contains('Excel')").html('<i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i> Excel');
                        window.location.href = '{!! route('ultimos_precios_XLS') !!}';
                        setTimeout(function () {
                            $( "button:contains('Excel')").html('<span><i class="fa fa-file-excel-o"></i> Excel</span>');
                        }, 2500);
                    }         
        }, 
        {            
            text: '<i class="fa fa-file-pdf-o"></i> Pdf',           
            className: "btn-danger",            
                    action: function ( e, dt, node, config ) {                                
                       
                        window.open('reporte/ultimospreciosPDF', '_blank')                                   
                            
                    }           
        },
       
       
    ],
    });
    
    $('#tprecios thead tr').clone(true).appendTo( '#tprecios thead' );
    $('#tprecios thead tr:eq(1) th').each( function (i) {
    var title = $(this).text();
    $(this).html( '<input style="color: black;"  type="text" placeholder="Filtro '+title+'" />' );

    $( 'input', this ).on( 'keyup change', function () {       
            
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search(this.value, true, false)
                    
                    .draw();
            } 
                
    } );    
} ); 
    $("#sidebarCollapse").on("click", function() {
            $("#sidebar").toggleClass("active");
            $("#page-wrapper").toggleClass("content");
            $(this).toggleClass("active");
            table.columns.adjust().draw();
        });
    var xhrBuscador = null;
    
    $('#sel_articulos').selectpicker({    
        noneSelectedText: 'Selecciona una opción',   
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
    //$(".buttons-excel").attr('disabled', true)
    
    var ignore = true;
    $("#fstart").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true
    }).on("change", function() {
         comboboxArticulos_reload();   
    });
    //$('#fstart').datepicker('setDate', new Date(2019,1,1));
    
    ignore = false;
    comboboxArticulos_reload();    
    let todos_articulos = 0;
   
    var wrapper = $('#wrapper');
    var resizeStartHeight = wrapper.height();
    var height = (resizeStartHeight *90)/100;
    if ( height < 200 ) {
        height = 200;
    }
    console.log(wrapper.height()+' height_datatable ' + height)

    $("#sel_articulos").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
        //comboboxProveedores_reload();
        //console.log($("#sel_articulos option:selected").text())
        var code = $('option:selected', this).attr("data-code");
        var descr = $('option:selected', this).attr("data-descr");
        var udm = $('option:selected', this).attr("data-udm");
        $("#mat_code").val(code)
        $("#mat_descr").val(descr)
        $("#mat_udm").val(udm)
        $("#mat_udm2").val(udm)
        codestr = code +" - "+ descr + ', UDM:'+udm;
        $("#tprecios").DataTable().clear().draw();
        table.button( 0 ).enable( false);
        table.button( 1 ).enable( false);
    });
    function comboboxArticulos_reload() {
        $("#tprecios").DataTable().clear().draw();
        table.button( 0 ).enable( false);
        table.button( 1 ).enable( false);
        var options = [];         
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: { 
                "_token": "{{ csrf_token() }}",
                fstart: ($('#fstart').val() == '')? '01/01/2019' : $('#fstart').val(),
            },
            url: "cpp_combobox_articulos_ultimos_precios",
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
               setTimeout($.unblockUI, 2000);
               table.columns.adjust().draw();
               //$("#tprecios").DataTable().clear().draw();
            },
            success: function(data){
                options = [];
                
                $("#sel_articulos").empty();
                for (var i = 0; i < data.oitms.length; i++) { 
                    //options.push('<option value="' + data.oitms[i]['codigo'] + '">' +
                    //data.oitms[i]['descripcion'] + '</option>');
                    let str = '<option value="' + data.oitms[i]['codigo'] + '" data-code="' + data.oitms[i]['codigo'] + '" data-descr="' + data.oitms[i]['descr'] + '" data-udm="' + data.oitms[i]['udm'] + '">'+ data.oitms[i]['descripcion']+'</option>'
                   
                    options.push(str);
                    
                    }
                if ( data.oitms.length <= 0 ) {
                    bootbox.dialog({
                    title: "Mensaje",
                    message: "<div class='alert alert-danger m-b-0'>No hay artículos dentro del intervalo de fechas</div>",
                    buttons: {
                    success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                    }
                    }
                    }).find('.modal-content').css({'font-size': '14px'} );
                } else {
                    //todos_articulos = data.oitms.length;
                    $('#sel_articulos').append(options);
                    //$('#sel_articulos option').attr("selected","selected");
                    $('#sel_articulos').selectpicker('refresh');
                }                             
                                          
            }
        });
    }    
    /* function comboboxProveedores_reload() {
       
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
            var options = [];         
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: { 
                    "_token": "{{ csrf_token() }}",
                    fstart: $('#fstart').val(),
                    fend: $('#fend').val(),
                    articulos: articulos,
                    todos_articulos: (todos_articulos === registros) ? true : false
                },
                url: "cpp_combobox_proveedores",
                beforeSend: function() {
                
                },
                complete: function() {
                
                   // $("#tprecios").DataTable().clear().draw();
                },
                success: function(data){
                    options = [];
                   
                    $("#sel_proveedores").empty();
                    for (var i = 0; i < data.proveedores.length; i++) { 
                        options.push('<option value="' + data.proveedores[i]['codigo'] + '">' +
                        data.proveedores[i]['descripcion'] + '</option>');
                        }
                    if (data.proveedores.length > 0) {     
                        todos_proveedores = data.proveedores.length;                   
                        $('#sel_proveedores').append(options);
                        $('#sel_proveedores option').attr("selected","selected");
                        $('#sel_proveedores').selectpicker('refresh');
                    }
                }
            });  
        
        
    } */
   
    $('#boton-mostrar').on('click', function(e) {
        e.preventDefault();
        if($('#sel_articulos').val() == ''){
            bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'>Selecciona Artículo</div>",
            buttons: {
            success: {
            label: "Ok",
            className: "btn-success m-r-5 m-b-5"
            }
            }
            }).find('.modal-content').css({'font-size': '14px'} );
        } else {

            recargarTabla();
        }
    });
    $('#btn_xls').on('click', function (e) {
        $(this).html('<i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i> Excel');
        window.location.href = '{!! route('cppXLS') !!}'; 
        setTimeout(function () {
            $( "button:contains('Excel')").html('<span><i class="fa fa-file-excel-o"></i> Excel</span>');            
        }, 2500);
           
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
            
        $("#tprecios").DataTable().clear().draw();

        $.ajax({
            type: 'GET',
            async: true,       
            url: '{!! route('datatables_ultimos_precios') !!}',
            data: {
                "_token": "{{ csrf_token() }}",
                articulos: $('#sel_articulos').val(),
                fstart: ($('#fstart').val() == '')? '01/01/2019' : $('#fstart').val(),
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
                    $("#tprecios").dataTable().fnAddData(data.registros);
                   var selectedRows = table.rows().count();
                    
                    table.button( 0 ).enable( true);
                    table.button( 1 ).enable( true);

                    table.columns.adjust().draw();
                    // $('#btn_xls').prop('disabled', false);        
                }else{
                    table.button( 0 ).enable( false);
                    table.button( 1 ).enable( false);
                    bootbox.dialog({
                    title: "Mensaje",
                    message: "<div class='alert alert-danger m-b-0'>No hay registros que cumplan los parámetros.</div>",
                    buttons: {
                    success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                    }
                    }
                    }).find('.modal-content').css({'font-size': '14px'} );
                    //$('#btn_xls').prop('disabled', true);
                }            
            }
        });
    }
 
                                 
}                                                                                                    
                </script>
