/**
 * Created by mgl_l on 21/05/2020.
 */

var nombreInputOtId;// = 'idOt';
var urlOts;// = 'getOtsBuscadorREQ';
var isEditarArtExis;
var banderaFilaOC = 0;
var existenteOmiscelaneo = 0;

var BuscadoresOtsREQ = function () {
    "use strict";
    return {
        //main function
        init: function () {
            handleBuscadoresOTREQ();
        }
    };
}();

var handleBuscadoresOTREQ = function() {
    "use strict";

    if ($('#btnBuscarOts').length !== 0) {


        $("#tabla-ordenesTrabajo").dataTable({

            language: {
                "url": "/plugins/DataTables/json/spanish.json"
            },
            "aaSorting": [],
            dom: 'T<"clear">lfrtip',
            ajax: {
                "url": urlOts,
                type: 'POST'
            },
            deferRender: true,
            columns: [{data: "OT_Codigo"},
                {data: "ART_CodigoArticulo"},
                {data: "ART_Nombre"}
            ],
            /*
             "columnDefs": [
             {
             "targets": [ 4 ],
             "visible": false,
             "searchable": false
             }
             ],
             */
            tableTools: {sSwfPath: "/plugins/DataTables/swf/copy_csv_xls_pdf.swf"}
        });

        $("#btnBuscarOts" ).on( "click", function() {

            $('#modal-ordenesTrabajo').on('show.bs.modal', function () {
            }).modal("show");
        });

        $("#btnBuscarOtss" ).on( "click", function() {

            $('#modal-ordenesTrabajo').on('show.bs.modal', function () {
                existenteOmiscelaneo = 1;
            }).modal("show");
        });

        var table = $('#tabla-ordenesTrabajo').DataTable();

        $('#tabla-ordenesTrabajo tbody').on( 'click', 'tr', function () {
            if ( !$(this).hasClass('selected') ) {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                // $(this).removeClass('selected');
            }
            //else {
            //    //table.$('tr.selected').removeClass('selected');
            //    //$(this).addClass('selected');
            //}

        } );

        $('#tabla-ordenesTrabajo tbody').on( 'dblclick', 'tr', function () {

            if(banderaFilaOC == 0){

                id = $(this)[0]['id'];
                var row = $(this);
                var datos = table.row(row).data();
                var OT_Codigo = datos['OT_Codigo'];

                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                if(existenteOmiscelaneo == 1){

                    $('#btnBuscarOtss').text(OT_Codigo);
                    $('#modal-ordenesTrabajo').modal("hide");
                    $('#modal-articulosMiscelaneos #' + nombreInputOtId).val(id);
                    $('#modal-articulosMiscelaneos #' + nombreInputOtId).change();

                }
                else{

                    otSeleccionado(OT_Codigo);

                }

            }
            else if(banderaFilaOC == 1){

                var fila = $('#modal-ordenesTrabajo #input-fila').val();
                var tabla_artExist = $('#tblArticulosExistentesNueva').DataTable();
                var dataAE = tabla_artExist.row(fila).data();

                var row = table.$('tr.selected');
                id = row[0]['id'];
                var datos = table.row(row).data();
                var OT_Codigo = datos['OT_Codigo'];

                dataAE['ID_OT'] = id;
                dataAE["CODIGO_OT"] = OT_Codigo;

                tabla_artExist.row(fila).nodes(fila, COL_ID_OT).to$().find("td:eq('" + COL_ID_OT + "')").text(id);
                tabla_artExist.row(fila).nodes(fila, COL_CODIGO_OT).to$().find('input#input-ot-codigoAE').val(OT_Codigo);

                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                $('#modal-ordenesTrabajo').modal("hide");

            }
            else if(banderaFilaOC == 2){

                var fila = $('#modal-ordenesTrabajo #input-fila').val();
                var tabla_artMisc = $('#tblArticulosMiscelaneosNueva').DataTable();
                var dataAM = tabla_artMisc.row(fila).data();

                var row = table.$('tr.selected');
                id = row[0]['id'];
                var datos = table.row(row).data();
                var OT_Codigo = datos['OT_Codigo'];

                dataAM['ID_OT'] = id;
                dataAM["CODIGO_OT"] = OT_Codigo;

                tabla_artMisc.row(fila).nodes(fila, COL_ID_OT).to$().find("td:eq('" + COL_ID_OT + "')").text(id);
                tabla_artMisc.row(fila).nodes(fila, COL_CODIGO_OT).to$().find('input#input-ot-codigoAM').val(OT_Codigo);

                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                $('#modal-ordenesTrabajo').modal("hide");

            }

        } );

        $("#modal-ordenesTrabajo button#boton-aceptarR").off().click(function() {

            if(banderaFilaOC == 0){

                var row = table.$('tr.selected');
                id = row[0]['id'];
                var datos = table.row(row).data();
                var OT_Codigo = datos['OT_Codigo'];
                if(existenteOmiscelaneo == 1){

                    $('#btnBuscarOtss').text(OT_Codigo);
                    $('#modal-ordenesTrabajo').modal("hide");
                    $('#modal-articulosMiscelaneos #' + nombreInputOtId).val(id);
                    $('#modal-articulosMiscelaneos #' + nombreInputOtId).change();

                }
                else{

                    otSeleccionado(OT_Codigo);

                }

            }
            else if(banderaFilaOC == 1){

                var fila = $('#modal-ordenesTrabajo #input-fila').val();
                var tabla_artExist = $('#tblArticulosExistentesNueva').DataTable();
                var dataAE = tabla_artExist.row(fila).data();

                var row = table.$('tr.selected');
                id = row[0]['id'];
                var datos = table.row(row).data();
                var OT_Codigo = datos['OT_Codigo'];

                dataAE['ID_OT'] = id;
                dataAE["CODIGO_OT"] = OT_Codigo;

                tabla_artExist.row(fila).nodes(fila, COL_ID_OT).to$().find("td:eq('" + COL_ID_OT + "')").text(id);
                tabla_artExist.row(fila).nodes(fila, COL_CODIGO_OT).to$().find('input#input-ot-codigoAE').val(OT_Codigo);

                $('#modal-ordenesTrabajo').modal("hide");

            }
            else if(banderaFilaOC == 2){

                var fila = $('#modal-ordenesTrabajo #input-fila').val();
                var tabla_artMisc = $('#tblArticulosMiscelaneosNueva').DataTable();
                var dataAM = tabla_artMisc.row(fila).data();

                var row = table.$('tr.selected');
                id = row[0]['id'];
                var datos = table.row(row).data();
                var OT_Codigo = datos['OT_Codigo'];

                dataAM['ID_OT'] = id;
                dataAM["CODIGO_OT"] = OT_Codigo;

                tabla_artMisc.row(fila).nodes(fila, COL_ID_OT).to$().find("td:eq('" + COL_ID_OT + "')").text(id);
                tabla_artMisc.row(fila).nodes(fila, COL_CODIGO_OT).to$().find('input#input-ot-codigoAM').val(OT_Codigo);

                $('#modal-ordenesTrabajo').modal("hide");

            }

        });
    }

};

function otSeleccionado(codigo){

    $('#btnBuscarOts').text(codigo);
    $('#modal-ordenesTrabajo').modal("hide");
    $('#' + nombreInputOtId).val(id);
    $('#' + nombreInputOtId).change();

}