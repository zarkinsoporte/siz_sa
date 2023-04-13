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

//var COL_CODIGO_OT = 19;
//var COL_ID_OT = 20;

//var COL_ID_ARTICULO = 21;
//var COL_ID_AUX = 22;
//var COL_ID_UMI = 23;
//var COL_ID_UMC = 24;
var COL_CTA_MAYOR = 2;


// Inicializa tabla oc
var tableOC = $("#tableOC").DataTable({
    language: {
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
    },
    dom: 'rit',
    order: [[1, 'asc']],
    buttons: [],
    scrollX: true,
    scrollY: "430px",
    scrollCollapse: true,
    deferRender: true,
    pageLength: -1,
    "paging": false,
    createdRow: function (row, data, dataIndex) {
        //console.log(data)
        $(row).attr('data-id', data.DocEntry);
    },
    columns: [
        { data: "BTN_EDITAR" },
        { data: "NumOC" },
        { data: "Proveedor" },
        { data: "Estatus" },
        { data: "Total" },
        { data: "Moneda" },
        { data: "FechaOC" },
        { data: "Comentario" }
    ],
    'columnDefs': [
        {
            "targets": [COL_BTN_EDITAR],
            "searchable": false,
            "orderable": false,
            'className': "dt-body-center",
            "render": function (data, type, row) {
                if (row['Estatus'] == 'CERRADA')
                    return '<button type="button" class="btn btn-sm btn-danger btn-outline-danger" style="margin-left:5px" id="boton-pdf"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>';
                else return '<button type="button" class="btn btn-sm btn-primary" id="btnEditar"> <span class="glyphicon glyphicon-pencil"></span> </button>'
                    + '<button type="button" class="btn btn-sm btn-danger" style="margin-left:5px" id="btnEliminar"> <span class="glyphicon glyphicon-trash"></span></button>'
                    + '<button type="button" class="btn btn-sm btn-danger btn-outline-danger" style="margin-left:5px" id="boton-pdf"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>';


            }

        },
        {

            "targets": [5],
            "searchable": false,
            "orderable": false,
            "render": function (data, type, row) {

                return number_format(row['Total'], 2, '.', ',');

            }

        }
    ],
});

$('#tableOC thead tr').clone(true).appendTo('#tableOC thead');
$('#tableOC thead tr:eq(1) th').each(function (i) {
    var title = $(this).text();
    $(this).html('<input style="color: black;"  type="text" placeholder="Filtro ' + title + '" />');

    $('input', this).on('keyup change', function () {

        if (tableOC.column(i).search() !== this.value) {
            tableOC
                .column(i)
                .search(this.value, true, false)

                .draw();
        }

    });
});    
window.onload = function () {
    tableOC.columns.adjust().draw();
}
function InicializaTablas() {

    TBL_ART_EXIST = $('#tblArticulosExistentesNueva').DataTable({
        language: {
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
            { data: "PARTIDA" },
            { data: "CODIGO_ARTICULO" },
            { data: "NOMBRE_ARTICULO" },

            { data: "UNIDAD_MEDIDA_INV" },
            { data: "FACTOR_CONVERSION" },
            { data: "UNIDAD_MEDIDA_COMPRAS" },

            { data: "CANTIDAD" },
            { data: "PRECIO" },
            { data: "SUBTOTAL" },

            { data: "DESCUENTO" },
            { data: "MONTO_DESCUENTO" },
            { data: "IVA" },

            { data: "MONTO_IVA" },
            { data: "TOTAL" },
            { data: "FECHA_ENTREGA", orderable: false },

            { data: "EDO_CANTIDAD_PENDIENTE" },
            { data: "PARTIDA_CERRADA" },
            { data: "BTN_ELIMINAR", orderable: false },

            { data: "ID_IVA", orderable: false, visible: false },
            { data: "ID_PARTIDA", orderable: false, visible: false },//20
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
                "orderable": false,
                "render": function (data, type, row) {
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
                "orderable": false
            },
            {
                "targets": [COL_UNIDAD_MEDIDA_INV],
                "orderable": false
            },
            {
                "targets": [COL_FACTOR_CONVERSION],
                "orderable": false
            },
            {
                "targets": [COL_UNIDAD_MEDIDA_COMPRAS],
                "orderable": false
            },
            {
                "targets": [COL_CANTIDAD],
                "orderable": false,
                "render": function (data, type, row) {
                    return '<input id="input-cantidadAE" style="" class="form-control input-sm cantidad" type="number" value="' + parseFloat(row['CANTIDAD']).toFixed(DECIMALES) + '">';
                }
            },
            {
                "targets": [COL_PRECIO],
                "orderable": false,
                "render": function (data, type, row) {
                    return '<input id= "input-precioAE" style="width: 100px" class="form-control input-sm precio" value="' + parseFloat(row['PRECIO']).toFixed(DECIMALES) + '" type="number">'
                }
            },
            {
                "targets": [COL_SUBTOTAL],
                "orderable": false
            },
            {
                "targets": [COL_DESCUENTO],
                "orderable": false,
                "render": function (data, type, row) {
                    return '<input id= "input-descuentoAE" style="width: 100px" class="form-control input-sm" value="' + parseFloat(DESCUENTO_GENERAL).toFixed(DECIMALES) + '" type="number">'
                }
            },
            {
                "targets": [COL_MONTO_DESCUENTO],
                "orderable": false
            },
            {
                "targets": [COL_IVA],
                "orderable": false,
                "render": function (data, type, row) {
                    var id = row[1];
                    var opciones = [];
                    for (var i = 0; i < IVA.length; i++) {
                        if (id == IVA[i]['CMIVA_IVAId']) {
                            opciones.push('<option selected value="' + IVA[i]['CMIVA_IVAId'] + '">' + IVA[i]['CMIVA_Porcentaje'] + '</option>');
                        } else {
                            opciones.push('<option value="' + IVA[i]['CMIVA_IVAId'] + '">' + IVA[i]['CMIVA_Porcentaje'] + '</option>');
                        }
                    }

                    var select = '<select data-live-search="true" class="boot-select selectpicker form-control" data-style="btn-sm btn-success" style="padding:1px !important; display: block !important" id="cboIVAAE">' + opciones + '</select>';
                    return select;
                }
            },
            {
                "targets": [COL_MONTO_IVA],
                "orderable": false
            },
            {
                "targets": [COL_TOTAL],
                "orderable": false
            },
            {
                "targets": [COL_FECHA_ENTREGA_COMPRA],
                "orderable": false,
                "render": function (data, type, row) {
                    var fecha_entrega = $('#input-fecha-entrega').val();
                    return '<input id= "input-fecha-entrega-linea" style="width: 100px" class="form-control input-sm fila-dt" type="date" value="' + fecha_entrega + '">'
                }
            },

            //articulos[i]["OVD_DetalleId"] == '' ? id nuevo : articulos[i]["OVD_DetalleId"];

            {
                "targets": [COL_PARTIDA_CERRADA],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function (data, type, row) {

                    return '<input type="checkbox" id="cerrarPartidaCheck" class="editor-active" disabled>';

                }
            },
            {
                "targets": [COL_BTN_ELIMINAR_COMPRA],
                "className": "dt-body-center",
                "render": function (data, type, row) {
                    return '<a class="btn btn-danger btn-sm" id="boton-eliminarAE"> <span class="fa fa-trash"></span> </a>';
                }
            }
        ]
    });

    $('#tblArticulosExistentesNueva').dataTable().fnAdjustColumnSizing(false);
    ARTICULO_BUSCADOR_INDEX = $('#tblArticulosExistentesNueva').DataTable().row('.selected').index() == undefined ? '' : $('#tblArticulosExistentesNueva').DataTable().row('.selected').index();

    TBL_ART_MISC = $('#tblArticulosMiscelaneosNueva').DataTable({
        language: {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        "drawCallback": function (settings) {
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
        dom: 'T<"clear">lfrtip',
        columns: [
            { data: "PARTIDA" },
            { data: "NOMBRE_ARTICULO" },
            { data: "CTA_MAYOR" },

            { data: "CANTIDAD" },
            { data: "PRECIO" },
            { data: "SUBTOTAL" },

            { data: "DESCUENTO" },
            { data: "MONTO_DESCUENTO" },
            { data: "IVA" },

            { data: "MONTO_IVA" },
            { data: "TOTAL" },
            { data: "FECHA_ENTREGA", orderable: false },

            { data: "PARTIDA_CERRADA" },
            { data: "BTN_ELIMINAR", orderable: false },
            { data: "ID_IVA", orderable: false, visible: false },

            { data: "ID_PARTIDA", orderable: false, visible: false },//17
            { data: "ESTATUS_PARTIDA", orderable: false, visible: false }//18

        ],
        "columnDefs": [
            {
                searchable: false,
                orderable: false,
                targets: 0,
            },
            {
                "targets": [1],
                "orderable": false,
                "render": function (data, type, row) {
                    return '<input id= "input-nombreART-miselaneos"  class="form-control input-sm" type="text">'
                }
            },

            {
                "targets": [2],
                "orderable": false,
                "render": function (data, type, row) {
                    var id = row[1];
                    var opciones = [];

                    for (var i = 0; i < CTAS_MAYOR.length; i++) {
                        if (id == CTAS_MAYOR[i]['ControlId']) {
                            opciones.push('<option selected value="' + CTAS_MAYOR[i]['ControlId'] + '">' + CTAS_MAYOR[i]['Valor'] + '</option>');
                        } else {
                            opciones.push('<option value="' + CTAS_MAYOR[i]['ControlId'] + '">' + CTAS_MAYOR[i]['Valor'] + '</option>');
                        }
                    }

                    var select = '<select data-live-search="true" class=" boot-select selectpicker form-control" data-style="btn-sm btn-success" style="padding: 1px !important; display: block !important" id="cboTPM">' + opciones + '</select>';
                    return select;
                }
            },
            {
                "targets": [3],
                "orderable": false,
                "render": function (data, type, row) {
                    return '<input id="input-cantidadAM"  class="form-control input-sm cantidad" type="number" value="' + parseFloat(row['CANTIDAD']).toFixed(DECIMALES) + '">';
                }
            },
            {
                "targets": [4],
                "orderable": false,
                "render": function (data, type, row) {
                    return '<input id= "input-precioAM"  class="form-control input-sm precio" value="' + parseFloat(row['PRECIO']).toFixed(DECIMALES) + '" type="number">'
                }
            },
            {
                "targets": [5],
                "orderable": false
            },
            {
                "targets": [6],
                "orderable": false,
                "render": function (data, type, row) {
                    return '<input id= "input-descuentoAM"  class="form-control input-sm" value="' + parseFloat(DESCUENTO_GENERAL).toFixed(DECIMALES) + '" type="number">'
                }
            },
            {
                "targets": [7],
                "orderable": false
            },
            {
                "targets": [8],
                "orderable": false,
                "render": function (data, type, row) {
                    var id = row[1];
                    var opciones = [];

                    for (var i = 0; i < IVA.length; i++) {
                        if (id == IVA[i]['CMIVA_IVAId']) {
                            opciones.push('<option selected value="' + IVA[i]['CMIVA_IVAId'] + '">' + IVA[i]['CMIVA_Porcentaje'] + '</option>');
                        } else {
                            opciones.push('<option value="' + IVA[i]['CMIVA_IVAId'] + '">' + IVA[i]['CMIVA_Porcentaje'] + '</option>');
                        }
                    }

                    var select = '<select data-live-search="true" class="boot-select selectpicker form-control" data-style="btn-sm btn-success" style="padding: 1px !important; display: block !important" id="cboIVAAM">' + opciones + '</select>';
                    return select;
                }
            },
            {
                "targets": [9], //monto IVA
                "orderable": false
            },
            {
                "targets": [10], //total
                "orderable": false
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
                "render": function (data, type, row) {
                    return '<button type="button" class="btn btn-danger btn-sm" id="boton-eliminarAM"> <span class="fa fa-trash"></span> </button>';
                }
            },
            {
                "targets": [14],
                "orderable": false
            },
        ]
    });

    $('#tblArticulosMiscelaneosNueva').dataTable().fnAdjustColumnSizing(false);
    //ARTICULO_BUSCADOR_INDEX = $('#tblArticulosMiscelaneosNueva').DataTable().row('.selected').index() == undefined ? '' : $('#tblArticulosMiscelaneosNueva').DataTable().row('.selected').index();

}


function insertarFila() {
    console.log('insertar fila: ' + BanderaOC)
    if (BanderaOC == 0) {
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
                , "FECHA_ENTREGA": ""

                , "EDO_CANTIDAD_PENDIENTE": ""
                , "BTN_ELIMINAR": null
                , "ID_IVA": "W3"

                , "ID_PARTIDA": ""
                , "ESTATUS_PARTIDA": ""//20
            }
        ).draw(false);
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
                , "ID_IVA": "W3"

                , "ID_PARTIDA": ""
                , "ESTATUS_PARTIDA": ""//17
            }
        ).draw(true);
        actualizaLineaPartidaAM();
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