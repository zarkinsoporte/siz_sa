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
                        <div class="col-md-12">
                            <h3 class="">
                              Actualizar Precios
                                <small><b>{{' LISTA DE PRECIOS #'.$deplist.' - '.$deplistname}}</b></small>
                            
                            </h3> 
                            <div class="pull-right">
                                
                            </div>
                            
                            <div class="row pull-right">
                                    <a style="margin-right: 5px;" id="btn_enviar" class="btn btn-success btn-sm" data-operacion='1'><i
                                        class="fa fa-send"></i> Actualizar <span class="badge"></span></a>
                                    @if (!$hide_rollout)
                                        <button  style="margin-right: 5px;" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#confirma">
                                            <i class="fa fa-cogs" aria-hidden="true"></i> Roll Out
                                        </button>
                                    @endif
                                        
                                   
                            </div>
                                                                   
                        </div>
                          <input type="text" style="display: none" class="form-control input-sm" id="input-lista" value="{{$deplist}}">
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                            
                            <div class="">
                                <div class="">
                                    <div class="">                                                                                                                  
                                      <div >
                                            <div >
                                                <div >
                                                    <div >
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="table-scroll" id="registros-ordenes-venta">
                                                                    <table id="tabla_arts" class="table table-striped table-bordered hover" width="100%">
                                                                        <thead>
                                                                            <tr>              
                                                                                <th>Código</th>
                                                                                <th>Descripción</th>
                                                                                <th>UM</th>                                        
                                                                                <th>UM Compras</th>
                                                                                <th>F. Conversión</th>
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
                                                    <label for="fecha_provision">Moneda</label>
                                                <select class="form-control" id="moneda_nueva" 
                                                name="moneda_nueva" style="margin-bottom: 10px;" 
                                                class="form-control selectpicker"
                                                @if ($hide_rollout)
                                                    {{'disabled'}}
                                                @endif
                                                >
                                                    <option value=""> Selecciona una moneda </option>
                                                    <option value="MXP">MXP</option>
                                                    <option value="USD">USD</option>
                                                    <option value="CAN">CAN</option>
                                                        
                                                </select>
                                                </div>
                                            </div>
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
                                    <button id='btn-actualiza-precio'class="btn btn-primary"> Actualizar</button>
                                </div>
                    
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="confirma" tabindex="-1" role="dialog" >
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="pwModalLabel">Ejecución RollOut</h4>
                                </div>
                             
                                <div class="modal-body">

                                    <div class="form-group">
                                        <div>
                                           <h4>¿Desea continuar?</h4>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                    <button type="button" id="btn_roll" class="btn btn-primary">Guardar</button>
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
    $("#precio_nuevo").click(function(){
     $("#ch1").prop("checked", true);
     $("#ch2").prop("checked", false);
    });
    $("#precio_porcentaje").click(function(){
     $("#ch2").prop("checked", true);
     $("#ch1").prop("checked", false);
    });
    $(window).on('load', function () {

        /*GENERAR OP*/
    var table = $("#tabla_arts").DataTable({
        language: {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        scrollX: true,
        scrollY: "410px",
        dom: 'Brtip',
        buttons: [
            {
                text: '<i class="fa fa-check-square"></i>',
                titleAttr: 'seleccionar',
                action: function() {
                    table.rows({
                        page: 'current'
                    }).select();
                    var count = table.rows( '.selected' ).count();
                    var $badge = $('#btn_enviar').find('.badge'); 
                    $badge.text(count);
                }
            },
            {
                text: '<i class="fa fa-square"></i>',
                titleAttr: 'deseleccionar',
                action: function() {
                    table.rows({
                        page: 'current'
                    }).deselect();
                    var count = table.rows( '.selected' ).count();
                    var $badge = $('#btn_enviar').find('.badge'); 
                    $badge.text(count);
                }
            }
            
            ],
        scrollCollapse: true,
        deferRender: true,
        pageLength: -1,
        columns: [
            { data: "codigo" },
            { data: "descripcion" },
            { data: "um" },
            { data: "umc" },
            { data: "factor_conversion" },
            { data: "precio" },
            { data: "moneda" }

        ],
        'columnDefs': [
                {

                "targets": [ 4 ],
                "searchable": false,
                "orderable": false,
                "render": function ( data, type, row ) {

                    if(row['factor_conversion'] != ''){

                        return number_format(row['factor_conversion'],4,'.',',');

                    }
                    else{

                        return '';

                    }

                }

            },
            {

                "targets": [ 5 ],
                "searchable": false,
                "orderable": false,
                "render": function ( data, type, row ) {

                    if(row['precio'] != ''){

                        return number_format(row['precio'],4,'.',',');

                    }
                    else{

                        return '0.0000';

                    }

                }

            }
        ],
       
    });

    $('#tabla_arts thead tr').clone(true).appendTo( '#tabla_arts thead' );

    $('#tabla_arts thead tr:eq(1) th').each( function (i) {
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

    reload_tabla_arts(true);

    $('#tabla_arts tbody').on('click', 'tr', function (e) {
        if ($(e.target).hasClass("ignoreme")) {

        } else {
            $(this).toggleClass('selected');
        }
        //var ordvta = table.rows('.selected').data();
        //var registros = ordvta == null ? 0 : ordvta.length;

        var count = table.rows('.selected').count();
        var $badge = $('#btn_enviar').find('.badge');
        $badge.text(count);

        //console.log(registros);
    });

    $('#btn_enviar').on('click', function (e) {
        e.preventDefault();
        //var oper = $('#btn_enviar').attr('data-operacion');
        click_programar_cambios();
            
    });
    $('#btn_roll').on('click', function (e) {
        e.preventDefault();
        
        $.ajax({
                type: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    "_token": "{{ csrf_token() }}",
                    priceList: $('#input-lista').val()
                },
                url: '{!! route('process-rollout') !!}',
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
                    //rollout
                    
                    setTimeout($.unblockUI, 1500);
                   setTimeout( function () { bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-success m-b-0'>Proceso Terminado</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-success m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({ 'font-size': '14px' });
                    } ,2000)
                },
                success: function (data) {
                    setTimeout(function () {
                                var respuesta = JSON.parse(JSON.stringify(data));
                                console.log(respuesta)
                                if(respuesta.codigo == 302){
                                    window.location = '{{ url("auth/login") }}';

                                }
                            }, 2000);

                    reload_tabla_arts(false);
                    var $badge = $('#btn_enviar').find('.badge');
                    $badge.text('');
                    $('#updateprogramar').modal('hide');
                    $('#precio_nuevo').val('');
                    $('#moneda_nueva').val('').selectpicker('refresh');
                    $('#precio_porcentaje').val('');
                    $('#confirma').modal('hide');
                            //console.log(data)
                    if (data.mensaje.includes('Error')) {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>" + data.mensaje + "</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-success m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({ 'font-size': '14px' });
                    }else if (data.mensaje.includes('Aviso')) {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-info m-b-0'>" + data.mensaje + "</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-success m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({ 'font-size': '14px' });
                    } else{

                    }
                   
                }
            });
        
            
    });
    $('#tabla_arts tbody').on('dblclick','tr',function(e){
        var fila = table.rows(this).data()
        var num = parseFloat(fila[0]['precio']).toFixed(4);
        var code = fila[0]['codigo'];
        var moneda = fila[0]['moneda'];
        $('#precio_nuevo').val(num)
        $('#moneda_nueva').val(moneda).selectpicker('refresh');
        $("#ch1").prop("checked", true);
        $("#ch2").prop("checked", false);
        $('#updateprogramar').modal('show');
         
     
        table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            if(this.data().codigo === code){
                var node=this.node();
               // console.log($(node).hasClass("selected"))
                if ( $(node).hasClass("selected")) {

                } else {
                    $(node).toggleClass('selected');
                }
                var count = table.rows('.selected').count();
                var $badge = $('#btn_enviar').find('.badge');
                $badge.text(count);
            }
        } );

        
    })

    function click_programar_cambios() {
        var countOP = table.rows('.selected').count();
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
            if (countOP > 1) {
                $('#precio_nuevo').val('')
                $('#moneda_nueva').val('').selectpicker('refresh');
            } else {
                var fila = table.rows('.selected').data()
                var num = parseFloat(fila[0]['precio']).toFixed(4)
                var moneda = fila[0]['moneda']
                $('#precio_nuevo').val(num)
                $('#moneda_nueva').val(moneda).selectpicker('refresh');
            }
            $("#ch1").prop("checked", true);
            $("#ch2").prop("checked", false);
            $('#updateprogramar').modal('show');
        }
    }

    $('#btn-actualiza-precio').on('click', function (e) {
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
        var ordvta = table.rows('.selected').data();
        //var ordvtac = table.rows('.selected').node();
        //console.log(ordvtac[0])
        var ops = '';
        var registros = ordvta == null ? 0 : ordvta.length;
        for (var i = 0; i < registros; i++) {
            if (i == registros - 1) {
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
                    moneda_nueva: $( "#moneda_nueva option:selected" ).val(),
                    precio_porcentaje: $('#precio_porcentaje').val(),
                    option: option,
                    priceList: $('#input-lista').val()
                },
                url: '{!! route('actualizarPrecios') !!}',
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
                    reload_tabla_arts(false);
                    var $badge = $('#btn_enviar').find('.badge');
                    $badge.text('');
                    $('#updateprogramar').modal('hide');
                    $('#precio_nuevo').val('');
                    $('#moneda_nueva').val('').selectpicker('refresh');
                    $('#precio_porcentaje').val('');
                            console.log(data.mensajeErr)
                    if (data.mensajeErr.includes('Error')) {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>" + data.mensajeErr + "</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-success m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({ 'font-size': '14px' });
                    }else{
                       
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

    function reload_tabla_arts(asyncc) {
        $("#tabla_arts").DataTable().clear().draw();
        $.ajax({
        type: 'GET',
        async: asyncc,
        url: '{!! route('datatables.arts') !!}',
        data: {
            deplist: $('#input-lista').val()
        },
        beforeSend: function () {
            if (asyncc) {
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
            }
            
        },
        complete: function () {
            if (asyncc) {
                setTimeout($.unblockUI, 1500);
            }
            
        },
        success: function (data) {

            if (data.arts.length > 0) {
                $("#tabla_arts").dataTable().fnAddData(data.arts);
            } else {

            }
        }
    });
    }

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
});//fin on load

}  //fin js_iniciador               
function val_btn(val) {

    $('#btn_enviar').attr('data-operacion', val);
}
                </script>
