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
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               Lista de Precios
                                <small><b>{{$deplist.' - '.$deplistname}}</b></small>
                            
                            </h3>                                        
                        </div>
                          <input type="text" style="display: none" class="form-control input-sm" id="input-lista" value="{{$deplist}}">
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                            
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="default-tab-1">
                                    <div class="container">                                                                                                                  
                                      <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="table-scroll" id="registros-ordenes-venta">
                                                                    <table id="tabla_arts" class="table table-striped table-bordered hover" width="100%">
                                                                        <thead>
                                                                            <tr>                                                                              
                                                                                <th></th>
                                                                                <th>Código</th>
                                                                                <th>Descripción</th>
                                                                                <th>UM</th>                                        
                                                                                <th>UM Compras</th>
                                                                                <th>Factor Conversion</th>
                                                                                <th>Precio Lista</th>                                                                
                                                                                <th>Moneda</th>                                                                
                                                                                                                       
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
                                                                                              
                                                   
                            </div>  <!-- /.tab-content -->                     
                        </div>  <!-- /.row -->                     
                    </div>   <!-- /.container -->
                    <div class="modal fade" id="updateprogramar" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" >Datos a Actualizar</h4>
                                </div>
                    
                                <div class="modal-body" style='padding:16px'>
                                    
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="fecha_provision">Prog. Corte</label>
                                                        <input maxlength="10" type="text" id="programar_progCorte" name="programar_progCorte" class='form-control' autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="cant">Sec. Compra</label>
                                                        <input maxlength="10" type="text" class="form-control" id="programar_secCompra" autocomplete="off">
                                                       
                                                    </div>
                                                </div>
                                            </div><!-- /.row -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="fecha_provision">Sec. OT</label>
                                                        <input maxlength="49" type="text" id="programar_secOt" name="programar_secOt" class='form-control' autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="cant">Estatus</label>
                                                        
                                                        {!! Form::select("cboestadoprogramar", [], null, ["class" => "form-control selectpicker","id"
                                                            =>"programar_estatus", "data-size" => "8", "data-style"=>"btn-success"])
                                                            !!}
                                                    </div>
                                                </div>
                                            </div><!-- /.row -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="programar_fCompra">F. Compra</label>
                                                        <input type="text" id="programar_fCompra"  class='form-control' autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="programar_fProduccion">F. Producción</label>
                                                        <input type="text" class="form-control" id="programar_fProduccion" autocomplete="off">
                                                       
                                                    </div>
                                                </div>
                                            </div><!-- /.row -->
                                                                                       
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                    <button id='btn-guarda-programar'class="btn btn-primary"> Actualizar</button>
                                </div>
                    
                            </div>
                        </div>
                    </div>
                    @endsection

                     <script>
  function js_iniciador() {
    $('.toggle').bootstrapSwitch();
    $('[data-toggle="tooltip"]').tooltip();
    $('.boot-select').selectpicker();
    $('.dropdown-toggle').dropdown();
    setTimeout(function () {
        $('#infoMessage').fadeOut('fast');
    }, 5000); // <-- time in milliseconds
    $("#sidebarCollapse").on("click", function () {
        $("#sidebar").toggleClass("active");
        $("#page-wrapper").toggleClass("content");
        $(this).toggleClass("active");
    });

    document.onkeyup = function (e) {
        if (e.shiftKey && e.which == 112) {
            var namefile = 'RG_' + $('#btn_pdf').attr('ayudapdf') + '.pdf';
            //console.log(namefile)
            $.ajax({
                url: "{{ URL::asset('ayudas_pdf') }}" + "/" + namefile,
                type: 'HEAD',
                error: function () {
                    //file not exists
                    window.open("{{ URL::asset('ayudas_pdf') }}" + "/AY_00.pdf", "_blank");
                },
                success: function () {
                    //file exists
                    var pathfile = "{{ URL::asset('ayudas_pdf') }}" + "/" + namefile;
                    window.open(pathfile, "_blank");
                }
            });


        }
    }
    $(window).on('load', function () {

        /*GENERAR OP*/
        var table = $("#tabla_arts").DataTable({
            language: {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            scrollX: true,
            scrollY: "430px",
            dom: 'lfrtip',
            scrollCollapse: true,
            deferRender: true,
            pageLength: -1,
            columns: [
                { data: "checkbox" },
                { data: "codigo" },
                { data: "descripcion" },
                { data: "um" },
                { data: "umc" },
                { data: "factor_conversion" },
                { data: "precio" },
                { data: "moneda" }

            ],
            'columnDefs': [{
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'className': 'dt-body-center',
                'render': function (data, type, full, meta) {
                    return '<input type="checkbox" name="selectCheck" value="' + $('<div/>').text(data).html() + '">';
                }

            },
            ],
            "rowCallback": function (row, data, index) {

                if (data['precio_lista'] == null || data['precio_lista'] == 0) {
                    $('td', row).addClass("ignoreme");
                }
            },
        });

        $.ajax({
            type: 'GET',
            async: true,
            url: '{!! route('datatables.arts') !!}',
            data: {
                deplist: $('#input-lista').val()
            },
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

                if (data.arts.length > 0) {
                    $("#tabla_arts").dataTable().fnAddData(data.arts);
                } else {

                }
            }
        });

        $('#tabla_pedidos tbody').on('click', 'tr', function (e) {
            if ($(e.target).hasClass("ignoreme")) {

            } else {
                $(this).toggleClass('selected');
            }
            var ordvta = table.rows('.selected').data();
            var registros = ordvta == null ? 0 : ordvta.length;

            var count = table.rows('.selected').count();
            var $badge = $('#btn_enviar').find('.badge');
            $badge.text(count);

            //console.log(registros);
        });

        $('#btn_enviar').on('click', function (e) {
            e.preventDefault();
            var oper = $('#btn_enviar').attr('data-operacion');
            //console.log(oper);
            switch (oper) {
                case '1':
                    click_pedidos();
                    break;
                case '2':
                    click_series();
                    break;
                case '3':
                    click_programar_cambios();
                    break;
                case '4':
                    click_liberacion();
                    break;
                case '5':
                    click_impresion();
                    break;

                default:
                    break;
            }
        });
        function click_programar_cambios() {
            var countOP = tabla_programar.rows('.selected').count();
            if (countOP == 0) {
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
            } else {
                $('#updateprogramar').modal('show');
            }
        }
        $('#btn-guarda-programar').on('click', function (e) {
            click_programar();
        });
        function click_programar() {
            var ordvta = tabla_programar.rows('.selected').data();
            //var ordvtac = table.rows('.selected').node();
            //console.log(ordvtac[0])
            var ops = '';
            var registros = ordvta == null ? 0 : ordvta.length;
            for (var i = 0; i < registros; i++) {
                if (i == registros - 1) {
                    ops += ordvta[i].DOCNUM;
                } else {
                    ops += ordvta[i].DOCNUM + ",";
                }
                //console.log(ordvta[i]);         
            }

            if (registros > 0) {
                var estatus_filtro = '';
                if ($('#cbo_estadoprogramar').val() == 0 && $('#programar_estatus').val() != 0) {//filtro estado = Planificadas
                    estatus_filtro = $('#programar_estatus').val();
                }

                $.ajax({
                    type: 'GET',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: {
                        "_token": "{{ csrf_token() }}",
                        ordenes: ops,
                        prog_corte: $('#programar_progCorte').val(),
                        sec_compra: $('#programar_secCompra').val(),
                        sec_ot: $('#programar_secOt').val(),
                        estatus: estatus_filtro,
                        fCompra: $('#programar_fCompra').val(),
                        fProduccion: $('#programar_fProduccion').val()
                    },
                    url: '{!! route('programarOP') !!}',
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
                        reloadOrdenesProgramar();
                        var $badge = $('#btn_enviar').find('.badge');
                        $badge.text('');
                        setTimeout($.unblockUI, 1500);
                        // reloadOrdenesImpresion();
                        $('#updateprogramar').modal('hide');
                        $('#programar_progCorte').val('');
                        $('#programar_secCompra').val('');
                        $('#programar_secOt').val('');
                        $('#programar_estatus').val(0);
                        $("#programar_estatus").selectpicker("refresh");
                        $('#programar_fCompra').datepicker('setStartDate', today);
                        $('#programar_fCompra').datepicker().val('');
                        $('#programar_fProduccion').datepicker('setStartDate', today);
                        $('#programar_fProduccion').datepicker().val('');


                    },
                    success: function (data) {
                        if (data.mensajeErrr.includes('Error')) {
                            bootbox.dialog({
                                title: "Mensaje",
                                message: "<div class='alert alert-danger m-b-0'>" + data.orders + "</div>",
                                buttons: {
                                    success: {
                                        label: "Ok",
                                        className: "btn-success m-r-5 m-b-5"
                                    }
                                }
                            }).find('.modal-content').css({ 'font-size': '14px' });
                        }
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

        function click_pedidos() {
            var ordvta = table.rows('.selected').data();
            //var ordvtac = table.rows('.selected').node();
            //console.log(ordvtac[0])
            var ovs = '';
            var registros = ordvta == null ? 0 : ordvta.length;
            for (var i = 0; i < registros; i++) {
                if (i == registros - 1) {
                    ovs += ordvta[i].Pedido + "&" + ordvta[i].Grupal + "&" + ordvta[i].Codigo + "&" + ordvta[i].LineNum;
                } else {
                    ovs += ordvta[i].Pedido + "&" + ordvta[i].Grupal + "&" + ordvta[i].Codigo + "&" + ordvta[i].LineNum + ",";
                }
                console.log(ordvta[i]);
            }

            if (registros > 0) {
                $.ajax({
                    type: 'GET',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: {
                        "_token": "{{ csrf_token() }}",
                        ordenesvta: ovs,

                    },
                    url: '{!! route('generarOP') !!}',
                    beforeSend: function () {
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
                    complete: function () {
                        reloadOrdenesPedidos();
                        // reloadOrdenesSeries();
                        setTimeout($.unblockUI, 1500);
                    },
                    success: function (data) {
                        if (data.orders.includes('Error')) {
                            bootbox.dialog({
                                title: "Mensaje",
                                message: "<div class='alert alert-danger m-b-0'>" + data.orders + "</div>",
                                buttons: {
                                    success: {
                                        label: "Ok",
                                        className: "btn-success m-r-5 m-b-5"
                                    }
                                }
                            }).find('.modal-content').css({ 'font-size': '14px' });
                        }
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

        $('#tabla_pedidos tbody').on('change', 'td input', function (e) {

            e.preventDefault();

            //var tbl = $('#tableFacturas').DataTable();
            var fila = $(this).closest('tr');

            var datos = table.row(fila).data();

            var check = datos['Grupal'];



            if (check == 0) {
                datos['Grupal'] = 1;
            } else {
                datos['Grupal'] = 0;
            }
            // console.log(datos) 
        });

        function reloadOrdenesPedidos() {
            $("#tabla_pedidos").DataTable().clear().draw();
            $.ajax({
                type: 'GET',
                async: true,
                url: '{!! route('datatables.gop') !!}',
                data: {

                },
                beforeSend: function () {

                },
                complete: function () {
                    // setTimeout($.unblockUI, 1500);
                    var $badge = $('#btn_enviar').find('.badge');
                    $badge.text('');
                },
                success: function (data) {
                    if (data.pedidos_gop.length > 0) {
                        $("#tabla_pedidos").dataTable().fnAddData(data.pedidos_gop);
                    } else {

                    }
                }
            });
        }
        // FIN GENERAR OP

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
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });


    });//fin on load


}  //fin js_iniciador               
function val_btn(val) {

    $('#btn_enviar').attr('data-operacion', val);
}
                </script>
