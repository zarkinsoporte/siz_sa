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
                div.dataTables_wrapper { }
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               PLANEACION SIZ
                                <small><b>Ordenes de Producción:</b></small>
                            <div class="pull-right width-full">
                                <a id="btn_enviar" class="btn btn-success btn-sm" data-operacion='1' 
                                    ><i class="fa fa-send"></i> Enviar</a>
                            </div>
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
                                                                                <th>Cantidad</th>
                                                                                <th>Cant. Completada</th>
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
                                                <div id="hiddendiv" class="progress" style="display: none">
                                                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100"
                                                        aria-valuemin="0" aria-valuemax="100" style="width: 50%">
                                                        <span>Espere un momento...<span class="dotdotdot"></span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                                                                                 
                                    </div> 
                                </div>
                                                          
                                <div class="tab-pane fade " id="default-tab-2">
                                    <div class="container">                                                     
                                        @include('Mod02_Planeacion.plantillaGenerarOP')
                                    </div>
                                </div>

                                <div class="tab-pane fade " id="default-tab-3">
                                    <div class="container">                                                        
                                        @include('Mod02_Planeacion.plantillaGenerarOP')
                                    </div>
                                </div>                      
                                <div class="tab-pane fade " id="default-tab-4">
                                    <div class="container">                         
                                        @include('Mod02_Planeacion.plantillaGenerarOP')
                                    </div>
                                </div>   
                                <div class="tab-pane fade " id="default-tab-5">
                                    <div class="container">                                                            
                                       @include('Mod02_Planeacion.plantillaGenerarOP')
                                    </div>
                                </div>                                                               
                                                   
                            </div>  <!-- /.tab-content -->                     
                        </div>  <!-- /.row -->                     
                    </div>   <!-- /.container -->

                    @endsection

                    @section('homescript')
                    
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

                            {{-- window.open("{{ URL::asset('ayudas_pdf') }}"+"/AY_00.pdf","_blank"); --}}
                           // var namefile= 'RG_'+$('#btn_pdf').attr('ayudapdf')+'.pdf';
                            //var pathfile = "{{ URL::asset('ayudas_pdf') }}"+"/"+namefile;                           
                           // window.open(pathfile,"_blank");
                        }
                    }
                  
                {{-- GENERAR OP--}}
                var table = $("#tabla_pedidos").DataTable({
                language:{
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                }, 
                scrollX: true,
                 
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
                    {data: "Cantidad"},
                    {data: "CantidadCompletada"},
                    {data: "CantidadSolicitada"},
                    {data: "Procesado"},
                    {data: "Pendiente"},

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
                {{--FIN GENERAR OP--}}
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
                $("#tabla_pedidos").dataTable().fnAddData(data.pedidos_gop);           
        }
    });
    $('#tabla_pedidos tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );
     $('#btn_enviar').on('click', function(e) {
        e.preventDefault();
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
        var oper = $('#btn_enviar').attr('data-operacion');
       $.ajax({
    type: 'POST',
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
    },
    success: function(data){   
      
    }
    });            
    });
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
                    @endsection                                      
                <script>                  
                   function val_btn(val) {                     
                          $('#btn_enviar').attr('data-operacion', val);                                                     
                    } 
                </script>
