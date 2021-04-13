@extends('home')

            @section('homecontent')
            <style>
                th, td { white-space: nowrap; }
                .btn{
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
                tr:nth-of-type(odd) {
                background: white;
                }
                .row-id {
                width: 15%;
                }
                .row-nombre {
                width: 60%;
                }
                .row-movimiento {
                width: 25%;
                }
                table{
                    table-layout: auto;
                }
                .width-full{
                    margin: 5px;
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
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               PLANEACION SIZ
                                <small><b>Ordenes de Producción:</b></small>
                            
                            </h3>                                        
                        </div>
                          
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                         <div class="row" id="panel-body-datos">
                            <input type="text" style="display: none" class="form-control input-sm" id="input-cliente-id">
                            
                            <ul class="nav nav-tabs" >
                                <li id="lista-tab1" class="active"><a onclick = "val_btn(1)" href="#default-tab-1" data-toggle="tab"
                                    aria-expanded="true">Generar OP</a></li>
                                <li id="lista-tab2" class=""><a onclick = "val_btn(2)" href="#default-tab-2" data-toggle="tab"
                                    aria-expanded="false">Series</a></li>
                                <li id="lista-tab3" class=""><a onclick = "val_btn(3)" href="#default-tab-3" data-toggle="tab"
                                    aria-expanded="false">Programación</a></li>
                                <li id="lista-tab4" class=""><a onclick = "val_btn(4)" href="#default-tab-4" data-toggle="tab"
                                    aria-expanded="false">Liberar</a></li>
                                <li id="lista-tab5" class=""><a onclick = "val_btn(5)" href="#default-tab-5" data-toggle="tab" 
                                    aria-expanded="false">Impresión</a></li>
                                <div class="pull-right">
                                    <a style="margin-right: 30px;" id="btn_enviar" class="btn btn-success btn-sm" data-operacion='1'><i class="fa fa-send"></i> Enviar</a>
                                </div>
                                
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="default-tab-1">
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
                                                            <div class="col-md-12">
                                                                <div class="table-scroll" id="registros-ordenes-venta">
                                                                    <table id="tabla_pedidos" class="table table-striped table-bordered hover" width="100%">
                                                                        <thead>
                                                                            <tr>                                                                              
                                                                                <th>Grupal</th>
                                                                                <th>Inicio</th>
                                                                                <th>Prioridad</th>
                                                                                <th>Cliente</th>                                        
                                                                                <th>Pedido</th>
                                                                                <th>Entrega</th>
                                                                                <th>Código</th>                                                                
                                                                                <th>Descripcion</th>
                                                                                <th>Cant. Solicitada</th>
                                                                                <th>Procesado</th>
                                                                                <th>Pendiente</th>                                       
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
                                </div>
                                                          
                                <div class="tab-pane fade " id="default-tab-2">                                                                                        
                                    @include('Mod02_Planeacion.plantillaSeries')                                   
                                </div>

                                <div class="tab-pane fade " id="default-tab-3">
                                    <div class="container">                                                        
                                        @include('Mod02_Planeacion.plantillaProgramacion')
                                    </div>
                                </div>                      
                                <div class="tab-pane fade " id="default-tab-4">
                                    <div class="container">                         
                                        @include('Mod02_Planeacion.plantillaLiberacion')
                                    </div>
                                </div>   
                                <div class="tab-pane fade " id="default-tab-5">
                                    <div class="container">                                                            
                                       @include('Mod02_Planeacion.plantillaImpresion')
                                    </div>
                                </div>                                                               
                                                   
                            </div>  <!-- /.tab-content -->                     
                        </div>  <!-- /.row -->                     
                    </div>   <!-- /.container -->

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
                    
                    document.onkeyup = function(e) {
                        if (e.shiftKey && e.which == 112) {
                            var namefile= 'RG_'+$('#btn_pdf').attr('ayudapdf')+'.pdf';
                            console.log(namefile)
                            $.ajax({
                            url:"{{ URL::asset('ayudas_pdf') }}"+"/"+namefile,
                            type:'HEAD',
                            error: function()
                            {
                                //file not exists
                                window.open("{{ URL::asset('ayudas_pdf') }}"+"/AY_00.pdf","_blank");
                            },
                            success: function()
                            {
                                //file exists
                                var pathfile = "{{ URL::asset('ayudas_pdf') }}"+"/"+namefile;
                                window.open(pathfile,"_blank");
                            }
                            });

                           
                        }
                    }
      $(window).on('load',function(){            
                /*GENERAR OP*/
                var table = $("#tabla_pedidos").DataTable({
                language:{
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                }, 
                scrollX: true,
                dom: 'lfrtip',
                scrollCollapse: true,
                deferRender: true,        
                   pageLength:-1,
                    columns: [                   
                    {data: "Grupal"},
                    {data: "FechaInicio"},
                    {data: "Prioridad"},
                    {data: "Cliente"},
                    {data: "Pedido"},
                    {data: "FechaEntrega"},
                    {data: "Codigo"},
                    {data: "Descripcion"},
                    {data: "CantidadSolicitada",
                    render: function(data){                     
                        return parseInt(data);
                    }},
                    {data: "Procesado"},
                    {data: "Pendiente",
                    render: function(data){
                        return parseInt(data);
                    }},

                    ],
                    'columnDefs': [{
                        'targets': 0,
                        'searchable': false,
                        'orderable': false,
                        'className': 'dt-body-center',
                        'render': function (data, type, full, meta){
                           return '<input type="checkbox" name="selectCheck" value="' + $('<div/>').text(data).html() + '">';
                        }
                        
                    },
                    ],
                    });
               
                $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.gop') !!}',
        data: {
           
        },
        beforeSend: function() {
            
        },
        complete: function() {
           // setTimeout($.unblockUI, 1500);
        },
        success: function(data){            
                   
                if(data.pedidos_gop.length > 0){
                $("#tabla_pedidos").dataTable().fnAddData(data.pedidos_gop);
                }else{
                
                }        
        }
    });
    $('#tabla_pedidos tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );
   
     $('#btn_enviar').on('click', function(e) {
        e.preventDefault();
        var oper = $('#btn_enviar').attr('data-operacion');
        console.log(oper);
        switch (oper) {
            case '1':
                click_pedidos();
                break;
            case '2':
                click_series();
                break;
            case '3':
                //click_series();
                break;
            case '4':
                console.log(' click liberar');
                click_liberacion();
                break;
            case '5':
                //click_series();
                break;
        
            default:
                break;
        }
     });
     function click_liberacion() {
        var ordvta = tabla_liberacion.rows('.selected').data();
        //var ordvtac = table.rows('.selected').node();
        //console.log(ordvtac[0])
        var ops = '';
        var registros = ordvta == null ? 0 : ordvta.length;
        for(var i=0; i < registros; i++){
            if (i == registros - 1) {
                ops += ordvta[i].Estado + "&" + ordvta[i].OP;
            } else {
                ops += ordvta[i].Estado + "&"+ ordvta[i].OP + ",";
            }
            //console.log(ordvta[i]);         
        }
        
        if(registros > 0){
                $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                "_token": "{{ csrf_token() }}",
                ordenes: ops,
            
                },
                url: '{!! route('liberacionOP') !!}',
                beforeSend: function() {
                $.blockUI({
                message: '<h2>Procesando</h2><h3>espere...<i class="fa fa-spin fa-spinner"></i></h3>',
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
                    reloadOrdenesLiberacion();
                },
                success: function(data){   
                    if (data.mensajeErrr.includes('Error')) {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>"+data.orders+"</div>",
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
        }else{
              bootbox.dialog({
                title: "Mensaje",
                message: "<div class='alert alert-danger m-b-0'>No hay registros seleccionados.</div>",
                buttons: {
                success: {
                label: "Ok",
                className: "btn-success m-r-5 m-b-5"
                }
                }
                }).find('.modal-content').css({'font-size': '14px'} );
        }           
    }
     function click_pedidos() {
        var ordvta = table.rows('.selected').data();
        //var ordvtac = table.rows('.selected').node();
        //console.log(ordvtac[0])
        var ovs = '';
        var registros = ordvta == null ? 0 : ordvta.length;
        for(var i=0; i < registros; i++){
            if (i == registros - 1) {
                ovs += ordvta[i].Pedido + "&" + ordvta[i].Grupal + "&" + ordvta[i].Codigo;
            } else {
                ovs += ordvta[i].Pedido + "&"+ ordvta[i].Grupal + "&" + ordvta[i].Codigo + ",";
            }
            //console.log(ordvta[i]);         
        }
        
        if(registros > 0){
                $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                "_token": "{{ csrf_token() }}",
                ordenesvta: ovs,
            
                },
                url: '{!! route('generarOP') !!}',
                beforeSend: function() {
                $.blockUI({
                message: '<h2>Procesando</h2><h3>espere...<i class="fa fa-spin fa-spinner"></i></h3>',
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
                    reloadOrdenesPedidos();
                },
                success: function(data){   
                    if (data.orders.includes('Error')) {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>"+data.orders+"</div>",
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
        }else{
              bootbox.dialog({
                title: "Mensaje",
                message: "<div class='alert alert-danger m-b-0'>No hay registros seleccionados.</div>",
                buttons: {
                success: {
                label: "Ok",
                className: "btn-success m-r-5 m-b-5"
                }
                }
                }).find('.modal-content').css({'font-size': '14px'} );
        }           
    }
    function enviaOrdenes(item, index) {
        console.log(item);
    }

    $('#tabla_pedidos tbody').on( 'change', 'td input', function (e) {

    e.preventDefault();

    //var tbl = $('#tableFacturas').DataTable();
    var fila = $(this).closest('tr');
    console.log(fila)
    var datos = table.row(fila).data();
    console.log(datos)
    var check = datos['Grupal'];
    console.log(check)
    

    if(check == 0){
        datos['Grupal'] = 1;
    } else {
        datos['Grupal'] = 0;
    }
    console.log(datos) 
});
function reloadOrdenesPedidos(){
    $("#tabla_pedidos").DataTable().clear().draw();
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.gop') !!}',
        data: {
           
        },
        beforeSend: function() {
            
        },
        complete: function() {
           // setTimeout($.unblockUI, 1500);
        },
        success: function(data){   
            if(data.pedidos_gop.length > 0){
                $("#tabla_pedidos").dataTable().fnAddData(data.pedidos_gop);           
            }else{ 

            }        
        }
    });
}
// FIN GENERAR OP
// INICIO GENERAR SERIES
 var tabla_series = $("#tabla_series").DataTable({
                language:{
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                }, 
                dom: 'lfrtip',
                scrollX: true,
                scrollY: "200px",
                scrollCollapse: true,
                deferRender: true,        
                   pageLength:-1,
                    columns: [                   
                    {data: "Grupal"},
                    {data: "Orden"},
                    {data: "Codigo"},
                    {data: "Descripcion"},
                    {data: "Cliente"},
                    {data: "Pedido"},
                    ],
                    'columnDefs': [{
                        'targets': 0,
                        'searchable': false,
                        'orderable': false,
                        'className': 'dt-body-center',
                        'render': function (data, type, full, meta){
                           return '<input type="checkbox" name="selectCheck" value="' + $('<div/>').text(data).html() + '">';
                        }
                        
                    },
                    ],
                    });
               
                $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.tabla_series') !!}',
        data: {
           
        },
        beforeSend: function() {
            
        },
        complete: function() {
           // setTimeout($.unblockUI, 1500);
        },
        success: function(data){            
                   
                if(data.tabla_series.length > 0){
                $("#tabla_series").dataTable().fnAddData(data.tabla_series);
                }else{
                
                } 
                tabla_series.columns.adjust().draw();       
        }
    });
    $('#tabla_series tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );
   
    function click_series() {
        
        var ordvta = tabla_series.rows('.selected').data();
        
        var ovs = '';
        var registros = ordvta == null ? 0 : ordvta.length;
        for(var i=0; i < registros; i++){
            if (i == registros - 1) {
                ovs += ordvta[i].Orden + "&" + ordvta[i].Grupal;
            } else {
                ovs += ordvta[i].Orden + "&"+ ordvta[i].Grupal +  ",";
            }
            //console.log(ordvta[i]);         
        }
        console.log(ovs)
        if(registros > 0){
        
                $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                "_token": "{{ csrf_token() }}",
                ordenes: ovs,
            
                },
                url: '{!! route('asignar_series') !!}',
                beforeSend: function() {
                $.blockUI({
                message: '<h2>Procesando</h2><h3>espere...<i class="fa fa-spin fa-spinner"></i></h3>',
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
                    reloadOrdenesSeries();
                },
                success: function(data){   
                    if (data.mensajeErrr.includes('Error')) {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>"+data.mensajeErrr+"</div>",
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
        }else{
              bootbox.dialog({
                title: "Mensaje",
                message: "<div class='alert alert-danger m-b-0'>No hay registros seleccionados.</div>",
                buttons: {
                success: {
                label: "Ok",
                className: "btn-success m-r-5 m-b-5"
                }
                }
                }).find('.modal-content').css({'font-size': '14px'} );
        }           
    }

    $('#tabla_series tbody').on( 'change', 'td input', function (e) {

    e.preventDefault();

    //var tbl = $('#tableFacturas').DataTable();
    var fila = $(this).closest('tr');
    console.log(fila)
    var datos = tabla_series.row(fila).data();
    console.log(datos)
    var check = datos['Grupal'];
    console.log(check)
    

    if(check == 0){
        datos['Grupal'] = 1;
    } else {
        datos['Grupal'] = 0;
    }
    console.log(datos) 
});
function reloadOrdenesSeries(){
    $("#tabla_series").DataTable().clear().draw();
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.tabla_series') !!}',
        data: {
           
        },
        beforeSend: function() {
            
        },
        complete: function() {
           // setTimeout($.unblockUI, 1500);
        },
        success: function(data){   
            if(data.tabla_series.length > 0){
                $("#tabla_series").dataTable().fnAddData(data.tabla_series);           
            }else{ 

            }        
        }
    });
}
// FIN GENERAR SERIES
// INICIO LIBERACION
var tabla_liberacion = $("#tabla_liberacion").DataTable({
                language:{
                    "url": "{{ asset('assets/lang/Spanish.json') }}",
                }, 
                dom: 'Bfrtip',
                buttons: [
                    {
text: '<i class="fa fa-check-square"></i>',
titleAttr: 'seleccionar',
action: function() {
    tabla_liberacion.rows({
page: 'current'
}).select();
}
},
{
text: '<i class="fa fa-square"></i>',
titleAttr: 'deseleccionar',
action: function() {
    tabla_liberacion.rows({
page: 'current'
}).deselect();
}
},
       
    ],
                scrollX: true,
                scrollY: "200px",
                scrollCollapse: true,
                deferRender: true,        
                   pageLength:-1,
                    columns: [                   
                    {data: "Estado"},
                    {data: "Pedido"},
                    {data: "OP"},
                    {data: "Codigo"},
                    {data: "Descripcion"},
                    {data: "Cliente"}
                    ],
                    'columnDefs': [
                        
                    ],
                    });
               
                $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.tabla_liberacion') !!}',
        data: {
           
        },
        beforeSend: function() {
            
        },
        complete: function() {
           // setTimeout($.unblockUI, 1500);
        },
        success: function(data){            
                   
                if(data.tabla_liberacion.length > 0){
                $("#tabla_liberacion").dataTable().fnAddData(data.tabla_liberacion);
                }else{
                
                } 
                tabla_liberacion.columns.adjust().draw();       
        }
    });
    $('#tabla_liberacion tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );

   function reloadOrdenesLiberacion(){
    var estado = $('#cbo_estadoOP').val();
    var tipo = $('#cbo_tipoOP').val();
    console.log(estado);
    console.log(tipo);
    $("#tabla_liberacion").DataTable().clear().draw();
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.tabla_liberacion') !!}',
        data: {
            "_token": "{{ csrf_token() }}",
            estado: estado,
            tipo: tipo,
        },
        beforeSend: function() {
            
        },
        complete: function() {
           // setTimeout($.unblockUI, 1500);
        },
        success: function(data){   
            if(data.tabla_liberacion.length > 0){
                $("#tabla_liberacion").dataTable().fnAddData(data.tabla_liberacion);           
            }else{ 

            }        
        }
    });
}
$('#boton-mostrar').on('click', function(e) {
    e.preventDefault();
    reloadOrdenesLiberacion();
});

// FIN LIBERACION


/* NOTA: cuando la tabla esta dentro de elementos ocultos por ejemplo en tab
o en un collapsable hay que ajustar las cabeceras cuando la tabla va 
a ser visible:

If table is in the collapsible element, you need to adjust headers when collapsible element becomes visible.
For example, for Bootstrap Collapse plugin:
$('#myCollapsible').on('shown.bs.collapse', function () {
$($.fn.dataTable.tables(true)).DataTable()
.columns.adjust();
});

If table is in the tab, you need to adjust headers when tab becomes visible.
For example, for Bootstrap Tab plugin:
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
$($.fn.dataTable.tables(true)).DataTable()
.columns.adjust();
});
*/
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
$($.fn.dataTable.tables(true)).DataTable()
.columns.adjust();
});
      });//fin on load
}  //fin js_iniciador               
                   function val_btn(val) {                     
                          $('#btn_enviar').attr('data-operacion', val);                                                     
                    } 
                </script>
