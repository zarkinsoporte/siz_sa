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
   
    var COL_BTN_EDITAR = 0;
    var COL_BTN_ELIMINAR = 1;
    var COL_BTN_PDF = 2;
    var estado_text = 'Cerrar';
    var estado_text2 = 'CERRADA';
    var NumOC_elimina = '';
    var datos_elimina = [];
    var fila_elimina = null;
    $(document).on('click', '#boton-cerrarOC', function (e) {
        e.preventDefault();
        btn_eliminar_OC(0);
    });
    $(document).on('click', '#boton-cancelarOC', function (e) {
        e.preventDefault();
        btn_eliminar_OC(1);
    });
    
    consultaDatos();
    InicializaComboBox();
    $('#ordenesCompraOC').hide();
    $('#btnBuscadorOC').show();
    InicializaBuscadorArticulos()
    cargaTablaArticulos();
    InicializaTablas();
    
     $("#input-fecha").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true
    }).on("change", function() {
          
    });
    $('#input-fecha').datepicker('setDate', new Date());
    
    var today = new Date();
    var dd = today.getDate()+1;
    var mm = today.getMonth() + 1; //January is 0!

    var yyyy = today.getFullYear();
    if (dd < 10) { dd = '0' + dd }
    if (mm < 10) { mm = '0' + mm }
    today = yyyy + '-' + mm + '-' + dd; 
    console.log(today)
    $('#input-fecha-entrega').val(today);
    //$('#input-fecha-entrega').val(today);

    $("#input_date").daterangepicker({
        
        autoUpdateInput: false,
        format: "DD/MM/YYYY",
        "locale": {
            "separator": " - ",
            "applyLabel": "Cargar OC",
            "cancelLabel": "Cancelar",
            "fromLabel": "DE",
            "toLabel": "HASTA",
            "customRangeLabel": "Custom",
            "daysOfWeek": [
                "Dom",
                "Lun",
                "Mar",
                "Mie",
                "Jue",
                "Vie",
                "Sáb"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            "firstDay": 1
    }}, 
    function(start, end, label) {
        //alert("A new date range was chosen: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        reloadOrdenes(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
    });
    function btn_eliminar_OC(cancelar_OC){
        var tblOC = $('#tableOC').DataTable();
        if (cancelar_OC == 1) {
                estado_text = 'Cancelar';
                estado_text2 = 'CANCELADA';
            } else {
                estado_text = 'Cerrar';
                estado_text2 = 'CERRADA';
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                async: false,
                data: {
                    docNum: NumOC_elimina,
                    cancelar: cancelar_OC
                },
                dataType: "json",
                url: routeapp + "cancelOC",
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
                success: function (data) {
                    //reloadBuscadorOC();
                    //console.log(data)
                    $.unblockUI();

                    if (data.Status == 'Valido') {
                        //console.log(data)  
                        
                        datos_elimina['Estatus'] = estado_text2;
                        tblOC.row(fila_elimina).data(datos_elimina);
                        
                        bootbox.alert({
                            size: "large",
                            title: "<h4><i class='fa fa-info-circle'></i> Ordenes de Compra</h4>",
                            message: "<div class='alert alert-success m-b-0'> " + "OC" + NumOC + ' ' + estado_text2 +" </div> "
                        });
                    } else {
                        bootbox.alert({
                            size: "large",
                            title: "<h4><i class='fa fa-info-circle'></i> Ordenes de Compra</h4>",
                            message: "<div class='alert alert-danger m-b-0'> " + data.Mensaje + " </div> "
                        });
                        
                    }

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $.unblockUI();
                    //console.log(xhr.responseText)
                    var error = [];
                    bootbox.alert({
                        size: "large",
                        title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                        message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                            (error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '') +
                            (error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '') +
                            (error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '') + '</div>'
                    });
                }
            });
        
    }
  function InicializaComboBox()  {
        $('#cboMoneda').selectpicker({
            noneSelectedText: 'Selecciona una opción',
        });
        $('.selectpicker').selectpicker({
            noneSelectedText: 'Selecciona una opción',
            container: "body"
        });
        
        
        var options = [];
        var opciones = [ 
            { 'llave': 'MXP', 'valor': 'MXP' },
            { 'llave': 'USD', 'valor': 'USD' },
            { 'llave': 'EUR', 'valor': 'EUR' },
        ];
        for (var i = 0; i < opciones.length; i++) {
            options.push('<option value="' + opciones[i]['llave'] + '">' + opciones[i]['valor'] + '</option>');
        }
        $('#cboMoneda').append(options);
        $('#cboMoneda').selectpicker('refresh');
        
        $('#cboTPM').selectpicker('refresh');
        $('#sel-tipo-oc').val(0);
        $('#sel-tipo-oc').selectpicker('refresh');
        $("#miscelaneosOC").hide();
    };

    document.onkeyup = function(e) {
        if (e.shiftKey && e.which == 112) {
            var namefile= 'RG_'+$('#btn_pdf').attr('ayudapdf')+'.pdf';
            //console.log(namefile)
            $.ajax({
                 headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: assetapp + "ayudas_pdf/"+namefile,
            type:'HEAD',
            error: function()
            {
                //file not exists
                window.open(assetapp + "ayudas_pdf/AY_00.pdf","_blank");
            },
            success: function()
            {
                //file exists
                var pathfile = assetapp + "ayudas_pdf/"+namefile;
                window.open(pathfile,"_blank");
            }
            });

            
        }
    }

// set_columns_index(0)
// Inicializa tabla oc
var tableOC = $("#tableOC").DataTable({
                language:{
                     "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                }, 
                dom: 'TBlrti',
                order: [[1, 'desc']],
                buttons: [],
                scrollX: true,
                scrollY: "450px",
                scrollCollapse: true,
                deferRender: true,        
                pageLength:-1,
                "paging": false,
                createdRow: function (row, data, dataIndex) {
                    //console.log(data)
                    $(row).attr('data-id', data.DocEntry);
                },
                buttons:[
                    //boton-nuevo
                    {
                        text: '<i class="fa fa-plus"></i> Nuevo',
                        className: "btn-primary",
                        action: function (e, dt, node, config) {
                            OC_nueva = 1;
                            MuestraComponentesOC(OC_nueva)
                            $('#ordenesCompraOC').show();
                            $('#btnBuscadorOC').hide();
                            InicializaComponentesOC();

                            $("#articulosOC").show();
                            $("#miscelaneosOC").hide();
                            BanderaOC = 0;
                            set_columns_index(0);
                            $('#guardar2').attr("id", "guardar");
                            $('#guardar2').removeAttr("disabled");
                            $('#guardar').attr("id", "guardar");
                            $('#guardar').removeAttr("disabled");
                        }
                    }
                ],
                columns: [                   
                    {data: "BTN_EDITAR"},    
                    {data: "NumOC"},
                    {data: "Proveedor"},
                    {data: "Estatus"},
                    {data: "Total"},
                    {data: "Moneda"},
                    {data: "FechaOC"},
                    {data: "Comentario"}
                ],
                'columnDefs': [
                {
                    "targets": [ COL_BTN_EDITAR ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {
                        if(row['Estatus'] !== 'ABIERTA')  
                            return '<button type="button" class="btn btn-sm btn-primary" id="btnEditar"> <span class="glyphicon glyphicon-pencil"></span> </button>'
                            +'<button type="button" class="btn btn-sm btn-danger btn-outline-danger" style="margin-left:5px" id="boton-pdf"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>';
                        else  
                        return '<button type="button" class="btn btn-sm btn-primary" id="btnEditar"> <span class="glyphicon glyphicon-pencil"></span> </button>'
                            + '<button type="button" class="btn btn-sm btn-danger" style="margin-left:5px" id="btnEliminar"> <span class="glyphicon glyphicon-trash"></span></button>'
                            + '<button type="button" class="btn btn-sm btn-danger btn-outline-danger" style="margin-left:5px" id="boton-pdf"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>';
                       

                    }

                },
                {

                    "targets": [ 4 ],
                    "searchable": false,
                    "orderable": false,
                    "render": function ( data, type, row ) {

                            return number_format(row['Total'],3,'.',',');

                    }

                } 
        ],       
});

$('#tableOC thead tr').clone(true).appendTo( '#tableOC thead' );
$('#tableOC thead tr:eq(1) th').each( function (i) {
    var title = $(this).text();
    $(this).html( '<input style="color: black;"  type="text" placeholder="Filtro '+title+'" />' );

    $( 'input', this ).on( 'keyup change', function () {       
            
            if ( tableOC.column(i).search() !== this.value ) {
                tableOC
                    .column(i)
                    .search(this.value, true, false)
                    
                    .draw();
            } 
                
    } );    
} );    
reloadBuscadorOC();
$('#tableOC tbody').on( 'click', 'tr', function () {
} );

$('#tableOC').on( 'click', 'button#boton-pdf', function (e) {
    e.preventDefault();

    $.blockUI({ css: {
        border: 'none',
        padding: '15px',
        backgroundColor: '#000',
        '-webkit-border-radius': '10px',
        '-moz-border-radius': '10px',
        opacity: .5,
        color: '#fff'
    } });

    var tipoReporte = '';
    var tipoFormato = 'pdf';
    var isChkPaginar = true;
    var isChkMostrarLogo = true;
    var tblOC = $('#tableOC').DataTable();
    var fila = $(this).closest('tr');
    var datos = tblOC.row(fila).data();
    NumOC = datos['NumOC'];
    window.open(routeapp + 'orden_compra_pdf/'+NumOC, '_blank')
    
    $.unblockUI();
});
    
$('#tableOC').on('click', 'button#btnEliminar', function (e) {
    e.preventDefault();
    var tblOC = $('#tableOC').DataTable();
    var fila = $(this).closest('tr');
    fila_elimina = $(this).closest('tr');
    var docentry = fila.attr('data-id')
    var datos = tblOC.row(fila).data();
    datos_elimina = tblOC.row(fila).data();
    NumOC_elimina = docentry;
    NumOC = datos['NumOC'];
    
    if (datos['Estatus'] !== 'CANCELADA') {//datos['Estatus'] == 'ABIERTA'

        //text: '<p></p> <div><button class="btn btn-secondary">Cancelar</button> <button class="btn btn-danger" id="boton-cancelarOC">Cancelar OC</button> <button class="btn btn-primary" id="boton-cerrarOC">Cerrar OC</button>  </div>',
        swal({
            title: '¿Cancelar Orden de Compra?.',
            html: true,
            text: '<p></p> <div><button class="btn btn-secondary">No</button> <button class="btn btn-danger" id="boton-cerrarOC">Cancelar OC</button>  </div>',
            type: "warning",
            showConfirmButton: false,
            showCancelButton: false
        });

        // swal({
        //     title: '¿Estas seguro de '+ estado_text +' la Orden de Compra?.',
        //     text: "",
        //     type: 'warning',
        //     showCancelButton: true,
        //     confirmButtonText: 'OK',
        //     cancelButtonText: 'Cancelar',
        //     closeOnConfirm: false,
        //     showLoaderOnConfirm: false,
        // }, function () {
            
              

             
       // });
    } else {
        swal("", "La OC esta Cancelada", "error", {
            buttons: false,
            timer: 2000,
        });   
    }
    
});
 
    
$('#boton-cerrar').off().on('click', function(e) {

    $('#ordenesCompraOC').hide();
    $('#btnBuscadorOC').show();
    reloadBuscadorOC();
    InicializaComponentesOC();
    $("#tblArticulosExistentesNueva").DataTable().clear().draw();
    $("#tblArticulosMiscelaneosNueva").DataTable().clear().draw();
    //$("#tblArticulosExistentesResumenNueva").DataTable().clear().draw();
    //$("#tblArticulosMiscelaneosResumenNueva").DataTable().clear().draw();
    calculaTotalOrdenCompra();
    //calculaTipoCambio();
});
$('#boton-nuevo').on('click', function(e) {
   
});
$('#boton-mostrar-oc').on('click', function(e) {
    get_oc();
});
$('#boton-mostrar-calendar').on('click', function(e) {
   // $('#input_date').trigger('show.daterangepicker');
    $('#input_date').click();
});

$('#boton-mostrar-serie').on('click', function(e) {
    //getserie_empaque();
});
$(document).keyup(function(event) {
    if ($("#input_oc").is(":focus") && event.key == "Enter") {
        get_oc();
    }
    
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
    $("#cboMoneda").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
        e.preventDefault();
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
        var tipo_cambio_anterior = $('#input_tc_anterior').val();
        console.log('cambiando moneda: ')
        var moneda_anterior = oldValue;
        var moneda = $('option:selected', this).val();
        console.log('cboMoneda: '+ oldValue + ' > '+ moneda + ' tc_ant:'+tipo_cambio_anterior)
        carga_tipo_cambio(moneda);
        
        console.log('cboMoneda: ' + moneda_anterior + ' > '+ moneda + ' tc_ant:'+tipo_cambio_anterior)
        calculaNuevaMoneda(moneda_anterior, moneda, tipo_cambio_anterior);
       
        $("#input_tc_anterior").val($("#input_tc").val());
        recargaTablaArticulos('');
        cargaTablaArticulos();
        setTimeout($.unblockUI, 2000);
    });
    $('#input_tc').change(function (e){
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
        var moneda = $('#cboMoneda').val();       
        var tipo_cambio_anterior = $('#input_tc_anterior').val(); 
        calculaNuevaTipoCambio(moneda, tipo_cambio_anterior);

        recargaTablaArticulos('');
        cargaTablaArticulos();
        setTimeout($.unblockUI, 2000);
        swal("", "Tipo Cambio Aplicado...", "success", {
            buttons: false,
            timer: 2000,
        });
    });
    $('#input-fecha-entrega').change(function (e){
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
        actualiza_fecha_entrega()
        setTimeout($.unblockUI, 2000);
    });
    function actualiza_fecha_entrega() {
        var tabla_nombre = '#tblArticulosExistentesNueva';
        if (BanderaOC == 1) {
            tabla_nombre = '#tblArticulosMiscelaneosNueva';
        }
        var tabla = $(tabla_nombre).DataTable();
        var fila = $(tabla_nombre+' tbody tr').length;
        var datos_Tabla = tabla.rows().data();
        //var tbl_datos = new Array();

        if (datos_Tabla.length != 0) {
            //var siguiente = 0;
            //var producto = '';
            var fecha_entrega = $('#input-fecha-entrega').val();
            for (var i = 0; i < fila; i++) {
                console.log(datos_Tabla[i])
                //producto = datos_Tabla[i]["PRODUCTO"]
                $('input#input-fecha-entrega-linea', tabla.row(i).node()).val(fecha_entrega)

            }
            

        }

    }
    $("#sel-proveedor").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
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
        var proveedor_moneda = $('option:selected', this).attr("data-moneda");
        if (proveedor_moneda != $("#cboMoneda").val()) {
            $("#cboMoneda").val(proveedor_moneda);
            $('#cboMoneda').selectpicker('refresh');
            carga_tipo_cambio(proveedor_moneda);
            recargaTablaArticulos('');
            cargaTablaArticulos();
            //$('#tblArticulosExistentesNueva').DataTable().clear().draw();
            //$('#tblArticulosMiscelaneosNueva').DataTable().clear().draw();
            //insertarFila();   
        }
        var proveedorId = $('option:selected', this).val();
        carga_info_proveedor(proveedorId);
        
        setTimeout($.unblockUI, 2000);   
    });
    $("#sel-estatus-oc").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
        reloadBuscadorOC(); 
    });

    $('#tblArticulosExistentesNueva').on('click', 'a#boton-articuloAE', function (e) {
        e.preventDefault();
        console.log('moneda_val: ' + $("#cboMoneda").val())
        var moneda_a = $("#cboMoneda").val()
        if ($("#sel-proveedor").val() != "" && moneda_a) {
            var tabla = $('#tblArticulosExistentesNueva').DataTable();
            var fila = $(this).closest('tr');
            fila = tabla.row(fila).index();
            $('#modal-articulo #input-fila').val(fila);
            $('#modal-articulo').modal('show');
        } else {
            swal("", "No se ha elegido Proveedor o Moneda.", "error", {
                buttons: false,
                timer: 2000,
            });
        }

});

    
    window.onload = function () { 
        //tableOC.columns.adjust().draw();     
    }
    


$('#guardar').off().on('click', function(e) {

    var estadoOC = $('#estadoOC').text();
    if (estadoOC == 'CERRADA'){
    
        swal("", "La OC esta Cerrada", "error", {
            buttons: false,
            timer: 2000,
        });

    }
    else{
        
        swal({
            title: '¿Guardar Orden de Compra?.',
            text: "",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: " Guardar",
            cancelButtonText: " Cancelar",
        }, function () {
                $(".sweet-alert p").html('<i class="fa fa-spinner fa-lg fa-spin fa-fw"></i> Espera un momento..')
                validarCampos();
                if (bandera == 0) { //validado, campos correctos entonces
                    registraOC();
                    $('button.confirm').attr("disabled", "disabled");
                }
            });
    }

});

$("#sel-tipo-oc").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
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
              
        $("#tblArticulosExistentesNueva").DataTable().clear().draw();
        $("#tblArticulosMiscelaneosNueva").DataTable().clear().draw();
        // $("#tblArticulosExistentesResumenNueva").DataTable().clear().draw();
        // $("#tblArticulosMiscelaneosResumenNueva").DataTable().clear().draw();
        calculaTotalOrdenCompra();
        let val = $('option:selected', this).val();
        //console.log('sel-tipoOC: '+ val);
        if (val == 0) {
            $("#articulosOC").show();
            $("#miscelaneosOC").hide();
            BanderaOC = 0;
            set_columns_index(0);
            insertarFila(BanderaOC);
        } else {
            $("#articulosOC").hide();
            $("#miscelaneosOC").show();
            BanderaOC = 1;
            set_columns_index(1);
            insertarFila(BanderaOC);
            //TBL_ART_MISC
            var tabla_artExist = $("#tblArticulosMiscelaneosNueva").DataTable();
            tabla_artExist.row(0).nodes(0, COL_IVA).to$().find("select#cboIVAAM").val("W3");
            tabla_artExist.row(0).nodes(0, COL_ID_IVA).to$().find("select#cboIVAAM").val("W3");
            tabla_artExist.row(0).nodes(0, COL_ID_IVA).to$().find("select#cboIVAAM").selectpicker('refresh');
        }
        
         //dataAE["ID_IVA"] = ivaIDOC;
        //console.log('sel-tipo-oc: '+BanderaOC)
        setTimeout($.unblockUI, 2000);
    });

$('#tblArticulosMiscelaneosNueva').on('change','input#input-nombreART-miselaneos',function (e) {

    e.preventDefault();

    var moneda_a = $("#cboMoneda").val()
    if ($("#sel-proveedor").val() != "" && moneda_a) {

        swal("", "No se ha elegido un Proveedor o Moneda.", "error", {
            buttons: false,
            timer: 2000,
        });
        $(this).val("");

    }

});

$('#tblArticulosMiscelaneosNueva').on('change','select#cboUMAM',function (e) {

    e.preventDefault();

    if ($("#sel-proveedor").val() == "") {
        swal("", "No se ha elegido un proveedor, elige uno por favor para continuar.", "error", {
            buttons: false,
            timer: 2000,
        });
        $(this).val("");
    }
});
    $('#tblArticulosExistentesNueva').on('change', 'input#input-cantidadAE', function (e) {
        e.preventDefault();
        if ($(this).val() == '' || $(this).val() < 0) {
            $(this).val(parseFloat('0.00').toFixed(DECIMALES));
        }

        $(this).val(parseFloat(this.value).toFixed(DECIMALES));

        if ($("#sel-proveedor").val() != "") {
            var tabla = $('#tblArticulosExistentesNueva').DataTable();
            var fila = $(this).closest('tr');
            var index = tabla.row(fila).index();

            var datos = tabla.row(fila).data();
            var precio = 'input-precioAE';
            var cantidad = 'input-cantidadAE';
            var descuento = 'input-descuentoAE';
            // var referenciaId = datos['ID_AUX'];
            // var Cantidad_anterior = datos['CANTIDAD'];
            // var Precio_anterior = datos['PRECIO'];
            // var Descuento_anterior = datos['DESCUENTO'];
            // var IVA_anterior = datos['IVA'];
            // var ID_IVA_anterior = datos['ID_IVA'];
            // var cantidadNueva = $(this).val();

            datos['CANTIDAD'] = $(this).val();

            if (datos['CODIGO_ARTICULO'] != "") {
                RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                //console.log(['precio ' + precio, 'cantidad ' +cantidad, 'descuento ' + descuento]);
                calculaTotalOrdenCompra();
                //calculaTipoCambio();
                // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                //     PartidaResumenAE(index);
                // }
            }
            else {
                swal("", "No se ha elegido un articulo, elige uno por favor para continuar.", "error", {
                    buttons: false,
                    timer: 2000,
                });
                $(this).val(parseFloat('0.00').toFixed(DECIMALES));
            }

        } else {
            swal("", "No se ha elegido un proveedor, elige uno por favor para continuar.", "error", {
                buttons: false,
                timer: 2000,
            });
            $(this).val("");
        }
    });

    $('#tblArticulosMiscelaneosNueva').on('change','input#input-cantidadAM',function (e) {
        e.preventDefault();
        if($(this).val() == '' || $(this).val() < 0){
            $(this).val(parseFloat('0.00').toFixed(DECIMALES));
        }

        $(this).val(parseFloat(this.value).toFixed(DECIMALES));

        if ($("#sel-proveedor").val() != ""){
            var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
            var fila = $(this).closest('tr');
            var index = tabla.row(fila).index();
            //VARIFICAR EN LOS DEMAS CAMPOS Y VERIFICAR CAMPO DE CTASMAYOR        
            var datos = tabla.row(fila).data();
            datos['NOMBRE_ARTICULO'] = tabla.row(fila).nodes(fila, 1).to$().find("input#input-nombreART-miselaneos").val();
            
            //console.log(datos)
            var precio = 'input-precioAM';
            var cantidad = 'input-cantidadAM';
            var descuento = 'input-descuentoAM';
            // var referenciaId = datos['ID_AUX'];
            // var Cantidad_anterior = datos['CANTIDAD'];
            // var Precio_anterior = datos['PRECIO'];
            // var Descuento_anterior = datos['DESCUENTO'];
            // var IVA_anterior = datos['IVA'];
            // var ID_IVA_anterior = datos['ID_IVA'];
            // var cantidadNueva = $(this).val();

            
                if (datos['NOMBRE_ARTICULO'] != ""){
                    datos['CANTIDAD'] = $(this).val();
                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                    calculaTotalOrdenCompra();
                }
                else{
                    swal("", "No se ha capturado articulo, captura para continuar.", "error", {
                        buttons: false,
                        timer: 2000,
                    });
                    $(this).val(parseFloat('0.00').toFixed(DECIMALES));
                }

        }else{
            swal("", "No se ha elegido un proveedor, elige uno por favor para continuar.", "error", {
                buttons: false,
                timer: 2000,
            });
            $(this).val("");
        }
    });

    $('#tblArticulosExistentesNueva').on('change', 'input#input-precioAE', function (e) {
        e.preventDefault();
        if ($(this).val() == '' || $(this).val() < 0) {
            $(this).val(parseFloat('0.00').toFixed(DECIMALES));
        }

        $(this).val(parseFloat(this.value).toFixed(DECIMALES));
        if ($("#sel-proveedor").val() != "") {
            var tabla = $('#tblArticulosExistentesNueva').DataTable();
            var fila = $(this).closest('tr');
            var index = tabla.row(fila).index();

            var datos = tabla.row(fila).data();
            var precio = 'input-precioAE';
            var cantidad = 'input-cantidadAE';
            var descuento = 'input-descuentoAE';
            // var referenciaId = datos['ID_AUX'];
            // var Cantidad_anterior = datos['CANTIDAD'];
            // var Precio_anterior = datos['PRECIO'];
            // var Descuento_anterior = datos['DESCUENTO'];
            // var IVA_anterior = datos['IVA'];
            // var ID_IVA_anterior = datos['ID_IVA'];
            // var precioNuevo = $(this).val();


            datos['PRECIO'] = $(this).val();

            if (datos['ID_ARTICULO'] != "") {
                RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                calculaTotalOrdenCompra();
                // calculaTipoCambio();
                // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                //     PartidaResumenAE(index);
                // }
            }
            else {
                swal("", "No se ha elegido un articulo, elige uno por favor para continuar.", "error", {
                    buttons: false,
                    timer: 2000,
                });
                $(this).val(parseFloat('0.00').toFixed(DECIMALES));
            }



        }
        else {
            swal("", "No se ha elegido un proveedor, elige uno por favor para continuar.", "error", {
                buttons: false,
                timer: 2000,
            });
            $(this).val("");
        }
    });

    $('#tblArticulosMiscelaneosNueva').on('change','input#input-precioAM',function (e) {
        e.preventDefault();
        if($(this).val() == '' || $(this).val() < 0){
            $(this).val(parseFloat('0.00').toFixed(DECIMALES));
        }

        $(this).val(parseFloat(this.value).toFixed(DECIMALES));
        if ($("#sel-proveedor").val() != ""){

            var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
            var fila = $(this).closest('tr');
            var index = tabla.row(fila).index();

            var datos = tabla.row(fila).data();
            var precio = 'input-precioAM';
            var cantidad = 'input-cantidadAM';
            var descuento = 'input-descuentoAM';
            // var referenciaId = datos['ID_AUX'];
            // var Cantidad_anterior = datos['CANTIDAD'];
            // var Precio_anterior = datos['PRECIO'];
            // var Descuento_anterior = datos['DESCUENTO'];
            // var IVA_anterior = datos['IVA'];
            // var ID_IVA_anterior = datos['ID_IVA'];
            // var precioNuevo = $(this).val();

        
                if (datos['NOMBRE_ARTICULO'] != ""){
                        datos['PRECIO'] = $(this).val();
                        RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                        calculaTotalOrdenCompra();
                        
                }
                else{
                    
                    swal("", "No se ha capturado articulo, captura para continuar.", "error", {
                        buttons: false,
                        timer: 2000,
                    });
                    $(this).val(parseFloat('0.00').toFixed(DECIMALES));
                }

        }
        else{
            swal("", "No se ha elegido un proveedor, elige uno por favor para continuar.", "error", {
                buttons: false,
                timer: 2000,
            });
            $(this).val("");
        }
    });

    $('#tblArticulosExistentesNueva').on('change', 'input#input-articulo-codigoAE', function (e) {

        if ($(this).val() !== '' ){
           
            console.log('moneda_val: ' + $("#cboMoneda").val())
            var moneda_a = $("#cboMoneda").val()
            if ($("#sel-proveedor").val() != "" && moneda_a) {
            var tbl = $('#tblArticulosExistentesNueva').DataTable();
            var fila = $(this).closest('tr');
            var index = tbl.row(fila).index();
            var dataAE = tbl.row(fila).data();

            console.log($(this).val());   
            cargaArticulo($(this).val(), index) 
           
        }
        else {
            swal("", "No se ha elegido Proveedor o Moneda.", "error", {
                buttons: false,
                timer: 2000,
            });
            $(this).val("");
        }
    }
    });
    $('#tblArticulosExistentesNueva').on('change', 'input#input-descuentoAE', function (e) {

        if ($(this).val() == '' || $(this).val() < 0) {
            $(this).val(parseFloat('0.00').toFixed(DECIMALES));
        }

        if ($(this).val() >= 100) {
            $(this).val(parseFloat('100.00').toFixed(DECIMALES));
        }

        $(this).val(parseFloat(this.value).toFixed(DECIMALES));
        if ($("#sel-proveedor").val() != "") {
            var tabla = $('#tblArticulosExistentesNueva').DataTable();
            var fila = $(this).closest('tr');
            var index = tabla.row(fila).index();

            var datos = tabla.row(fila).data();
            var precio = 'input-precioAE';
            var cantidad = 'input-cantidadAE';
            var descuento = 'input-descuentoAE';
            // var referenciaId = datos['ID_AUX'];
            // var Cantidad_anterior = datos['CANTIDAD'];
            // var Precio_anterior = datos['PRECIO'];
            // var Descuento_anterior = datos['DESCUENTO'];
            // var IVA_anterior = datos['IVA'];
            // var ID_IVA_anterior = datos['ID_IVA'];
            // var descuentoNuevo = $(this).val();

            datos['DESCUENTO'] = $(this).val();

            if (datos['ID_ARTICULO'] != "") {
                console.log("cambiando descuento ................")
                RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                calculaTotalOrdenCompra();
                // calculaTipoCambio();
                // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                //     PartidaResumenAE(index);
                // }
            }
            else {
                swal("", "No se ha elegido un articulo, elige uno por favor para continuar.", "error", {
                    buttons: false,
                    timer: 2000,
                });
                $(this).val(parseFloat('0.00').toFixed(DECIMALES));
            }

        }
        else {
            swal("", "No se ha elegido un proveedor, elige uno por favor para continuar.", "error", {
                buttons: false,
                timer: 2000,
            });
            $(this).val("");
        }
    });

    $('#tblArticulosMiscelaneosNueva').on('change','input#input-descuentoAM',function (e) {

        if($(this).val() == '' || $(this).val() < 0){
            $(this).val(parseFloat('0.00').toFixed(DECIMALES));
        }

        if ($(this).val() >= 100){
            $(this).val(parseFloat('100.00').toFixed(DECIMALES));
        }

        $(this).val(parseFloat(this.value).toFixed(DECIMALES));
        if ($("#sel-proveedor").val() != ""){

            var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
            var fila = $(this).closest('tr');
            var index = tabla.row(fila).index();

            var datos = tabla.row(fila).data();
            var precio = 'input-precioAM';
            var cantidad = 'input-cantidadAM';
            var descuento = 'input-descuentoAM';
            // var referenciaId = datos['ID_AUX'];
            // var Cantidad_anterior = datos['CANTIDAD'];
            // var Precio_anterior = datos['PRECIO'];
            // var Descuento_anterior = datos['DESCUENTO'];
            // var IVA_anterior = datos['IVA'];
            // var ID_IVA_anterior = datos['ID_IVA'];
            // var descuentoNuevo = $(this).val();
        
                if (datos['NOMBRE_ARTICULO'] != ""){    
                    datos['DESCUENTO'] = $(this).val();
                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                    calculaTotalOrdenCompra();
                
                }
                else{
                    swal("", "No se ha capturado articulo, captura para continuar.", "error", {
                        buttons: false,
                        timer: 2000,
                    });
                    $(this).val(parseFloat('0.00').toFixed(DECIMALES));
                }
        }
        else{
            
            swal("", "No se ha elegido un proveedor, elige uno por favor para continuar.", "error", {
                buttons: false,
                timer: 2000,
            });
            $(this).val("");
        }
    });

    $('#tblArticulosExistentesNueva').on('change', 'select#cboIVAAE', function (e) {
        e.preventDefault();
        if ($("#sel-proveedor").val() != "") {
            var tabla = $('#tblArticulosExistentesNueva').DataTable();
            var fila = $(this).closest('tr');
            var index = tabla.row(fila).index();

            var datos = tabla.row(fila).data();
            var precio = 'input-precioAE';
            var cantidad = 'input-cantidadAE';
            var descuento = 'input-descuentoAE';
            // var iva = 'cboIVAAM';
            // var referenciaId = datos['ID_AUX'];
            // var Cantidad_anterior = datos['CANTIDAD'];
            // var Precio_anterior = datos['PRECIO'];
            // var Descuento_anterior = datos['DESCUENTO'];
            // var IVA_anterior = datos['IVA'];
            // var ID_IVA_anterior = datos['ID_IVA'];
            var id_iva_nuevo = $(this).val();
            var iva_nuevo = $(this).val() == '' ? '0' : $(this).find('option:selected').text();



            datos["ID_IVA"] = id_iva_nuevo;
            datos["IVA"] = iva_nuevo;

            if (datos['ID_ARTICULO'] != "") {
                RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                calculaTotalOrdenCompra();
                // calculaTipoCambio();
                // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                //     PartidaResumenAE(index);
                // }
            }
            else {
                swal("", "No se ha elegido un articulo, elige uno por favor para continuar.", "error", {
                    buttons: false,
                    timer: 2000,
                });
                $(this).val(parseFloat('0.00').toFixed(DECIMALES));
            }



        }
    });

    $('#tblArticulosMiscelaneosNueva').on('change','select#cboIVAAM',function (e) {
        e.preventDefault();
        if ($("#sel-proveedor").val() != ""){
            var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
            var fila = $(this).closest('tr');
            var index = tabla.row(fila).index();

            var datos = tabla.row(fila).data();
            var precio = 'input-precioAM';
            var cantidad = 'input-cantidadAM';
            var descuento = 'input-descuentoAM';
            // var iva = 'cboIVAAM';
            // var referenciaId = datos['ID_AUX'];
            // var Cantidad_anterior = datos['CANTIDAD'];
            // var Precio_anterior = datos['PRECIO'];
            // var Descuento_anterior = datos['DESCUENTO'];
            // var IVA_anterior = datos['IVA'];
            // var ID_IVA_anterior = datos['ID_IVA'];
            var id_iva_nuevo = $(this).val();
            var iva_nuevo = $(this).val() == '' ? '0' : $(this).find('option:selected').text();


            datos["ID_IVA"] = id_iva_nuevo;
            datos["IVA"] = iva_nuevo;

            RealizaCalculos(fila, tabla, precio, cantidad, descuento);
            calculaTotalOrdenCompra();
        }
    });

    $('#tblArticulosExistentesNueva').on('click', 'a#boton-eliminarAE', function (e) {
        e.preventDefault();
        
        //var datos = tabla.row(fila).data();
        //console.log(datos['CODIGO_ARTICULO'])
        var tabla = $('#tblArticulosExistentesNueva').DataTable();
        var fila = $(this).closest('tr');
        swal({
            title: '¿Eliminar partida?',
            text: "",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancelar',            
            closeOnConfirm: true,
            showLoaderOnConfirm: false,
        }, function () {
                //var index = tabla.row(fila).index();  
                if (tabla.rows().count() == 1) {
                    // swal("", "La OC debe contener al menos una partida.", "error", {
                    //     buttons: true,
                    // });
                    bootbox.alert({
                        size: "large",
                        title: "<h4><i class='fa fa-info-circle'></i> Ordenes de Compra</h4>",
                        message: "<div class='alert alert-danger m-b-0'> La OC debe contener al menos una partida.</div> "
                    });

                } else {
                    tabla.row(fila).remove().draw(false);
                    calculaTotalOrdenCompra();
                    actualizaLineaPartidaAE();
                }
        });
        //calculaTipoCambio();
        //PartidaResumenAE();
    });

    $('#tblArticulosMiscelaneosNueva').on('click', 'a#boton-eliminarAM', function (e) {
        e.preventDefault();       
        //var datos = tabla.row(fila).data();
        var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
        var fila = $(this).closest('tr');
        swal({
            title: '¿Eliminar partida?',
            text: "",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false,
            showLoaderOnConfirm: false,
        }, function () {
                //var index = tabla.row(fila).index();  
                if (tabla.rows().count() == 1) {
                    swal("", "La OC debe contener al menos una partida.", "error", {
                        buttons: false,
                        timer: 2000,
                    });

                } else {
                    tabla.row(fila).remove().draw(false);
                    calculaTotalOrdenCompra();
                    actualizaLineaPartidaAM();
                }
        });
    });

    //boton editar OC
    $('#tableOC').on('click', 'button#btnEditar', function (e) {
        e.preventDefault();
        var tblOC = $('#tableOC').DataTable();
        var fila = $(this).closest('tr');
        var docentry = fila.attr('data-id')
        var datos = tblOC.row(fila).data();
        var NumOC = datos['NumOC'];
        if (true) {
        //if (datos['Estatus'] == 'ABIERTA') {

            mostrarOC(NumOC, 0) //0 indica que no es una OC nueva, por que se entro a editar
           
        } else {
            swal("", "La OC no esta Abierta", "error", {
                buttons: false,
                timer: 2000,
            });
        }

    });

    $('#tblArticulosMiscelaneosNueva').on('change','input#cerrarPartidaCheck',function (e) {

        var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
        var fila = $(this).closest('tr');
        var index = tabla.row(fila).index();
        var datos = tabla.row(fila).data();
        if (OC_nueva == 0) {
            $.blockUI({
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                }
            });

            bootbox.dialog({
                message: "¿Estas seguro de cerrar la partida?.",
                title: "Ordenes de Compra",
                buttons: {
                    success: {
                        label: "Si",
                        className: "btn-success",
                        callback: function () {

                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                url: routeapp + "cerrar_partida",
                                data: {

                                    "oc_docEntry": $("#docEntryOC").text(),
                                    "oc_docNum": $('#ordenesCompraOC #codigoOC').text(),
                                    "line_num": datos['ID_PARTIDA']

                                },
                                type: "POST",
                                async: true,
                                success: function (datos, x, z) {
                                    console.log(datos)
                                    //$.unblockUI();
                                    if (datos["Status"] == "Error") {
                                        bootbox.dialog({
                                            title: "Mensaje",
                                            message: "<div class='alert alert-danger m-b-0'>" + datos["Mensaje"] + "</div>",
                                            buttons: {
                                                success: {
                                                    label: "Ok",
                                                    className: "btn-success m-r-5 m-b-5"
                                                }
                                            }
                                        }).find('.modal-content').css({ 'font-size': '14px' });

                                    }
                                    else {
                                        swal("", "partida cerrada", "success", {
                                            buttons: false,
                                            timer: 2000,
                                        });

                                    }
                                    InicializaComponentesOC();
                                    $("#tblArticulosExistentesNueva").DataTable().clear().draw();
                                    $("#tblArticulosMiscelaneosNueva").DataTable().clear().draw();
                                    console.log("id: " + datos["id"])
                                    
                                    if(datos["id"] == ''){
                                        mostrarOC($('#ordenesCompraOC #codigoOC').text(), 0)
                                    } else {
                                        mostrarOC(datos["id"], 0);//0 indica que no es una OC nueva, solo se recargaOC
                                    }
                                    
                                    $.unblockUI();
                                },
                                error: function (x, e) {
                                    var errorMessage = 'Error \n' + x.responseText;
                                    mostrarOC($('#ordenesCompraOC #codigoOC').text(), 0)
                                    $.unblockUI();
                                    bootbox.dialog({
                                        title: "Mensaje",
                                        message: "<div class='alert alert-danger m-b-0'>" + errorMessage + "</div>",
                                        buttons: {
                                            success: {
                                                label: "Ok",
                                                className: "btn-success m-r-5 m-b-5"
                                            }
                                        }
                                    }).find('.modal-content').css({ 'font-size': '14px' });

                                }
                            });

                            // datos['CANT_PENDIENTE']="0.00";
                            // datos['PARTIDA_CERRADA']=1;
                            // tbl.row(fila).data(datos);
                            // tbl.row(fila).nodes(fila, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').prop("checked", true);
                            // tbl.row(fila).nodes(fila, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').attr("disabled","disabled");

                            // tbl.row(fila).nodes(fila, COL_CODIGO_ART).to$().find('input#input-articulo-codigoAE').attr("disabled", "disabled");
                            // tbl.row(fila).nodes(fila, COL_CODIGO_ART).to$().find('a#boton-articuloAE').attr("disabled", "disabled");
                            // tbl.row(fila).nodes(fila, COL_CODIGO_ART).to$().find('a#boton-articuloAE').attr("id", "disabled");


                            // tbl.row(fila).nodes(fila, COL_CANTIDAD).to$().find('input#input-cantidadAE').attr("disabled", "disabled");
                            // tbl.row(fila).nodes(fila, COL_PRECIO).to$().find('input#input-precioAE').attr("disabled", "disabled");
                            // tbl.row(fila).nodes(fila, COL_DESCUENTO).to$().find('input#input-descuentoAE').attr("disabled", "disabled");
                            // tbl.row(fila).nodes(fila, COL_IVA).to$().find('select#cboIVAAE').attr("disabled", "disabled");
                            // //tbl.row(fila).nodes(fila, COL_FECHA_ENTREGA_COMPRA).to$().find('input#boton-detalleAE').attr("disabled","disabled");
                            // // tbl.row(fila).nodes(fila, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("disabled", "disabled");
                            // //tbl.row(fila).nodes(fila, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("id", "disabled");

                            // tbl.row(fila).nodes(fila, COL_FECHA_ENTREGA_COMPRA).to$().find('input#input-fecha-entrega-linea').attr("disabled", "disabled");

                            // tbl.row(fila).nodes(fila, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("disabled", "disabled");
                            // tbl.row(fila).nodes(fila, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("id", "disabled");
                            // console.log(fila)

                        }
                    },
                    default: {
                        label: "No",
                        className: "btn-default m-r-5 m-b-5",
                        callback: function () {
                            tbl.row(fila).nodes(fila, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').prop("checked", false);
                            $.unblockUI();
                        }
                    }
                }
            });
        }//if OC_nueva

    });

    $('#tblArticulosExistentesNueva').on('change','input#cerrarPartidaCheck',function (e) {

        var tbl = $('#tblArticulosExistentesNueva').DataTable();
        var fila = $(this).closest('tr');
        var index = tbl.row(fila).index();
        var datos = tbl.row(fila).data();    
        if (OC_nueva == 0) {
            $.blockUI({
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                }
            });    
        
        bootbox.dialog({
            message: "¿Estas seguro de cerrar la partida?.",
            title: "Ordenes de Compra",
            buttons: {
                success: {
                    label: "Si",
                    className: "btn-success",
                    callback: function () {

                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: routeapp + "cerrar_partida",
                            data: {
                               
                                "oc_docEntry": $("#docEntryOC").text(),
                                "oc_docNum": $('#ordenesCompraOC #codigoOC').text(),
                                "line_num": datos['ID_PARTIDA']
                                
                            },
                            type: "POST",
                            async: true,
                            success: function (datos, x, z) {
                                console.log(datos)
                                //$.unblockUI();
                                if (datos["Status"] == "Error") {
                                    bootbox.dialog({
                                        title: "Mensaje",
                                        message: "<div class='alert alert-danger m-b-0'>" + datos["Mensaje"] + "</div>",
                                        buttons: {
                                            success: {
                                                label: "Ok",
                                                className: "btn-success m-r-5 m-b-5"
                                            }
                                        }
                                    }).find('.modal-content').css({ 'font-size': '14px' });

                                }
                                else {
                                    swal("", "partida cerrada", "success", {
                                        buttons: false,
                                        timer: 2000,
                                    });

                                }
                                InicializaComponentesOC();
                                $("#tblArticulosExistentesNueva").DataTable().clear().draw();
                                $("#tblArticulosMiscelaneosNueva").DataTable().clear().draw();
                                console.log("id: " + datos["id"])
                                
                                mostrarOC(datos["id"], 0);//0 indica que no es una OC nueva, solo es recargaOC
                                $.unblockUI();
                            },
                            error: function (x, e) {
                                var errorMessage = 'Error \n' + x.responseText;
                                // mostrarOC(datos["id"]);
                                $.unblockUI();
                                bootbox.dialog({
                                    title: "Mensaje",
                                    message: "<div class='alert alert-danger m-b-0'>" + errorMessage + "</div>",
                                    buttons: {
                                        success: {
                                            label: "Ok",
                                            className: "btn-success m-r-5 m-b-5"
                                        }
                                    }
                                }).find('.modal-content').css({ 'font-size': '14px' });

                            }
                        });

                        // datos['CANT_PENDIENTE']="0.00";
                        // datos['PARTIDA_CERRADA']=1;
                        // tbl.row(fila).data(datos);
                        // tbl.row(fila).nodes(fila, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').prop("checked", true);
                        // tbl.row(fila).nodes(fila, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').attr("disabled","disabled");
                        
                        // tbl.row(fila).nodes(fila, COL_CODIGO_ART).to$().find('input#input-articulo-codigoAE').attr("disabled", "disabled");
                        // tbl.row(fila).nodes(fila, COL_CODIGO_ART).to$().find('a#boton-articuloAE').attr("disabled", "disabled");
                        // tbl.row(fila).nodes(fila, COL_CODIGO_ART).to$().find('a#boton-articuloAE').attr("id", "disabled");
                        

                        // tbl.row(fila).nodes(fila, COL_CANTIDAD).to$().find('input#input-cantidadAE').attr("disabled", "disabled");
                        // tbl.row(fila).nodes(fila, COL_PRECIO).to$().find('input#input-precioAE').attr("disabled", "disabled");
                        // tbl.row(fila).nodes(fila, COL_DESCUENTO).to$().find('input#input-descuentoAE').attr("disabled", "disabled");
                        // tbl.row(fila).nodes(fila, COL_IVA).to$().find('select#cboIVAAE').attr("disabled", "disabled");
                        // //tbl.row(fila).nodes(fila, COL_FECHA_ENTREGA_COMPRA).to$().find('input#boton-detalleAE').attr("disabled","disabled");
                        // // tbl.row(fila).nodes(fila, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("disabled", "disabled");
                        // //tbl.row(fila).nodes(fila, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("id", "disabled");

                        // tbl.row(fila).nodes(fila, COL_FECHA_ENTREGA_COMPRA).to$().find('input#input-fecha-entrega-linea').attr("disabled", "disabled");

                        // tbl.row(fila).nodes(fila, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("disabled", "disabled");
                        // tbl.row(fila).nodes(fila, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("id", "disabled");
                        // console.log(fila)

                    } 
                },
                default: {
                    label: "No",
                    className: "btn-default m-r-5 m-b-5",
                    callback: function () {
                        tbl.row(fila).nodes(fila,COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').prop("checked",false);
                        $.unblockUI();
                    }
                }
            }
        });
        }//if OC_nueva
    });
}  //fin js_iniciador       
                   