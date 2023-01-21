var CANTIDAD_DECIMALES;
var PRECIOS_DECIMALES;
var TC_DECIMALES;
var PORCENTAJE_DECIMALES;

//buscador
var COL_CODIGO_OC = 0;
var COL_BTN_EDITAR = 7;
var COL_BTN_ELIMINAR = 8;
var COL_BTN_PDF = 9;

var registroNuevo = 0;
var SIMBOLO_MONEDA;
var bandera;
var banderaDatosAdicionales = 0;
var arregloFechasRequeridas = [];
var arregloFechasRequeridasEditar = [];

var descuentoOC;
var ivaOC;

var COL_OCD_BTN_FILA = 0;
var COL_OCD_PARTIDA = 1;
var COL_OCD_ARTICULO = 2;
var COL_OCD_FECHAREQUERIDA = 3;
var COL_OCD_UMI = 4;
var COL_OCD_CANTIDAD = 5;
var COL_OCD_UMC = 6;
var COL_OCD_CANTIDAD_COMPRA = 7;
var COL_OCD_SIGNO_PRECIO = 8;
var COL_OCD_PRECIO = 9;
var COL_OCD_SIGNO_SUBTOTAL = 10;
var COL_OCD_SUBTOTAL = 11;
var COL_OCD_SIGNO_DESCUENTO = 12;
var COL_OCD_DESCUENTO = 13;
var COL_OCD_SIGNO_IVA = 14;
var COL_OCD_IVA = 15;
var COL_OCD_SIGNO_TOTAL = 16;
var COL_OCD_TOTAL = 17;
var COL_OCD_EDITAR = 18;
var COL_OCD_ELIMINAR = 19;
var COL_OCD_COMENTARIO = 20;
var COL_OCD_ARTICULO_ID = 21;
var COL_OCD_UMI_ID = 22;
var COL_OCD_UMC_ID = 23;
var COL_OCD_PORCENTAJE_DESCUENTO= 24;
var COL_OCD_FACTOR_CONVERSION = 25;
var COL_OCD_PARTIDA_ID = 26;
var COL_PRECIO_CON_FACTOR = 27;
var COL_ART_CON_FACTOR = 28;
var COL_ESTATUS_OCFR = 29;
var COL_OCD_IVA_ID = 30;
var COL_OCD_PORCENTAJE_IVA = 31;
var COL_OCD_TIPO_PARTIDA_MISC_ID = 32;

//tabla pedimento
var COL_NO_PEDIMENTO = 0;
var COL_PESO_PEDIMENTO = 1;
var COL_NO_LOTE_PROVEEDOR_PEDIMENTO = 2;
var COL_PLANTA_PEDIMENTO = 3;
var COL_NO_FACTURA_PEDIMENTO = 4;
var COL_FECHA_FACTURA_PEDIMENTO = 5;
var COL_ARANCEL_PEDIMENTO = 6;
var COL_ESTADO_PEDIMENTO = 7;
var COL_BTN_EDITAR_PEDIMENTO = 8;
var COL_ID_PEDIMENTO = 9;
var COL_ID_OCFR = 10;

var banderaAddPedimento = 0;
var ocId;
var editaProveedor = 0;

var InicializaFunciones = function () {

    "use strict";
    return {

        init: function (cantidad_decimales, precios_decimales, porcentaje_decimales, tc_decimales) {

            CANTIDAD_DECIMALES = cantidad_decimales;
            PRECIOS_DECIMALES = precios_decimales;
            PORCENTAJE_DECIMALES = porcentaje_decimales;
            TC_DECIMALES = tc_decimales;
            $("#ordenesCompraOC").hide();
            InicializaComponentes();

        }

    }

}();

var InicializaComponentes = function () {

    handleDatepicker();
    InicializaBuscador();
    InicializaButton();
    InicializaBuscadorProveedores();

};

var handleDatepicker = function() {

    $('#fechaDesde').datepicker({

        language: 'es',
        format: 'dd/mm/yyyy'

    }).datepicker("refresh");

    $('#fechaHasta').datepicker({

        language: 'es',
        format: 'dd/mm/yyyy'

    }).datepicker("refresh");

    /////////////////////////////

    $('#fechaRequerida').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    }).datepicker("refresh");

    $('#fechaRequerida').datepicker("setDate", new Date());

    $('#fechaRequeridaMisc').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    }).datepicker("refresh");

    $('#fechaRequeridaMisc').datepicker("setDate", new Date());

    $('#fechaRequeridaAdd').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    }).datepicker("refresh");

    $('#fechaRequeridaAdd').datepicker("setDate", new Date());

    $('#fechaRequeridaAddEditar').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    }).datepicker("refresh");

    $('#fechaRequeridaAddEditar').datepicker("setDate", new Date());

    $('#renglonfechaRequerida').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    }).datepicker("refresh");

    $('#renglonfechaRequerida').datepicker("setDate", new Date());

    $('#cambiarfechaRequerida').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    }).datepicker("refresh");

    //$('#cambiarfechaRequerida').datepicker("setDate", new Date());

    $('#calendario1').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    }).datepicker("refresh");

    $('#calendario1').datepicker("setDate", new Date());

};

$("#ActualizarTablaOC" ).on( "click", function() {

    reloadBuscadorOC();

});

var InicializaBuscadorProveedores = function () {

    nombreInputId = 'input-proveedor';
    urlProveedores = 'getProveedoresBuscador';
    controlaFuncion = 1;
    Buscadores.init();

}

$('#input-proveedor').change(function (e){

    $('#ordenesCompraOC #cboTipoOC').val('05CA103A-B4B7-4E04-B2D8-080E0216AECF');
    $('#ordenesCompraOC #cboTipoOC').selectpicker('refresh');
    changeProveedor();

});

var InicializaButton = function() {

    $('#registros-OC').append('<button id="nuevo" style="display: inline-block; margin-left: 30px;" type="button" class="btn btn-primary m-r-5 m-b-5" onclick="boton()">Nuevo</button>');

};

var InicializaBuscador = function () {

    $("#tableOC").DataTable({

        language:{

            "url": "plugins/DataTables/json/spanish.json"

        },
        "iDisplayLength": 10,
        "aaSorting": [],
        dom: 'T<"clear">lfrtip',
        deferRender: true,
        columns: [

            {data: "OC_CodigoOC"},
            {data: "OC_PRO_Nombre"},
            {data: "OC_CMM_EstadoOC"},
            {data: "MON_Nombre"},
            {data: "OC_Total"},
            {data: "OC_FechaOC"},
            {data: "OC_FechaUltimaModificacion", orderDataType: "dom-date-euro"},
            {data: "BTN_EDITAR"},
            {data: "BTN_ELIMINAR"},
            {data: "PDF"}

        ],
        "columnDefs": [

            {

                "targets": [ COL_BTN_EDITAR ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    return '<button type="button" class="btn btn-primary" id="btnEditar"> <span class="glyphicon glyphicon-pencil"></span> </button>';

                }

            },
            {

                "targets": [ COL_BTN_ELIMINAR ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    return '<button type="button" class="btn btn-danger" id="btnEliminar"> <span class="glyphicon glyphicon-trash"></span> </button>';

                }

            },
            {

                "targets": [ COL_BTN_PDF ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    return '<button type="button" class="btn btn-outline-success" style="background-color:transparent;" id="boton-pdf"> <img src="img/pdf-icon.png" height="35"/> </button>';
                }

            }

        ],
        fixedColumns: true,
        tableTools: {sSwfPath: "plugins/DataTables/swf/copy_csv_xls_pdf.swf"},
        'order': [[COL_CODIGO_OC, 'DESC']]

    });

    reloadBuscadorOC();

}

function reloadBuscadorOC(){

    $("#tableOC").DataTable().clear().draw();

    $.ajax({

        type: 'POST',
        async: true,
        url: "compras/ordenCompra-registros",
        data:{

            "fechaDesde": $('#fechaDesde').val(),
            "fechaHasta": $('#fechaHasta').val()

        },
        success: function(data){

            if(JSON.parse(data.oc).data != ''){

                $("#tableOC").dataTable().fnAddData(JSON.parse(data.oc).data);

            }

        },
        error: function (xhr, ajaxOptions, thrownError) {

            $.unblockUI();
            var error = JSON.parse(xhr.responseText);
            bootbox.alert({

                size: "large",
                title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                ( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
                ( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
                ( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'

            });

        }

    });

}

function blurFecha(){

    if($('#input-fecha').val() == ''){

        $('#input-fecha').datepicker('setDate', new Date());

    }

}

function boton(){

    $("#btnBuscadorOC").hide();
    $("#ordenesCompraOC").show();
    registroNuevo = 0;
    InicializaComponentesOC();
    validaCambiarFechaRequerida();
    $("#datosPedimento").hide();

}

function InicializaComponentesOC() {

    $("#btnBuscarProveedores").text('Selecciona Proveedor');
    $("#btnBuscarProveedores").removeAttr('disabled');
    $("#input-proveedor").text('');
    $("#input-proveedor").removeAttr('oldvalue');
    $("#input-proveedor").removeAttr('publicoGeneral');
    $("#ordenesCompraOC #input-fecha").attr('disabled',true);
    $("#ordenesCompraOC #boton-datos-adicionales").attr('disabled', true);

    $('#ordenesCompraOC #nombreProveedor').text('');
    $('#ordenesCompraOC #direccionProveedor').text('');
    $("#ordenesCompraOC #codigoPostalProveedor").text('');
    $('#ordenesCompraOC #localizacionProveedor').text('');
    $('#ordenesCompraOC #telefonicosProveedor').text('');
    $('#ordenesCompraOC #contactoProveedor').text('');
    $('#ordenesCompraOC #rfcProveedor').text('');

    $('#ordenesCompraOC #nombreSucursal').text('');
    $('#ordenesCompraOC #domicilioSucursal').text('');
    $('#ordenesCompraOC #telefonicosSucursal').text('');
    $('#ordenesCompraOC #emailSucursal').text('');
    $('#ordenesCompraOC #codigopostalSucursal').text('');
    $('#ordenesCompraOC #ciudadSucursal').text('');

    $("#ordenesCompraOC #cboSucursal").empty();
    $("#ordenesCompraOC #cboSucursal").attr('disabled',true);
    $('#ordenesCompraOC #cboSucursal').append('<option value="">Selecciona una opción</option>');
    $("#ordenesCompraOC #cboSucursal").selectpicker('refresh');
    $("#ordenesCompraOC #cboMoneda").attr('disabled',true);
    $("#ordenesCompraOC #cboMoneda").val('');
    $("#ordenesCompraOC #cboMoneda").selectpicker('refresh');
    $("#ordenesCompraOC #cboTipoCambio").attr('disabled',true);
    $("#ordenesCompraOC #cboTipoCambio").val('');
    $("#ordenesCompraOC #cboTipoCambio").selectpicker('refresh');
    $("#ordenesCompraOC #tipoCambio").hide();
    //$("#ordenesCompraOC #cboAgente").attr('disabled',true);
    $("#ordenesCompraOC #cboAgente").val('');
    $("#ordenesCompraOC #cboAgente").attr('disabled',true);
    $("#ordenesCompraOC #cboAgente").selectpicker('refresh');
    $("#ordenesCompraOC #cboSucursalAgente").val('');
    $("#ordenesCompraOC #cboSucursalAgente").attr('disabled',true);
    $("#ordenesCompraOC #cboSucursalAgente").selectpicker('refresh');
    //$("#ordenesCompraOC #agenteAduanal").hide();
    //$("#ordenesCompraOC #tipoCambio").show();
    $("#ordenesCompraOC #cboTipoOC").attr('disabled',true);
    $("#ordenesCompraOC #cboTipoOC").val('');
    $("#ordenesCompraOC #cboTipoOC").selectpicker('refresh');
    $("#ordenesCompraOC #cboAlmacen").attr('disabled',true);
    $("#ordenesCompraOC #cboAlmacen").val('');
    $("#ordenesCompraOC #cboAlmacen").selectpicker('refresh');

    $('#ordenesCompraOC #codigoOC').text('');
    $('#ordenesCompraOC #estadoOC').text('Abierta');

    $("#ordenesCompraOC #boton-datos-adicionales").attr('disabled', true);

    //limpiar datos adicionales
    $("#modal-datos #cboProyectos").val('');
    $("#ordenesCompraOC #cboProyectos").selectpicker('refresh');
    $("#modal-datos #cboOT").val('');
    $("#ordenesCompraOC #cboOT").selectpicker('refresh');
    $("#modal-datos #cboLibreABordo").val('');
    $("#ordenesCompraOC #cboLibreABordo").selectpicker('refresh');
    $("#modal-datos #cboIVA").val('');
    $("#ordenesCompraOC #cboIVA").selectpicker('refresh');
    $("#modal-datos #input-descuento").val('');
    $("#modal-datos #input-comentarios").val('');

    InicializaDatepicker();

};

function validaCambiarFechaRequerida(){

    if(registroNuevo == 1){

        $('#modal-datos #inputCambiarFecReq').show();

    }
    else{

        $('#modal-datos #inputCambiarFecReq').hide();

    }

}

var InicializaDatepicker = function() {

    $('#input-fecha').datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true
    }).datepicker("setDate", new Date());

};

function changeProveedor(){

    $.ajax({

        cache: false,
        async: false,
        url: "compras/consultaSucursalesProveedor",
        data: {

            "proveedorId": $('#input-proveedor').val(),
            "monedaId": $("#ordenesCompraOC #cboMoneda").val()

        },
        type: "POST",
        success: function( datos ) {

            var respuesta = JSON.parse(JSON.stringify(datos));
            if(respuesta.codigo == 200){

                llenaDatosProvedor(respuesta.data);
                llenaComboArtExis(respuesta.data2);

            }
            else{

                bootbox.dialog({
                    message: respuesta.respuesta,
                    title: "Orden de Compra",
                    buttons: {
                        success: {
                            label: "Si",
                            className: "btn-success"
                        }
                    }
                });

            }

        },
        error: function (xhr, ajaxOptions, thrownError) {

            $.unblockUI();
            var error = JSON.parse(xhr.responseText);
            bootbox.alert({

                size: "large",
                title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                ( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
                ( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
                ( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'

            });

        }

    });

}

function llenaDatosProvedor(data){

    var cuentaData = data.length;

    var direccionOCId = '';
    var nombreSucursal = '';
    var domicilioSucursal = '';
    var telefonicosSucursal = '';
    var emailSucursal = '';
    var codigopostalSucursal = '';
    var ciudadSucursal = '';

    if(cuentaData > 0){

        for(var i=0; i<data.length; i++){

            if(data[i]['PDOC_DireccionOCDefault'] == 1){

                direccionOCId = data[i]['PDOC_DireccionOCId'];
                nombreSucursal = data[i]['PDOC_Nombre'];
                domicilioSucursal = data[i]['PDOC_Domicilio'];
                telefonicosSucursal = data[i]['PDOC_Telefono'];
                emailSucursal = data[i]['PDOC_Email'];
                codigopostalSucursal = data[i]['PDOC_CodigoPostal'];
                ciudadSucursal = data[i]['PDOC_Ciudad'];

            }

            $('#ordenesCompraOC #cboSucursal').append('<option id="'+data[i]['PDOC_DireccionOCId']+'" value="'+data[i]['PDOC_DireccionOCId']+'">'+data[i]['PDOC_Nombre']+'</option>');

        }
        $('#ordenesCompraOC #cboSucursal').val(direccionOCId);
        $('#ordenesCompraOC #nombreSucursal').text(nombreSucursal);
        $('#ordenesCompraOC #domicilioSucursal').text(domicilioSucursal);
        $('#ordenesCompraOC #telefonicosSucursal').text(telefonicosSucursal);
        $('#ordenesCompraOC #emailSucursal').text(emailSucursal);
        $('#ordenesCompraOC #codigopostalSucursal').text(codigopostalSucursal);
        $('#ordenesCompraOC #ciudadSucursal').text(ciudadSucursal);
        $('#ordenesCompraOC #cboSucursal').selectpicker('refresh');

    }

}

function llenaTablaPedimentos(datos){

    limpiaTablaPedimentos();
    var bandera = false;
    var cuentaDatos = datos.length;
    for(var x = 0; x < cuentaDatos; x ++){

        //LLENAR TABLA PEDIMENTOS
        var tblPedimentos = document.getElementById('tblPedimentos').getElementsByTagName('tbody')[0];
        //var index = tblPedimentos.rows.length + 1;
        var fila = tblPedimentos.insertRow(tblPedimentos.rows.length);

        var noPedimento = fila.insertCell(COL_NO_PEDIMENTO);
        var pesoPedimento = fila.insertCell(COL_PESO_PEDIMENTO);
        var noLoteProveedor = fila.insertCell(COL_NO_LOTE_PROVEEDOR_PEDIMENTO);
        var planta = fila.insertCell(COL_PLANTA_PEDIMENTO);
        var noFactura = fila.insertCell(COL_NO_FACTURA_PEDIMENTO);
        var fechaFactura = fila.insertCell(COL_FECHA_FACTURA_PEDIMENTO);
        var arancel = fila.insertCell(COL_ARANCEL_PEDIMENTO);
        var estatus = fila.insertCell(COL_ESTADO_PEDIMENTO);
        var btnEditar = fila.insertCell(COL_BTN_EDITAR_PEDIMENTO);
        var id = fila.insertCell(COL_ID_PEDIMENTO);
        var idOCFR = fila.insertCell(COL_ID_OCFR);

        noPedimento.innerHTML = datos[x]['OCP_Pedimento'];
        noPedimento.setAttribute("nowrap", "true");
        pesoPedimento.innerHTML = datos[x]['OCP_PesoPedimento'];
        pesoPedimento.setAttribute("nowrap", "true");
        noLoteProveedor.innerHTML = datos[x]['OCP_LoteProveedor'];
        noLoteProveedor.setAttribute("nowrap", "true");
        planta.innerHTML = datos[x]['OCP_Planta'];
        planta.setAttribute("nowrap", "true");
        noFactura.innerHTML = datos[x]['OCP_NumeroFactura'];
        noFactura.setAttribute("nowrap", "true");

        var subCadena = datos[x]['OCP_FechaFactura'].substring(0, 10);
        var divideFecha = subCadena.split('-');
        var fecha = divideFecha[2] + "/" + divideFecha[1] + "/" + divideFecha[0];
        fechaFactura.innerHTML = fecha;
        fechaFactura.setAttribute("nowrap", "true");

        arancel.innerHTML = datos[x]['OCP_Arancel'];
        arancel.setAttribute("nowrap", "true");

        var edo = datos[x]['OCP_Abierto'];
        if(edo == 1){

            estatus.innerHTML = "Abierto";
            estatus.setAttribute("nowrap", "true");
            btnEditar.innerHTML = '<button type="button" class="btn btn-primary" onclick="editarDatosPartidaPedimento(this.parentNode.parentNode.sectionRowIndex)" data-toggle="tooltip" data-placement="right" title="" data-original-title="Editar Fila"> <span class="glyphicon glyphicon-pencil"></span> </button>';
            bandera = true;

        }
        else{

            estatus.innerHTML = "Cerrado";
            estatus.setAttribute("nowrap", "true");
            btnEditar.innerHTML = "";

        }

        id.innerHTML = datos[x]['OCP_PedimentoId'];
        id.setAttribute("nowrap", "true");
        idOCFR.innerHTML = datos[x]['OCP_OCFR_FechaRequeridaId'];
        idOCFR.setAttribute("nowrap", "true");

        id.style.display = "none";
        idOCFR.style.display = "none";

    }

    if(bandera){

        $("#agregarPedimentoNuevo").hide();
        banderaAddPedimento = 1;

    }
    else{

        $("#agregarPedimentoNuevo").show();
        banderaAddPedimento = 0;

    }

    var tipoMoneda = $("#cboMoneda").val();
    if(tipoMoneda != '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1' && registroNuevo == 1){

        $("#datosPedimento").show();

    }
    else{

        $("#datosPedimento").hide();

    }

}

function editarDatosPartidaPedimento(fila){

    filaTablaPedimento = fila;
    $('#modal-datos-pedimento #OCP_Pedimento').val($($("#tblPedimentos tbody tr")[fila]).find("td:eq(0)").text());
    $('#modal-datos-pedimento #OCP_PesoPedimento').val($($("#tblPedimentos tbody tr")[fila]).find("td:eq(1)").text());
    $('#modal-datos-pedimento #OCP_LoteProveedor').val($($("#tblPedimentos tbody tr")[fila]).find("td:eq(2)").text());
    $('#modal-datos-pedimento #OCP_Planta').val($($("#tblPedimentos tbody tr")[fila]).find("td:eq(3)").text());
    $('#modal-datos-pedimento #OCP_NumeroFactura').val($($("#tblPedimentos tbody tr")[fila]).find("td:eq(4)").text());
    $('#modal-datos-pedimento #OCP_FechaFactura').val($($("#tblPedimentos tbody tr")[fila]).find("td:eq(5)").text());
    $('#modal-datos-pedimento #OCP_Arancel').val($($("#tblPedimentos tbody tr")[fila]).find("td:eq(6)").text());

    $("#agregarPedimentoNuevo").show();

}

function limpiaTablaPedimentos(){

    $("#tblPedimentos tbody tr").remove();

}

function llenaComboSucursalesAgente(datos){

    $('#ordenesCompraOC #cboSucursalAgente').removeAttr("disabled");
    $('#ordenesCompraOC #cboSucursalAgente').empty();
    $('#ordenesCompraOC #cboSucursalAgente').append('<option id="" value="">Selecciona una opción</option>');

    var cuentaDatos = datos.length;
    if(cuentaDatos > 0){

        for(var x = 0; x < cuentaDatos; x ++){

            $('#ordenesCompraOC #cboSucursalAgente').append('<option id="'+datos[x]['PDOC_DireccionOCId']+'" value="'+datos[x]['PDOC_DireccionOCId']+'">'+datos[x]['PDOC_Nombre']+'</option>');

        }

    }

    $('#ordenesCompraOC #cboSucursalAgente').selectpicker('refresh');

}

function llenaComboArtExis(datos){

    $('#modal-articulosExistentes #cboArticulosExistentes').removeAttr("disabled");
    $('#modal-articulosExistentes #cboArticulosExistentes').empty();
    $('#modal-articulosExistentes #cboArticulosExistentes').append('<option id="" value="">Seleccione Articulo</option>');

    var cuentaDatos = datos.length;
    if(cuentaDatos > 0){

        for(var x = 0; x < cuentaDatos; x ++){

            $('#modal-articulosExistentes #cboArticulosExistentes').append('<option id="'+datos[x]['ART_ArticuloId']+'" value="'+datos[x]['ART_ArticuloId']+'">'+datos[x]['ART_Nombre']+'</option>');

        }

    }

    $('#modal-articulosExistentes #cboArticulosExistentes').selectpicker('refresh');

    /*else{

        bootbox.dialog({
            message: "No se encontraron articulos en la lista de precio de compra.",
            title: "Orden de Compra",
            buttons: {
                success: {
                    label: "Si",
                    className: "btn-success"
                }
            }
        });

    }*/

}

$('#agregarPedimento').off().on( 'click', function (e) {

    //agrega a renglon
    var bandera = validarDatosPedimento();
    if(bandera){

        if(banderaAddPedimento == 1){

            actualizaDatosTablaPedimento();

        }
        else{

            agregaDatosTablaPedimento();

        }
        limpiarDatosPedimento();
        $("#agregarPedimentoNuevo").hide();
        banderaAddPedimento = 1;

    }

});

function actualizaDatosTablaPedimento(){

    var noPedimento = $("#modal-datos-pedimento #OCP_Pedimento").val();
    var pesoPedimento = $("#modal-datos-pedimento #OCP_PesoPedimento").val();
    var loteProveedor = $("#modal-datos-pedimento #OCP_LoteProveedor").val();
    var planta = $("#modal-datos-pedimento #OCP_Planta").val();
    var noFactura = $("#modal-datos-pedimento #OCP_NumeroFactura").val();
    var fechaFactura = $("#modal-datos-pedimento #OCP_FechaFactura").val();
    var arancel = $("#modal-datos-pedimento #OCP_Arancel").val();

    $($("#tblPedimentos tbody tr")[filaTablaPedimento]).find("td:eq(0)").text(noPedimento);
    $($("#tblPedimentos tbody tr")[filaTablaPedimento]).find("td:eq(1)").text(pesoPedimento);
    $($("#tblPedimentos tbody tr")[filaTablaPedimento]).find("td:eq(2)").text(loteProveedor);
    $($("#tblPedimentos tbody tr")[filaTablaPedimento]).find("td:eq(3)").text(planta);
    $($("#tblPedimentos tbody tr")[filaTablaPedimento]).find("td:eq(4)").text(noFactura);
    $($("#tblPedimentos tbody tr")[filaTablaPedimento]).find("td:eq(5)").text(fechaFactura);
    $($("#tblPedimentos tbody tr")[filaTablaPedimento]).find("td:eq(6)").text(arancel);

}

function limpiarDatosPedimento(){

    $("#modal-datos-pedimento #OCP_Pedimento").val('');
    $("#modal-datos-pedimento #OCP_PesoPedimento").val('');
    $("#modal-datos-pedimento #OCP_LoteProveedor").val('');
    $("#modal-datos-pedimento #OCP_Planta").val('');
    $("#modal-datos-pedimento #OCP_NumeroFactura").val('');
    $("#modal-datos-pedimento #OCP_FechaFactura").val('');
    $("#modal-datos-pedimento #OCP_Arancel").val('');

}

function agregaDatosTablaPedimento(){

    var tblArtExis = document.getElementById('tblPedimentos').getElementsByTagName('tbody')[0];
    //var index = tblArtExis.rows.length + 1;
    var fila   = tblArtExis.insertRow(tblArtExis.rows.length);

    var noPedimento = fila.insertCell(COL_NO_PEDIMENTO);
    var pesoPedimento = fila.insertCell(COL_PESO_PEDIMENTO);
    var noLoteProveedor = fila.insertCell(COL_NO_LOTE_PROVEEDOR_PEDIMENTO);
    var planta = fila.insertCell(COL_PLANTA_PEDIMENTO);
    var noFactura = fila.insertCell(COL_NO_FACTURA_PEDIMENTO);
    var fechaFactura = fila.insertCell(COL_FECHA_FACTURA_PEDIMENTO);
    var arancel = fila.insertCell(COL_ARANCEL_PEDIMENTO);
    var estatus = fila.insertCell(COL_ESTADO_PEDIMENTO);
    var btnEditar = fila.insertCell(COL_BTN_EDITAR_PEDIMENTO);
    var id = fila.insertCell(COL_ID_PEDIMENTO);
    var idOCFR = fila.insertCell(COL_ID_OCFR);

    noPedimento.innerHTML = $("#modal-datos-pedimento #OCP_Pedimento").val();
    noPedimento.setAttribute("nowrap", "true");
    pesoPedimento.innerHTML = $("#modal-datos-pedimento #OCP_PesoPedimento").val();
    pesoPedimento.setAttribute("nowrap", "true");
    noLoteProveedor.innerHTML = $("#modal-datos-pedimento #OCP_LoteProveedor").val();
    noLoteProveedor.setAttribute("nowrap", "true");
    planta.innerHTML = $("#modal-datos-pedimento #OCP_Planta").val();
    planta.setAttribute("nowrap", "true");
    noFactura.innerHTML = $("#modal-datos-pedimento #OCP_NumeroFactura").val();
    noFactura.setAttribute("nowrap", "true");
    fechaFactura.innerHTML = $("#modal-datos-pedimento #OCP_FechaFactura").val();
    fechaFactura.setAttribute("nowrap", "true");
    arancel.innerHTML = $("#modal-datos-pedimento #OCP_Arancel").val();
    arancel.setAttribute("nowrap", "true");
    estatus.innerHTML = "Abierto";
    estatus.setAttribute("nowrap", "true");
    btnEditar.innerHTML = '<button type="button" class="btn btn-primary" onclick="editarDatosPartidaPedimento(this.parentNode.parentNode.sectionRowIndex)" data-toggle="tooltip" data-placement="right" title="" data-original-title="Editar Fila"> <span class="glyphicon glyphicon-pencil"></span> </button>';
    id.innerHTML = "";
    id.setAttribute("nowrap", "true");
    idOCFR.innerHTML = comboFechaRequeridaId;
    idOCFR.setAttribute("nowrap", "true");

    id.style.display = "none";
    idOCFR.style.display = "none";

}

function validarDatosPedimento(){

    var bandera = true;
    var noPedimento = $("#modal-datos-pedimento #OCP_Pedimento").val();
    var pesoPedimento = $("#modal-datos-pedimento #OCP_PesoPedimento").val();
    var loteProveedor = $("#modal-datos-pedimento #OCP_LoteProveedor").val();
    var planta = $("#modal-datos-pedimento #OCP_Planta").val();
    var numeroFactura = $("#modal-datos-pedimento #OCP_NumeroFactura").val();
    var fechaFactura = $("#modal-datos-pedimento #OCP_FechaFactura").val();
    var arancel = $("#modal-datos-pedimento #OCP_Arancel").val();

    if(noPedimento == ''){

        bandera = false;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Ingrese No. Pedimento.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }
    else if(pesoPedimento == ''){

        bandera = false;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Ingrese peso pedimento.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }
    else if(loteProveedor == ''){

        bandera = false;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Ingrese lote proveedor.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }
    else if(planta == ''){

        bandera = false;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Ingrese planta.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }
    else if(numeroFactura == ''){

        bandera = false;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Ingrese No. de Factura.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }
    else if(fechaFactura == ''){

        bandera = false;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Seleccione fecha de Factura.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }
    else if(arancel == ''){

        bandera = false;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Ingrese arancel.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }

    return bandera;

}

$('#boton-datos-pedimento').off().on( 'click', function (e) {

    limpiarDatosPedimento();
    if(banderaAddPedimento == 1){

        $("#agregarPedimentoNuevo").hide();

    }
    else{

        $("#agregarPedimentoNuevo").show();

    }
    $("#modal-datos-pedimento").modal("show");

});

$('#boton-datos-adicionales').off().on( 'click', function (e) {

    e.preventDefault();
    $('#modal-datos #cboProyectos').selectpicker('refresh');
    $('#modal-datos #cboOT').selectpicker('refresh');
    $('#modal-datos #cboLibreABordo').selectpicker('refresh');
    $('#modal-datos #cboIVA').selectpicker('refresh');
    $('#modal-datos').modal('show');

});

$('#modal-datos #aceptar').off().on('click', function(e) {

    e.preventDefault();
    validaDatosAdicionales();
    ivaYDescuento();
    if(banderaDatosAdicionales == 0){

        if(registroNuevo == 1 || $("#ordenesCompraOC #cboTipoOC").val() == '05CA103A-B4B7-4E04-B2D8-080E0216AECF'){//ESTANDAR

            cambiarFechasRequeridasTablas();

        }
        $('#modal-datos').modal('hide');

    }

});

function cambiarFechasRequeridasTablas(){

    var fechaReq = $('#modal-datos #cambiarfechaRequerida').val();

    if(fechaReq != ''){

        $('#tblArticulosExistentes tbody tr').each(function(row, tr){

            $(tr).find('td:eq(3)').text(fechaReq);

        });

        $('#tblArticulosMiscelaneos tbody tr').each(function(row, tr){

            $(tr).find('td:eq(3)').text(fechaReq);

        });

        $('#modal-datos #cambiarfechaRequerida').val('');

    }

}

$('#boton-cerrar').off().on('click', function(e) {

    //window.location = '#compras/ordenCompra';
    //handleLoadPage(window.location.hash);
    $('#ordenesCompraOC').hide();
    $('#btnBuscadorOC').show();
    reloadBuscadorOC();
    InicializaComponentesOC();
    limpiaTablaArticulosExistentes();
    calculaTotalArticulosExistentes();
    calculaTotalArticulosExistentesResumen();
    limpiarTablaFechasRequeridas();
    limpiaTablaArticulosMiscelaneos();
    calculaTotalArticulosMiscelaneos();
    calculaTotalArticulosMiscelaneosResumen();
    calculaTotalOC();
    banderaAddPedimento = 0;

});

$('#guardar').off().on('click', function(e) {

    var estadoOC = $('#estadoOC').text();
    if(estadoOC == 'Completa'){

        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'No puedes editar la Orden de Compra porque ya esta Completa.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function(dialog){
                    dialog.close();
                }
            }]
        });

    }
    else if(estadoOC == 'Correspondida Completa'){

        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'No puedes editar la Orden de Compra porque ya esta Correspondida Completa.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function(dialog){
                    dialog.close();
                }
            }]
        });

    }
    else{

        validarCampos();
        if(bandera == 0){

            registraOC();

        }

    }

});

function registraOC(){

    var datosTablaArtExis;
    datosTablaArtExis = getTblArtExis();

    var datosTablaArtMisc;
    datosTablaArtMisc = getTblArtMisc();

    var datosTablaPedimentos;
    datosTablaPedimentos = getTblPedimento();

    var paridad = '';
    if($("#cboMoneda").val() == '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1'){//pesos
        paridad = '';
    }
    else{
        paridad = $("#cboTipoCambio option:selected").text();
    }

    $.ajax({
        url: "compras/registraOC",
        data: {
            "status": registroNuevo,
            "OC_CodigoOC": $("#codigoOC").text(),
            "OC_PRO_ProveedorId": $("#input-proveedor").val(),
            "OC_PDOC_DireccionOCId": $("#cboSucursal").val(),
            "OC_CMM_TipoOCId": $("#cboTipoOC").val(),
            "OC_MON_MonedaId": $("#cboMoneda").val(),
            "OC_MONP_Paridad": paridad,
            "OC_MONP_ParidadId": $("#cboTipoCambio").val(),
            "OC_ALM_AlmacenId": $("#cboAlmacen").val(),
            "OC_CMM_AgenteAduanal": $("#cboAgente").val(),
            "OC_AGE_PDOC_DireccionOCId": $("#cboSucursalAgente").val(),
            "OC_CMM_LibreABordoId": $("#modal-datos #cboLibreABordo").val(),
            "OC_PorcentajeDescuento": $("#modal-datos #input-descuento").val(),
            "OC_CMIVA_IVAId": $("#modal-datos #cboIVA").val(),
            "OCD_CMIVA_PorcentajeIVA": $("#cboIVA option:selected").text(),
            "OC_EV_ProyectoId": $("#modal-datos #cboProyectos").val(),
            "OC_OT_OrdenTrabajoId": $("#modal-datos #cboOT").val(),
            "OC_Comentarios": $("#modal-datos #input-comentarios").val(),
            "ArrayFehasRequeridas": arregloFechasRequeridas,
            "ArrayFehasRequeridasEditar": arregloFechasRequeridasEditar,
            "TablaArticulosExistentes": datosTablaArtExis,
            "TablaArticulosMiscelaneos": datosTablaArtMisc,
            "TablaPedimentos": datosTablaPedimentos,
            "editaProveedor": editaProveedor
        },
        type: "POST",
        async:false,
        success: function (datos, x, z) {
            if(datos["Status"] == "Error"){
                BootstrapDialog.show({
                    title: 'Error',
                    type: BootstrapDialog.TYPE_DANGER,
                    message: datos["Mensaje"],
                    cssClass: 'login-dialog',
                    buttons: [{
                        label: 'Aceptar',
                        cssClass: 'btn-default',
                        action: function(dialog){
                            dialog.close();
                        }
                    }]
                });
            }
            else{
                //$('#RequisicionesNuevo').modal('hide');
                //$('#requisiciones').DataTable().ajax.reload();
                arregloFechasRequeridas = [];
                arregloFechasRequeridasEditar = [];
                $('#ordenesCompraOC').hide();
                $('#btnBuscadorOC').show();
                reloadBuscadorOC();
                InicializaComponentesOC();
                limpiaTablaArticulosExistentes();
                calculaTotalArticulosExistentes();
                calculaTotalArticulosExistentesResumen();
                limpiarTablaFechasRequeridas();
                limpiaTablaArticulosMiscelaneos();
                calculaTotalArticulosMiscelaneos();
                calculaTotalArticulosMiscelaneosResumen();
                calculaTotalOC();
                banderaAddPedimento = 0;
                //window.location = '#compras/ordenCompra';
                //handleLoadPage(window.location.hash);
            }
        },
        error: function (x, e) {
            var errorMessage = 'Error \n' + x.responseText;
            BootstrapDialog.alert({
                title: 'Error',
                message: errorMessage,
                type: BootstrapDialog.TYPE_DANGER, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                closable: true, // <-- Default value is false
                draggable: true, // <-- Default value is false
                buttonLabel: 'OK' // <-- Default value is 'OK',
            });
        }
    });

}

function getTblArtExis(){

    var tblArticulosExistentes = new Array();

    $('#tblArticulosExistentes tbody tr').each(function(row, tr){

        tblArticulosExistentes[row]={
            "0" : $(tr).find('td:eq(0)').text(),
            "1" : $(tr).find('td:eq(1)').text(),
            "2" : $(tr).find('td:eq(2)').text(),
            "3" : $(tr).find('td:eq(3)').text(),
            "4" : $(tr).find('td:eq(4)').text(),
            "5" : $(tr).find('td:eq(5)').text(),
            "6" : $(tr).find('td:eq(6)').text(),
            "7" : $(tr).find('td:eq(7)').text(),
            "8" : $(tr).find('td:eq(8)').text(),
            "9" : $(tr).find('td:eq(9)').text(),
            "10" : $(tr).find('td:eq(10)').text(),
            "11" : $(tr).find('td:eq(11)').text(),
            "12" : $(tr).find('td:eq(12)').text(),
            "13" : $(tr).find('td:eq(13)').text(),
            "14" : $(tr).find('td:eq(14)').text(),
            "15" : $(tr).find('td:eq(15)').text(),
            "16" : $(tr).find('td:eq(16)').text(),
            "17" : $(tr).find('td:eq(17)').text(),
            "18" : $(tr).find('td:eq(18)').text(),
            "19" : $(tr).find('td:eq(19)').text(),
            "20" : $(tr).find('td:eq(20)').text(),
            "21" : $(tr).find('td:eq(21)').text(),
            "22" : $(tr).find('td:eq(22)').text(),
            "23" : $(tr).find('td:eq(23)').text(),
            "24" : $(tr).find('td:eq(24)').text(),
            "25" : $(tr).find('td:eq(25)').text(),
            "26" : $(tr).find('td:eq(26)').text(),
            "27" : $(tr).find('td:eq(27)').text(),
            "28" : $(tr).find('td:eq(28)').text(),
            "29" : $(tr).find('td:eq(29)').text(),
            "30" : $(tr).find('td:eq(30)').text(),
            "31" : $(tr).find('td:eq(31)').text()
        }

    });

    return JSON.stringify(tblArticulosExistentes);

}

function getTblArtMisc(){

    var tblArticulosMiscelaneos = new Array();

    $('#tblArticulosMiscelaneos tbody tr').each(function(row, tr){

        tblArticulosMiscelaneos[row]={
            "0" : $(tr).find('td:eq(0)').text(),
            "1" : $(tr).find('td:eq(1)').text(),
            "2" : $(tr).find('td:eq(2)').text(),
            "3" : $(tr).find('td:eq(3)').text(),
            "4" : $(tr).find('td:eq(4)').text(),
            "5" : $(tr).find('td:eq(5)').text(),
            "6" : $(tr).find('td:eq(6)').text(),
            "7" : $(tr).find('td:eq(7)').text(),
            "8" : $(tr).find('td:eq(8)').text(),
            "9" : $(tr).find('td:eq(9)').text(),
            "10" : $(tr).find('td:eq(10)').text(),
            "11" : $(tr).find('td:eq(11)').text(),
            "12" : $(tr).find('td:eq(12)').text(),
            "13" : $(tr).find('td:eq(13)').text(),
            "14" : $(tr).find('td:eq(14)').text(),
            "15" : $(tr).find('td:eq(15)').text(),
            "16" : $(tr).find('td:eq(16)').text(),
            "17" : $(tr).find('td:eq(17)').text(),
            "18" : $(tr).find('td:eq(18)').text(),
            "19" : $(tr).find('td:eq(19)').text(),
            "20" : $(tr).find('td:eq(20)').text(),
            "21" : $(tr).find('td:eq(21)').text(),
            "22" : $(tr).find('td:eq(22)').text(),
            "23" : $(tr).find('td:eq(23)').text(),
            "24" : $(tr).find('td:eq(24)').text(),
            "25" : $(tr).find('td:eq(25)').text(),
            "26" : $(tr).find('td:eq(26)').text(),
            "27" : $(tr).find('td:eq(27)').text(),
            "28" : $(tr).find('td:eq(28)').text(),
            "29" : $(tr).find('td:eq(29)').text(),
            "30" : $(tr).find('td:eq(30)').text(),
            "31" : $(tr).find('td:eq(31)').text(),
            "32" : $(tr).find('td:eq(32)').text()
        }

    });

    return JSON.stringify(tblArticulosMiscelaneos);

}

function getTblPedimento(){

    var tblPedimentos = new Array();

    $('#tblPedimentos tbody tr').each(function(row, tr){

        tblPedimentos[row]={
            "0" : $(tr).find('td:eq(0)').text(),
            "1" : $(tr).find('td:eq(1)').text(),
            "2" : $(tr).find('td:eq(2)').text(),
            "3" : $(tr).find('td:eq(3)').text(),
            "4" : $(tr).find('td:eq(4)').text(),
            "5" : $(tr).find('td:eq(5)').text(),
            "6" : $(tr).find('td:eq(6)').text(),
            "7" : $(tr).find('td:eq(7)').text(),
            "8" : $(tr).find('td:eq(8)').text(),
            "9" : $(tr).find('td:eq(9)').text(),
            "10" : $(tr).find('td:eq(10)').text()
        }

    });

    return JSON.stringify(tblPedimentos);

}

function validarCampos(){

    var moneda = $("#cboMoneda").val();
    var tipoCambio = $("#cboTipoCambio").val();
    var tipoOC = $("#cboTipoOC").val();
    var almacen = $("#cboAlmacen").val();
    var tablaArtExis = document.getElementById("tblArticulosExistentes");
    var cuentaTablaArtExis = tablaArtExis.rows.length;
    var tablaArtMisc = document.getElementById("tblArticulosMiscelaneos");
    var cuentaTablaArtMisc = tablaArtMisc.rows.length;
    bandera = 0;

    if(moneda == ''){

        bandera = 1;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Elija el tipo de moneda.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }
    else if(moneda != '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1' && tipoCambio == ""){//PESOS

        //if(tipoCambio == ""){

        bandera = 1;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Elija un tipo de cambio.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

        //}

    }
    else if(tipoOC == ""){

        bandera = 1;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Elija un tipo de orden de compra.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }
    else if(cuentaTablaArtExis > 2 && almacen == ""){

        //if(almacen == ""){

        bandera = 1;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Elija un almacen de recibo.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

        //}

    }
    else if(cuentaTablaArtExis < 3 && cuentaTablaArtMisc < 3){

        bandera = 1;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'Agregue minimo un articulo a las tablas.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }

}

function calculaTotalOC(){

    var articulos = getLengthTblArticulosExistentes() + getLengthTblArticulosMiscelaneos();
    var subtotal = Number($($("#tblArticulosExistentes tfoot tr")[0]).find("td:eq(2)").text()) + Number($($("#tblArticulosMiscelaneos tfoot tr")[0]).find("td:eq(2)").text());
    var descuento = Number($($("#tblArticulosExistentes tfoot tr")[0]).find("td:eq(4)").text()) + Number($($("#tblArticulosMiscelaneos tfoot tr")[0]).find("td:eq(4)").text());
    var iva = Number($($("#tblArticulosExistentes tfoot tr")[0]).find("td:eq(6)").text()) + Number($($("#tblArticulosMiscelaneos tfoot tr")[0]).find("td:eq(6)").text());
    var total = Number($($("#tblArticulosExistentes tfoot tr")[0]).find("td:eq(8)").text()) + Number($($("#tblArticulosMiscelaneos tfoot tr")[0]).find("td:eq(8)").text());

    validaSimbolo();

    $('#ordenCompra-articulos').text(articulos);
    $('#ordenCompra-subtotal').text(SIMBOLO_MONEDA + ' ' + number_format(subtotal,PRECIOS_DECIMALES,'.',','));
    $('#ordenCompra-descuento').text(SIMBOLO_MONEDA + ' ' + number_format(descuento,PRECIOS_DECIMALES,'.',','));
    $('#ordenCompra-iva').text(SIMBOLO_MONEDA + ' ' + number_format(iva,PRECIOS_DECIMALES,'.',','));
    $('#ordenCompra-total').text(SIMBOLO_MONEDA + ' ' + number_format(total,PRECIOS_DECIMALES,'.',','));

}

function validaSimbolo(){

    if($('#cboMoneda').val() == '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1'){//PESOS

        SIMBOLO_MONEDA = '$';

    }
    else if($('#cboMoneda').val() == '1EA50C6D-AD92-4DE6-A562-F155D0D516D3'){//DOLAR

        SIMBOLO_MONEDA = 'USD';

    }
    else if($('#cboMoneda').val() == '63D4F280-EE48-44A3-84F3-91ADF075BEBC'){//EURO

        SIMBOLO_MONEDA = '€';

    }

}

function ivaYDescuento(){

    if($('#modal-datos #input-descuento').val() == ''){

        descuentoOC = parseFloat(0);

    }
    else{

        descuentoOC = parseFloat($('#modal-datos #input-descuento').val());

    }

    if($('#modal-datos #cboIVA').val() == ''){

        ivaOC = parseFloat(0);

    }
    else{

        ivaOC = parseFloat($('#modal-datos #cboIVA option:selected').text());

    }

    recalculaIvaYDescuento();

}

function recalculaIvaYDescuento(){

    var subtotal = 0.00;
    var descuento = 0.00;
    var iva = 0.00;
    var total = 0.00;

    $('#tblArticulosExistentes tbody tr').each(function (index, tr) {

        subtotal = Number($(tr).find('td:eq(11)').text());

        descuento = ((parseFloat(subtotal) * parseFloat(descuentoOC)) / 100);
        $(tr).find('td:eq(13)').text(descuento.toFixed(PRECIOS_DECIMALES));

        iva = (parseFloat(subtotal) - parseFloat(descuento)) * parseFloat(ivaOC);
        $(tr).find('td:eq(15)').text(iva.toFixed(PRECIOS_DECIMALES));

        total = (parseFloat(subtotal) - (descuento)) + parseFloat(iva);
        $(tr).find('td:eq(17)').text(total.toFixed(PRECIOS_DECIMALES));

        $(tr).find('td:eq(31)').text(ivaOC);
        $(tr).find('td:eq(30)').text($('#modal-datos #cboIVA').val());

    });

    subtotal = 0.00;
    descuento = 0.00;
    iva = 0.00;
    total = 0.00;

    $('#tblArticulosExistentesResumen tbody tr').each(function (index, tr) {

        subtotal = Number($(tr).find('td:eq(9)').text());

        descuento = ((parseFloat(subtotal) * parseFloat(descuentoOC)) / 100);
        $(tr).find('td:eq(11)').text(descuento.toFixed(PRECIOS_DECIMALES));

        iva = (parseFloat(subtotal) - parseFloat(descuento)) * parseFloat(ivaOC);
        $(tr).find('td:eq(13)').text(iva.toFixed(PRECIOS_DECIMALES));

        total = (parseFloat(subtotal) - (descuento)) + parseFloat(iva);
        $(tr).find('td:eq(15)').text(total.toFixed(PRECIOS_DECIMALES));

    });

    var subtotal2 = 0.00;
    var descuento2 = 0.00;
    var iva2 = 0.00;
    var total2 = 0.00;

    $('#tblArticulosMiscelaneos tbody tr').each(function (index, tr) {

        subtotal2 = Number($(tr).find('td:eq(11)').text());

        descuento2 = ((parseFloat(subtotal2) * parseFloat(descuentoOC)) / 100);
        $(tr).find('td:eq(13)').text(descuento2.toFixed(PRECIOS_DECIMALES));

        iva2 = (parseFloat(subtotal2) - parseFloat(descuento2)) * parseFloat(ivaOC);
        $(tr).find('td:eq(15)').text(iva2.toFixed(PRECIOS_DECIMALES));

        total2 = (parseFloat(subtotal2) - (descuento2)) + parseFloat(iva2);
        $(tr).find('td:eq(17)').text(total2.toFixed(PRECIOS_DECIMALES));

        $(tr).find('td:eq(31)').text(ivaOC);
        $(tr).find('td:eq(30)').text($('#modal-datos #cboIVA').val());

    });

    subtotal2 = 0.00;
    descuento2 = 0.00;
    iva2 = 0.00;
    total2 = 0.00;

    $('#tblArticulosMiscelaneosResumen tbody tr').each(function (index, tr) {

        subtotal2 = Number($(tr).find('td:eq(9)').text());

        descuento2 = ((parseFloat(subtotal2) * parseFloat(descuentoOC)) / 100);
        $(tr).find('td:eq(11)').text(descuento2.toFixed(PRECIOS_DECIMALES));

        iva2 = (parseFloat(subtotal2) - parseFloat(descuento2)) * parseFloat(ivaOC);
        $(tr).find('td:eq(13)').text(iva2.toFixed(PRECIOS_DECIMALES));

        total2 = (parseFloat(subtotal2) - (descuento2)) + parseFloat(iva2);
        $(tr).find('td:eq(15)').text(total2.toFixed(PRECIOS_DECIMALES));

    });

    calculaTotalArticulosExistentes();
    calculaTotalArticulosExistentesResumen();
    calculaTotalArticulosMiscelaneos();
    calculaTotalArticulosMiscelaneosResumen();
    calculaTotalOC();

}

function validaDatosAdicionales(){

    descuentoOC = $('#modal-datos #input-descuento').val();

    if(descuentoOC < 0 || descuentoOC > 100){

        banderaDatosAdicionales = 1;
        BootstrapDialog.show({
            title: 'Error',
            type: BootstrapDialog.TYPE_DANGER,
            message: 'EL porcentaje de descuento debe ser igual o mayor a 0 y/o menor o igual a 100.',
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Aceptar',
                cssClass: 'btn-default',
                action: function (dialog) {
                    dialog.close();
                }
            }]
        });

    }
    else{

        banderaDatosAdicionales = 0;

    }

}

$('#cboMoneda').change(function (e){

    changeMoneda();

});

function changeMoneda(){

    var tipoMoneda = $('#ordenesCompraOC #cboMoneda').val();
    var proveedor = $('#input-proveedor').val();
    var fechaOc = $('#input-fecha').val();

    $.ajax({

        cache: false,
        async: false,
        url: "compras/consultaTipoCambio",
        data: {

            "tipoMonedaId": tipoMoneda,
            "proveedor": proveedor,
            "fechaOc": fechaOc

        },
        type: "POST",
        success: function( datos ) {

            var respuesta = JSON.parse(JSON.stringify(datos));
            if(respuesta.codigo == 200){

                llenarComboTipoCambio(respuesta.data);
                llenaComboArtExis(respuesta.data2);

            }
            else{

                bootbox.dialog({
                    message: respuesta.respuesta,
                    title: "Orden de Compra",
                    buttons: {
                        success: {
                            label: "Si",
                            className: "btn-success"
                        }
                    }
                });

            }

        },
        error: function (xhr, ajaxOptions, thrownError) {

            $.unblockUI();
            var error = JSON.parse(xhr.responseText);
            bootbox.alert({

                size: "large",
                title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                ( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
                ( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
                ( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'

            });

        }

    });

}

function llenarComboTipoCambio(datos){

    var tipoMoneda = $('#ordenesCompraOC #cboMoneda').val();

    if(tipoMoneda != '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1'){//PESOS


        var cuentaData = datos.length;
        $('#ordenesCompraOC #cboTipoCambio').empty();
        $('#ordenesCompraOC #cboTipoCambio').append('<option id="" value="">Selecciona una opción</option>');
        if(cuentaData > 0){

            for(var i = 0; i < cuentaData; i ++){

                $('#ordenesCompraOC #cboTipoCambio').append('<option id="'+datos[i]['MONP_ParidadMonedaId']+'" value="'+datos[i]['MONP_ParidadMonedaId']+'">'+datos[i]['MONP_TipoCambioOficial']+'</option>');

            }

        }
        //$('#ordenesCompraOC #cboTipoCambio').selectpicker('refresh');
        $("#ordenesCompraOC #cboTipoCambio").attr('disabled',false);
        $("#ordenesCompraOC #cboTipoCambio").selectpicker('refresh');
        $("#ordenesCompraOC #tipoCambio").show();

    }
    else{

        $("#ordenesCompraOC #cboTipoCambio").val('');
        $("#ordenesCompraOC #cboTipoCambio").attr('disabled',true);
        $("#ordenesCompraOC #cboTipoCambio").selectpicker('refresh');
        $("#ordenesCompraOC #tipoCambio").hide();

    }

}

$('#cboAgente').change(function (e){

    changeAgente();

});

function changeAgente(){

    var agenteId = $('#ordenesCompraOC #cboAgente').val();

    $.ajax({

        cache: false,
        async: false,
        url: "compras/consultaSucursalesAgente",
        data: {

            "agenteId": agenteId

        },
        type: "POST",
        success: function( datos ) {

            var respuesta = JSON.parse(JSON.stringify(datos));
            if(respuesta.codigo == 200){

                llenarComboSucursalesAgente(respuesta.data);

            }
            else{

                bootbox.dialog({
                    message: respuesta.respuesta,
                    title: "Orden de Compra",
                    buttons: {
                        success: {
                            label: "Si",
                            className: "btn-success"
                        }
                    }
                });

            }

        },
        error: function (xhr, ajaxOptions, thrownError) {

            $.unblockUI();
            var error = JSON.parse(xhr.responseText);
            bootbox.alert({

                size: "large",
                title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                ( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
                ( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
                ( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'

            });

        }

    });

}

function llenarComboSucursalesAgente(datos){

    var cuentaData = datos.length;
    $('#ordenesCompraOC #cboSucursalAgente').empty();
    $('#ordenesCompraOC #cboSucursalAgente').append('<option id="" value="">Selecciona una opción</option>');
    if(cuentaData > 0){

        for(var i = 0; i < cuentaData; i ++){

            $('#ordenesCompraOC #cboSucursalAgente').append('<option id="'+datos[i]['PDOC_DireccionOCId']+'" value="'+datos[i]['PDOC_DireccionOCId']+'">'+datos[i]['PDOC_Nombre']+'</option>');

        }

    }
    $("#ordenesCompraOC #cboSucursalAgente").attr('disabled',false);
    $("#ordenesCompraOC #cboSucursalAgente").selectpicker('refresh');

}

$('#tableOC').on( 'click', 'button#btnEditar', function (e) {

    e.preventDefault();

    registroNuevo = 1;
    var tblOC = $('#tableOC').DataTable();
    var fila = $(this).closest('tr');
    var datos = tblOC.row(fila).data();
    ocId = datos['DT_RowId'];

    $.blockUI({ css: {
        border: 'none',
        padding: '15px',
        backgroundColor: '#000',
        '-webkit-border-radius': '10px',
        '-moz-border-radius': '10px',
        opacity: .5,
        color: '#fff'
    } });

    $.ajax({
        type: "POST",
        async: false,
        data: {
            ocId: ocId
        },
        dataType: "json",
        url: "compras/buscaOC",
        success: function (data) {
            setTimeout($.unblockUI, 2000);
            setTimeout(function () {
                var respuesta = JSON.parse(JSON.stringify(data));
                if(respuesta.codigo == 200){

                    llenaComboSucursalesAgente(respuesta.sucursalesAgente);
                    agregarDatosOC(respuesta.ordenCompra,respuesta.consultaTipoMonedaProveedor);
                    //llenaTablaPedimentos(respuesta.pedimentos);
                    agregaPartidasOCDetalle(respuesta.ordenCompraDetalle);
                    agregaDatosArregloFR(respuesta.ordenCompraFechasRequeridas);
                    llenaComboArtExis(respuesta.consultaArticulosProveedor);
                    calculaTotalArticulosExistentes();
                    calculaTotalArticulosExistentesResumen();
                    calculaTotalArticulosMiscelaneos();
                    calculaTotalArticulosMiscelaneosResumen();
                    calculaTotalOC();
                    validaCambiarFechaRequerida();
                    $("#btnBuscadorOC").hide();
                    $("#ordenesCompraOC").show();
                    registroNuevo = 1;

                } else {
                    setTimeout($.unblockUI, 2000);
                    bootbox.dialog({
                        message: respuesta.respuesta,
                        title: "Orden de Compra",
                        buttons: {
                            success: {
                                label: "Si",
                                className: "btn-success"
                            }
                        }
                    });
                }
            }, 2000);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $.unblockUI();
            var error = JSON.parse(xhr.responseText);
            bootbox.alert({
                size: "large",
                title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                ( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
                ( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
                ( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'
            });
        }
    });

});

function agregarDatosOC(datos,datos2){

    $('#input-proveedor').val(datos[0]['OC_PRO_ProveedorId']);
    $('#btnBuscarProveedores').text(datos[0]['PRO_Nombre']);
    //$('#btnBuscarProveedores').attr("disabled","disabled");
    $("#input-fecha").val(datos[0]['OC_FechaOC']);
    $('#ordenesCompraOC #cboSucursal').append('<option id="'+datos[0]['OC_PDOC_DireccionOCId']+'" value="'+datos[0]['OC_PDOC_DireccionOCId']+'">'+datos[0]['PDOC_Nombre']+'</option>');
    $("#ordenesCompraOC #cboSucursal").val(datos[0]['OC_PDOC_DireccionOCId']);
    $("#ordenesCompraOC #cboMoneda").val(datos[0]['OC_MON_MonedaId']);

    if(datos[0]['OC_MON_MonedaId'] != '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1'){//PESOS

        changeMoneda();
        $("#ordenesCompraOC #cboTipoCambio").val(datos[0]['OC_MONP_ParidadId']);
        $("#ordenesCompraOC #tipoCambio").show();

    }
    else{

        $("#ordenesCompraOC #cboTipoCambio").val('');
        $("#ordenesCompraOC #tipoCambio").hide();

    }

    $("#ordenesCompraOC #cboAgente").val(datos[0]['OC_CMM_AgenteAduanalId']);
    $("#ordenesCompraOC #cboAgente").selectpicker('refresh');

    $("#ordenesCompraOC #cboSucursalAgente").val(datos[0]['OC_AGE_PDOC_DireccionOCId']);
    $("#ordenesCompraOC #cboSucursalAgente").selectpicker('refresh');

    /*if(datos2[0]['PCA_MON_MonedaId'] != '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1'){//PESOS

        $("#ordenesCompraOC #cboAgente").val(datos[0]['OC_CMM_AgenteAduanalId']);
        $("#ordenesCompraOC #cboAgente").selectpicker('refresh');
        $("#ordenesCompraOC #agenteAduanal").show();

    }
    else{

        $("#ordenesCompraOC #cboAgente").val('');
        $("#ordenesCompraOC #cboAgente").removeAttr('disabled');
        $("#ordenesCompraOC #cboAgente").selectpicker('refresh');
        $("#ordenesCompraOC #agenteAduanal").hide();

    }*/

    $("#ordenesCompraOC #cboTipoOC").val(datos[0]['OC_TipoOCId']);
    $("#ordenesCompraOC #cboAlmacen").val(datos[0]['OC_ALM_AlmacenId']);

    $("#modal-datos #cboProyectos").val(datos[0]['OC_EV_ProyectoId']);
    $("#modal-datos #cboOT").val(datos[0]['OC_OT_OrdenTrabajoId']);
    $("#modal-datos #cboLibreABordo").val(datos[0]['OC_LibreABordoId']);
    $("#modal-datos #cboIVA").val(datos[0]['OC_CMIVA_IVAId']);
    $("#modal-datos #input-descuento").val(datos[0]['OC_PorcentajeDescuento']);
    $("#modal-datos #input-comentarios").val(datos[0]['OC_Comentarios']);
    $('#boton-datos-adicionales').removeAttr("disabled");

    document.getElementById('nombreProveedor').innerText = datos[0]['PRO_Nombre'];
    document.getElementById('direccionProveedor').innerText = datos[0]['PRO_Domicilio'];
    document.getElementById('codigoPostalProveedor').innerText = datos[0]['PRO_CodigoPostal'];
    document.getElementById('rfcProveedor').innerText = datos[0]['PRO_RFC'];
    document.getElementById('telefonicosProveedor').innerText = datos[0]['PRO_Telefono'];
    document.getElementById('contactoProveedor').innerText = datos[0]['PRO_Contacto'];

    document.getElementById('nombreSucursal').innerText = datos[0]['PDOC_Nombre'];
    document.getElementById('domicilioSucursal').innerText = datos[0]['PDOC_Domicilio'];
    document.getElementById('telefonicosSucursal').innerText = datos[0]['PDOC_Telefono'];
    document.getElementById('emailSucursal').innerText = datos[0]['PDOC_Email'];
    document.getElementById('codigopostalSucursal').innerText = datos[0]['PDOC_CodigoPostal'];
    document.getElementById('ciudadSucursal').innerText = datos[0]['PDOC_Ciudad'];

    document.getElementById('codigoOC').innerText = datos[0]['OC_CodigoOC'];
    document.getElementById('estadoOC').innerText = datos[0]['CMM_EstatusOC'];

    $("#ordenesCompraOC #cboSucursal").removeAttr('disabled');
    $("#ordenesCompraOC #cboSucursal").selectpicker('refresh');
    $("#ordenesCompraOC #cboMoneda").removeAttr('disabled');
    $("#ordenesCompraOC #cboMoneda").selectpicker('refresh');
    $("#ordenesCompraOC #cboTipoCambio").removeAttr('disabled');
    $("#ordenesCompraOC #cboTipoCambio").selectpicker('refresh');
    $("#ordenesCompraOC #cboTipoOC").removeAttr('disabled');
    $("#ordenesCompraOC #cboTipoOC").selectpicker('refresh');
    $("#ordenesCompraOC #cboAlmacen").removeAttr('disabled');
    $("#ordenesCompraOC #cboAlmacen").selectpicker('refresh');
    $("#ordenesCompraOC #cboAgente").removeAttr('disabled');
    $("#ordenesCompraOC #cboAgente").selectpicker('refresh');
    $("#ordenesCompraOC #cboSucursalAgente").removeAttr('disabled');
    $("#ordenesCompraOC #cboSucursalAgente").selectpicker('refresh');

    ivaYDescuento();

}

function agregaPartidasOCDetalle(datos){

    validaSimbolo();
    var tipoOC = $('#cboTipoOC').val();
    var cuentaDatos = datos.length;
    for(var x = 0; x < cuentaDatos; x ++){

        if(datos[x]['OCD_ART_ArticuloId'] != null){

            //LLENAR TABLA ARTICULOS EXISTENTES
            var tblArtExis = document.getElementById('tblArticulosExistentes').getElementsByTagName('tbody')[0];
            var index = tblArtExis.rows.length + 1;
            var fila   = tblArtExis.insertRow(tblArtExis.rows.length);

            var boton_fila = fila.insertCell(COL_OCD_BTN_FILA);
            var partida = fila.insertCell(COL_OCD_PARTIDA);
            var articulo = fila.insertCell(COL_OCD_ARTICULO);
            var fechaRequerida = fila.insertCell(COL_OCD_FECHAREQUERIDA);
            var umi = fila.insertCell(COL_OCD_UMI);
            var cantidad = fila.insertCell(COL_OCD_CANTIDAD);
            var umc = fila.insertCell(COL_OCD_UMC);
            var cantidad_compra = fila.insertCell(COL_OCD_CANTIDAD_COMPRA);
            var signo_precio = fila.insertCell(COL_OCD_SIGNO_PRECIO);
            var precio = fila.insertCell(COL_OCD_PRECIO);
            var signo_subtotal = fila.insertCell(COL_OCD_SIGNO_SUBTOTAL);
            var subtotal = fila.insertCell(COL_OCD_SUBTOTAL);
            var signo_descuento = fila.insertCell(COL_OCD_SIGNO_DESCUENTO);
            var descuento = fila.insertCell(COL_OCD_DESCUENTO);
            var signo_iva = fila.insertCell(COL_OCD_SIGNO_IVA);
            var iva = fila.insertCell(COL_OCD_IVA);
            var signo_total = fila.insertCell(COL_OCD_SIGNO_TOTAL);
            var total = fila.insertCell(COL_OCD_TOTAL);
            var editar = fila.insertCell(COL_OCD_EDITAR);
            var eliminar = fila.insertCell(COL_OCD_ELIMINAR);
            var comentario = fila.insertCell(COL_OCD_COMENTARIO);
            var articuloId = fila.insertCell(COL_OCD_ARTICULO_ID);
            var umiId = fila.insertCell(COL_OCD_UMI_ID);
            var umcId = fila.insertCell(COL_OCD_UMC_ID);
            var porcentajeDescuento = fila.insertCell(COL_OCD_PORCENTAJE_DESCUENTO);
            var factorConversion = fila.insertCell(COL_OCD_FACTOR_CONVERSION);
            var partidaId = fila.insertCell(COL_OCD_PARTIDA_ID);
            var precioConFactor = fila.insertCell(COL_PRECIO_CON_FACTOR);
            var artConFactor = fila.insertCell(COL_ART_CON_FACTOR);
            var estatus = fila.insertCell(COL_ESTATUS_OCFR);
            var ivaId = fila.insertCell(COL_OCD_IVA_ID);
            var porcentajeIva = fila.insertCell(COL_OCD_PORCENTAJE_IVA);

            boton_fila.innerHTML = '';
            partida.innerHTML = index;
            partida.setAttribute("nowrap", "true");
            articulo.innerHTML = datos[x]['OCD_DescripcionArticulo'];
            articulo.setAttribute("nowrap", "true");
            if(tipoOC == '015B2F9A-17A5-4885-A516-35CB19B91F72') {//parcialidades

                fechaRequerida.innerHTML = '';
                fechaRequerida.setAttribute("nowrap", "true");

            }
            else{

                var divideFecha = datos[x]['OCFR_FechaRequerida'].split('-');
                var fecha = divideFecha[2] + "/" + divideFecha[1] + "/" + divideFecha[0];
                fechaRequerida.innerHTML = fecha;
                fechaRequerida.setAttribute("nowrap", "true");

            }
            umi.innerHTML = datos[x]['OCD_CMUM_UMInventario'];
            umi.setAttribute("nowrap", "true");
            cantidad.innerHTML = parseFloat(datos[x]['OCD_CantidadRequerida']).toFixed(PRECIOS_DECIMALES);
            cantidad.setAttribute("nowrap", "true");
            umc.innerHTML = datos[x]['OCD_CMUM_UMCompras'];
            umc.setAttribute("nowrap", "true");
            cantidad_compra.innerHTML = parseFloat(datos[x]['OCD_CantidadCompra']).toFixed(PRECIOS_DECIMALES);
            cantidad_compra.setAttribute("nowrap", "true");
            signo_precio.innerHTML = SIMBOLO_MONEDA;
            precio.innerHTML = parseFloat(datos[x]['OCD_PrecioUnitario']).toFixed(PRECIOS_DECIMALES);
            precio.setAttribute("nowrap", "true");
            signo_subtotal.innerHTML = SIMBOLO_MONEDA;
            subtotal.innerHTML = parseFloat(datos[x]['OCD_Subtotal']).toFixed(PRECIOS_DECIMALES);
            subtotal.setAttribute("nowrap", "true");
            signo_descuento.innerHTML = SIMBOLO_MONEDA;
            descuento.innerHTML = parseFloat(datos[x]['OCD_Descuento']).toFixed(PRECIOS_DECIMALES);
            descuento.setAttribute("nowrap", "true");
            signo_iva.innerHTML = SIMBOLO_MONEDA;
            iva.innerHTML = parseFloat(datos[x]['OCD_Iva']).toFixed(PRECIOS_DECIMALES);
            iva.setAttribute("nowrap", "true");
            signo_total.innerHTML = SIMBOLO_MONEDA;
            total.innerHTML = parseFloat(datos[x]['OCD_Total']).toFixed(PRECIOS_DECIMALES);
            total.setAttribute("nowrap", "true");
            editar.innerHTML = '<button type="button" class="btn btn-primary" onclick="editarArtExis(this.parentNode.parentNode.sectionRowIndex)" data-toggle="tooltip" data-placement="right" title="" data-original-title="Editar Fila"> <span class="glyphicon glyphicon-pencil"></span> </button>';
            eliminar.innerHTML = '<button type="button" class="btn btn-danger" onclick="eliminarArtExis(this.parentNode.parentNode.sectionRowIndex)" data-toggle="tooltip" data-placement="right" title="" data-original-title="Eliminar Fila"> <span class="glyphicon glyphicon-trash"></span> </button>';
            comentario.innerHTML = datos[x]['OCD_Comentarios'];
            articuloId.innerHTML = datos[x]['OCD_ART_ArticuloId'];
            articuloId.setAttribute("nowrap", "true");
            umiId.innerHTML = datos[x]['OCD_CMUM_UMInventarioId'];
            umiId.setAttribute("nowrap", "true");
            umcId.innerHTML = datos[x]['OCD_CMUM_UMComprasId'];
            umcId.setAttribute("nowrap", "true");
            porcentajeDescuento.innerHTML = datos[x]['OCFR_PorcentajeDescuento'];
            porcentajeDescuento.setAttribute("nowrap", "true");
            factorConversion.innerHTML = datos[x]['OCD_AFC_FactorConversion'];
            factorConversion.setAttribute("nowrap", "true");
            partidaId.innerHTML = datos[x]['OCD_PartidaId'];
            partidaId.setAttribute("nowrap", "true");
            precioConFactor.innerHTML = datos[x]['OCFR_PrecioConFactor'];
            precioConFactor.setAttribute("nowrap", "true");
            artConFactor.innerHTML = datos[x]['ART_ConFactor'];
            artConFactor.setAttribute("nowrap", "true");
            estatus.innerHTML = datos[x]['OCFR_CMM_EstadoFechaRequerida'];
            estatus.setAttribute("nowrap", "true");
            ivaId.innerHTML = datos[x]['OCD_CMIVA_IVAId'];
            ivaId.setAttribute("nowrap", "true");
            porcentajeIva.innerHTML = datos[x]['OCD_CMIVA_PorcentajeIVA'];
            porcentajeIva.setAttribute("nowrap", "true");

            cantidad.style.textAlign = "right";
            cantidad_compra.style.textAlign = "right";
            signo_precio.style.textAlign = "right";
            precio.style.textAlign = "right";
            signo_subtotal.style.textAlign = "right";
            subtotal.style.textAlign = "right";
            signo_descuento.style.textAlign = "right";
            descuento.style.textAlign = "right";
            signo_iva.style.textAlign = "right";
            iva.style.textAlign = "right";
            signo_total.style.textAlign = "right";
            total.style.textAlign = "right";

            comentario.style.display = "none";
            articuloId.style.display = "none";
            umiId.style.display = "none";
            umcId.style.display = "none";
            porcentajeDescuento.style.display = "none";
            factorConversion.style.display = "none";
            partidaId.style.display = "none";
            precioConFactor.style.display = "none";
            artConFactor.style.display = "none";
            estatus.style.display = "none";
            ivaId.style.display = "none";
            porcentajeIva.style.display = "none";
            cantidad_compra.style.display = "none";

            //LLENAR TABLA ARTICULOS EXISTENTES RESUMEN
            var tblArtExisRes = document.getElementById('tblArticulosExistentesResumen').getElementsByTagName('tbody')[0];
            var index2 = tblArtExisRes.rows.length + 1;
            var fila2   = tblArtExisRes.insertRow(tblArtExisRes.rows.length);

            var partida2 = fila2.insertCell(COL_OCAE_PARTIDA - 1);//1
            var articulo2 = fila2.insertCell(COL_OCAE_ARTICULO - 1);//2
            var umi2 = fila2.insertCell(COL_OCAE_UMI - 2);//4
            var cantidad2 = fila2.insertCell(COL_OCAE_CANTIDAD - 2);//5
            var umc2 = fila2.insertCell(COL_OCAE_UMC - 2);
            var cantidad_compra2 = fila2.insertCell(COL_OCAE_CANTIDAD_COMPRA - 2);
            var signo_precio2 = fila2.insertCell(COL_OCAE_SIGNO_PRECIO - 2);
            var precio2 = fila2.insertCell(COL_OCAE_PRECIO - 2);
            var signo_subtotal2 = fila2.insertCell(COL_OCAE_SIGNO_SUBTOTAL - 2);
            var subtotal2 = fila2.insertCell(COL_OCAE_SUBTOTAL - 2);
            var signo_descuento2 = fila2.insertCell(COL_OCAE_SIGNO_DESCUENTO - 2);
            var descuento2 = fila2.insertCell(COL_OCAE_DESCUENTO - 2);
            var signo_iva2 = fila2.insertCell(COL_OCAE_SIGNO_IVA - 2);
            var iva2 = fila2.insertCell(COL_OCAE_IVA - 2);
            var signo_total2 = fila2.insertCell(COL_OCAE_SIGNO_TOTAL - 2);
            var total2 = fila2.insertCell(COL_OCAE_TOTAL - 2);

            partida2.innerHTML = index2;
            partida2.setAttribute("nowrap", "true");
            articulo2.innerHTML = datos[x]['OCD_DescripcionArticulo'];
            articulo2.setAttribute("nowrap", "true");
            umi2.innerHTML = datos[x]['OCD_CMUM_UMInventario'];
            umi2.setAttribute("nowrap", "true");
            cantidad2.innerHTML = parseFloat(datos[x]['OCD_CantidadRequerida']).toFixed(PRECIOS_DECIMALES);
            cantidad2.setAttribute("nowrap", "true");
            umc2.innerHTML = datos[x]['OCD_CMUM_UMCompras'];
            umc2.setAttribute("nowrap", "true");
            cantidad_compra2.innerHTML = parseFloat(datos[x]['OCD_CantidadRequerida']).toFixed(PRECIOS_DECIMALES);
            cantidad_compra2.setAttribute("nowrap", "true");
            signo_precio2.innerHTML = SIMBOLO_MONEDA;
            precio2.innerHTML = parseFloat(datos[x]['OCD_PrecioUnitario']).toFixed(PRECIOS_DECIMALES);
            precio2.setAttribute("nowrap", "true");
            signo_subtotal2.innerHTML = SIMBOLO_MONEDA;
            subtotal2.innerHTML = parseFloat(datos[x]['OCD_Subtotal']).toFixed(PRECIOS_DECIMALES);
            subtotal2.setAttribute("nowrap", "true");
            signo_descuento2.innerHTML = SIMBOLO_MONEDA;
            descuento2.innerHTML = parseFloat(datos[x]['OCD_Descuento']).toFixed(PRECIOS_DECIMALES);
            descuento2.setAttribute("nowrap", "true");
            signo_iva2.innerHTML = SIMBOLO_MONEDA;
            iva2.innerHTML = parseFloat(datos[x]['OCD_Iva']).toFixed(PRECIOS_DECIMALES);
            iva2.setAttribute("nowrap", "true");
            signo_total2.innerHTML = SIMBOLO_MONEDA;
            total2.innerHTML = parseFloat(datos[x]['OCD_Total']).toFixed(PRECIOS_DECIMALES);
            total2.setAttribute("nowrap", "true");

            cantidad2.style.textAlign = "right";
            cantidad_compra2.style.textAlign = "right";
            signo_precio2.style.textAlign = "right";
            precio2.style.textAlign = "right";
            signo_subtotal2.style.textAlign = "right";
            subtotal2.style.textAlign = "right";
            signo_descuento2.style.textAlign = "right";
            descuento2.style.textAlign = "right";
            signo_iva2.style.textAlign = "right";
            iva2.style.textAlign = "right";
            signo_total2.style.textAlign = "right";
            total2.style.textAlign = "right";

            cantidad_compra2.style.display = "none";

        }
        else{

            //LLENAR TABLA ARTICULOS MISCELANEOS
            var tblArtMisc = document.getElementById('tblArticulosMiscelaneos').getElementsByTagName('tbody')[0];
            var index = tblArtMisc.rows.length + 1;
            var fila   = tblArtMisc.insertRow(tblArtMisc.rows.length);

            var boton_fila = fila.insertCell(COL_OCD_BTN_FILA);
            var partida = fila.insertCell(COL_OCD_PARTIDA);
            var articulo = fila.insertCell(COL_OCD_ARTICULO);
            var fechaRequerida = fila.insertCell(COL_OCD_FECHAREQUERIDA);
            var umi = fila.insertCell(COL_OCD_UMI);
            var cantidad = fila.insertCell(COL_OCD_CANTIDAD);
            var umc = fila.insertCell(COL_OCD_UMC);
            var cantidad_compra = fila.insertCell(COL_OCD_CANTIDAD_COMPRA);
            var signo_precio = fila.insertCell(COL_OCD_SIGNO_PRECIO);
            var precio = fila.insertCell(COL_OCD_PRECIO);
            var signo_subtotal = fila.insertCell(COL_OCD_SIGNO_SUBTOTAL);
            var subtotal = fila.insertCell(COL_OCD_SUBTOTAL);
            var signo_descuento = fila.insertCell(COL_OCD_SIGNO_DESCUENTO);
            var descuento = fila.insertCell(COL_OCD_DESCUENTO);
            var signo_iva = fila.insertCell(COL_OCD_SIGNO_IVA);
            var iva = fila.insertCell(COL_OCD_IVA);
            var signo_total = fila.insertCell(COL_OCD_SIGNO_TOTAL);
            var total = fila.insertCell(COL_OCD_TOTAL);
            var editar = fila.insertCell(COL_OCD_EDITAR);
            var eliminar = fila.insertCell(COL_OCD_ELIMINAR);
            var comentario = fila.insertCell(COL_OCD_COMENTARIO);
            var articuloId = fila.insertCell(COL_OCD_ARTICULO_ID);
            var umiId = fila.insertCell(COL_OCD_UMI_ID);
            var umcId = fila.insertCell(COL_OCD_UMC_ID);
            var porcentajeDescuento = fila.insertCell(COL_OCD_PORCENTAJE_DESCUENTO);
            var factorConversion = fila.insertCell(COL_OCD_FACTOR_CONVERSION);
            var partidaId = fila.insertCell(COL_OCD_PARTIDA_ID);
            var precioConFactor = fila.insertCell(COL_PRECIO_CON_FACTOR);
            var artConFactor = fila.insertCell(COL_ART_CON_FACTOR);
            var estatus = fila.insertCell(COL_ESTATUS_OCFR);
            var ivaId = fila.insertCell(COL_OCD_IVA_ID);
            var porcentajeIva = fila.insertCell(COL_OCD_PORCENTAJE_IVA);
            var tipoPartidaMiscId = fila.insertCell(COL_OCD_TIPO_PARTIDA_MISC_ID);

            boton_fila.innerHTML = '';
            partida.innerHTML = index;
            partida.setAttribute("nowrap", "true");
            articulo.innerHTML = datos[x]['OCD_DescripcionArticulo'];
            articulo.setAttribute("nowrap", "true");
            if(tipoOC == '015B2F9A-17A5-4885-A516-35CB19B91F72') {//parcialidades

                fechaRequerida.innerHTML = '';
                fechaRequerida.setAttribute("nowrap", "true");

            }
            else{

                var divideFecha = datos[x]['OCFR_FechaRequerida'].split('-');
                var fecha = divideFecha[2] + "/" + divideFecha[1] + "/" + divideFecha[0];
                fechaRequerida.innerHTML = fecha;
                fechaRequerida.setAttribute("nowrap", "true");

            }
            umi.innerHTML = datos[x]['OCD_CMUM_UMInventario'];
            umi.setAttribute("nowrap", "true");
            cantidad.innerHTML = parseFloat(datos[x]['OCD_CantidadRequerida']).toFixed(PRECIOS_DECIMALES);
            cantidad.setAttribute("nowrap", "true");
            umc.innerHTML = datos[x]['OCD_CMUM_UMCompras'];
            umc.setAttribute("nowrap", "true");
            cantidad_compra.innerHTML = parseFloat(datos[x]['OCD_CantidadCompra']).toFixed(PRECIOS_DECIMALES);
            cantidad_compra.setAttribute("nowrap", "true");
            signo_precio.innerHTML = SIMBOLO_MONEDA;
            precio.innerHTML = parseFloat(datos[x]['OCD_PrecioUnitario']).toFixed(PRECIOS_DECIMALES);
            precio.setAttribute("nowrap", "true");
            signo_subtotal.innerHTML = SIMBOLO_MONEDA;
            subtotal.innerHTML = parseFloat(datos[x]['OCD_Subtotal']).toFixed(PRECIOS_DECIMALES);
            subtotal.setAttribute("nowrap", "true");
            signo_descuento.innerHTML = SIMBOLO_MONEDA;
            descuento.innerHTML = parseFloat(datos[x]['OCD_Descuento']).toFixed(PRECIOS_DECIMALES);
            descuento.setAttribute("nowrap", "true");
            signo_iva.innerHTML = SIMBOLO_MONEDA;
            iva.innerHTML = parseFloat(datos[x]['OCD_Iva']).toFixed(PRECIOS_DECIMALES);
            iva.setAttribute("nowrap", "true");
            signo_total.innerHTML = SIMBOLO_MONEDA;
            total.innerHTML = parseFloat(datos[x]['OCD_Total']).toFixed(PRECIOS_DECIMALES);
            total.setAttribute("nowrap", "true");
            editar.innerHTML = '<button type="button" class="btn btn-primary" onclick="editarArtMisc(this.parentNode.parentNode.sectionRowIndex)" data-toggle="tooltip" data-placement="right" title="" data-original-title="Editar Fila"> <span class="glyphicon glyphicon-pencil"></span> </button>';
            eliminar.innerHTML = '<button type="button" class="btn btn-danger" onclick="eliminarArtMisc(this.parentNode.parentNode.sectionRowIndex)" data-toggle="tooltip" data-placement="right" title="" data-original-title="Eliminar Fila"> <span class="glyphicon glyphicon-trash"></span> </button>';
            comentario.innerHTML = datos[x]['OCD_Comentarios'];
            articuloId.innerHTML = datos[x]['OCD_ART_ArticuloId'];
            articuloId.setAttribute("nowrap", "true");
            umiId.innerHTML = datos[x]['OCD_CMUM_UMInventarioId'];
            umiId.setAttribute("nowrap", "true");
            umcId.innerHTML = datos[x]['OCD_CMUM_UMComprasId'];
            umcId.setAttribute("nowrap", "true");
            porcentajeDescuento.innerHTML = datos[x]['OCFR_PorcentajeDescuento'];
            porcentajeDescuento.setAttribute("nowrap", "true");
            factorConversion.innerHTML = datos[x]['OCD_AFC_FactorConversion'];
            factorConversion.setAttribute("nowrap", "true");
            partidaId.innerHTML = datos[x]['OCD_PartidaId'];
            partidaId.setAttribute("nowrap", "true");
            precioConFactor.innerHTML = datos[x]['OCFR_PrecioConFactor'];
            precioConFactor.setAttribute("nowrap", "true");
            artConFactor.innerHTML = datos[x]['ART_ConFactor'];
            artConFactor.setAttribute("nowrap", "true");
            estatus.innerHTML = datos[x]['OCFR_CMM_EstadoFechaRequerida'];
            estatus.setAttribute("nowrap", "true");
            ivaId.innerHTML = datos[x]['OCD_CMIVA_IVAId'];
            ivaId.setAttribute("nowrap", "true");
            porcentajeIva.innerHTML = datos[x]['OCD_CMIVA_PorcentajeIVA'];
            porcentajeIva.setAttribute("nowrap", "true");
            tipoPartidaMiscId.innerHTML = datos[x]['OCD_CMM_TipoPartidaMiscelaneaId'];
            tipoPartidaMiscId.setAttribute("nowrap", "true");

            cantidad.style.textAlign = "right";
            cantidad_compra.style.textAlign = "right";
            signo_precio.style.textAlign = "right";
            precio.style.textAlign = "right";
            signo_subtotal.style.textAlign = "right";
            subtotal.style.textAlign = "right";
            signo_descuento.style.textAlign = "right";
            descuento.style.textAlign = "right";
            signo_iva.style.textAlign = "right";
            iva.style.textAlign = "right";
            signo_total.style.textAlign = "right";
            total.style.textAlign = "right";

            comentario.style.display = "none";
            articuloId.style.display = "none";
            umiId.style.display = "none";
            umcId.style.display = "none";
            porcentajeDescuento.style.display = "none";
            factorConversion.style.display = "none";
            partidaId.style.display = "none";
            precioConFactor.style.display = "none";
            artConFactor.style.display = "none";
            estatus.style.display = "none";
            ivaId.style.display = "none";
            porcentajeIva.style.display = "none";
            cantidad_compra.style.display = "none";
            tipoPartidaMiscId.style.display = "none";

            //LLENAR TABLA ARTICULOS EXISTENTES RESUMEN
            var tblArtMiscRes = document.getElementById('tblArticulosMiscelaneosResumen').getElementsByTagName('tbody')[0];
            var index2 = tblArtMiscRes.rows.length + 1;
            var fila2   = tblArtMiscRes.insertRow(tblArtMiscRes.rows.length);

            var partida2 = fila2.insertCell(COL_OCAE_PARTIDA - 1);//1
            var articulo2 = fila2.insertCell(COL_OCAE_ARTICULO - 1);//2
            var umi2 = fila2.insertCell(COL_OCAE_UMI - 2);//4
            var cantidad2 = fila2.insertCell(COL_OCAE_CANTIDAD - 2);//5
            var umc2 = fila2.insertCell(COL_OCAE_UMC - 2);
            var cantidad_compra2 = fila2.insertCell(COL_OCAE_CANTIDAD_COMPRA - 2);
            var signo_precio2 = fila2.insertCell(COL_OCAE_SIGNO_PRECIO - 2);
            var precio2 = fila2.insertCell(COL_OCAE_PRECIO - 2);
            var signo_subtotal2 = fila2.insertCell(COL_OCAE_SIGNO_SUBTOTAL - 2);
            var subtotal2 = fila2.insertCell(COL_OCAE_SUBTOTAL - 2);
            var signo_descuento2 = fila2.insertCell(COL_OCAE_SIGNO_DESCUENTO - 2);
            var descuento2 = fila2.insertCell(COL_OCAE_DESCUENTO - 2);
            var signo_iva2 = fila2.insertCell(COL_OCAE_SIGNO_IVA - 2);
            var iva2 = fila2.insertCell(COL_OCAE_IVA - 2);
            var signo_total2 = fila2.insertCell(COL_OCAE_SIGNO_TOTAL - 2);
            var total2 = fila2.insertCell(COL_OCAE_TOTAL - 2);

            partida2.innerHTML = index2;
            partida2.setAttribute("nowrap", "true");
            articulo2.innerHTML = datos[x]['OCD_DescripcionArticulo'];
            articulo2.setAttribute("nowrap", "true");
            umi2.innerHTML = datos[x]['OCD_CMUM_UMInventario'];
            umi2.setAttribute("nowrap", "true");
            cantidad2.innerHTML = parseFloat(datos[x]['OCD_CantidadRequerida']).toFixed(PRECIOS_DECIMALES);
            cantidad2.setAttribute("nowrap", "true");
            umc2.innerHTML = datos[x]['OCD_CMUM_UMCompras'];
            umc2.setAttribute("nowrap", "true");
            cantidad_compra2.innerHTML = parseFloat(datos[x]['OCD_CantidadRequerida']).toFixed(PRECIOS_DECIMALES);
            cantidad_compra2.setAttribute("nowrap", "true");
            signo_precio2.innerHTML = SIMBOLO_MONEDA;
            precio2.innerHTML = parseFloat(datos[x]['OCD_PrecioUnitario']).toFixed(PRECIOS_DECIMALES);
            precio2.setAttribute("nowrap", "true");
            signo_subtotal2.innerHTML = SIMBOLO_MONEDA;
            subtotal2.innerHTML = parseFloat(datos[x]['OCD_Subtotal']).toFixed(PRECIOS_DECIMALES);
            subtotal2.setAttribute("nowrap", "true");
            signo_descuento2.innerHTML = SIMBOLO_MONEDA;
            descuento2.innerHTML = parseFloat(datos[x]['OCD_Descuento']).toFixed(PRECIOS_DECIMALES);
            descuento2.setAttribute("nowrap", "true");
            signo_iva2.innerHTML = SIMBOLO_MONEDA;
            iva2.innerHTML = parseFloat(datos[x]['OCD_Iva']).toFixed(PRECIOS_DECIMALES);
            iva2.setAttribute("nowrap", "true");
            signo_total2.innerHTML = SIMBOLO_MONEDA;
            total2.innerHTML = parseFloat(datos[x]['OCD_Total']).toFixed(PRECIOS_DECIMALES);
            total2.setAttribute("nowrap", "true");

            cantidad2.style.textAlign = "right";
            cantidad_compra2.style.textAlign = "right";
            signo_precio2.style.textAlign = "right";
            precio2.style.textAlign = "right";
            signo_subtotal2.style.textAlign = "right";
            subtotal2.style.textAlign = "right";
            signo_descuento2.style.textAlign = "right";
            descuento2.style.textAlign = "right";
            signo_iva2.style.textAlign = "right";
            iva2.style.textAlign = "right";
            signo_total2.style.textAlign = "right";
            total2.style.textAlign = "right";

            cantidad_compra2.style.display = "none";

        }

    }

    validaBotonProveedor();

}

function validaBotonProveedor(){

    var cont = 0;
    $('#tblArticulosMiscelaneos tbody tr').each(function (index, tr) {

        cont++;

    });

    if(cont > 0){

        $('#btnBuscarProveedores').removeAttr("disabled");
        editaProveedor = 1;

    }
    else{

        $('#btnBuscarProveedores').attr("disabled","disabled");
        editaProveedor = 0;

    }

}

function agregaDatosArregloFR(datos){

    arrayTablaFecReq = [];
    if(datos.length > 0){

        var cuentaDatos = datos.length;
        var artExiste = datos[0]['OCD_ART_ArticuloId'];
        var num = '';
        var fecha = '';
        var cantidad = '';
        var idFecha = '';
        var idEstatus = '';
        for(var x = 0; x < cuentaDatos; x ++){

            var divideFecha = datos[x]['OCFR_FechaRequerida'];
            console.log();
            var cadena = divideFecha.split("-");
            var nuevaFecha = cadena[2] + "/" + cadena[1] + "/" + cadena[0];
            if(artExiste != datos[x]['OCD_ART_ArticuloId']){

                arregloFechasRequeridas.push({'idArticulo':artExiste,'arrayFechas':arrayTablaFecReq});
                num = datos[x]['OCFR_NumeroLinea'];
                fecha = nuevaFecha;
                cantidad = datos[x]['OCFR_CantidadRequerida'];
                idFecha = datos[x]['OCFR_FechaRequeridaId'];
                idEstatus = datos[x]['OCFR_CMM_EstadoFechaRequeridaId'];
                arrayTablaFecReq.push(num,fecha,cantidad,idFecha,idEstatus);
                artExiste = datos[x]['OCD_ART_ArticuloId'];

            }
            else{
                num = datos[x]['OCFR_NumeroLinea'];
                fecha = nuevaFecha;
                cantidad = datos[x]['OCFR_CantidadRequerida'];
                idFecha = datos[x]['OCFR_FechaRequeridaId'];
                idEstatus = datos[x]['OCFR_CMM_EstadoFechaRequeridaId'];
                arrayTablaFecReq.push(num,fecha,cantidad,idFecha,idEstatus);

            }

        }
        arregloFechasRequeridas.push({'idArticulo':artExiste,'arrayFechas':arrayTablaFecReq});

    }
    console.log(arregloFechasRequeridas);

}

$('#tableOC').on( 'click', 'button#btnEliminar', function (e) {

    e.preventDefault();

    var tblOC = $('#tableOC').DataTable();
    var fila = $(this).closest('tr');
    var datos = tblOC.row(fila).data();
    ocId = datos['DT_RowId'];
    var codigoOC = datos['OC_CodigoOC'];
    var estadoOC = datos['OC_CMM_EstadoOC'];

    if(estadoOC == 'Abierta'){

        bootbox.dialog({

            title: "Orden de Compra",
            message: "¿Estás seguro de eliminar la orden de compra "+ codigoOC +"?, No podrás deshacer el cambio.",
            buttons: {

                success: {

                    label: "Si",
                    className: "btn-success m-r-5 m-b-5",
                    callback: function () {

                        $.blockUI({ css: {

                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .5,
                            color: '#fff'

                        } });

                        $.ajax({

                            type: "POST",
                            async: false,
                            data: {

                                ocId: ocId

                            },
                            dataType: "json",
                            url: "compras/eliminarOC",
                            success: function (data) {

                                setTimeout($.unblockUI, 2000);
                                setTimeout(function () {

                                    var respuesta = JSON.parse(JSON.stringify(data));
                                    if(respuesta.codigo == 200){

                                        bootbox.dialog({

                                            message: "Se ha eliminado la orden de compra " + codigoOC + " con exito.",
                                            title: "Orden de Compra",
                                            buttons: {

                                                success: {

                                                    label: "Ok",
                                                    className: "btn-success",
                                                    callback: function () {

                                                        reloadBuscadorOC();

                                                    }

                                                }

                                            }

                                        });

                                    }
                                    else{

                                        setTimeout($.unblockUI, 2000);
                                        bootbox.dialog({

                                            message: respuesta.respuesta,
                                            title: "Orden de Compra",
                                            buttons: {

                                                success: {

                                                    label: "ok",
                                                    className: "btn-success"

                                                }

                                            }

                                        });

                                    }

                                }, 2000);

                            },
                            error: function (xhr, ajaxOptions, thrownError) {

                                $.unblockUI();
                                var error = JSON.parse(xhr.responseText);
                                bootbox.alert({

                                    size: "large",
                                    title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                                    message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                                    ( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
                                    ( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
                                    ( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'

                                });

                            }

                        });

                    }

                },
                default: {

                    label: "No",
                    className: "btn-default m-r-5 m-b-5"

                }

            }

        });

    }
    else{

        bootbox.dialog({

            message: 'Solo puedes eliminar Ordenes de Compra que esten con estatus Abierta.',
            title: "Orden de Compra",
            buttons: {

                success: {

                    label: "ok",
                    className: "btn-success"

                }

            }

        });

    }

});

function number_format(number, decimals, dec_point, thousands_sep) {
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
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


$('#tableOC').on( 'click', 'button#boton-pdf', function (e) {
    e.preventDefault();
//$('#tableOC #boton-pdf').click(function () {
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
        ocId = datos['DT_RowId'];

        $('#mode-group .active').each(function(){
            tipoReporte = $(this).attr('id');
        });

        if (!$('#chkPaginar').is(":checked")) {
            isChkPaginar = false;
        }

        if ($('#chkMostrarLogo').is(":checked")) {
            isChkMostrarLogo = true;
        }

        var form = document.createElement("form");
        form.target = "_blank";
        form.method = "POST";
        form.action = "compras/reporte-comprasFicha-exportar";
        form.style.display = "none";

        var isChkPaginarInput = document.createElement("input");
        isChkPaginarInput.type = "text";
        isChkPaginarInput.name = "isChkPaginar";
        isChkPaginarInput.value = isChkPaginar;
        form.appendChild(isChkPaginarInput);

        var isChkMostrarLogoInput = document.createElement("input");
        isChkMostrarLogoInput.type = "text";
        isChkMostrarLogoInput.name = "isChkMostrarLogo";
        isChkMostrarLogoInput.value = isChkMostrarLogo;
        form.appendChild(isChkMostrarLogoInput);

        var tipoFormatoInput = document.createElement("input");
        tipoFormatoInput.type = "text";
        tipoFormatoInput.name = "tipoFormato";
        tipoFormatoInput.value = tipoFormato;
        form.appendChild(tipoFormatoInput);

        var ocIdInput = document.createElement("input");
        ocIdInput.type = "text";
        ocIdInput.name = "ocId";
        ocIdInput.value = ocId;
        form.appendChild(ocIdInput);

        var tipoReporteInput = document.createElement("input");
        tipoReporteInput.type = "text";
        tipoReporteInput.name = "tipoReporte";
        tipoReporteInput.value = tipoReporte;
        form.appendChild(tipoReporteInput);


        document.body.appendChild(form);

        form.submit();

        $.unblockUI();
});