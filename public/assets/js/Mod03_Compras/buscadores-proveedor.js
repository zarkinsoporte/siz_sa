/**
 * Created by Carlos Omar Anaya Barajas on 03/05/2017.
 * Idea tomada del archivo buscadores.js de Victor Moreno
 */

    var PagoProveedor = true;
    var nombreInputId;
    var urlProveedores;
    var controlaFuncion;
    var MonId = '';
    var editaProveedor = 0;
    var BanderaPagos = false;

    var id = '';

    var handleBuscadores = function() {
        "use strict";

        if ($('#btnBuscarProveedores').length !== 0) {

            $("#tblProveedores").dataTable({

                language: {
                    "url": "/plugins/DataTables/json/spanish.json"
                },
                "aaSorting": [],
                dom: 'T<"clear">lfrtip',
                ajax: {
                    "url": urlProveedores,
                    type: 'POST'
                },
                columns: [{data: "PRO_CodigoProveedor"},
                    {data: "PRO_Nombre"},
                    {data: "PRO_NombreComercial"},
                    {data: "PCA_MON_MonedaId"},
                    {data: "MON_Nombre"}
                ],

                "columnDefs": [
                    {
                        "targets": [ 3 ],
                        "visible": false,
                        "searchable": false
                    },

                    {
                        "targets": [ 4 ],
                        "visible": false,
                        "searchable": false
                    }
                ],

                tableTools: {sSwfPath: "/plugins/DataTables/swf/copy_csv_xls_pdf.swf"}
            });

            $("#btnBuscarProveedores" ).on( "click", function() {

                $('#modalBuscadorProveedores').on('show.bs.modal', function () {
                }).modal("show");
            });

            var table = $('#tblProveedores').DataTable();

            $('#tblProveedores tbody').on( 'click', 'tr', function () {
                if ( !$(this).hasClass('selected') ) {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                    // $(this).removeClass('selected');
                }
                //else {
                //    //table.$('tr.selected').removeClass('selected');
                //    //$(this).addClass('selected');
                //}

            });

            $('#tblProveedores tbody').on( 'dblclick', 'tr', function () {

                id = $(this)[0]['id'];
                var row = $(this);
                var datos = table.row(row).data();
                var codigoCliente = datos['PRO_CodigoProveedor'];
                var razonSocial = datos['PRO_Nombre'];
                var domicilio = datos['PRO_Domicilio'];
                var codigoPostal = datos['PRO_CodigoPostal'];
                var rfc = datos['PRO_RFC'];
                var telefono = datos['PRO_Telefono'];
                var contacto = datos['PCON_Nombre'];
                var MonedaId = datos['PCA_MON_MonedaId'];
                var Moneda = datos['MON_Nombre'];

                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');

                if(controlaFuncion == 1){

                    ProveedorSeleccionadoOC(codigoCliente, razonSocial, MonedaId, Moneda, domicilio, codigoPostal, rfc, telefono, contacto);

                }
                else if(controlaFuncion == 2){

                    ProveedorSeleccionadoFlujoEfectivo(codigoCliente, razonSocial);

                }
                else{

                    ProveedorSeleccionado(codigoCliente, razonSocial, MonedaId, Moneda);

                }

            });
/**********************************************/    
$("#sel_proveedor").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
    
    $.ajax({
        type: 'GET',
        async: true,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: routeapp + 'getProveedorOC',
        data: {
            'id': newValue,
        },
        beforeSend: function() {
            $.blockUI({
                message: "<h1>Buscando Proveedor</h1><h3>por favor espere un momento...<i class='fa fa-spin fa-spinner'></i></h3>",
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
            console.log(data)
            // var codigoCliente = datos['PRO_CodigoProveedor'];
            // var razonSocial = datos['PRO_Nombre'];
            // var domicilio = datos['PRO_Domicilio'];
            // var codigoPostal = datos['PRO_CodigoPostal'];
            // var rfc = datos['PRO_RFC'];
            // var telefono = datos['PRO_Telefono'];
            // var contacto = datos['PCON_Nombre'];
            // var MonedaId = datos['PCA_MON_MonedaId'];
            // var Moneda = datos['MON_Nombre'];
            // ProveedorSeleccionadoOC(codigoCliente, razonSocial, MonedaId, Moneda, domicilio, codigoPostal, rfc, telefono, contacto);
        }
    });

});
            $("button#aceptarProveedor").off().click(function() {

                var row = table.$('tr.selected');
                id = row[0]['id'];
                var datos = table.row(row).data();
                var codigoCliente = datos['PRO_CodigoProveedor'];
                var razonSocial = datos['PRO_Nombre'];
                var domicilio = datos['PRO_Domicilio'];
                var codigoPostal = datos['PRO_CodigoPostal'];
                var rfc = datos['PRO_RFC'];
                var telefono = datos['PRO_Telefono'];
                var contacto = datos['PCON_Nombre'];
                var MonedaId = datos['PCA_MON_MonedaId'];
                var Moneda = datos['MON_Nombre'];

                if(controlaFuncion == 1){

                    ProveedorSeleccionadoOC(codigoCliente, razonSocial, MonedaId, Moneda, domicilio, codigoPostal, rfc, telefono, contacto);

                }
                else if(controlaFuncion == 2){

                    ProveedorSeleccionadoFlujoEfectivo(codigoCliente, razonSocial);

                }
                else{

                    ProveedorSeleccionado(codigoCliente, razonSocial, MonedaId, Moneda);

                }

            });
        }
    };

    function ProveedorSeleccionadoFlujoEfectivo(codigo, razonSocial){

        $('#btnBuscarProveedores').text(codigo + ' - ' + razonSocial);
        $('#' + nombreInputId).val(id);
        $('#modalBuscadorProveedores').modal('hide');

    }

    function ProveedorSeleccionadoOC(codigo, razonSocial, MonedaId, Moneda, domicilio, codigoPostal, rfc, telefono, contacto){

        if(editaProveedor == 0){

            $('#btnBuscarProveedores').text(codigo + ' - ' + razonSocial);
            //MonId = MonedaId;
            document.getElementById('cboMoneda').value = MonedaId;
            document.getElementById('nombreProveedor').innerText = razonSocial;
            document.getElementById('direccionProveedor').innerText = domicilio;
            document.getElementById('codigoPostalProveedor').innerText = codigoPostal;
            document.getElementById('rfcProveedor').innerText = rfc;
            document.getElementById('telefonicosProveedor').innerText = telefono;
            document.getElementById('contactoProveedor').innerText = contacto;

            $("#ordenesCompraOC #cboSucursal").removeAttr('disabled');
            $("#ordenesCompraOC #cboSucursal").selectpicker('refresh');
            $("#ordenesCompraOC #cboMoneda").removeAttr('disabled');
            $("#ordenesCompraOC #cboMoneda").selectpicker('refresh');
            $("#ordenesCompraOC #cboTipoOC").removeAttr('disabled');
            $("#ordenesCompraOC #cboTipoOC").selectpicker('refresh');
            $("#ordenesCompraOC #cboAlmacen").removeAttr('disabled');
            $("#ordenesCompraOC #cboAlmacen").selectpicker('refresh');
            $("#ordenesCompraOC #boton-datos-adicionales").removeAttr('disabled');
            $("#ordenesCompraOC #cboAgente").removeAttr('disabled');
            $("#ordenesCompraOC #cboAgente").selectpicker('refresh');
            $("#ordenesCompraOC #cboSucursalAgente").removeAttr('disabled');
            $("#ordenesCompraOC #cboSucursalAgente").selectpicker('refresh');

            /*if(MonedaId != '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1'){//PESOS

             $("#ordenesCompraOC #cboAgente").removeAttr('disabled');
             $("#ordenesCompraOC #cboAgente").selectpicker('refresh');
             $("#ordenesCompraOC #agenteAduanal").show();

             }
             else{

             $("#ordenesCompraOC #cboAgente").val('');
             $("#ordenesCompraOC #cboAgente").selectpicker('refresh');
             $("#ordenesCompraOC #cboAgente").attr('disabled','disabled');
             $("#ordenesCompraOC #agenteAduanal").hide();

             }*/

        }

        $('#' + nombreInputId).val(id);
        $('#' + nombreInputId).change();

        $('#cboMoneda').change();
        $('#modalBuscadorProveedores').modal('hide');
        //$('#modalBuscadorProveedores').on('show.bs.modal', function () { }).modal("show");
    }

    function ProveedorSeleccionado(codigo, razonSocial, MonedaId, Moneda){

        $('#btnBuscarProveedores').text(codigo + ' - ' + razonSocial);
        $('#' + nombreInputId).val(id);

        if( document.getElementById('TipoModena') ){
         
            document.getElementById('TipoModena').value = MonedaId;
            document.getElementById('MonedaProv').value = MonedaId;
            MonId = MonedaId;
        }

        if(BanderaPagos){
            
            document.getElementById('monedas').value = MonedaId; 
            $('#monedas').selectpicker('refresh');       
        }

        if(PagoProveedor) {

            document.getElementById('NombreMoneda').innerText = 'Tipo Moneda: ' + Moneda;

            $('#modalBuscadorFechasDebito').on('show.bs.modal', function () { }).modal("show");
        }

        else {

            enviar_Inf();
            $('#modalBuscadorProveedores').modal("hide");
        }
    }

    function ComprobarElemento(variable) {

        return (typeof(window[variable]) !== "undefined");
    }



    function TipoModedaFacturas(){

        if($('#TipoModena').val() != '' && $('#ChkbIncluyeFactura').is(":checked") && PagoProveedor === true) {

            document.getElementById('NombreMoneda').innerText = 'Tipo Moneda: ' + document.getElementById('TipoModena').options[document.getElementById('TipoModena').selectedIndex].text;
            $('#modalBuscadorFechasDebito').on('show.bs.modal', function () {}).modal("show");
        }

        else
            enviar_Inf();

        document.getElementById('MonedaProv').value = document.getElementById('TipoModena').value;
    }

    function enviar_Inf(){

        if ($('#ChkbIncluyeFactura').is(":checked") || PagoProveedor === false)
            $('#' + nombreInputId).change();

        else
            FuncionAsignada();
    }

    $("button#aceptarFechas").off().click(function() {

        var M = $('#Mes').val();
        var MH = $('#MesHasta').val();
        var F1 = $('#Dia1').val();
        var F2 = $('#Dia2').val();
        var Anno = $('#Año').val();

        $('#monthAutSrv').val(M);
        $('#monthAutSrvHasta').val(MH);
        $('#startDateAutSrv').val(F1);
        $('#endDateAutSrv').val(F2);
        $('#yearAutSrv').val(Anno);

        $('#modalBuscadorFechasDebito').modal("hide");
        $('#modalBuscadorProveedores').modal("hide");

        enviar_Inf();
    });

    function cancelar_Fecha(){

        $('#modalBuscadorFechasDebito').modal("hide");

        $('#TipoModena').val('');
        $('#btnBuscarProveedores').text('Selecciona Proveedor');
        $('#' + nombreInputId).val('');
    }

    var Buscadores = function () {
        "use strict";
        return {
            //main function
            init: function () {
                handleBuscadores();
            }
        };
    }();

//======================================================================================================================
//======================================================================================================================

    $("#ChkbIncluyeFactura").change(function() {

        if ($(this).is(":checked"))
            document.getElementById('IncluyeFact').innerText = 'SI';

        else
            document.getElementById('IncluyeFact').innerText = 'NO';

    });