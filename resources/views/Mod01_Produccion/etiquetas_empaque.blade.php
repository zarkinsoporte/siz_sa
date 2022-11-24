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
                .ignoreme{
                    background-color: hsla(0, 100%, 46%, 0.10) !important;       
                }
                /*Botones de datatable*/
                .left-col {
                    float: left;
                    width: 25%;
                }
                
                .center-col {
                    float: left;
                    width: 50%;
                }
                
                .right-col {
                    float: left;
                    width: 25%;
                }
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               ETIQUETAS ALMACEN
                            </h3>                                        
                        </div>
                          
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                         <div class="row" id="panel-body-datos">
                            <input type="text" style="display: none" class="form-control input-sm" id="input-cliente-id">
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
                                                <div class="row" style="margin-bottom: 40px">
                                                    <div class="form-group">
                                                       <div class="col-md-4 col-xs-12 col-sm-6">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" placeholder="Ingresa OP..." id="input_op">
                                                            <span class="input-group-btn">
                                                              <button class="btn btn-success" id="boton-mostrar" type="button"><i
                                                                class="fa fa-plus"></i> Agregar OP</button>
                                                            </span>
                                                          </div><!-- /input-group -->
                                                       </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                       <div class="table-scroll" id="registros-impresion">
                                                        <table id="tabla_impresion" class="table table-striped table-bordered hover" width="100%">
                                                            <thead>
                                                                <tr>
                                                                    
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
                            $("#sidebar").toggleClass("active"); 
                            $("#page-wrapper").toggleClass("content"); 
                            $(this).toggleClass("active"); 

                            const today = new Date();
                            $("#input_op").val('');

                    document.onkeyup = function(e) {
                        if (e.shiftKey && e.which == 112) {
                            var namefile= 'RG_'+$('#btn_pdf').attr('ayudapdf')+'.pdf';
                            //console.log(namefile)
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
//$(window).on('load',function(){      
         
                /*GENERAR OP*/


     
    
    
    function click_impresion() {
        var ordvta = tabla_impresion.rows('.selected').data();
        //var ordvtac = table.rows('.selected').node();
        //console.log(ordvtac[0])
        var ops = '';
        var registros = ordvta == null ? 0 : ordvta.length;
        for(var i=0; i < registros; i++){
            if (i == registros - 1) {
                ops += ordvta[i].OP;
            } else {
                ops += ordvta[i].OP + ",";
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
                url: '{!! route('impresionOP_empaque') !!}',
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
                    //reloadOrdenesImpresion();
                    setTimeout($.unblockUI, 1500);
                },
                success: function(data){   
                    if (data.mensajeErrr.includes('Error')) {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>Error al generar el PDF"+data.mensajeErrr+"</div>",
                            buttons: {
                            success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                            }
                            }
                            }).find('.modal-content').css({'font-size': '14px'} );
                    }else{
                        if (data.mensajeErrr.includes('SAP')) {
                            bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>Error, "+data.mensajeErrr+"</div>",
                            buttons: {
                            success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                            }
                            }
                            }).find('.modal-content').css({'font-size': '14px'} );
                        }
                        window.open('{{url()}}'+data.file,"_blank");
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

// FIN LIBERACION
// INICIO IMPRESION
var tabla_impresion = $("#tabla_impresion").DataTable({
                language:{
                     "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                }, 
                dom: 'Bfrtip',
                order: [[1, 'asc']],
                buttons: [
                    {
                text: '<i class="fa fa-check-square"></i>',
                titleAttr: 'seleccionar',
                action: function() {
                    tabla_impresion.rows({
                        page: 'current'
                    }).select();
                    var count = tabla_impresion.rows( '.selected' ).count();
                    var $badge = $('#btn_enviar').find('.badge'); 
                    $badge.text(count);
                }
                },
                {
                text: '<i class="fa fa-square"></i>',
                titleAttr: 'deseleccionar',
                action: function() {
                    tabla_impresion.rows({
                        page: 'current'
                    }).deselect();
                    var count = tabla_impresion.rows( '.selected' ).count();
                    var $badge = $('#btn_enviar').find('.badge'); 
                    $badge.text(count);
                }
                },
                {
                text: '<x class="danger" target="_blank" id="btn_enviar"> Imprimir <span class="badge"></span></x>',
                className: 'fa fa-file-pdf-o btn-danger',
                action: function() {
                   // window.open('{{url()}}'+'{{$file_anterior}}',"_blank");
                   click_impresion();
                }
               
                }    
                    ],
                scrollX: true,
                scrollY: "430px",
                scrollCollapse: true,
                deferRender: true,        
                   pageLength:-1,
                    columns: [                   
                    
                    {data: "Pedido"},
                    {data: "OP"},
                    {data: "Codigo"},
                    {data: "Descripcion"},
                    {data: "Cliente"}
                    ],
                    'columnDefs': [
                        
                    ],       
});

$('#tabla_impresion thead tr').clone(true).appendTo( '#tabla_impresion thead' );
$('#tabla_impresion thead tr:eq(1) th').each( function (i) {
    var title = $(this).text();
    $(this).html( '<input style="color: black;"  type="text" placeholder="Filtro '+title+'" />' );

    $( 'input', this ).on( 'keyup change', function () {       
            
            if ( tabla_impresion.column(i).search() !== this.value ) {
                tabla_impresion
                    .column(i)
                    .search(this.value, true, false)
                    
                    .draw();
            } 
                
    } );    
} );    

$('#tabla_impresion tbody').on( 'click', 'tr', function () {
    $(this).toggleClass('selected');
    var count = tabla_impresion.rows( '.selected' ).count();
    var $badge = $('#btn_enviar').find('.badge'); 
    $badge.text(count);
} );
reloadOrdenesImpresion();
function reloadOrdenesImpresion(){
    
    $("#tabla_impresion").DataTable().clear().draw();
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.tabla_impresion_empaque') !!}',
        data: {
            "_token": "{{ csrf_token() }}",
        },
        beforeSend: function() {
            
        },
        complete: function() {
           // setTimeout($.unblockUI, 1500);
           var $badge = $('#btn_enviar').find('.badge'); 
                $badge.text('');
               
        },
        success: function(data){   
            if(data.tabla_impresion.length > 0){
                $("#tabla_impresion").dataTable().fnAddData(data.tabla_impresion);           
            }else{ 

            }        
        }
    });
}
//FIN IMPRESION
function getop_empaque() {
    $.ajax({

        type: 'GET',
        async: true,
        url: '{!! route('getOP_empaque') !!}',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:{
            "op": $('#input_op').val()
        },
        beforeSend: function() {
            $.blockUI({
                message: '<h1>Agregando OP,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
            $("#input_op").val('');
                     
        },
        success: function(data){
            setTimeout($.unblockUI, 500);  

            if (data.respuesta != 'ok') {
                swal("", "La OP no existe", "error",  {
                        buttons: false,
                        timer: 2000,
                    });
            } else {
                var bnd = 0;
                //console.log();
                tabla_impresion.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data2 = this.data();
                    console.log(data2.OP);
                    if (data2.OP == data.data.OP) {
                        bnd = 1;
                        var node=this.node();
                        if ( $(node).hasClass("selected")) {

                        } else {
                            $(node).toggleClass('selected');
                        }
                    } 
                } );
                if (bnd == 0) {
                    var a = $("#tabla_impresion").dataTable().fnAddData((data.data));
                 
                    var nTr =$("#tabla_impresion").dataTable().fnSettings().aoData[ a[0] ].nTr;
                    nTr.className = "selected";  
                    swal("", "OP Agregada a la tabla!", "success",  {
                        buttons: false,
                        timer: 2000,
                    });
                }
                var count = tabla_impresion.rows( '.selected' ).count();
                    
                var $badge = $('#btn_enviar').find('.badge'); 
                $badge.text(count);
            }
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {          
            $.unblockUI();
            swal("", "Error agregando OP", "error",  {
                        buttons: false,
                        timer: 2000,
                    });    
        }

    });
}
$('#boton-mostrar').on('click', function(e) {
    getop_empaque();
});
$(document).keyup(function(event) {
    if ($("#input_op").is(":focus") && event.key == "Enter") {
        getop_empaque();
    }
    
});
}  //fin js_iniciador               
                   function val_btn(val) { 
                   
                          $('#btn_enviar').attr('data-operacion', val);                                                     
                    } 
                </script>
