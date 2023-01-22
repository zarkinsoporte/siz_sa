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
        font-size: 12px;
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
    .hidden {
        display: none;
    }
</style>

<div class="container">
   <br>
    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                MANTENIMIENTO DE ACABADOS
                <small><b>Mantenimiento de la lista de Materiales que Cambian de Acuerdo al Acabado</b></small>

            </h3>
            
        </div>

        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
    </div> <!-- /.row -->
    <div class="row">
    
        <div class="col-md-8 col-sm-12">
            
                 
                        <h5>Acabado</h5>
                        <div class="input-group">
    
                            <select data-live-search="true" class="boot-select form-control" 
                            id="sel_acabado" name="sel_acabado">
                                <option value="">
                                    <span>Selecciona Acabado</span>
                                </option>
                                @foreach ($acabados as $acabado)
                                <option value="{{old('sel_acabado',$acabado->CODIDATO)}}">
                                    <span>{{$acabado->CODIDATO."  -  ".$acabado->DESCDATO}}</span>
                                </option>
                                @endforeach
                            </select>
                            <span class="input-group-btn">
                                <button id="btn_add_code" class="btn btn-primary" type="button"><i class="fa fa-plus"></i> Código</button>
                                <button id="btn_edit_acabado" class="btn btn-primary" type="button"><i class="fa fa-pencil"></i> Acabado</button>
                                <button id="btn_add_acabado" class="btn btn-primary" type="button"><i class="fa fa-plus"></i> Acabado</button>
                                <button id="btn_recuperar_acabado" class="btn btn-primary" type="button"><i class="fa fa-trash"></i> Recuperar</button>
                                <button id="btn_del_acabado" class="btn btn-danger" type="button"><i class="fa fa-trash"></i> Acabado</button>
                                <a  id="btn_download_pdf" href="{!!url('mtto_acabados_PDF')!!}"
                                target="_blank"
                                class="btn btn-danger" type="button"><i class="fa fa-file-pdf-o"></i> PDF</a>
                            </span>
                        </div><!-- /input-group -->
                  
            
        </div>
       
    </div><br>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="table_acabados" class="table table-striped table-bordered nowrap" width="100%">
                    <thead>
                        <tr>
                        <th>HIDE ID</th>
                        <th>Acción</th>
                        <th>Código</th>
                        <th>Código Sustituto</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_add_acabado" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Agregar Acabado <codigo id='text_categoria'></codigo></h4>
                </div>
                <div class="modal-body" style='padding:16px'>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sel_codigo_acabado">Código del acabado</label>
                                
                                <select data-live-search="true" class="boot-select form-control" id="sel_codigo_acabado" 
                                    name="sel_codigo_acabado" title="Selecciona...">
                                    
                                    @foreach ($codigos_acabados as $item)
                                    <option value="{{$item->Acabado}}">
                                        <span>{{$item->Acabado}}</span>
                                    </option>
                                    @endforeach
                                </select>
                                
                                <br>
                                <label for="text_acabado_descripcion">Descripción del acabado</label>
                                <input type="text" id="text_acabado_descripcion" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <a id='btn_guarda_acabado' class="btn btn-success">Agregar</a>
                </div>
    
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_edit_acabado" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Editar Acabado <codigo id='text_categoria'></codigo></h4>
                </div>
                <div class="modal-body" style='padding:16px'>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sel_codigo_acabado">Código del acabado</label>
                                <input type="text" id="text_codigo_acabado" class="form-control" readonly>
                                
                                <br>
                                <label for="text_acabado_descripcion">Descripción del acabado</label>
                                <input type="text" id="text_edit_acabado_descripcion" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <a id='btn_editar_acabado' class="btn btn-success">Guardar</a>
                </div>
    
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_recuperar_acabado" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Recuperar Acabado Eliminado<codigo id='text_categoria'></codigo></h4>
                </div>
                <div class="modal-body" style='padding:16px'>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sel_recuperar_acabado">Código del acabado</label>
                                
                                <select data-live-search="true" class="boot-select form-control" id="sel_recuperar_acabado" 
                                    name="sel_recuperar_acabado" title="Selecciona...">
                                    
                                    @foreach ($recuperar_acabados as $item)
                                    <option value="{{$item->DESCDATO}}">
                                        <span>{{$item->CODIDATO}}</span>
                                    </option>
                                    @endforeach
                                </select>
                                
                                <br>
                                <label for="text_recuperar_acabado_descripcion">Descripción del acabado</label>
                                <input type="text" id="text_recuperar_acabado_descripcion" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <a id='btn_guarda_recuperar' class="btn btn-success">Recuperar</a>
                </div>
    
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_edit_material" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"> Material Acabado</h4>
                </div>

                <div class="modal-body" style='padding:16px'>
                    <input id="material_id" type="hidden">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sel_codigo">Código</label>
                                <select data-live-search="true" class="boot-select form-control" id="sel_codigo" 
                                name="sel_codigo" title="Selecciona...">
                                    
                                    @foreach ($oitms_negro as $item)
                                    <option value="{{$item->ItemCode}}">
                                        <span>{{$item->ItemCode."  -  ".$item->ItemName}}</span>
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="button" id="check" hidden>
                    </div><!-- /.row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sel_surtir">Código a Surtir</label>
                                <select data-live-search="true" class="boot-select form-control" id="sel_surtir" 
                                name="sel_surtir" title="Selecciona...">
                                   
                                    @foreach ($oitms_otros as $item)
                                    <option value="{{$item->ItemCode}}">
                                        <span>{{$item->ItemCode."  -  ".$item->ItemName}}</span>
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                    </div><!-- /.row -->                                       
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button id='btn_guarda_material'class="btn btn-primary"> Guardar</button>
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
            
            $('#btn_add_code').prop('disabled', true);
            $('#btn_edit_acabado').prop('disabled', true);
            $('#btn_del_acabado').prop('disabled', true);
         
       
            var data,
            tableName = '#table_acabados',
            table_acabados;
        $(window).on('load', function() {
            var xhrBuscador = null;
            createTable();
            var wrapper = $('#page-wrapper');
            var resizeStartHeight = wrapper.height();
            var height = (resizeStartHeight * 65)/100;
            if ( height < 200 ) {   
                height = 200;
            }
            console.log('height_datatable' + height)
            console.log('wrapp' + wrapper)
            console.log('resizeStartHeight' + resizeStartHeight)
            console.log('(resizeStartHeight *70)/100' + resizeStartHeight *75)
            function createTable(){
                table_acabados = $(tableName).DataTable({
                    deferRender: true,
                    "paging": true,
                    dom: 'lrftip',
                    "pageLength": 10,
                    "lengthMenu": [[100, 50, 25, 10, -1], [100, 50, 25, 10, "Todo"]],
                    scrollX: true,
                    scrollY: height,
                    
                    ajax: {
                        url: '{!! route('datatables_acabados') !!}',
                        data: function (d) {                            
                            d.acabado_code = $('#sel_acabado').val()
                                 
                        }              
                    },
                    processing: true,
                    columns: [   
                        {"data" : "ID"},
                        {data: "ACA_Eliminado", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                            if ( oData.PCXC_Activo != 0 ) {
                                $(nTd).html("<a id='btneliminar' role='button' class='btn btn-sm btn-danger' style='margin-right: 5px;'><i class='fa fa-trash'></i></a><a id='btnedit role='button' class='btn btn-sm btn-primary' style='margin-right: 5px;'><i class='fa fa-exchange'></i></a>");
                            }
                        }},
                        {"data" : "Arti", 
                        render: function ( data, type, row ) {
                        return '<b>'+ row['Arti'] + '</b> - '+ row['inval01_al0102'] ;
                        }},
                        {"data" : "Surtir",
                        render: function ( data, type, row ) {
                        return '<b>'+ row['Surtir'] + '</b> - '+ row['inval01_descripcion2'] ;
                        }},
                        
                    ],
                    "language": {
                        "url": "{{ asset('assets/lang/Spanish.json') }}",
                    },
                    columnDefs: [
                        {
                            "targets": [ 0 ],
                            "visible": false
                        },
                    ],
                    "initComplete": function(settings, json) {
                     
                    }
                });
            }
       
            $("#sel_acabado").on("changed.bs.select", 
                function(e, clickedIndex, newValue, oldValue) {
                console.log(this.value)
               
                //console.log(selectedText.trim())
                if (this.value == "") {
                    $('#btn_add_code').prop('disabled', true);
                    $('#btn_edit_acabado').prop('disabled', true);
                    $('#btn_del_acabado').prop('disabled', true);
                } else {
                    var selectedText = $(this).find("option:selected").text();
                    var arrtext = selectedText.split("-");
                    var descacabado = arrtext[1].trim()
                    $('#btn_add_code').prop('disabled', false);
                    $('#btn_edit_acabado').prop('disabled', false);
                    $('#btn_del_acabado').prop('disabled', false);
                    $('#text_codigo_acabado').val(this.value);
                    $('#text_edit_acabado_descripcion').val(descacabado);
                }

                table_acabados.ajax.reload();

            });
            let recuperar_acabado = '';
            $("#sel_recuperar_acabado").on("changed.bs.select", 
                function(e, clickedIndex, newValue, oldValue) {
                recuperar_acabado = $(this).siblings('.btn.dropdown-toggle').attr('title');
                $('#text_recuperar_acabado_descripcion').val(this.value)
              
            });

            $('#table_acabados tbody').on( 'click', 'a', function (event) {
                event.preventDefault();
                var rowdata = table_acabados.row( $(this).parents('tr') ).data();

                console.log(event.currentTarget.id);
                var id = rowdata['ID'];  
                //var acabado = rowdata['CODIDATO'];  
                //var descacabado = rowdata['DESCDATO'];  
                var codigo = rowdata['Arti'];  
                var codigo_surtir = rowdata['Surtir'];  
                
                if ( event.currentTarget.id+'' == 'btneliminar' ) {
                    bootbox.confirm({
                    size: "small",
                    centerVertical: true,
                    message: "Confirma para eliminar...",
                    callback: function(result){ 
                    
                        if (result) {
                            $.ajax({
                            type: 'POST',       
                            url: '{!! route('eliminar_material_acabado') !!}',
                            data: {
                                "_token": "{{ csrf_token() }}",                       
                                id_mat : id                
                            },
                            beforeSend: function() {
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
                            complete: function() {
                                setTimeout($.unblockUI, 1500);
                                
                            }, 
                            success: function(data){
                                table_acabados.ajax.reload();
                            
                            }
                            });
                        }
                    }
                    });
                     
                }else if ( event.currentTarget.id+'' == 'btnedit' ) {
                    $('#material_id').val(id);
                    $("#sel_codigo").val(codigo);
                    $("#sel_codigo").selectpicker("refresh");
                    $("#sel_surtir").val(codigo_surtir);
                    $("#sel_surtir").selectpicker("refresh");
                    $('#modal_edit_material').modal('show');
                } else {

                  
                }
                
            });

            $('#btn_add_acabado').on('click', function (e) {
                e.preventDefault();
                $('#text_acabado_descripcion').val('');
                $('#sel_codigo_acabado').val('');
                $('#sel_codigo_acabado').selectpicker('refresh');
                $('#modal_add_acabado').modal('show');
            });
            $('#btn_edit_acabado').on('click', function (e) {
                e.preventDefault();
               
                $('#modal_edit_acabado').modal('show');
            });
            $('#btn_recuperar_acabado').on('click', function (e) {
                e.preventDefault();
                $('#text_recuperar_acabado_descripcion').val('')
                $('#modal_recuperar_acabado').modal('show');
            });
            $('#btn_del_acabado').on('click', function (e) {
                e.preventDefault();
                bootbox.confirm({
                    size: "small",
                    centerVertical: true,
                    message: "Confirma para eliminar Acabado...",
                    callback: function(result){ 
                    
                        if (result) {
                            $.ajax({
                            type: 'POST',       
                            url: '{!! route('eliminar_acabado') !!}',
                            data: {
                                "_token": "{{ csrf_token() }}",                       
                                acabado_code : $('#sel_acabado').val()                
                            },
                            beforeSend: function() {
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
                            complete: function() {
                                setTimeout($.unblockUI, 1500);
                                
                            }, 
                            success: function(data){
                                table_acabados.ajax.reload();
                                var itemSelectorOption = $('#sel_acabado option:selected');
                                
                                $("#sel_recuperar_acabado").append('<option value="'+itemSelectorOption.text()+'">'+itemSelectorOption.val()+'</option>');
                                $('#sel_recuperar_acabado').selectpicker('refresh');
                                
                                itemSelectorOption.remove(); 
                                $('#sel_acabado').selectpicker('refresh');
                            }
                            });
                        }
                    }
                    });
            });

            $('#btn_guarda_material').on('click', function (e) {
                e.preventDefault();
                var codigo_a = $('#sel_codigo').val();
                var codigo_b = $('#sel_surtir').val();                
                var codigo_acabado = ($('#sel_acabado option:selected').text()).toUpperCase();                
                var id_material = $('#material_id').val();
                if (codigo_a == '' || codigo_b == '') {
                    bootbox.dialog({
                        title: "Mensaje",
                        message: "<div class='alert alert-danger m-b-0'>No se admiten campos vacíos.</div>",
                        buttons: {
                            success: {
                                label: "Ok",
                                className: "btn-primary m-r-5 m-b-5"
                            }
                        }
                    }).find('.modal-content').css({ 'font-size': '14px' });
                } else {
                    if (codigo_a == codigo_b) {
                        bootbox.dialog({
                                title: "Mensaje",
                                message: "<div class='alert alert-danger m-b-0'>El código a surtir no puede ser el mismo.</div>",
                                buttons: {
                                    success: {
                                        label: "Ok",
                                        className: "btn-primary m-r-5 m-b-5"
                                    }
                                }
                        }).find('.modal-content').css({ 'font-size': '14px' });
                        
                    } else {
                        $.ajax({
                            type: 'POST',       
                            url: '{!! route('guarda_material_acabado') !!}',
                            data: {
                                "_token": "{{ csrf_token() }}",                       
                                id_mat : id_material,
                                codigo_acabado : codigo_acabado,
                                codigo_a : $('#sel_codigo option:selected').text(),
                                codigo_b : $('#sel_surtir option:selected').text()                
                            },
                            beforeSend: function() {
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
                            complete: function() {
                                setTimeout($.unblockUI, 1500);
                                $('#modal_edit_material').modal('hide');
                            }, 
                            success: function(data){
                                table_acabados.ajax.reload();
                                
                            }
                        });
                    }
                } 
            });
            
            $('#btn_guarda_acabado').on('click', function (e) {
                e.preventDefault();
                var codigo_nuevo = $('#sel_codigo_acabado').val();
                var existe = $('#sel_acabado').find('[value='+codigo_nuevo.toUpperCase()+']').length;                
                console.log('acabado nuevo: '+ codigo_nuevo)
                console.log('acabado existe?: '+ existe)
                if (existe == 1) {
                    bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>El código del acabado ya existe!.</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-primary m-r-5 m-b-5"
                                }
                            }
                    }).find('.modal-content').css({ 'font-size': '14px' });
                    $('#modal_add_acabado').modal('hide');
                } else {
                    if (codigo_nuevo !== '' && $('#text_acabado_descripcion').val() !== '') {                   
                        $("#sel_acabado").append('<option value="'+codigo_nuevo+'">'+codigo_nuevo+' - '+$('#text_acabado_descripcion').val()+'</option>');
                        $("#sel_acabado").val(codigo_nuevo);
                        $("#sel_acabado").selectpicker("refresh");
                        $('#btn_add_code').prop('disabled', false);
                        $('#btn_edit_acabado').prop('disabled', false);
                        $('#modal_add_acabado').modal('hide');
                        $('#btn_del_acabado').prop('disabled', false);
                        var itemSelectorOption = $('#sel_codigo_acabado option:selected');
                        itemSelectorOption.remove();
                        $('#sel_codigo_acabado').selectpicker('refresh');
                        table_acabados.ajax.reload();
                    } else {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>No se admiten campos vacíos.</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-primary m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({ 'font-size': '14px' });
                    }
                } 
            });
            $('#btn_guarda_recuperar').on('click', function (e) {
                e.preventDefault();
                    if (recuperar_acabado !== '' && $('#text_recuperar_acabado_descripcion').val() !== '') {  
                        $.ajax({
                            type: 'POST',       
                            url: '{!! route('dbrecuperar_acabado') !!}',
                            data: {
                                "_token": "{{ csrf_token() }}",                       
                                acabado_code : recuperar_acabado ,
                                acabado_descr: $('#text_recuperar_acabado_descripcion').val()
                            },
                            beforeSend: function() {
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
                            complete: function() {
                                setTimeout($.unblockUI, 1500);
                                
                            }, 
                            success: function(data){
                                $("#sel_acabado").append('<option value="'+recuperar_acabado+'">'+recuperar_acabado+' - '+$('#text_recuperar_acabado_descripcion').val()+'</option>');
                                $("#sel_acabado").val(recuperar_acabado);
                                $("#sel_acabado").selectpicker("refresh");
                                
                                $('#btn_add_code').prop('disabled', false);
                                $('#btn_edit_acabado').prop('disabled', false);
                                $('#modal_recuperar_acabado').modal('hide');
                                $('#btn_del_acabado').prop('disabled', false);
                                
                                var itemSelectorOption = $('#sel_recuperar_acabado option:selected');
                                itemSelectorOption.remove();
                                $('#sel_recuperar_acabado').selectpicker('refresh');
                                table_acabados.ajax.reload();
                            }
                        });                 
                       
                    } else {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>No se admiten campos vacíos.</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-primary m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({ 'font-size': '14px' });
                    }
                
            });

            $('#btn_add_code').on('click', function (e) {
               
                $('#material_id').val('');
                $("#sel_codigo").val('');
                $("#sel_codigo").selectpicker("refresh");
                $("#sel_surtir").val('');
                $("#sel_surtir").selectpicker("refresh");
                $('#modal_edit_material').modal('show');
            });

            $('#btn_editar_acabado').on('click', function (e) {
                e.preventDefault();
                bootbox.confirm({
                    size: "small",
                    centerVertical: true,
                    message: "Confirma para Guardar Acabado...",
                    callback: function(result){ 
                    
                        if (result) {
                            $.ajax({
                            type: 'POST',       
                            url: '{!! route('guardar_acabado') !!}',
                            data: {
                                "_token": "{{ csrf_token() }}",                       
                                acabado_code : $('#text_codigo_acabado').val(),              
                                descacabado : $('#text_edit_acabado_descripcion').val()                
                            },
                            beforeSend: function() {
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
                            complete: function() {
                                setTimeout($.unblockUI, 1500);
                                
                            }, 
                            success: function(data){
                                //table_acabados.ajax.reload();
                                var itemSelectorOption = $('#sel_acabado option:selected');
                                itemSelectorOption.remove(); 
                                var acabado_code = $('#text_codigo_acabado').val()
                                var descacabado = $('#text_edit_acabado_descripcion').val()

                                $("#sel_acabado").append('<option value="'+acabado_code+'">'+ acabado_code+ ' - ' + descacabado+'</option>');                                
                                
                                $('#sel_acabado').val(acabado_code);
                                $('#sel_acabado').selectpicker('refresh');
                                $('#modal_edit_acabado').modal('hide');
                            }
                            });
                        }
                    }
                    });
            });
        }); //fin on load
    } //fin js_iniciador               
</script>