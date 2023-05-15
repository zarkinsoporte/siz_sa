/**
 * Created by mgl_l on 15/08/2019.
 */

var DECIMALES = 2;
var CANTIDAD_DECIMALES = 2;
var PRECIOS_DECIMALES = 2;
var TC_DECIMALES = 4;
var PORCENTAJE_DECIMALES = 2;

//buscador
var COL_CODIGO_OC = 0;
var COL_BTN_EDITAR = 9;
var COL_BTN_ELIMINAR = 10;
var COL_BTN_PDF = 11;

var OC_nueva = 0;
var descuentoOC = 0;
var banderaDatosAdicionales = 0;
var ivaOC = 16;
var ivaIDOC = 'W3';
var DESCUENTO_GENERAL = 0;
var IVA = [];
var BanderaOC = 0;
var SIMBOLO_MONEDA = '';
var JSON_DETALLES = {};
var JSON_PARTIDA = {};
var DATOS_TEMP = [];
var UNIDAD = [];
var TIPO_PARTIDA_MISC = [];
var CTAS_MAYOR = [];
var bandera;
var editaProveedor = 0;
var PARTIDA_ART_EXIS_ELIMINADA = [];
var PARTIDA_ART_MISC_ELIMINADA = [];
var banderaAddPedimento = 0;
var comboFechaRequeridaId;
var banderaBuscaPedimento = 0;

var TBL_ART_EXIST = '';
var TBL_ART_MISC = '';
var ARTICULO_BUSCADOR_INDEX = '';
var TBLResumenArtExis = '';
var TBLResumenArtMisc = '';

//Tablas
var COL_PARTIDA = 0;
var COL_CODIGO_ART = 1;
var COL_NOMBRE_ART = 2;
var COL_NOMBRE_ART_MISC = 1;
var COL_UNIDAD_MEDIDA_INV = 3;
var COL_FACTOR_CONVERSION = 4;
var COL_UNIDAD_MEDIDA_COMPRAS = 5;
var COL_CANTIDAD = 6;
var COL_PRECIO = 7;
var COL_SUBTOTAL = 8;
var COL_DESCUENTO = 9;
var COL_MONTO_DESCUENTO = 10;
var COL_IVA = 11;
var COL_MONTO_IVA = 12;
var COL_TOTAL = 13;
var COL_FECHA_ENTREGA_COMPRA = 14;
var COL_CANT_PENDIENTE = 15;
var COL_PARTIDA_CERRADA = 16;
var COL_BTN_ELIMINAR_COMPRA = 17;
var COL_ID_IVA = 18;
var COL_ID_PARTIDA = 19;
var COL_ESTATUS_PARTIDA = 20;

var COL_CODIGO_OT = 19;
var COL_ID_OT = 20;

var COL_ID_ARTICULO = 21;
var COL_ID_AUX = 22;
var COL_ID_UMI = 23;
var COL_ID_UMC = 24;
var COL_CTA_MAYOR = 2;

//Tabla articulos delproveedor
var ARTICULO_BUSCADOR_ID = '';
var ARTICULO_BUSCADOR_CODIGO = '';
var ARTICULO_BUSCADOR_NOMBRE = '';
var ARTICULO_BUSCADOR_TIPO = '';
var ARTICULO_BUSCADOR_UMI = '';
var ARTICULO_BUSCADOR_FACTOR_CONVERSION = '';
var ARTICULO_BUSCADOR_UMC = '';
var ARTICULO_BUSCADOR_PRECIO_COMPRA = '';
var ARTICULO_BUSCADOR_UMI_ID = '';
var ARTICULO_BUSCADOR_UMC_ID = '';
var ARTICULO_BUSCADOR_INDEX = '';
var ARTICULO_BUSCADOR_ML = '';
var ARTICULO_BUSCADOR_PRECIO_MON = '';

// Mapeo columnas tabla detalles
var COL_DETALLE_CANTIDAD = 0;
var COL_DETALLE_FECHAREQ = 1;
var COL_DETALLE_FECHA_PROMESA = 2;
var COL_DETALLE_ELIMINAR = 3;
var COL_DETALLE_OVD_DETALLEID = 4;

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
    InicializaButton();
    consultaDatos();
    InicializaBuscadorProveedores();
    InicializaBuscadorArticulos();
    InicializaBuscadorOtReq();
    
    InicializaTablas();
    insertarFila()
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

    $('#calendario1').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    }).datepicker("refresh");

    $('#calendario1').datepicker("setDate", new Date());

    /////////////////////////////

};



var InicializaButton = function() {

    $('#registros-OC').append('<button id="nuevo" style="display: inline-block; margin-left: 30px;" type="button" class="btn btn-primary m-r-5 m-b-5" onclick="boton()">Nuevo</button>');

};

function consultaDatos (){

    $.ajax({

        cache: false,
        async: false,
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        url: routeapp + 'consultaIvasYUnidadesMedida',
        type: "POST",
        success: function( datos ) {

            var respuesta = JSON.parse(JSON.stringify(datos));
            if(respuesta.codigo == 200){

                IVA = respuesta.ivas;
               // UNIDAD = respuesta.unidadesMedida;
                CTAS_MAYOR = respuesta.ctasmayor;
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

var InicializaBuscadorProveedores = function () {

    nombreInputId = 'input-proveedor';
    urlProveedores = 'getProveedoresBuscador';
    controlaFuncion = 1;
    Buscadores.init();

}

function InicializaBuscadorOtReq(){
    nombreInputOtId = 'idOt';
    urlOts = 'getOtsBuscadorREQ';
    //controlaFuncion = 2;
    BuscadoresOtsREQ.init();
}


function InicializaTablas(){

    TBL_ART_EXIST = $('#tblArticulosExistentesNueva').DataTable({
        language:{
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        searching: false,
        iDisplayLength: 100,
        aaSorting: [],
        deferRender: true,
        paging: false,
        dom: 'TB<"clear">lfrtip',
        buttons: [
            {
                text: '<i class="fa fa-plus"></i> Fila',
                className: "btn-success",
                action: function (e, dt, node, config) {
                    insertarFila(); 
                }
            }
        ],
        columns: [
            {data: "PARTIDA"},
            {data: "CODIGO_ARTICULO"},
            {data: "NOMBRE_ARTICULO"},

            {data: "UNIDAD_MEDIDA_INV"},
            {data: "FACTOR_CONVERSION"},
            {data: "UNIDAD_MEDIDA_COMPRAS"},

            {data: "CANTIDAD"},
            {data: "PRECIO"},
            {data: "SUBTOTAL"},

            {data: "DESCUENTO"},
            {data: "MONTO_DESCUENTO"},
            {data: "IVA"},

            {data: "MONTO_IVA"},
            {data: "TOTAL"},
            {data: "FECHA_ENTREGA", orderable: false},

            { data: "CANT_PENDIENTE" },
            {data: "PARTIDA_CERRADA"},
            {data: "BTN_ELIMINAR", orderable: false},

            {data: "ID_IVA", orderable:false, visible:false},
            {data: "ID_PARTIDA", orderable:false, visible:false},//20
            { data: "ESTATUS_PARTIDA", orderable: false, visible: false }//21

        ],

        "rowCallback": function (row, data, index) {
            //console.log(data['Existencia']);
            if (parseFloat(data['Existencia']) == 0) {
                //  $(row).addClass("ignoreme");
                $('td', row).addClass("ignoreme");
            }
        },
        "columnDefs": [
            {
                searchable: false,
                orderable: false,
                targets: 0,
            },
            {
                "targets": [COL_CODIGO_ART],
                "orderable" : false,
                "render": function ( data, type, row ) {
                    return '                            <div class="input-group">\n' +
                    '                                <input type="text" class="form-control input-sm codigo" style=""id="input-articulo-codigoAE" value="' + row['CODIGO_ARTICULO'] + '">\n' +
                    '                                <div class="input-group-btn">\n' +
                    '                                    <a style="cursor: pointer;" class="btn btn-sm btn-success m-r-5" id="boton-articuloAE"><i class="fa fa-search" aria-hidden="true"></i></a>\n' +
                    '                                </div>\n' +
                    '                            </div>';
                }
            },
            {
                "targets": [COL_NOMBRE_ART],
                "orderable" : false
            },
            {
                "targets": [COL_UNIDAD_MEDIDA_INV],
                "orderable" : false
            },
            {
                "targets": [COL_FACTOR_CONVERSION],
                "orderable" : false
            },
            {
                "targets": [COL_UNIDAD_MEDIDA_COMPRAS],
                "orderable" : false
            },
            {
                "targets": [COL_CANTIDAD],
                "orderable" : false,
                "render": function ( data, type, row ) {
                    return '<input id="input-cantidadAE" style="" class="form-control input-sm cantidad" type="number" value="' + parseFloat(row['CANTIDAD']).toFixed(DECIMALES) + '">';
                }
            },
            {
                "targets": [COL_PRECIO],
                "orderable" : false,
                "render": function (data,type,row) {
                    return '<input id= "input-precioAE" style="width: 100px" class="form-control input-sm precio" value="' + parseFloat(row['PRECIO']).toFixed(DECIMALES) + '" type="number">'
                }
            },
            {
                "targets": [COL_SUBTOTAL],
                "orderable" : false
            },
            {
                "targets": [COL_DESCUENTO],
                "orderable" : false,
                "render": function (data,type,row) {
                    return '<input id= "input-descuentoAE" style="width: 100px" class="form-control input-sm" value="' + parseFloat(DESCUENTO_GENERAL).toFixed(DECIMALES) + '" type="number">'
                }
            },
            {
                "targets": [COL_MONTO_DESCUENTO],
                "orderable" : false
            },
            {
                "targets": [COL_IVA],
                "orderable" : false,
                "render": function (data,type,row) {
                    var id = row[1];
                    var opciones =  [];
                    for (var i=0; i < IVA.length; i++){
                        if  (id == IVA[i]['CMIVA_IVAId']){
                            opciones.push('<option selected value="'+IVA[i]['CMIVA_IVAId']+'">'+IVA[i]['CMIVA_Porcentaje']+'</option>');
                        }else{
                            opciones.push('<option value="'+IVA[i]['CMIVA_IVAId']+'">'+IVA[i]['CMIVA_Porcentaje']+'</option>');
                        }
                    }

                    var select = '<select data-live-search="true" class="boot-select selectpicker form-control" data-style="btn-sm btn-success" style="padding:1px !important; display: block !important" id="cboIVAAE">' + opciones +'</select>';
                    return select;
                }
            },
            {
                "targets": [COL_MONTO_IVA],
                "orderable" : false
            },
            {
                "targets": [COL_TOTAL],
                "orderable" : false
            },
            {
                "targets": [COL_FECHA_ENTREGA_COMPRA],
                "orderable" : false,
                "render": function (data, type, row) {
                    var fecha_entrega = $('#input-fecha-entrega').val();
                    return '<input id= "input-fecha-entrega-linea" style="width: 100px" class="form-control input-sm fila-dt" type="date" value="' + fecha_entrega +'">'
                }
            },
            
            //articulos[i]["OVD_DetalleId"] == '' ? id nuevo : articulos[i]["OVD_DetalleId"];
           
            {
                "targets": [COL_PARTIDA_CERRADA],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    return '<input type="checkbox" id="cerrarPartidaCheck" class="editor-active" disabled>';

                }
            },
            {
                "targets": [COL_BTN_ELIMINAR_COMPRA],
                "className": "dt-body-center",
                "render": function ( data, type, row ) {
                    return '<a class="btn btn-danger btn-sm" id="boton-eliminarAE"> <span class="fa fa-trash"></span> </a>';
                }
            }
        ]
    });     

    $('#tblArticulosExistentesNueva').dataTable().fnAdjustColumnSizing( false );
    ARTICULO_BUSCADOR_INDEX = $('#tblArticulosExistentesNueva').DataTable().row('.selected').index() == undefined ? '' : $('#tblArticulosExistentesNueva').DataTable().row('.selected').index();
   
    TBL_ART_MISC = $('#tblArticulosMiscelaneosNueva').DataTable({
        language:{
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
         "drawCallback": function( settings ) {
            $(".selectpicker").selectpicker({
                noneSelectedText: 'Selecciona una opción',
                container: "body"
            });
        },
        searching: false,
        iDisplayLength: 100,
        aaSorting: [],
        deferRender: true,
        paging: false,
        dom: 'TB<"clear">lfrtip',
        buttons: [
            {
                text: '<i class="fa fa-plus"></i> Fila',
                className: "btn-success",
                action: function (e, dt, node, config) {
                    insertarFila();
                }
            }
        ],
        columns: [          
            {data: "PARTIDA"},
            {data: "NOMBRE_ARTICULO"},
            {data: "CTA_MAYOR"},

            {data: "CANTIDAD"},
            {data: "PRECIO"},
            {data: "SUBTOTAL"},

            {data: "DESCUENTO"},
            {data: "MONTO_DESCUENTO"},
            {data: "IVA"},

            {data: "MONTO_IVA"},
            {data: "TOTAL"},
            {data: "FECHA_ENTREGA", orderable: false },
            
            {data: "PARTIDA_CERRADA"},
            {data: "BTN_ELIMINAR", orderable: false},
            {data: "ID_IVA", orderable:false, visible:false},

            {data: "ID_PARTIDA", orderable:false, visible:false},//17
            {data: "ESTATUS_PARTIDA", orderable:false, visible:false}//18
            
        ],
        "columnDefs": [
            {
                searchable: false,
                orderable: false,
                targets: 0,
            },
            {
                "targets": [1],
                "orderable" : false,
                "render": function (data,type,row) {
                    return '<input id= "input-nombreART-miselaneos"  class="form-control input-sm" type="text">'
                }
            },
            
            {
                "targets": [2],
                "orderable" : false,
                "render": function (data,type,row) {
                    var id = row[1];
                    var opciones = [];

                    for (var i=0; i < CTAS_MAYOR.length; i++){
                        if  (id == CTAS_MAYOR[i]['ControlId']){
                            opciones.push('<option selected value="'+CTAS_MAYOR[i]['ControlId']+'">'+CTAS_MAYOR[i]['Valor']+'</option>');
                        }else{
                            opciones.push('<option value="'+CTAS_MAYOR[i]['ControlId']+'">'+CTAS_MAYOR[i]['Valor']+'</option>');
                        }
                    }

                    var select = '<select data-live-search="true" class=" boot-select selectpicker form-control" data-style="btn-sm btn-success" style="padding: 1px !important; display: block !important" id="cboTPM">' + opciones +'</select>';
                    return select;
                }
             },
            {
                "targets": [3],
                "orderable" : false,
                "render": function ( data, type, row ) {
                    return '<input id="input-cantidadAM"  class="form-control input-sm cantidad" type="number" value="' + parseFloat(row['CANTIDAD']).toFixed(DECIMALES) + '">';
                }
            },
            {
                "targets": [4],
                "orderable" : false,
                "render": function (data,type,row) {
                    return '<input id= "input-precioAM"  class="form-control input-sm precio" value="' + parseFloat(row['PRECIO']).toFixed(DECIMALES) + '" type="number">'
                }
            },
            {
                "targets": [5],
                "orderable" : false
            },
            {
                "targets": [6],
                "orderable" : false,
                "render": function (data,type,row) {
                    return '<input id= "input-descuentoAM"  class="form-control input-sm" value="' + parseFloat(DESCUENTO_GENERAL).toFixed(DECIMALES) + '" type="number">'
                }
            },
            {
                "targets": [7],
                "orderable" : false
            },
            {
                "targets": [8],
                "orderable" : false,
                "render": function (data,type,row) {
                    var id = row[1];
                    var opciones =  [];

                    for (var i=0; i < IVA.length; i++){
                        if  (id == IVA[i]['CMIVA_IVAId']){
                            opciones.push('<option selected value="'+IVA[i]['CMIVA_IVAId']+'">'+IVA[i]['CMIVA_Porcentaje']+'</option>');
                        }else{
                            opciones.push('<option value="'+IVA[i]['CMIVA_IVAId']+'">'+IVA[i]['CMIVA_Porcentaje']+'</option>');
                        }
                    }

                    var select = '<select data-live-search="true" class="boot-select selectpicker form-control" data-style="btn-sm btn-success" style="padding: 1px !important; display: block !important" id="cboIVAAM">' + opciones +'</select>';
                    return select;
                }
            },
            {
                "targets": [9], //monto IVA
                "orderable" : false
            },
            {
                "targets": [10], //total
                "orderable" : false
            },
            {
                "targets": [11],
                "orderable": false,
                "render": function (data, type, row) {
                    var fecha_entrega = $('#input-fecha-entrega').val();
                    return '<input id= "input-fecha-entrega-linea" style="width: 100px" class="form-control input-sm fila-dt" type="date" value="' + fecha_entrega + '">'
                }
            },

            //articulos[i]["OVD_DetalleId"] == '' ? id nuevo : articulos[i]["OVD_DetalleId"];

            {
                "targets": [12],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function (data, type, row) {

                    return '<input type="checkbox" id="cerrarPartidaCheck" class="editor-active" disabled>';

                }
            },
            
            {
                "targets": [13],
                "className": "dt-body-center",
                "render": function ( data, type, row ) {
                    return '<button type="button" class="btn btn-danger btn-sm" id="boton-eliminarAM"> <span class="fa fa-trash"></span> </button>';
                }
            },
            {
                "targets": [14],
                "orderable" : false
            },          
        ]
    });

    $('#tblArticulosMiscelaneosNueva').dataTable().fnAdjustColumnSizing( false );
    //ARTICULO_BUSCADOR_INDEX = $('#tblArticulosMiscelaneosNueva').DataTable().row('.selected').index() == undefined ? '' : $('#tblArticulosMiscelaneosNueva').DataTable().row('.selected').index();

}

function InicializaBuscadorArticulos(){

    var tabla = $("#tabla-articulo").DataTable({

        language:{

            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"

        },
        "iDisplayLength": 10,
        "aaSorting": [],
        dom: 'T<"clear">lfrtip',
        deferRender: true,
        columns: [

            {data: "ART_CodigoArticulo"},
            {data: "ART_Nombre"},
            {data: "ATP_Descripcion"},
            {data: "UMI"},
            {data: "AFC_FactorConversion"},
            {data: "UMC"},
            {data: "Precio"},
            {data: "M_L"},
            {data: "Precio_Tipo_Cambio"}

        ],
        fixedColumns: false,
        
        'order': [[COL_CODIGO_OC, 'DESC']]

    });

    $('#tabla-articulo tbody').on( 'click', 'tr', function () {
        if (!$(this).hasClass('selected')) {
            var row = $(this);
            var datos = tabla.row(row).data();

            if(datos != undefined){

                //ARTICULO_BUSCADOR_ID = datos['DT_RowId'];
                ARTICULO_BUSCADOR_CODIGO = datos['ART_CodigoArticulo'];
                ARTICULO_BUSCADOR_NOMBRE = datos['ART_Nombre'];
                ARTICULO_BUSCADOR_TIPO = datos['ATP_Descripcion'];
                ARTICULO_BUSCADOR_UMI = datos['UMI'];
                ARTICULO_BUSCADOR_FACTOR_CONVERSION = datos['AFC_FactorConversion'];
                ARTICULO_BUSCADOR_UMC = datos['UMC'];
                ARTICULO_BUSCADOR_PRECIO_COMPRA = datos['Precio_Tipo_Cambio'];
                ARTICULO_BUSCADOR_ML = datos['M_L'];
                ARTICULO_BUSCADOR_PRECIO_MON = datos['Precio'];
                //ARTICULO_BUSCADOR_UMI_ID = datos['UMI_CMUM_UMInventarioId'];
                //ARTICULO_BUSCADOR_UMC_ID = datos['UMC_CMUM_UMCompraId'];
                tabla.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        }
    });

    $('#tabla-articulo_SSSSSSSSSS tbody').on( 'dblclick', 'tr', function () {
        var row = $(this);
        var datos = tabla.row(row).data();

        if(datos != undefined) {
            tabla.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');

            //ARTICULO_BUSCADOR_ID = datos['DT_RowId'];
            ARTICULO_BUSCADOR_CODIGO = datos['ART_CodigoArticulo'];
            ARTICULO_BUSCADOR_NOMBRE = datos['ART_Nombre'];
            ARTICULO_BUSCADOR_TIPO = datos['ATP_Descripcion'];
            ARTICULO_BUSCADOR_UMI = datos['UMI'];
            ARTICULO_BUSCADOR_FACTOR_CONVERSION = datos['AFC_FactorConversion'];
            ARTICULO_BUSCADOR_UMC = datos['UMC'];
            ARTICULO_BUSCADOR_PRECIO_COMPRA = datos['Precio_Tipo_Cambio'];
            ARTICULO_BUSCADOR_ML = datos['M_L'];
            ARTICULO_BUSCADOR_PRECIO_MON = datos['Precio'];
            //ARTICULO_BUSCADOR_UMI_ID = datos['UMI_CMUM_UMInventarioId'];
            //ARTICULO_BUSCADOR_UMC_ID = datos['UMC_CMUM_UMCompraId'];

            var fila = $('#modal-articulo #input-fila').val();

            if (BanderaOC == 0){
                var tabla_artExist = $('#tblArticulosExistentesNueva').DataTable();
                var dataAE = tabla_artExist.row(fila).data();

                var banderaExiste = false;
                var count = $('#tblArticulosExistentesNueva tbody tr').length;
                for (var i = 0; i < count; i++) {
                    var data_artExit = tabla_artExist.row(i).data();
                    if(data_artExit['CODIGO_ARTICULO'] == ARTICULO_BUSCADOR_CODIGO){
                        banderaExiste = true;
                        break;
                    }
                }

                if(banderaExiste){
                    bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>El Artículo ya existe en la Orden de Compra</div>",
                            buttons: {
                            success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                            }
                            }
                            }).find('.modal-content').css({'font-size': '14px'} );
                    
                }
                else{

                    //dataAE['ID_ARTICULO'] = ARTICULO_BUSCADOR_ID;
                    dataAE["CODIGO_ARTICULO"] = ARTICULO_BUSCADOR_CODIGO;
                    dataAE["NOMBRE_ARTICULO"] = ARTICULO_BUSCADOR_NOMBRE;
                    dataAE["UNIDAD_MEDIDA_INV"] = ARTICULO_BUSCADOR_UMI;
                    dataAE["FACTOR_CONVERSION"] = ARTICULO_BUSCADOR_FACTOR_CONVERSION;
                    dataAE["UNIDAD_MEDIDA_COMPRAS"] = ARTICULO_BUSCADOR_UMC;
                    dataAE["ID_UMI"] = ARTICULO_BUSCADOR_UMI_ID;
                    dataAE["ID_UMC"] = ARTICULO_BUSCADOR_UMC_ID;
                    dataAE["PRECIO"] = ARTICULO_BUSCADOR_PRECIO_COMPRA;
                    dataAE["IVA"] = ivaOC;
                    dataAE["ID_IVA"] = ivaIDOC;
                    dataAE["DESCUENTO"] = descuentoOC;

                    var COL_ESTATUS_PARTIDA = 20;
                    //tabla_artExist.row(fila).nodes(fila, COL_ID_ARTICULO).to$().find("td:eq('" + COL_ID_ARTICULO + "')").text(ARTICULO_BUSCADOR_ID);
                    tabla_artExist.row(fila).nodes(fila, COL_CODIGO_ART).to$().find('input#input-articulo-codigoAE').val(ARTICULO_BUSCADOR_CODIGO);
                    tabla_artExist.row(fila).nodes(fila, COL_NOMBRE_ART).to$().find("td:eq('" + COL_NOMBRE_ART + "')").text(ARTICULO_BUSCADOR_NOMBRE);
                    tabla_artExist.row(fila).nodes(fila, COL_UNIDAD_MEDIDA_INV).to$().find("td:eq('" + COL_UNIDAD_MEDIDA_INV + "')").text(ARTICULO_BUSCADOR_UMI);
                    tabla_artExist.row(fila).nodes(fila, COL_FACTOR_CONVERSION).to$().find("td:eq('" + COL_FACTOR_CONVERSION + "')").text(ARTICULO_BUSCADOR_FACTOR_CONVERSION);
                    tabla_artExist.row(fila).nodes(fila, COL_UNIDAD_MEDIDA_COMPRAS).to$().find("td:eq('" + COL_UNIDAD_MEDIDA_COMPRAS + "')").text(ARTICULO_BUSCADOR_UMC);
                    tabla_artExist.row(fila).nodes(fila, COL_PRECIO).to$().find("input#input-precioAE").val(ARTICULO_BUSCADOR_PRECIO_COMPRA);
                    tabla_artExist.row(fila).nodes(fila, COL_DESCUENTO).to$().find("input#input-descuentoAE").val(parseFloat(dataAE["DESCUENTO"]).toFixed(DECIMALES));
                    tabla_artExist.row(fila).nodes(fila, COL_IVA).to$().find("select#cboIVAAE").val(dataAE["ID_IVA"]);
                    tabla_artExist.row(fila).nodes(fila, COL_ID_IVA).to$().find("select#cboIVAAE").val(dataAE["ID_IVA"]);
                    tabla_artExist.row(fila).nodes(fila, COL_ID_UMI).to$().find("td:eq('" + COL_ID_UMI + "')").text(ARTICULO_BUSCADOR_UMI_ID);
                    tabla_artExist.row(fila).nodes(fila, COL_ID_UMC).to$().find("td:eq('" + COL_ID_UMC + "')").text(ARTICULO_BUSCADOR_UMC_ID);

                }

            }
            tabla.rows( '.selected' ).nodes().to$().removeClass( 'selected' );
            calculaTotalOrdenCompra();
            $('#modal-articulo').modal('hide');
        }
    });

    $('#modal-articulo #boton-aceptar').on('click', function (e) {
        e.preventDefault();

        var fila = $('#modal-articulo #input-fila').val();
        if (BanderaOC == 0){

            var tabla_artExist = $('#tblArticulosExistentesNueva').DataTable();
            var dataAE = tabla_artExist.row(fila).data();

            var banderaExiste = false;
            var count = $('#tblArticulosExistentesNueva tbody tr').length;
            for (var i = 0; i < count; i++) {
                var data_artExit = tabla_artExist.row(i).data();
                if(data_artExit['CODIGO_ARTICULO'] == ARTICULO_BUSCADOR_CODIGO){
                    banderaExiste = true;
                    break;
                }
            }

            if(banderaExiste){

               bootbox.dialog({
                    title: "Mensaje",
                    message: "<div class='alert alert-danger m-b-0'>El Artículo ya existe en la Orden de Compra</div>",
                    buttons: {
                    success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                    }
                    }
                }).find('.modal-content').css({'font-size': '14px'} );

            }
            else{

                //dataAE['ID_ARTICULO'] = ARTICULO_BUSCADOR_ID;
                dataAE["CODIGO_ARTICULO"] = ARTICULO_BUSCADOR_CODIGO;
                dataAE["NOMBRE_ARTICULO"] = ARTICULO_BUSCADOR_NOMBRE;
                dataAE["UNIDAD_MEDIDA_INV"] = ARTICULO_BUSCADOR_UMI;
                dataAE["FACTOR_CONVERSION"] = ARTICULO_BUSCADOR_FACTOR_CONVERSION;
                dataAE["UNIDAD_MEDIDA_COMPRAS"] = ARTICULO_BUSCADOR_UMC;
                dataAE["ID_UMI"] = ARTICULO_BUSCADOR_UMI_ID;
                dataAE["ID_UMC"] = ARTICULO_BUSCADOR_UMC_ID;
                dataAE["PRECIO"] = ARTICULO_BUSCADOR_PRECIO_COMPRA;
                dataAE["IVA"] = ivaOC;
                dataAE["ID_IVA"] = ivaIDOC;
                dataAE["DESCUENTO"] = descuentoOC;

                //tabla_artExist.row(fila).nodes(fila, COL_ID_ARTICULO).to$().find("td:eq('" + COL_ID_ARTICULO + "')").text(ARTICULO_BUSCADOR_ID);
                tabla_artExist.row(fila).nodes(fila, COL_CODIGO_ART).to$().find('input#input-articulo-codigoAE').val(ARTICULO_BUSCADOR_CODIGO);
                tabla_artExist.row(fila).nodes(fila, COL_NOMBRE_ART).to$().find("td:eq('" + COL_NOMBRE_ART + "')").text(ARTICULO_BUSCADOR_NOMBRE);
                tabla_artExist.row(fila).nodes(fila, COL_UNIDAD_MEDIDA_INV).to$().find("td:eq('" + COL_UNIDAD_MEDIDA_INV + "')").text(ARTICULO_BUSCADOR_UMI);
                tabla_artExist.row(fila).nodes(fila, COL_FACTOR_CONVERSION).to$().find("td:eq('" + COL_FACTOR_CONVERSION + "')").text(ARTICULO_BUSCADOR_FACTOR_CONVERSION);
                tabla_artExist.row(fila).nodes(fila, COL_UNIDAD_MEDIDA_COMPRAS).to$().find("td:eq('" + COL_UNIDAD_MEDIDA_COMPRAS + "')").text(ARTICULO_BUSCADOR_UMC);
                tabla_artExist.row(fila).nodes(fila, COL_PRECIO).to$().find("input#input-precioAE").val(ARTICULO_BUSCADOR_PRECIO_COMPRA);
                tabla_artExist.row(fila).nodes(fila, COL_DESCUENTO).to$().find("input#input-descuentoAE").val(parseFloat(dataAE["DESCUENTO"]).toFixed(DECIMALES));
                tabla_artExist.row(fila).nodes(fila, COL_IVA).to$().find("select#cboIVAAE").val(dataAE["ID_IVA"]);
                tabla_artExist.row(fila).nodes(fila, COL_ID_IVA).to$().find("select#cboIVAAE").val(dataAE["ID_IVA"]);
                // tabla_artExist.row(fila).nodes(fila, COL_ID_UMI).to$().find("td:eq('" + COL_ID_UMI + "')").text(ARTICULO_BUSCADOR_UMI_ID);
                // tabla_artExist.row(fila).nodes(fila, COL_ID_UMC).to$().find("td:eq('" + COL_ID_UMC + "')").text(ARTICULO_BUSCADOR_UMC_ID);

            }

        }
        tabla.rows( '.selected' ).nodes().to$().removeClass( 'selected' );
        calculaTotalOrdenCompra();
        $('#modal-articulo').modal('hide');
    });

    $('#modal-articulo #boton-cerrar').off().on('click', function (e) {
        e.preventDefault();

        var tabla = $('#tabla-articulo').DataTable();
        tabla.rows( '.selected' ).nodes().to$().removeClass( 'selected' );

        if(ARTICULO_BUSCADOR_INDEX != ''){
            $('#tabla-articulo').DataTable().rows( ARTICULO_BUSCADOR_INDEX ).nodes().to$().addClass( 'selected' );
        }
    });

    $('#modal-articulo').on('shown.bs.modal', function(e) {
        e.preventDefault();

        $('#tabla-articulo').dataTable().fnAdjustColumnSizing( false );
        ARTICULO_BUSCADOR_INDEX = $('#tabla-articulo').DataTable().row('.selected').index() == undefined ? '' : $('#tabla-articulo').DataTable().row('.selected').index();
    });

}

$("#ActualizarTablaOC" ).on( "click", function() {

    reloadBuscadorOC();

});

function InicializaComponentesOC() {

    $("#sel-proveedor").val('');
    $("#sel-proveedor").selectpicker('refresh');

    $('#ordenesCompraOC #nombreProveedor').text('');
    $('#ordenesCompraOC #direccionProveedor').text('');
    $("#ordenesCompraOC #emailProveedor").text('');
    $('#ordenesCompraOC #localizacionProveedor').text('');
    $('#ordenesCompraOC #telefonicosProveedor').text('');
    $('#ordenesCompraOC #contactoProveedor').text('');
    $('#ordenesCompraOC #rfcProveedor').text('');

    $("#cboMoneda").val('MXP');
    $("#cboMoneda").selectpicker('refresh');

    $("#ordenesCompraOC #input_tc").val(1);
    
    $("#sel-tipo-oc").val(0);
    $("#sel-tipo-oc").selectpicker('refresh');
      
    $('#ordenesCompraOC #codigoOC').text('Por Definir');
    $('#ordenesCompraOC #estadoOC').text('Abierta');

    $('#tblArticulosExistentesNueva').DataTable().clear().draw();
    $('#tblArticulosMiscelaneosNueva').DataTable().clear().draw();
    insertarFila();   
    InicializaDatepicker();

};

var InicializaDatepicker = function() {

    $('#input-fecha').datepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true
    }).datepicker("setDate", new Date());

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!

    var yyyy = today.getFullYear();
    if (dd < 10) { dd = '0' + dd }
    if (mm < 10) { mm = '0' + mm }
    today = yyyy + '-' + mm + '-' + dd;
    console.log(today)
    $('#input-fecha-entrega').val(today);
};

function changeProveedor(){ //??

    $.ajax({
         headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        cache: false,
        async: false,
        url: "compras/consultaSucursalesProveedor",
        data: {

            "proveedorId": $('#input-proveedor').val(),
            "monedaId": $("#cboMoneda").val()

        },
        type: "POST",
        success: function( datos ) {

            var respuesta = JSON.parse(JSON.stringify(datos));
            if(respuesta.codigo == 200){

                llenaDatosProvedor(respuesta.data);
                llenaDatosIvaMetodoEmbarqueLibreABordo(respuesta.data3);
                recargaTablaArticulos(respuesta.data2);
                insertarFila();

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
function cargaTablaArticulos(){
    console.log('------------- carga Articulos modal')
    console.log($('#input_tc').val()+ ' - ' +$('#cboMoneda').val())
    $.ajax({
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        cache: false,
        async: false,
        url: routeapp + "cargaTablaArticulos",
        data: {
            tipo_cambio: $('#input_tc').val(),
            moneda:  $('#cboMoneda').val()
        },
        type: "POST",
        success: function( datos ) {

            var respuesta = JSON.parse(JSON.stringify(datos));
            
            if(respuesta.codigo == 200){
                if ((respuesta.data2).length > 0){

                    recargaTablaArticulos(respuesta.data2);
                }
                //console.log(respuesta.sql)
            }
            else{

                bootbox.dialog({
                    message: respuesta.mensaje,
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

//??
function llenaDatosIvaMetodoEmbarqueLibreABordo(data){

    var cuentaData = data.length;

    if(cuentaData > 0){

        var ivaId = data[0]['PCA_CMIVA_IVAId'];
        var metodoEmbarqueId = data[0]['PCA_CMM_MetodoEmbarqueId'];
        var libreABordoId = data[0]['PCA_CMM_LibreABordoId'];
        var porcentajeDescuentoPro =  data[0]['PCA_PorcentajeDescuento'];

        ivaIDOC = ivaId;
        $('#modal-datos #cboIVA').val(ivaId);
        $('#modal-datos #cboMetodoEmbarque').val(metodoEmbarqueId);
        $('#modal-datos #cboLibreABordo').val(libreABordoId);
        $('#modal-datos #input-descuento').val(porcentajeDescuentoPro);

        $('#modal-datos #cboIVA').selectpicker('refresh');
        $('#modal-datos #cboMetodoEmbarque').selectpicker('refresh');
        $('#modal-datos #cboLibreABordo').selectpicker('refresh');
        ivaOC = parseFloat($('#modal-datos #cboIVA option:selected').text());
        descuentoOC = parseFloat(porcentajeDescuentoPro);

    }

}

function recargaTablaArticulos(datos){

    $("#tabla-articulo").DataTable().clear().draw();
    if(datos != ''){
        $("#tabla-articulo").dataTable().fnAddData(datos);
    }

}

function insertarFila(){
    console.log('insertar fila: (0->TBL_ART_EXIST, 1->TBL_ART_MISC) BanderaOC =' + BanderaOC)
     if (BanderaOC == 0){
        TBL_ART_EXIST.row.add(
            {
                "PARTIDA": ""
                , "CODIGO_ARTICULO": ""
                , "NOMBRE_ARTICULO": ""

                , "UNIDAD_MEDIDA_INV": ""
                , "FACTOR_CONVERSION": ""
                , "UNIDAD_MEDIDA_COMPRAS": ""

                , "CANTIDAD": "0.00"
                , "PRECIO": "0.00"
                , "SUBTOTAL": "0.00"

                , "DESCUENTO": "0.00"
                , "MONTO_DESCUENTO": "0.00"
                , "IVA": "16"

                , "MONTO_IVA": "0.00"
                , "TOTAL": "0.00"
                , "FECHA_ENTREGA":""

                , "CANT_PENDIENTE":""
                , "PARTIDA_CERRADA": 0
                , "BTN_ELIMINAR": null
                
                , "ID_IVA":"W3"                
                , "ID_PARTIDA": ""
                , "ESTATUS_PARTIDA":""//20
            }
        ).draw( false );
        actualizaLineaPartidaAE();
        } else {

         TBL_ART_MISC.row.add(
            {
                "PARTIDA": ""
                , "NOMBRE_ARTICULO": ""
                , "CTA_MAYOR": ""

                , "CANTIDAD": "0.00"
                , "PRECIO": "0.00"
                , "SUBTOTAL": "0.00"

                , "DESCUENTO": "0.00"
                , "MONTO_DESCUENTO": ""
                , "IVA": "16"

                , "MONTO_IVA": ""
                , "TOTAL": "0.00"
                , "FECHA_ENTREGA": ""

                , "PARTIDA_CERRADA": 0
                , "BTN_ELIMINAR": null
                , "ID_IVA":"W3"

                , "ID_PARTIDA": ""
                , "ESTATUS_PARTIDA":""//17
            }
        ).draw( true );
        actualizaLineaPartidaAM(); 
        }
}

function generateGuid() {
    var result, i, j;
    result = '';
    for(j=0; j<32; j++) {
        if( j == 8 || j == 12 || j == 16 || j == 20)
            result = result + '-';
        i = Math.floor(Math.random()*16).toString(16).toUpperCase();
        result = result + i;
    }
    return result;
}

$('#cboMoneda').change(function (e){

    changeMoneda();

});

function changeMoneda(){

    var tipoMoneda = $('#cboMoneda').val();
    var proveedor = $('#input-proveedor').val();
    var fechaOc = $('#input-fecha').val();

    $.ajax({
 headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
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
                recargaTablaArticulos(respuesta.data2);

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

//??
function llenarComboTipoCambio(datos){

    var tipoMoneda = $('#cboMoneda').val();

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

//??
$('#cboAgente').change(function (e){

    changeAgente();

});

//??
function changeAgente(){

    var agenteId = $('#ordenesCompraOC #cboAgente').val();

    $.ajax({
         headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
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

//??
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

//??
$('#boton-datos-adicionales').off().on( 'click', function (e) {

    e.preventDefault();
    $('#modal-datos #cboProyectos').selectpicker('refresh');
    //$('#modal-datos #cboOT').selectpicker('refresh');
    $('#modal-datos #cboLibreABordo').selectpicker('refresh');
    $('#modal-datos #cboMetodoEmbarque').selectpicker('refresh');
    //$('#modal-datos #cboIVA').val('2A9DC9E8-5AD7-48B6-82BD-B8C25C8CDF45');
    $('#modal-datos #cboIVA').selectpicker('refresh');
    $('#modal-datos').modal('show');

});

//??
$('#modal-datos #aceptar').off().on('click', function(e) {

    e.preventDefault();
    validaDatosAdicionales();
    ivaYDescuento();
    if(banderaDatosAdicionales == 0){

        if(OC_nueva == 1 || $("#ordenesCompraOC #cboTipoOC").val() == '05CA103A-B4B7-4E04-B2D8-080E0216AECF'){//ESTANDAR

            cambiarFechasRequeridasTablas();

        }
        $('#modal-datos').modal('hide');

    }

});

//??
function validaDatosAdicionales(){

    descuentoOC = $('#modal-datos #input-descuento').val();

    if(descuentoOC < 0 || descuentoOC > 100){

        banderaDatosAdicionales = 1;
       
        bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'>EL porcentaje de descuento debe ser igual o mayor a 0 y/o menor o igual a 100.</div>",
            buttons: {
            success: {
            label: "Ok",
            className: "btn-success m-r-5 m-b-5"
            }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

    }
    else{

        banderaDatosAdicionales = 0;

    }

}

//??
function ivaYDescuento(){

    if($('#modal-datos #input-descuento').val() == ''){

        descuentoOC = parseFloat(0);

    }
    else{

        descuentoOC = parseFloat($('#modal-datos #input-descuento').val());

    }

    if($('#modal-datos #cboIVA').val() == ''){

        ivaOC = parseFloat(0);
        ivaIDOC = '';

    }
    else{

        ivaOC = parseFloat($('#modal-datos #cboIVA option:selected').text());
        ivaIDOC = $('#modal-datos #cboIVA').val();

    }

    recalculaIvaYDescuento();

}
//??
function recalculaIvaYDescuento(){

}
//??
function cambiarFechasRequeridasTablas(){

}

// $('#tblArticulosExistentesNueva').on('click', 'span#boton-otAE', function (e) {
//     e.preventDefault();
//     if ($("#input-proveedor").val() != ""){
//         var tabla = $('#tblArticulosExistentesNueva').DataTable();
//         var fila = $(this).closest('tr');
//         fila = tabla.row(fila).index();
//         banderaFilaOC = 1;
//         $('#modal-ordenesTrabajo #input-fila').val(fila);
//         $('#modal-ordenesTrabajo').modal('show');
//     }
//     else{
//         bootbox.dialog({
//             message: "No se ha elegido un proveedor, elige uno por favor para continuar.",
//             title: "Ordenes de Compra",
//             buttons: {
//                 success: {
//                     label: "Si",
//                     className: "btn-success"
//                 }
//             }
//         });
//     }

// });
//boton-articuloAE
$('#tblArticulosExistentesNueva').on('click', 'span', function (e) {
    e.preventDefault();
    //console.log("ssssssssssssssssssss")
    if ($("#sel-proveedor").val() != ""){
        var tabla = $('#tblArticulosExistentesNueva').DataTable();
        var fila = $(this).closest('tr');
        fila = tabla.row(fila).index();
        $('#modal-articulo #input-fila').val(fila);
        $('#modal-articulo').modal('show');
    } else {
        bootbox.dialog({
            message: "No se ha elegido un proveedor, elige uno por favor para continuar.",
            title: "Ordenes de Compra",
            buttons: {
                success: {
                    label: "Si",
                    className: "btn-success"
                }
            }
        });
    }

});

function calculaTotalOrdenCompra(){

    validaSimbolo();
    $('#ordenCompra-articulos').text('0');
    $('#ordenCompra-subtotal').text('0.00');
    $('#ordenCompra-descuento').text('0.00');
    $('#ordenCompra-iva').text('0.00');
    $('#ordenCompra-total').text('0.00');

    var tabla = $('#tblArticulosExistentesNueva').DataTable();
    var table = $('#tblArticulosMiscelaneosNueva').DataTable();
    var dataAE = tabla.rows().data();
    var dataAM = table.rows().data();
    var articulos = 0;
    var subtotal = "";
    var descuento = 0;
    var iva = 0;
    var total = "";
    var Ttotal = 0;
    var TSubtotal = 0;
    var Tiva = 0;
    var Tdescuento = 0;

    var lengthAE = $('#tblArticulosExistentesNueva #input-articulo-codigoAE').closest('tr');
    var lengthAM = $('#tblArticulosMiscelaneosNueva #input-nombreART-miselaneos').closest('tr');
    var count = lengthAE.length + lengthAM.length;
    if (count > 0){

        for (var i = 0; i < count; i++) {
            subtotal = Number($($("#tblArticulosExistentesNueva tbody tr")[i]).find("td:eq(8)").text()) + Number($($("#tblArticulosMiscelaneosNueva tbody tr")[i]).find("td:eq(5)").text());
            descuento = Number($($("#tblArticulosExistentesNueva tbody tr")[i]).find("td:eq(10)").text()) + Number($($("#tblArticulosMiscelaneosNueva tbody tr")[i]).find("td:eq(7)").text());
            iva = Number($($("#tblArticulosExistentesNueva tbody tr")[i]).find("td:eq(12)").text()) + Number($($("#tblArticulosMiscelaneosNueva tbody tr")[i]).find("td:eq(9)").text());
            total = Number($($("#tblArticulosExistentesNueva tbody tr")[i]).find("td:eq(13)").text()) + Number($($("#tblArticulosMiscelaneosNueva tbody tr")[i]).find("td:eq(10)").text());
            
            //subtotal = Number($($("#tblArticulosExistentesNueva tbody tr")[i]).find("td:eq(8)").text());
            //descuento = Number($($("#tblArticulosExistentesNueva tbody tr")[i]).find("td:eq(10)").text());
            //iva = Number($($("#tblArticulosExistentesNueva tbody tr")[i]).find("td:eq(12)").text());
            
            // iva = Number($($("#tblArticulosExistentesNueva tbody tr")[i]).find("td:eq(11)").text());
            // iva = tbl.row(row).nodes(row, COL_ID_IVA).to$().find('select#cboIVAAE').val();
            //total = Number($($("#tblArticulosExistentesNueva tbody tr")[i]).find("td:eq(13)").text());
            
            console.log('calculaTotalOrdenCompra ............')
            console.log('subtotal ' + subtotal)
            console.log('descuento ' + descuento)
            console.log('iva ' + iva)
            console.log('total '+ total)
            Ttotal = Ttotal + total;
            TSubtotal  = TSubtotal  + subtotal;
            Tiva = Tiva + iva;
            Tdescuento = Tdescuento + descuento;

            $('#ordenCompra-subtotal').text(TSubtotal.toLocaleString('es-MX'));
            $('#ordenCompra-descuento').text(Tdescuento.toLocaleString('es-MX'));
            $('#ordenCompra-iva').text(Tiva.toLocaleString('es-MX'));
            $('#ordenCompra-total').text(SIMBOLO_MONEDA + ' ' + Ttotal.toLocaleString('es-MX'));
        }

        for (var i = 0; i < dataAM.length; i++){
            if (dataAM[i]["NOMBRE_ARTICULO"] == ""){
            }
            else {articulos++;}
        }
        for (var i = 0; i < dataAE.length; i++){
            if (dataAE[i]["CODIGO_ARTICULO"] == ""){
            }
            else{articulos++;}
        }
        $('#ordenCompra-articulos').text(articulos);
    }
}
function calculaNuevaMoneda(moneda_anterior, moneda, tipo_cambio_anterior){
    if (moneda_anterior != moneda) {
        var tabla = $('#tblArticulosExistentesNueva').DataTable();
        var datos = '';
        var precio = 'input-precioAE';
        var cantidad = 'input-cantidadAE';
        var descuento = 'input-descuentoAE';
        var Precio_anterior = 0;
        //var Precio = 0;
        var lengthAE = $('#tblArticulosExistentesNueva #boton-articuloAE').closest('tr');
        
        if (BanderaOC == 1) {
        ttabla = 'tblArticulosMiscelaneosNueva';

        tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
        precio = 'input-precioAM';
        cantidad = 'input-cantidadAM';
        descuento = 'input-descuentoAM';
        lengthAE = $('#tblArticulosMiscelaneosNueva #boton-articuloAM').closest('tr');
        } 
        
        var count = lengthAE.length; //+ lengthAM.length;
        if (count > 0){

            for (var i = 0; i < count; i++) {
                datos = tabla.row( i ).data();
                Precio_anterior = datos['PRECIO'];
                console.log('calculaNuevaMoneda....... ')
                console.log('precio_anterior: '+ Precio_anterior)
                console.log('moneda: '+ moneda)
                console.log('tipo_cambio_anterior: '+ tipo_cambio_anterior)
                miprecio = getNuevoPrecio(Precio_anterior, moneda_anterior, moneda, tipo_cambio_anterior);
                  if (BanderaOC == 1) {
                    tabla.row(i).nodes(i, 4).to$().find('input#' + precio).val(miprecio);
                    
                } else {                    
                    tabla.row(i).nodes(i, COL_PRECIO).to$().find('input#' + precio).val(miprecio);
                }
                //tabla.row(i).nodes(i, COL_PRECIO).to$().find('input#' + precio).val(miprecio);
                
                datos['PRECIO'] = miprecio;
                console.log('precio_nuevo: '+ miprecio)
                RealizaCalculos(i, tabla, precio, cantidad, descuento);
            }
        }
        calculaTotalOrdenCompra();
    }
}
function calculaNuevaTipoCambio(moneda, tipo_cambio){
    
    var datos = '';
    var ttabla = 'tblArticulosExistentesNueva';
        
    var tabla = $('#tblArticulosExistentesNueva').DataTable();
    var precio = 'input-precioAE';
    var cantidad = 'input-cantidadAE';
    var descuento = 'input-descuentoAE';
    var Precio_anterior = 0;
    var lengthAE = $('#tblArticulosExistentesNueva #boton-articuloAE').closest('tr');
    
    if (BanderaOC == 1) {
        ttabla = 'tblArticulosMiscelaneosNueva';

        tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
        precio = 'input-precioAM';
        cantidad = 'input-cantidadAM';
        descuento = 'input-descuentoAM';
        Precio_anterior = 0;
        lengthAE = $('#tblArticulosMiscelaneosNueva #boton-articuloAM').closest('tr');
    } 
    var count = lengthAE.length; //+ lengthAM.length;

        if (count > 0){

            for (var i = 0; i < count; i++) {
                datos = tabla.row( i ).data();
                Precio_anterior = datos['PRECIO'];
                console.log('calculaNuevoTipoCambio....... ')
                console.log('precio_anterior: '+ Precio_anterior)
                miprecio = getNuevoPrecioTC(Precio_anterior, tipo_cambio);
                if (BanderaOC == 1) {
                    tabla.row(i).nodes(i, 4).to$().find('input#' + precio).val(miprecio);
                    
                } else {                    
                    tabla.row(i).nodes(i, COL_PRECIO).to$().find('input#' + precio).val(miprecio);
                }
                datos['PRECIO'] = miprecio;
                console.log('precio_nuevo: '+ miprecio)
                RealizaCalculos(i, tabla, precio, cantidad, descuento);
            }
        }
        $("#input_tc_anterior").val($("#input_tc").val());
        calculaTotalOrdenCompra();
    
}
function getNuevoPrecioTC(Precio_anterior, tipo_cambio_anterior){
    var precio_nuevo = 0;
    console.log('nuevoTC: '+$('#input_tc').val())
    precio_nuevo = (Precio_anterior * tipo_cambio_anterior) / $('#input_tc').val();
    return precio_nuevo;
}
function getNuevoPrecio(Precio_anterior, moneda_anterior, moneda, tipo_cambio_anterior){
    var precio_nuevo = 0;
    console.log('nuevoTC: '+$('#input_tc').val())
    if (moneda_anterior == 'MXP') {
       precio_nuevo = Precio_anterior / $('#input_tc').val();
    } else {
        if (moneda == 'MXP') {
            precio_nuevo = Precio_anterior * tipo_cambio_anterior;
        } else {
            precio_nuevo = (Precio_anterior * tipo_cambio_anterior) / $('#input_tc').val();
        }
       
    }
   
    return precio_nuevo;
}
function validaSimbolo(){

    SIMBOLO_MONEDA = $('#cboMoneda').val() +' $';
    // if($('#cboMoneda').val() == '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1'){//PESOS

    //     SIMBOLO_MONEDA = '$';

    // }
    // else if($('#cboMoneda').val() == '1EA50C6D-AD92-4DE6-A562-F155D0D516D3'){//DOLAR

    //     SIMBOLO_MONEDA = 'USD';

    // }
    // else if($('#cboMoneda').val() == '63D4F280-EE48-44A3-84F3-91ADF075BEBC'){//EURO

    //     SIMBOLO_MONEDA = '€';

    // }

}

function RealizaCalculos(fila, tabla, input_precio, input_cantidad, input_descuento){
    console.log('Realiza Calculos.........')
    var tbl = tabla;
    var row = fila;
    var dataT = tabla.row(fila).data();
    var precio  = tbl.row(row).nodes(row, COL_PRECIO).to$().find('input#' + input_precio).val();
    var cantidad = tbl.row(row).nodes(row,COL_CANTIDAD).to$().find('input#' + input_cantidad).val();
    var subtotal = precio * cantidad;
        console.log('subtotal ' + subtotal)
        console.log('precio ' + precio)
        console.log('cantidad ' + cantidad)
        var descuento = subtotal * ((tbl.row(row).nodes(row,COL_DESCUENTO).to$().find('input#' + input_descuento).val()) / 100);
        console.log('descue '+ descuento)
    //console.log('dataT ' + dataT['IVA']);
    if(dataT['IVA'] == '')dataT['IVA'] = "0";
    var selected = dataT['IVA'];
    var iva = (subtotal - descuento) * (selected * .01);
    console.log('iva ' +iva);
    var total = subtotal-descuento + iva;

    tbl.row(row).nodes(row, COL_SUBTOTAL).to$().find("td:eq('" + COL_SUBTOTAL + "')").text(subtotal.toFixed(CANTIDAD_DECIMALES));
    tbl.row(row).nodes(row, COL_MONTO_DESCUENTO).to$().find("td:eq('" + COL_MONTO_DESCUENTO + "')").text(descuento.toFixed(CANTIDAD_DECIMALES));
    tbl.row(row).nodes(row, COL_MONTO_IVA).to$().find("td:eq('" + COL_MONTO_IVA + "')").text(iva.toFixed(CANTIDAD_DECIMALES));
    tbl.row(row).nodes(row, COL_TOTAL).to$().find("td:eq('" + COL_TOTAL + "')").text(total.toFixed(CANTIDAD_DECIMALES));

    if (BanderaOC == 1){
        // TABLA DE MISCELANEOS
        //var comboUM = document.getElementById("cboUMAM");
        //var selectedUV = comboUM.options[comboUM.selectedIndex].text;

        //var comboTPM = document.getElementById("cboTPM");
        //var selectedTPM = comboTPM.options[comboTPM.selectedIndex].text;

        //dataT["CODIGO_ARTICULO"] = selectedTPM;
        dataT["NOMBRE_ARTICULO"] = tbl.row(row).nodes(row, COL_NOMBRE_ART).to$().find("input#input-nombreART-miselaneos").val();
        //dataT["UNIDAD_MEDIDA_COMPRAS"] = selectedUV;
        dataT["CANTIDAD"] = tbl.row(row).nodes(row,COL_CANTIDAD).to$().find('input#' + input_cantidad).val();
        dataT["PRECIO"] = tbl.row(row).nodes(row, COL_PRECIO).to$().find('input#' + input_precio).val();
        dataT["SUBTOTAL"] = tbl.row(row).nodes(row, COL_SUBTOTAL).to$().find("td:eq('" + COL_SUBTOTAL + "')").text();
        dataT["DESCUENTO"] = tbl.row(row).nodes(row, COL_DESCUENTO).to$().find('input#' + input_descuento).val();
        dataT["MONTO_DESCUENTO"] = tbl.row(row).nodes(row, COL_MONTO_DESCUENTO).to$().find("td:eq('" + COL_MONTO_DESCUENTO + "')").text();
        //dataT["IVA"] = selected;
        dataT["MONTO_IVA"] = tbl.row(row).nodes(row, COL_MONTO_IVA).to$().find("td:eq('" + COL_MONTO_IVA + "')").text();
        dataT["TOTAL"] = tbl.row(row).nodes(row, COL_TOTAL).to$().find("td:eq('" + COL_TOTAL + "')").text();
        //dataT["ID_UMC"] = tbl.row(row).nodes(row, COL_ID_UMC).to$().find("select#cboUMAM").val();
        //dataT["ID_UMI"] = tbl.row(row).nodes(row, COL_ID_UMI).to$().find("select#cboTPM").val();
        //dataT["ID_IVA"] = tbl.row(row).nodes(row, COL_ID_IVA).to$().find('select#cboIVAAM').val();
    }
    else{
        //TABLA DE VENTAS
        dataT["CANTIDAD"] = tbl.row(row).nodes(row,COL_CANTIDAD).to$().find('input#' + input_cantidad).val();
        dataT["PRECIO"] = tbl.row(row).nodes(row, COL_PRECIO).to$().find('input#' + input_precio).val();
        dataT["SUBTOTAL"] = tbl.row(row).nodes(row, COL_SUBTOTAL).to$().find("td:eq('" + COL_SUBTOTAL + "')").text();
        dataT["DESCUENTO"] = tbl.row(row).nodes(row, COL_DESCUENTO).to$().find('input#' + input_descuento).val();
        dataT["MONTO_DESCUENTO"] = tbl.row(row).nodes(row, COL_MONTO_DESCUENTO).to$().find("td:eq('" + COL_MONTO_DESCUENTO + "')").text();
        //dataT["IVA"] = selected;
        //dataT["ID_IVA"] = tbl.row(row).nodes(row, COL_ID_IVA).to$().find('select#cboIVAAE').val();
        dataT["MONTO_IVA"] = tbl.row(row).nodes(row, COL_MONTO_IVA).to$().find("td:eq('" + COL_MONTO_IVA + "')").text();
        dataT["TOTAL"] = tbl.row(row).nodes(row, COL_TOTAL).to$().find("td:eq('" + COL_TOTAL + "')").text();
    }
}

function calculaTipoCambio(){

    var subtotal = parseFloat($('#ordenCompra-subtotal').text().replace(/[^0-9\.]/g, ''));
    var descuento = parseFloat($('#ordenCompra-descuento').text().replace(/[^0-9\.]/g, ''));
    var iva = parseFloat($('#ordenCompra-iva').text().replace(/[^0-9\.]/g, ''));
    var total = parseFloat($('#ordenCompra-total').text().replace(/[^0-9\.]/g, ''));

    $('#ordenCompra-subtotal').text(SIMBOLO_MONEDA + ' ' + subtotal.toFixed(DECIMALES));
    $('#ordenCompra-descuento').text(SIMBOLO_MONEDA + ' ' + descuento.toFixed(DECIMALES));
    $('#ordenCompra-iva').text(SIMBOLO_MONEDA + ' ' + iva.toFixed(DECIMALES));
    $('#ordenCompra-total').text(SIMBOLO_MONEDA + ' ' + total.toFixed(DECIMALES));

    /*$('#lbl-tc-subtotal').text('( ' + SIMBOLO_MONEDA_PREDETERMINADA + ' ' + number_format(parseFloat(parseFloat($('#orden-venta-subtotal').text().replace(/[^0-9\.]/g, '')) * parseFloat(TIPO_CAMBIO)), 2, '.', ', ') + ' )');
    $('#lbl-tc-descuento').text('( ' + SIMBOLO_MONEDA_PREDETERMINADA + ' ' + number_format(parseFloat(parseFloat($('#orden-venta-descuento').text().replace(/[^0-9\.]/g, '')) * parseFloat(TIPO_CAMBIO)), 2, '.', ', ') + ' )');
    $('#lbl-tc-iva').text('( ' + SIMBOLO_MONEDA_PREDETERMINADA + ' ' + number_format(parseFloat(parseFloat($('#orden-venta-iva').text().replace(/[^0-9\.]/g, '')) * parseFloat(TIPO_CAMBIO)), 2, '.', ', ') + ' )');
    $('#lbl-tc-total').text('( ' + SIMBOLO_MONEDA_PREDETERMINADA + ' ' + number_format(parseFloat(parseFloat($('#orden-venta-total').text().replace(/[^0-9\.]/g, '')) * parseFloat(TIPO_CAMBIO)), 2, '.', ', ') + ' )');*/

    $($("#tblArticulosExistentesResumenNueva tfoot tr")[0]).find("td:eq(1)").text(SIMBOLO_MONEDA);
    $($("#tblArticulosExistentesResumenNueva tfoot tr")[0]).find("td:eq(3)").text(SIMBOLO_MONEDA);
    $($("#tblArticulosExistentesResumenNueva tfoot tr")[0]).find("td:eq(5)").text(SIMBOLO_MONEDA);
    $($("#tblArticulosExistentesResumenNueva tfoot tr")[0]).find("td:eq(7)").text(SIMBOLO_MONEDA);

    $($("#tblArticulosMiscelaneosResumenNueva tfoot tr")[0]).find("td:eq(1)").text(SIMBOLO_MONEDA);
    $($("#tblArticulosMiscelaneosResumenNueva tfoot tr")[0]).find("td:eq(3)").text(SIMBOLO_MONEDA);
    $($("#tblArticulosMiscelaneosResumenNueva tfoot tr")[0]).find("td:eq(5)").text(SIMBOLO_MONEDA);
    $($("#tblArticulosMiscelaneosResumenNueva tfoot tr")[0]).find("td:eq(7)").text(SIMBOLO_MONEDA);

}
function PartidaResumenAE() {

}
function Old_PartidaResumenAE() {
    TBLResumenArtExis.clear().draw();
    var tabla = $('#tblArticulosExistentesNueva').DataTable();
    var count = $('#tblArticulosExistentesNueva tbody tr').length;
    var datos_venta = tabla.rows().data().toArray();
    if (datos_venta.length != 0){

        for (var i=0; i < count ;i++){
            TBLResumenArtExis.row.add(
                {
                    "PARTIDA": i+1
                    , "CODIGO_ARTICULO": datos_venta[i]["CODIGO_ARTICULO"]
                    , "NOMBRE_ARTICULO": datos_venta[i]["NOMBRE_ARTICULO"]
                    , "UNIDAD_MEDIDA_INV": datos_venta[i]["UNIDAD_MEDIDA_INV"]
                    , "FACTOR_CONVERSION": datos_venta[i]["FACTOR_CONVERSION"]
                    , "UNIDAD_MEDIDA_COMPRAS": datos_venta[i]["UNIDAD_MEDIDA_COMPRAS"]
                    , "CANTIDAD": parseFloat(datos_venta[i]["CANTIDAD"]).toFixed(DECIMALES)
                    , "PRECIO": parseFloat(datos_venta[i]["PRECIO"].replace(/[^0-9\.]/g, '')).toFixed(DECIMALES)
                    , "SUBTOTAL": parseFloat(datos_venta[i]["SUBTOTAL"].replace(/[^0-9\.]/g, '')).toFixed(DECIMALES)
                    , "DESCUENTO": datos_venta[i]["DESCUENTO"]
                    , "MONTO_DESCUENTO": parseFloat(datos_venta[i]["MONTO_DESCUENTO"].replace(/[^0-9\.]/g, '')).toFixed(DECIMALES)
                    , "IVA": datos_venta[i]["IVA"]
                    , "MONTO_IVA": parseFloat(datos_venta[i]["MONTO_IVA"].replace(/[^0-9\.]/g, '')).toFixed(DECIMALES)
                    , "TOTAL": parseFloat(datos_venta[i]["TOTAL"].replace(/[^0-9\.]/g, '')).toFixed(DECIMALES)
                }
            ).draw();
        }
    }
    else{
        TBLResumenArtExis.clear().draw();
    }
}
function actualizaLineaPartidaAE(){
    $('#tblArticulosExistentesNueva tbody tr').each(function (index, tr) {
        $(tr).find('td:eq(0)').text(index + 1);
    });
}

function cargaModal(cantidad_a_devolver, referenciaId) {
    $('#input-cantidad-devolver').val(cantidad_a_devolver);

    if(JSON_PARTIDA.hasOwnProperty(referenciaId)) {
        var referencia = JSON_PARTIDA[referenciaId];
        if(referencia.hasOwnProperty('Detalles')) {
            var detalle = referencia['Detalles'];
            for(var k in detalle) {
                var tabla = $('#tabla-detalles').DataTable();
                detalle[k]['VEN_Cantidad'] = cantidad_a_devolver;
                tabla.row.add( [detalle[k]['VEN_Cantidad'], detalle[k]['VEN_FechaRequerida'], detalle[k]['VEN_FechaPromesa'], '' ,detalle[k]['VEN_OVR_OVD_DetalleId']]).draw( false );

            }
        }
    }
    else{
    }
}
//??
$('#boton-agregar').on('click', function (e) {
    e.preventDefault();

    var tabla = $('#tabla-detalles').DataTable();
    tabla.row.add( [0, '', '', ''] ).draw( false );
});

$('#modal-detalles #boton-aceptar').off().on('click', function(e) {
    e.preventDefault();

    if(validaModal()){

        procesaDatos();
        limpiaComponentesDetalles();
        $("#modal-detalles").modal("hide");

    }
});
//??
function validaModal() {
    var tabla = $('#tabla-detalles').DataTable();
    var datos = tabla.rows().data().toArray();
    var total = 0;
    var cantidad_por_devolver = $('#input-cantidad-devolver').val();
    var banderaRequerida = true;
    var banderaPromesa = true;

    for(var i = 0; i < datos.length; i++) {
        if(datos[i][COL_DETALLE_FECHA_PROMESA] == ''){
            banderaRequerida = false;
        }
        if (datos[i][COL_FECHA_ENTREGA_COMPRA] == ''){
            banderaPromesa = false;
        }
        total = total + parseFloat(datos[i][COL_DETALLE_CANTIDAD]);
    }

    if(datos.length == 0){
        bootbox.alert({
            size:"large",
            title: "<h4><i class= 'fa fa-info-circle'></i> Alerta></h4>",
            message:"<div class='alert alert-danger m-b-0'> Mensaje : Debe existir mínimo una fila."
        });

        return false;
    }

    if(total != cantidad_por_devolver) {
        bootbox.alert({
            size:"large",
            title: "<h4><i class= 'fa fa-info-circle'></i> Alerta></h4>",
            message:"<div class='alert alert-danger m-b-0'> Mensaje : La sumatoria de las cantidades debe ser igual a la cantidad a devolver."
        });

        return false;
    }

    if(banderaRequerida == false) {
        bootbox.alert({
            size:"large",
            title: "<h4><i class= 'fa fa-info-circle'></i> Alerta></h4>",
            message:"<div class='alert alert-danger m-b-0'> Mensaje : La Fecha Requerida es obligatorio."
        });
        return false;
    }

    if(banderaPromesa == false) {
        bootbox.alert({
            size:"large",
            title: "<h4><i class= 'fa fa-info-circle'></i> Alerta></h4>",
            message:"<div class='alert alert-danger m-b-0'> Mensaje : La Fecha Prometida es obligatorio."
        });
        return false;
    }

    return true;
}
//??
function procesaDatos(){
    JSON_DETALLES = {};
    var tabla = $('#tabla-detalles').DataTable();
    var datos = tabla.rows().data().toArray();

    for (var i = 0; i < datos.length; i++) {
        var detalle = new Object();
        detalle.VEN_Cantidad = datos[i][0];
        detalle.VEN_FechaRequerida = datos[i][1];
        detalle.VEN_FechaPromesa = datos[i][2];
        if  (datos[i][4] != undefined){
            detalle.VEN_OVR_OVD_DetalleId = datos[i][4];
        }else{
            detalle.VEN_OVR_OVD_DetalleId = null;
        }

        JSON_DETALLES['detalle' + (i)] = detalle;
    }

    JSON_PARTIDA[DATOS_TEMP['ID_AUX']] = {};
    JSON_PARTIDA[DATOS_TEMP['ID_AUX']].VEN_ART_ArticuloId = DATOS_TEMP['ID_ARTICULO'];
    JSON_PARTIDA[DATOS_TEMP['ID_AUX']].VEN_ART_Nombre = DATOS_TEMP['NOMBRE_ARTICULO'];
    JSON_PARTIDA[DATOS_TEMP['ID_AUX']].Detalles = JSON_DETALLES;
}
//??
function limpiaComponentesDetalles(){
    $('#tabla-detalles').DataTable().clear().draw();
    DATOS_TEMP = [];
}
//??
$('#tabla-detalles').on('change', 'input#input-cantidad-detalle', function(e) {
    e.preventDefault();

    if($(this).val() == '' || $(this).val() < 0){
        $(this).val('0.00');
    }

    $(this).val(parseFloat(this.value).toFixed(CANTIDAD_DECIMALES));

    var tabla = $('#tabla-detalles').DataTable();
    var fila = $(this).closest('tr');
    var datos = tabla.row(fila).data();
    datos[COL_DETALLE_CANTIDAD] = parseFloat(this.value).toFixed(CANTIDAD_DECIMALES);
});
//??
$('#tabla-detalles').on('change', 'input#input-fecha-req', function(e) {
    e.preventDefault();

    var tabla = $('#tabla-detalles').DataTable();
    var fila = $(this).closest('tr');
    var datos = tabla.row(fila).data();
    datos[COL_DETALLE_FECHAREQ] = (this.value);
});
//??
$('#tabla-detalles').on('change', 'input#input-fecha-promesa', function(e)  {
    e.preventDefault();

    var tabla = $('#tabla-detalles').DataTable();
    var fila = $(this).closest('tr');
    var datos = tabla.row(fila).data();
    datos[COL_DETALLE_FECHA_PROMESA] = (this.value);
});

$(document).on('keyup', function (e) {

    e.preventDefault();

    if(e.shiftKey && e.keyCode == 13){
        insertarFila();
    }

});

function actualizaLineaPartidaAM(){
    $('#tblArticulosMiscelaneosNueva tbody tr').each(function (index, tr) {
        $(tr).find('td:eq(0)').text(index + 1);
    });
}
function PartidaResumenAM() {
    TBLResumenArtMisc.clear().draw();
    var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
    var count = $('#tblArticulosMiscelaneosNueva tbody tr').length;
    var datos_miselaneos = tabla.rows().data().toArray();

    if (datos_miselaneos.length != 0){

        for (var i=0; i < count ;i++){

            TBLResumenArtMisc.row.add(
                {
                    "PARTIDA": i+1
                    , "CODIGO_ARTICULO": datos_miselaneos[i]["CODIGO_ARTICULO"]
                    , "NOMBRE_ARTICULO": datos_miselaneos[i]["NOMBRE_ARTICULO"]
                    , "UNIDAD_MEDIDA_INV": datos_miselaneos[i]["UNIDAD_MEDIDA_INV"]
                    , "FACTOR_CONVERSION": datos_miselaneos[i]["FACTOR_CONVERSION"]
                    , "UNIDAD_MEDIDA_COMPRAS": datos_miselaneos[i]["UNIDAD_MEDIDA_COMPRAS"]
                    , "CANTIDAD": parseFloat(datos_miselaneos[i]["CANTIDAD"]).toFixed(DECIMALES)
                    , "PRECIO": parseFloat(datos_miselaneos[i]["PRECIO"].replace(/[^0-9\.]/g, '')).toFixed(DECIMALES)
                    , "SUBTOTAL": parseFloat(datos_miselaneos[i]["SUBTOTAL"].replace(/[^0-9\.]/g, '')).toFixed(DECIMALES)
                    , "DESCUENTO": datos_miselaneos[i]["DESCUENTO"]
                    , "MONTO_DESCUENTO": parseFloat(datos_miselaneos[i]["MONTO_DESCUENTO"].replace(/[^0-9\.]/g, '')).toFixed(DECIMALES)
                    , "IVA": datos_miselaneos[i]["IVA"]
                    , "MONTO_IVA": parseFloat(datos_miselaneos[i]["MONTO_IVA"].replace(/[^0-9\.]/g, '')).toFixed(DECIMALES)
                    , "TOTAL": parseFloat(datos_miselaneos[i]["TOTAL"].replace(/[^0-9\.]/g, '')).toFixed(DECIMALES)
                }
            ).draw();
        }
    }
    else{
        TBLResumenArtMisc.clear().draw();
    }
}



function validarCampos(){

    var moneda = $("#cboMoneda").val();
    var tipoCambio = $("#input_tc").val();
    //var tipoOC = $("#cboTipoOC").val();
    //var almacen = $("#cboAlmacen").val();
    var tablaArtExis = document.getElementById("tblArticulosExistentesNueva");
    if (BanderaOC == 1) {
        tablaArtExis = document.getElementById("tblArticulosMiscelaneosNueva");
        
    } 
    
    var cuentaTablaArtExis = tablaArtExis.rows.length;
    //var tablaArtMisc = document.getElementById("tblArticulosMiscelaneosNueva");
    //var cuentaTablaArtMisc = tablaArtMisc.rows.length;
    bandera = 0;

    if(moneda == ''){

        bandera = 1;
         bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'>Elegir tipo de Moneda</div>",
            buttons: {
            success: {
            label: "Ok",
            className: "btn-success m-r-5 m-b-5"
            }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

    }
    else if(moneda != 'MXP' && tipoCambio == ""){//PESOS

        //if(tipoCambio == ""){

        bandera = 1;
         bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'>Indica el Tipo de Cambio</div>",
            buttons: {
            success: {
            label: "Ok",
            className: "btn-success m-r-5 m-b-5"
            }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

        //}

    }
    // else if(tipoOC == ""){

    //     bandera = 1;
    //      bootbox.dialog({
    //         title: "Mensaje",
    //         message: "<div class='alert alert-danger m-b-0'>Elegir tipo de OC.</div>",
    //         buttons: {
    //         success: {
    //         label: "Ok",
    //         className: "btn-success m-r-5 m-b-5"
    //         }
    //         }
    //     }).find('.modal-content').css({'font-size': '14px'} );

    // }
    else if(cuentaTablaArtExis < 2){
    //else if(cuentaTablaArtExis < 2 && cuentaTablaArtMisc < 2){

        bandera = 1;
         bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'>Agregar Minimo un Artículo.</div>",
            buttons: {
            success: {
            label: "Ok",
            className: "btn-success m-r-5 m-b-5"
            }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

    }
    else if($('#ordenCompra-articulos').text() == '0'){
    //else if(cuentaTablaArtExis < 2 && cuentaTablaArtMisc < 2){

        bandera = 1;
         bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'>El artículo es inválido.</div>",
            buttons: {
            success: {
            label: "Ok",
            className: "btn-success m-r-5 m-b-5"
            }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

    }
    else if( parseFloat($('#ordenCompra-subtotal').text()) == 0){
    //else if(cuentaTablaArtExis < 2 && cuentaTablaArtMisc < 2){

        bandera = 1;
         bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'>El subtotal no puede ser cero</div>",
            buttons: {
            success: {
            label: "Ok",
            className: "btn-success m-r-5 m-b-5"
            }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

    }



}

function registraOC(){

    var datosTablaArtExis;
    datosTablaArtExis = getTblArtExis();
    datosTablaArtExis = JSON.stringify(datosTablaArtExis);

     var datosTablaArtMisc;
     datosTablaArtMisc = getTblArtMisc();
     datosTablaArtMisc = JSON.stringify(datosTablaArtMisc);
    
    paridad = $("#input_tc").val();
    console.log('registrando OC')
   
    $.ajax({
         headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        url: routeapp + "registraOC",
        data: {
            "status": OC_nueva,
            "oc_docEntry": $("#docEntryOC").text(),
            "oc_docNum": $('#ordenesCompraOC #codigoOC').text(),
            "oc_proveedor": $("#sel-proveedor option:selected").val(),
            //"OC_PDOC_DireccionOCId": $("#cboSucursal").val(),
            //"OC_CMM_TipoOCId": $("#cboTipoOC").val(),
            "oc_tipo": $("#sel-tipo-oc").val(),
            "oc_moneda": $("#cboMoneda").val(),
            "oc_tipo_cambio": paridad,
            //"OC_MONP_ParidadId": $("#cboTipoCambio").val(),
            //"OC_ALM_AlmacenId": $("#cboAlmacen").val(),
            "oc_fecha_entrega": $('#input-fecha-entrega').val(),
            //"OC_AGE_PDOC_DireccionOCId": $("#cboSucursalAgente").val(),
            //"OC_CMM_LibreABordoId": $("#modal-datos #cboLibreABordo").val(),
            //"OC_CMM_MetodoEmbarqueId": $("#modal-datos #cboMetodoEmbarque").val(),
            //"OC_PorcentajeDescuento": $("#modal-datos #input-descuento").val(),
            //"OC_CMIVA_IVAId": $("#modal-datos #cboIVA").val(),
            //"OCD_CMIVA_PorcentajeIVA": $("#cboIVA option:selected").text(),
            //"OC_EV_ProyectoId": $("#modal-datos #cboProyectos").val(),
            //"OC_OT_OrdenTrabajoId": $("#modal-datos #idOrdenTrabajo").val(),
            "oc_comentarios": $("#input-comentarios").val(),
            //"ArrayFehasRequeridas": TblPartidasDetalles,
            //"ArrayFehasRequeridasEditar": TblPartidasDetalles,
            "TablaArticulosExistentes": datosTablaArtExis,
            "TablaArticulosMiscelaneos": datosTablaArtMisc,
            //"TablaPedimentos": datosTablaPedimentos,
            //"editaProveedor": editaProveedor
        },
        type: "POST",
        async:true,
        success: function (datos, x, z) {
            console.log(datos)
            //$.unblockUI();
            if(datos["Status"] == "Error"){
                bootbox.dialog({
                    title: "Mensaje",
                    message: "<div class='alert alert-danger m-b-0'>"+datos["Mensaje"]+"</div>",
                    buttons: {
                    success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                    }
                    }
                }).find('.modal-content').css({'font-size': '14px'} );
                
            }
            else{

                //$('#ordenesCompraOC').hide();
                //$('#btnBuscadorOC').show();
                //reloadBuscadorOC();
                
                swal("", "OC guardada", "success", {
                    buttons: false,
                    timer: 2000,
                });
                
            }
            InicializaComponentesOC();
            $("#tblArticulosExistentesNueva").DataTable().clear().draw();
            $("#tblArticulosMiscelaneosNueva").DataTable().clear().draw();
            console.log("id: " + datos["id"])
            console.log("id2: " + datos.id)
            mostrarOC(datos["id"]);
            // $("#tblArticulosExistentesResumenNueva").DataTable().clear().draw();
            // $("#tblArticulosMiscelaneosResumenNueva").DataTable().clear().draw();
           // calculaTotalOrdenCompra();
            ////calculaTipoCambio();
        },
        error: function (x, e) {
            var errorMessage = 'Error \n' + x.responseText;
           // mostrarOC(datos["id"]);
            //$.unblockUI();
             bootbox.dialog({
                title: "Mensaje",
                message: "<div class='alert alert-danger m-b-0'>"+errorMessage+"</div>",
                buttons: {
                success: {
                label: "Ok",
                className: "btn-success m-r-5 m-b-5"
                }
                }
            }).find('.modal-content').css({'font-size': '14px'} );
            
        }
    });
   
}
function MuestraComponentesOC() {

    if (OC_nueva == 1) { //nueva OC
        $('.nuevaOC').show()
        $('#edit_tipo_oc').hide()
        $('#edit_proveedor').hide()
        $('#titulo').text('Orden de Compra Nueva')

    } else { //Editar OC
        $('.nuevaOC').hide()
        $('#edit_tipo_oc').show()
        $('#edit_proveedor').show()
        $('#titulo').text('Editar Orden de Compra')

    }
}
function CargaComponentesOC(resumen) {

    $("#edit_tipo_oc").val(resumen.OC_TIPO);
    $("#edit_proveedor").val(resumen.PRO_NOMBRE);
    $("#sel-proveedor").val(resumen.CardCode);
    $("#sel-proveedor").selectpicker('refresh');
    let opt = (resumen.OC_TIPO == 'ARTICULOS') ? 0 : 1;
    $("#sel-tipo-oc").val(opt);
    $("#sel-tipo-oc").selectpicker('refresh');

    carga_info_proveedor(resumen.CardCode);

    $("#cboMoneda").val(resumen.OC_MONEDA);
    $("#cboMoneda").selectpicker('refresh');
    console.log(resumen.OC_MONEDA)
    console.log($("#cboMoneda").val())
    //verificar cambio de moneda si es necesario

    $("#ordenesCompraOC #input_tc").val(resumen.OC_RATE);

    if (resumen.OC_MONEDA == 'MXP') {
        $("#div-tipo-cambio").hide();
        $("#input_tc_anterior").val(1);
        $('#input_tc').val(1)
    }

    $('#ordenesCompraOC #codigoOC').text(resumen.OC_NUM);
    $('#ordenesCompraOC #docEntryOC').text(resumen.OC_DOCENTRY);
    $('#ordenesCompraOC #estadoOC').text(resumen.OC_ESTATUS);

    $('#input-fecha').datepicker({ //fechaDOC
        language: 'es',
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true
    }).datepicker("setDate", resumen.OC_DATE);
    
    $('#input-fecha-entrega').val(resumen.OC_FECHA_ENTREGA);
    $("#input-comentarios").val(resumen.OC_COMENTARIO);

};

function agregaPartidasOCDetalle(datos, tipo) {

    $("#tblArticulosExistentesNueva").DataTable().clear().draw();
    $("#tblArticulosMiscelaneosNueva").DataTable().clear().draw();
    var contador1 = 0;
    var contador2 = 0;
    BanderaOC = 1;
    if (tipo == 'ARTICULOS') {
        BanderaOC = 0;
    }
    set_columns_index(BanderaOC);
    for (var x = 0; x < datos.length; x++) {

        if (tipo == 'ARTICULOS') {

            agregaArtExis(datos[x], contador1);
            actualizaLineaPartidaAE();

            contador1++;

        }
        else {

            agregaArtMisc(datos[x], contador2);
            actualizaLineaPartidaAM();
            contador2++;

        }

    }

    ////calculaTipoCambio();

}

function agregaArtExis(datos, pos) {

    var tbl = $('#tblArticulosExistentesNueva').DataTable();

    TBL_ART_EXIST.row.add(
        {
            "PARTIDA": datos['LIN_NUMERO']
            , "CODIGO_ARTICULO": datos['LIN_CODIGO']
            , "NOMBRE_ARTICULO": datos['LIN_DESCRIPCION']

            , "UNIDAD_MEDIDA_INV": datos['LIN_UM']
            , "FACTOR_CONVERSION": datos['LIN_FACTOR']
            , "UNIDAD_MEDIDA_COMPRAS": datos['BuyUnitMsr']

            , "CANTIDAD": datos['LIN_CANTIDAD']
            , "PRECIO": datos['LIN_PRECIO']
            , "SUBTOTAL": parseFloat(datos['LIN_TOTAL']).toFixed(DECIMALES)

            , "DESCUENTO": datos['LIN_PORCENTAJEDESCUENTO']
            , "MONTO_DESCUENTO": parseFloat(datos['LIN_DISC']).toFixed(DECIMALES)
            , "IVA": datos['LIN_PORCENTAJEIVA']

            , "MONTO_IVA": parseFloat(datos['LIN_IVA']).toFixed(DECIMALES)
            , "TOTAL": parseFloat(datos['LIN_GTOTAL']).toFixed(DECIMALES)
            , "FECHA_ENTREGA": datos['ShipDate']

            , "CANT_PENDIENTE": datos['CANTIDAD_PENDIENTE']
            , "BTN_ELIMINAR": null

            , "ID_IVA": datos['TaxCode']
            , "ID_PARTIDA": datos['LIN_NUMERO']
            , "ESTATUS_PARTIDA": datos['LineStatus']
        }
    ).draw();
    tbl.row(pos).nodes(pos, COL_DESCUENTO).to$().find('input#input-descuentoAE').val(parseFloat(datos['LIN_PORCENTAJEDESCUENTO']).toFixed(DECIMALES));
    tbl.row(pos).nodes(pos, COL_IVA).to$().find('select#cboIVAAE').val(datos['TaxCode']);

    if (datos['LineStatus'] == 'O') {

        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').prop("checked", false);
        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').removeAttr("disabled");

    }
    else {

        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').prop("checked", true);
        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('checkbox#cerrarPartidaCheck').attr("disabled", "disabled");

    }

    tbl.row(pos).nodes(pos, COL_CODIGO_ART).to$().find('input#input-articulo-codigoAE').attr("disabled", "disabled");
    tbl.row(pos).nodes(pos, COL_CODIGO_ART).to$().find('a#boton-articuloAE').attr("disabled", "disabled");
    tbl.row(pos).nodes(pos, COL_CODIGO_ART).to$().find('a#boton-articuloAE').attr("id", "disabled");
    if (datos['LineStatus'] != 'O' || datos['EDO_CANTIDAD_PENDIENTE'] != 'SIN SURTIR') {//SI ESTA CERRADA

        tbl.row(pos).nodes(pos, COL_CANTIDAD).to$().find('input#input-cantidadAE').attr("disabled", "disabled");
        tbl.row(pos).nodes(pos, COL_PRECIO).to$().find('input#input-precioAE').attr("disabled", "disabled");
        tbl.row(pos).nodes(pos, COL_DESCUENTO).to$().find('input#input-descuentoAE').attr("disabled", "disabled");
        tbl.row(pos).nodes(pos, COL_IVA).to$().find('select#cboIVAAE').attr("disabled", "disabled");
        //tbl.row(pos).nodes(pos, COL_FECHA_ENTREGA_COMPRA).to$().find('input#boton-detalleAE').attr("disabled","disabled");
        // tbl.row(pos).nodes(pos, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("disabled", "disabled");
        //tbl.row(pos).nodes(pos, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("id", "disabled");

        tbl.row(pos).nodes(pos, COL_FECHA_ENTREGA_COMPRA).to$().find('input#input-fecha-entrega-linea').attr("disabled", "disabled");

    }
    if (datos['EDO_CANTIDAD_PENDIENTE'] != 'SIN SURTIR') {
        tbl.row(pos).nodes(pos, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("disabled", "disabled");
        tbl.row(pos).nodes(pos, COL_BTN_ELIMINAR_COMPRA).to$().find('a#boton-eliminarAE').attr("id", "disabled");
    }

}
function carga_info_proveedor(proveedorId) {
    $.ajax({
        type: 'GET',
        async: true,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: routeapp + 'get_detalleProveedor',
        data: {
            id: proveedorId
        },
        beforeSend: function () {

        },
        complete: function () {
            // setTimeout($.unblockUI, 500);
        },
        success: function (data) {
            var datos = data.proveedor

            var codigoCliente = datos['PRO_Codigo'];
            var razonSocial = datos['PRO_Nombre'];
            var Moneda = datos['Moneda'];
            if (OC_nueva == 0) {
                Moneda = $('#cboMoneda').val();
            }  

            var domicilio = (datos['PRO_Domicilio'] === null || datos['PRO_Domicilio'] === undefined) ? '-' : datos['PRO_Domicilio'];
            var Email = (datos['PRO_Email'] === null || datos['PRO_Email'] === undefined) ? '-' : datos['PRO_Email'];
            var rfc = (datos['PRO_RFC'] === null || datos['PRO_RFC'] === undefined) ? '-' : datos['PRO_RFC'];
            var telefono = (datos['PRO_Telefono'] === null || datos['PRO_Telefono'] === undefined) ? '-' : datos['PRO_Telefono'];
            var contacto = (datos['CON_Contacto'] === null || datos['CON_Contacto'] === undefined) ? '-' : datos['CON_Contacto'];
            
            document.getElementById('nombreProveedor').innerText = codigoCliente + ' ' + razonSocial;
            document.getElementById('direccionProveedor').innerHTML = '<i class="fa fa-map-marker" aria-hidden="true"></i> ' + domicilio;
            document.getElementById('emailProveedor').innerHTML = '<i class="fa fa-envelope" aria-hidden="true"></i> ' + Email;            
            document.getElementById('rfcProveedor').innerHTML = '<i class="fa fa-building" aria-hidden="true"></i> ' + rfc;
            document.getElementById('telefonicosProveedor').innerHTML = '<i class="fa fa-phone" aria-hidden="true"></i> ' + telefono;
            document.getElementById('contactoProveedor').innerHTML = '<i class="fa fa-address-book" aria-hidden="true"></i> ' + contacto;
        }
    });

}

function carga_tipo_cambio(moneda) {
    if (moneda == 'MXP') {
        $("#div-tipo-cambio").hide();
        $("#input_tc").val(1);
        $("#input_tc_anterior").val(1);
    } else {
        $.ajax({
            type: 'GET',
            async: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: routeapp + 'get-rates',
            data: {
                mon: moneda
            },
            beforeSend: function () {

            },
            complete: function () {
                // setTimeout($.unblockUI, 500);
            },
            success: function (data) {
                if (data.rates.length > 0) {
                    $("#div-tipo-cambio").show();
                    $("#input_tc").val(data.rates[0].Rate);
                    $("#input_tc_anterior").val(data.rates[0].Rate);

                } else {
                    $("#div-tipo-cambio").hide();
                    $("#input_tc").val(1);
                    $("#input_tc_anterior").val(1);
                }
            }
        });
    }
}
function set_columns_index(BanderaOC) {
    if (BanderaOC == 0) {
        //Tabla Articulos
        COL_PARTIDA = 0;
        COL_CODIGO_ART = 1;
        COL_NOMBRE_ART = 2;

        COL_UNIDAD_MEDIDA_INV = 3;
        COL_FACTOR_CONVERSION = 4;
        COL_UNIDAD_MEDIDA_COMPRAS = 5;

        COL_CANTIDAD = 6;
        COL_PRECIO = 7;
        COL_SUBTOTAL = 8;

        COL_DESCUENTO = 9;
        COL_MONTO_DESCUENTO = 10;
        COL_IVA = 11;

        COL_MONTO_IVA = 12;
        COL_TOTAL = 13;
        COL_FECHA_ENTREGA_COMPRA = 14;

        COL_CANT_PENDIENTE = 15;

        COL_PARTIDA_CERRADA = 16;
        COL_BTN_ELIMINAR_COMPRA = 17;
        COL_ID_IVA = 18;

        COL_ID_PARTIDA = 19;
        COL_ESTATUS_PARTIDA = 20;

    } else {
        //tabla Miscelaneos
        COL_PARTIDA = 0;
        COL_NOMBRE_ART_MISC = 1;
        COL_CTA_MAYOR = 2;

        COL_CANTIDAD = 3;
        COL_PRECIO = 4;
        COL_SUBTOTAL = 5;

        COL_DESCUENTO = 6;
        COL_MONTO_DESCUENTO = 7;
        COL_IVA = 8;

        COL_MONTO_IVA = 9;
        COL_TOTAL = 10;
        COL_FECHA_ENTREGA_COMPRA = 11;

        COL_PARTIDA_CERRADA = 12;
        COL_BTN_ELIMINAR_COMPRA = 13;
        COL_ID_IVA = 14;

        COL_ID_PARTIDA = 15;
        COL_ESTATUS_PARTIDA = 16;
    }
}
function mostrarOC(NumOC) {
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

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        async: false,
        data: {
            NumOC: NumOC
        },
        dataType: "json",
        url: routeapp + "compras/buscaOC",
        success: function (data) {
            setTimeout($.unblockUI, 2000);

            OC_nueva = 0;
            $('#ordenesCompraOC').show();
            $('#btnBuscadorOC').hide();
            MuestraComponentesOC()
            console.log('1 ==> ' + $("#cboMoneda").val())
            CargaComponentesOC(data.resumen);
            console.log('2 ==> ' + $("#cboMoneda").val())
            agregaPartidasOCDetalle(data.detalle, (data.resumen).OC_TIPO);
            console.log('3 ==> ' + $("#cboMoneda").val())
            $("#cboMoneda").val((data.resumen).OC_MONEDA);
            $("#cboMoneda").selectpicker('refresh');
            calculaTotalOrdenCompra();
            console.log('4 ==> ' + $("#cboMoneda").val())
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $.unblockUI();
            var error = JSON.parse(xhr.responseText);
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
function getTblArtExis(){

    var tabla = $('#tblArticulosExistentesNueva').DataTable();
    var fila = $('#tblArticulosExistentesNueva tbody tr').length;
    var datos_Tabla = tabla.rows().data();
    var tblAE = new Array();

    if (datos_Tabla.length != 0){
        for (var i = 0; i < fila; i++) {
            //console.log(datos_Tabla[i])
            //var fecha_entrega = $('#input-fecha-entrega').val();
            tblAE[i]={

                "PARTIDA" : datos_Tabla[i]["PARTIDA"],
                "CODIGO_ARTICULO" : datos_Tabla[i]["CODIGO_ARTICULO"],
                "NOMBRE_ARTICULO" : datos_Tabla[i]["NOMBRE_ARTICULO"],
                "UNIDAD_MEDIDA_INV" : datos_Tabla[i]["UNIDAD_MEDIDA_INV"],
                "FACTOR_CONVERSION" : datos_Tabla[i]["FACTOR_CONVERSION"],
                "UNIDAD_MEDIDA_COMPRAS" : datos_Tabla[i]["UNIDAD_MEDIDA_COMPRAS"],
                "CANTIDAD" : datos_Tabla[i]["CANTIDAD"],
                "PRECIO" : datos_Tabla[i]["PRECIO"],
                "SUBTOTAL" : datos_Tabla[i]["SUBTOTAL"],
                "DESCUENTO" : datos_Tabla[i]["DESCUENTO"],
                "MONTO_DESCUENTO" : datos_Tabla[i]["MONTO_DESCUENTO"],
                "IVA" : datos_Tabla[i]["IVA"],
                "MONTO_IVA" : datos_Tabla[i]["MONTO_IVA"],
                "TOTAL": datos_Tabla[i]["TOTAL"],
                "FECHA_ENTREGA": $('input#input-fecha-entrega-linea', tabla.row(i).node()).val(),  
                "PARTIDA_CERRADA": ($('#cerrarPartidaCheck', tabla.row(i).node()).is(":checked")) ? 'bost_Close' : 'bost_Open',
                "ID_ARTICULO" : datos_Tabla[i]["ID_ARTICULO"],
                "ID_PARTIDA": datos_Tabla[i]["ID_PARTIDA"],
                "ID_AUX": datos_Tabla[i]["ID_AUX"],
                "ID_UMI": datos_Tabla[i]["ID_UMI"],
                "ID_UMC": datos_Tabla[i]["ID_UMC"],
                "ID_IVA": datos_Tabla[i]["ID_IVA"],
                "ESTATUS_PARTIDA": datos_Tabla[i]["ESTATUS_PARTIDA"],
                "CODIGO_OT" : datos_Tabla[i]["CODIGO_OT"],
                "ID_OT" : datos_Tabla[i]["ID_OT"]
            }
        };
        return tblAE;
    }
    else{return tblAE;}

}

function getTblArtMisc(){

    var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
    var fila = $('#tblArticulosMiscelaneosNueva tbody tr').length;
    var datos_Tabla = tabla.rows().data();
    var tblAM = new Array();

    if (datos_Tabla.length != 0){
        for (var i = 0; i < fila; i++) {

            tblAM[i]={

                "PARTIDA" : datos_Tabla[i]["PARTIDA"],
                "CODIGO_ARTICULO" : datos_Tabla[i]["CODIGO_ARTICULO"],
                "NOMBRE_ARTICULO" : datos_Tabla[i]["NOMBRE_ARTICULO"],
                //"UNIDAD_MEDIDA_INV" : datos_Tabla[i]["UNIDAD_MEDIDA_INV"],
                //"FACTOR_CONVERSION" : datos_Tabla[i]["FACTOR_CONVERSION"],
                //"UNIDAD_MEDIDA_COMPRAS" : datos_Tabla[i]["UNIDAD_MEDIDA_COMPRAS"],
                "CTA_MAYOR": datos_Tabla[i]["CTA_MAYOR"],
                "CANTIDAD" : datos_Tabla[i]["CANTIDAD"],
                "PRECIO" : datos_Tabla[i]["PRECIO"],
                "SUBTOTAL" : datos_Tabla[i]["SUBTOTAL"],
                "DESCUENTO" : datos_Tabla[i]["DESCUENTO"],
                "MONTO_DESCUENTO" : datos_Tabla[i]["MONTO_DESCUENTO"],
                "IVA" : datos_Tabla[i]["IVA"],
                "MONTO_IVA" : datos_Tabla[i]["MONTO_IVA"],
                "TOTAL": datos_Tabla[i]["TOTAL"],
                "FECHA_ENTREGA": $('input#input-fecha-entrega-linea', tabla.row(i).node()).val(),
                //"PARTIDA_CERRADA": datos_Tabla[i]["PARTIDA_CERRADA"],
                "PARTIDA_CERRADA": $('#cerrarPartidaCheck', tabla.row(i).node()).is(":checked"),
                //"ID_ARTICULO" : datos_Tabla[i]["ID_ARTICULO"],
                "ID_PARTIDA": datos_Tabla[i]["ID_PARTIDA"],
                "ID_AUX": datos_Tabla[i]["ID_AUX"],
                // "ID_UMI": datos_Tabla[i]["ID_UMI"],
                // "ID_UMC": datos_Tabla[i]["ID_UMC"],
                // "ID_IVA": datos_Tabla[i]["ID_IVA"],
                // "ESTATUS_PARTIDA": datos_Tabla[i]["ESTATUS_PARTIDA"],
                // "CODIGO_OT" : datos_Tabla[i]["CODIGO_OT"],
                "ID_OT" : datos_Tabla[i]["ID_OT"]
            }
        };
        return tblAM;
    }
    else{return tblAM;}

}

function agregaArtExis_old(datos,pos){

    var tbl = $('#tblArticulosExistentesNueva').DataTable();
    if(datos['OCFR_PartidaCerrada'] == null)datos['OCFR_PartidaCerrada'] = 0;
    TBL_ART_EXIST.row.add(
        {
            "PARTIDA": datos['OCD_NumeroLinea']
            , "CODIGO_ARTICULO": datos['ART_CodigoArticulo']
            , "NOMBRE_ARTICULO": datos['OCD_DescripcionArticulo']
            , "UNIDAD_MEDIDA_INV": datos['OCD_CMUM_UMInventario']
            , "FACTOR_CONVERSION": datos['OCD_AFC_FactorConversion']
            , "UNIDAD_MEDIDA_COMPRAS": datos['OCD_CMUM_UMCompras']
            , "CANTIDAD": datos['OCD_CantidadRequerida']
            , "PRECIO": datos['OCD_PrecioUnitario']
            , "SUBTOTAL": parseFloat(datos['OCD_Subtotal']).toFixed(DECIMALES)
            , "DESCUENTO": datos['OCFR_PorcentajeDescuento']
            , "MONTO_DESCUENTO": parseFloat(datos['OCD_Descuento']).toFixed(DECIMALES)
            , "IVA": datos['OCD_CMIVA_PorcentajeIVA']
            , "MONTO_IVA": parseFloat(datos['OCD_Iva']).toFixed(DECIMALES)
            , "TOTAL": parseFloat(datos['OCD_Total']).toFixed(DECIMALES)
            , "PARTIDA_CERRADA": datos['OCFR_PartidaCerrada']
            , "BTN_ELIMINAR": null
            , "ID_ARTICULO": datos['OCD_ART_ArticuloId']
            , "ID_PARTIDA": datos['OCD_PartidaId']
            , "ID_AUX" : datos['OCD_PartidaId']
            , "ID_UMI": datos['OCD_CMUM_UMInventarioId']
            , "ID_UMC": datos['OCD_CMUM_UMComprasId']
            , "ID_IVA": datos['OCD_CMIVA_IVAId']
            , "ESTATUS_PARTIDA": datos['OCFR_CMM_EstadoFechaRequerida']
            , "CODIGO_OT": datos['OT_Codigo']
            , "ID_OT": datos['OCD_OT_OrdenTrabajoId']

        }
    ).draw();
    tbl.row(pos).nodes(pos, COL_DESCUENTO).to$().find('input#input-descuentoAE').val(parseFloat(datos['OCFR_PorcentajeDescuento']).toFixed(DECIMALES));
    tbl.row(pos).nodes(pos, COL_IVA).to$().find('select#cboIVAAE').val(datos['OCD_CMIVA_IVAId']);

    if(datos['OCFR_PartidaCerrada'] == 0){

        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').prop("checked",false);
        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').removeAttr("disabled");

    }
    else{

        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').prop("checked",true);
        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('checkbox#cerrarPartidaCheck').attr("disabled","disabled");

    }

    if(datos['OCFR_CMM_EstadoFechaRequerida'] != 'Abierta'){//ABIERTA

        tbl.row(pos).nodes(pos, COL_CODIGO_ART).to$().find('input#input-articulo-codigoAE').attr("disabled","disabled");
        //tbl.row(pos).nodes(pos, COL_CODIGO_ART).to$().find('input#boton-articuloAE').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_CANTIDAD).to$().find('input#input-cantidadAE').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_PRECIO).to$().find('input#input-precioAE').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_DESCUENTO).to$().find('input#input-descuentoAE').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_IVA).to$().find('select#cboIVAAE').attr("disabled","disabled");
        //tbl.row(pos).nodes(pos, COL_FECHA_ENTREGA_COMPRA).to$().find('input#boton-detalleAE').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_BTN_ELIMINAR_COMPRA).to$().find('button#boton-eliminarAE').attr("disabled","disabled");

    }

}

function agregaArtMisc(datos,pos){

    var tbl = $('#tblArticulosMiscelaneosNueva').DataTable();
    if(datos['OCFR_PartidaCerrada'] == null)datos['OCFR_PartidaCerrada'] = 0;
    TBL_ART_MISC.row.add(
        {
            "PARTIDA": datos['OCD_NumeroLinea']
            , "CODIGO_ARTICULO": datos['CMM_TipoPartidaMisc']
            , "NOMBRE_ARTICULO": datos['OCD_DescripcionArticulo']
            , "UNIDAD_MEDIDA_INV": datos['OCD_CMUM_UMInventario']
            , "FACTOR_CONVERSION": datos['OCD_AFC_FactorConversion']
            , "UNIDAD_MEDIDA_COMPRAS": datos['OCD_CMUM_UMCompras']
            , "CANTIDAD": datos['OCD_CantidadRequerida']
            , "PRECIO": datos['OCD_PrecioUnitario']
            , "SUBTOTAL": parseFloat(datos['OCD_Subtotal']).toFixed(DECIMALES)
            , "DESCUENTO": datos['OCFR_PorcentajeDescuento']
            , "MONTO_DESCUENTO": parseFloat(datos['OCD_Descuento']).toFixed(DECIMALES)
            , "IVA": datos['OCD_CMIVA_PorcentajeIVA']
            , "MONTO_IVA": parseFloat(datos['OCD_Iva']).toFixed(DECIMALES)
            , "TOTAL": parseFloat(datos['OCD_Total']).toFixed(DECIMALES)
            , "PARTIDA_CERRADA": datos['OCFR_PartidaCerrada']
            , "BTN_ELIMINAR": null
            , "ID_ARTICULO": datos['OCD_ART_ArticuloId']
            , "ID_PARTIDA": datos['OCD_PartidaId']
            , "ID_AUX" : datos['OCD_PartidaId']
            , "ID_UMI": datos['OCD_CMUM_UMInventarioId']
            , "ID_UMC": datos['OCD_CMUM_UMComprasId']
            , "ID_IVA": datos['OCD_CMIVA_IVAId']
            , "ESTATUS_PARTIDA": datos['OCFR_CMM_EstadoFechaRequerida']
            , "CODIGO_OT": datos['OT_Codigo']
            , "ID_OT": datos['OCD_OT_OrdenTrabajoId']

        }
    ).draw();
    tbl.row(pos).nodes(pos, COL_CODIGO_ART).to$().find('select#cboTPM').val(datos['OCD_CMM_TipoPartidaMiscelaneaId']);
    tbl.row(pos).nodes(pos, COL_NOMBRE_ART_MISC).to$().find('input#input-nombreART-miselaneos').val(datos['OCD_DescripcionArticulo']);
    tbl.row(pos).nodes(pos, COL_UNIDAD_MEDIDA_COMPRAS).to$().find('select#cboUMAM').val(datos['OCD_CMUM_UMComprasId']);
    tbl.row(pos).nodes(pos, COL_DESCUENTO).to$().find('input#input-descuentoAM').val(parseFloat(datos['OCFR_PorcentajeDescuento']).toFixed(DECIMALES));
    tbl.row(pos).nodes(pos, COL_IVA).to$().find('select#cboIVAAM').val(datos['OCD_CMIVA_IVAId']);

    if(datos['OCFR_PartidaCerrada'] == 0){

        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').prop("checked",false);
        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').removeAttr("disabled");

    }
    else{

        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('#cerrarPartidaCheck').prop("checked",true);
        tbl.row(pos).nodes(pos, COL_PARTIDA_CERRADA).to$().find('checkbox#cerrarPartidaCheck').attr("disabled","disabled");

    }

    if(datos['OCFR_CMM_EstadoFechaRequerida'] != 'Abierta'){//ABIERTA

        tbl.row(pos).nodes(pos, COL_CODIGO_ART).to$().find('select#cboTPM').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_NOMBRE_ART_MISC).to$().find('input#input-nombreART-miselaneos').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_UNIDAD_MEDIDA_COMPRAS).to$().find('select#cboUMAM').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_CANTIDAD).to$().find('input#input-cantidadAM').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_PRECIO).to$().find('input#input-precioAM').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_DESCUENTO).to$().find('input#input-descuentoAM').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_IVA).to$().find('select#cboIVAAM').attr("disabled","disabled");
        tbl.row(pos).nodes(pos, COL_BTN_ELIMINAR_COMPRA).to$().find('button#boton-eliminarAM').attr("disabled","disabled");

    }

}


function eliminarPartidaArtExis(fila){
    PARTIDA_ART_EXIS_ELIMINADA.push($(document.getElementById('tblArticulosExistentesNueva').getElementsByTagName('tbody')[0]).children()[fila]);
    bootbox.dialog({
        title: "Ordenes de Compra",
        message: "¿Estás seguro de eliminar la partida?, No podrás deshacer el cambio.",
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

                    var tabla = $('#tblArticulosExistentesNueva').DataTable();
                    var datos_venta = tabla.row(fila).data();
                    var detalleId = datos_venta['ID_PARTIDA'];

                    if(detalleId != ''){
                        $.ajax({
                             headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                            type: "POST",
                            async: false,
                            data: {
                                detalleId: detalleId
                            },
                            dataType: "json",
                            url: "compras/eliminaPartidaDetalle",
                            success: function (data) {

                                $.unblockUI();
                                if(data['Status'] == 'Error'){

                                    bootbox.dialog({
                                        title: "Mensaje",
                                        message: "<div class='alert alert-danger m-b-0'>"+datos["Mensaje"]+"</div>",
                                        buttons: {
                                        success: {
                                        label: "Ok",
                                        className: "btn-success m-r-5 m-b-5"
                                        }
                                        }
                                    }).find('.modal-content').css({'font-size': '14px'} );

                                }
                                else{

                                    var tabla = $('#tblArticulosExistentesNueva').DataTable();
                                     tabla.row(fila).remove().draw(false);

                                     PartidaResumenAE();
                                     calculaTotalOrdenCompra();
                                     //calculaTipoCambio();

                                     var tabla = $('#tabla-detalles').DataTable();
                                     var datos = tabla.rows().data();

                                     for (var i = 0; i < datos.length-1; i++){
                                        if (JSON_DETALLES["detalle"+i]["VEN_OVR_OVD_DetalleId"] == detalleId){
                                            delete JSON_DETALLES["detalle"+i];
                                        }
                                     }

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
                    } else {
                        $.unblockUI();
                        PartidaResumenAE();
                        calculaTotalOrdenCompra();
                        //calculaTipoCambio();
                    }
                }
            },
            default: {
                label: "No",
                className: "btn-default m-r-5 m-b-5"
            }
        }
    });
}

function eliminarPartidaArtMisc(fila){
    PARTIDA_ART_MISC_ELIMINADA.push($(document.getElementById('tblArticulosMiscelaneosNueva').getElementsByTagName('tbody')[0]).children()[fila]);
    bootbox.dialog({
        title: "Ordenes de Compra",
        message: "¿Estás seguro de eliminar la partida?, No podrás deshacer el cambio.",
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

                    var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
                    var datos_venta = tabla.row(fila).data();
                    var detalleId = datos_venta['ID_PARTIDA'];

                    if(detalleId != ''){
                        $.ajax({
                            type: "POST",
                            async: false,
                            data: {
                                detalleId: detalleId
                            },
                            dataType: "json",
                            url: "compras/eliminaPartidaDetalle",
                            success: function () {

                                $.unblockUI();
                                if(data['Status'] == 'Error'){

                                    bootbox.dialog({
                                        title: "Mensaje",
                                        message: "<div class='alert alert-danger m-b-0'>"+datos["Mensaje"]+"</div>",
                                        buttons: {
                                        success: {
                                        label: "Ok",
                                        className: "btn-success m-r-5 m-b-5"
                                        }
                                        }
                                    }).find('.modal-content').css({'font-size': '14px'} );

                                }
                                else{

                                    var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
                                    tabla.row(fila).remove().draw(false);

                                    PartidaResumenAE();
                                    calculaTotalOrdenCompra();
                                    //calculaTipoCambio();

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
                    } else {
                        $.unblockUI();
                        PartidaResumenAE();
                        calculaTotalOrdenCompra();
                        //calculaTipoCambio();
                    }
                }
            },
            default: {
                label: "No",
                className: "btn-default m-r-5 m-b-5"
            }
        }
    });
}
function reloadBuscadorOC(){
     var end = moment();
    var start = moment().subtract(2, "days");;
    reloadOrdenes(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
 }
 function reloadOrdenes(fi, ff){
    
    $("#tableOC").DataTable().clear().draw();
    $.ajax({
        type: 'GET',
        async: true,       
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: routeapp + "get_oc_xfecha",
        data: {           
            "fi": fi,
            "ff": ff
        },
        beforeSend: function() {
             $.blockUI({
                message: '<h1>Solicitando OC</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
          setTimeout($.unblockUI, 500); 
        },
        success: function(data){

            if(data.data.length > 0){
                $("#tableOC").dataTable().fnAddData(data.data);           
            }else{ 

            }        
        }
    });
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