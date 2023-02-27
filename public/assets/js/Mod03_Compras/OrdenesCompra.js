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

  function InicializaComboBox()  {
        // $('#cboMoneda').selectpicker({
        //     noneSelectedText: 'Selecciona una opción',
        // });
        $('.selectpicker').selectpicker({
            noneSelectedText: 'Selecciona una opción',
            container: "body"
        });
        
        
        var options = [];
        var opciones = [ //la llave es el Id que hay en la tabla ItekniaDB.ArticulosTipos
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

// FIN LIBERACION
// INICIO IMPRESION
var tableOC = $("#tableOC").DataTable({
                language:{
                     "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                }, 
                dom: 'rit',
                order: [[1, 'asc']],
                buttons: [],
                scrollX: true,
                scrollY: "430px",
                scrollCollapse: true,
                deferRender: true,        
                pageLength:-1,
                "paging": false,
                columns: [                   
                    {data: "BTN_EDITAR"},    
                    {data: "NumOC"},
                    {data: "Proveedor"},
                    {data: "Elaboro"},
                    {data: "Estatus"},
                    {data: "Total"},
                    {data: "Moneda"},
                    {data: "FechaOC"}
                ],
                'columnDefs': [
                {
                    "targets": [ COL_BTN_EDITAR ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        return '<button type="button" class="btn btn-sm btn-primary" id="btnEditar"> <span class="glyphicon glyphicon-pencil"></span> </button>'
                        + '<button type="button" class="btn btn-sm btn-danger" style="margin-left:5px" id="btnEliminar"> <span class="glyphicon glyphicon-trash"></span></button>'
                         + '<button type="button" class="btn btn-sm btn-danger btn-outline-danger" style="margin-left:5px" id="boton-pdf"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>';

                    }

                },
                {

                    "targets": [ 5 ],
                    "searchable": false,
                    "orderable": false,
                    "render": function ( data, type, row ) {

                            return number_format(row['Total'],2,'.',',');

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
    /* $.ajax({
        type: 'POST',
        async: true,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: routeapp + 'orden_compra_pdf',
        data: {
            'NumOC': NumOC,
        },
        beforeSend: function() {
            $.blockUI({
                message: "<h1>Procesando Solicitud</h1><h3>por favor espere un momento...<i class='fa fa-spin fa-spinner'></i></h3>",
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
            var blob=new Blob([data]);
            var link=document.createElement('a');
            link.href=window.URL.createObjectURL(blob);
            link.download="OrdenCompra.pdf";
            link.click();
        }
    }); */

    $.unblockUI();
});
 function reloadBuscadorOC(){
     var end = moment();
    var start = moment().subtract(15, "days");;
    reloadOrdenes(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
 }
//reloadOrdenes();
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

//FIN IMPRESION
function get_oc() {
    $.ajax({

        type: 'GET',
        async: true,
        //url: '{!! route('get_oc') !!}',
        url: routeapp + "get_oc",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:{
            "oc": $('#input_oc').val()
        },
        beforeSend: function() {
            $.blockUI({
                message: '<h1>Solicitando.</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
            $("#input_oc").val('');
                     
        },
        success: function(data){
            setTimeout($.unblockUI, 500);  

            if (data.respuesta != 'ok') {
                swal("", "La OC no existe", "error",  {
                        buttons: false,
                        timer: 2000,
                    });
            } else {
                $("#tableOC").DataTable().clear().draw();
                $("#tableOC").dataTable().fnAddData((data.data));
                
                swal("", "OC encontrada", "success",  {
                    buttons: false,
                    timer: 2000,
                });
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {          
            $.unblockUI();
            swal("", "Error agregando OC", "error",  {
                        buttons: false,
                        timer: 2000,
                    });    
        }

    });
}
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
    registroNuevo = 0;
    $('#ordenesCompraOC').show();
    $('#btnBuscadorOC').hide();
    InicializaComponentesOC();
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
        var tipo_cambio_anterior = $('#input_tc').val();
        console.log('cambiando moneda: ')
        var moneda_anterior = oldValue;
        var moneda = $('option:selected', this).val();
        console.log('cboMoneda: '+ oldValue + ' > '+ moneda + ' tc:'+tipo_cambio_anterior)
        carga_tipo_cambio(moneda);
        calculaNuevaMoneda(moneda_anterior, moneda, tipo_cambio_anterior);
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
    });
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
        var proveedorId = $('option:selected', this).val();
        $("#cboMoneda").val(proveedor_moneda);
        $('#cboMoneda').selectpicker('refresh');
        carga_tipo_cambio(proveedor_moneda);
        carga_info_proveedor(proveedorId);
        recargaTablaArticulos('');
        cargaTablaArticulos();
        $('#tblArticulosExistentesNueva').DataTable().clear().draw();
        $('#tblArticulosMiscelaneosNueva').DataTable().clear().draw();
        insertarFila();   
        setTimeout($.unblockUI, 2000);   
    });

    $('#tblArticulosExistentesNueva').on('click', 'a#boton-articuloAE', function (e) {
        e.preventDefault();
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
$('#tblArticulosExistentesNueva').on('click', 'a#boton-eliminarAE', function (e) {
    e.preventDefault();

    var tabla = $('#tblArticulosExistentesNueva').DataTable();
    var fila = $(this).closest('tr');
    var datos = tabla.row(fila).data();
    var index = tabla.row(fila).index();
    var id = datos['ID_PARTIDA'];
    if(id == ""){
        tabla.row(fila).remove().draw(false);
    }
    else{
        if (registroNuevo == 1){
            //eliminarPartidaArtExis(fila);
        }
    }
    calculaTotalOrdenCompra();
    //actualizaLineaPartidaAE();
    //calculaTipoCambio();
    //PartidaResumenAE();
});
    function carga_info_proveedor(proveedorId){
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
            beforeSend: function() {
               
            },
            complete: function() {
               // setTimeout($.unblockUI, 500);
            },
            success: function(data){
                var datos = data.proveedor

                var codigoCliente = datos['PRO_Codigo'];
                var razonSocial = datos['PRO_Nombre'];        
                var Moneda = datos['Moneda'];
                var Email = datos['PRO_Email'];
                var domicilio = datos['PRO_Domicilio'];
                var rfc = datos['PRO_RFC'];
                var telefono = datos['PRO_Telefono'];
                var contacto = datos['CON_Contacto'];
                
                ProveedorSeleccionadoOC(codigoCliente, razonSocial, Moneda, Email,
                    domicilio, rfc, telefono, contacto);
            }
        });
        
    }
     function ProveedorSeleccionadoOC(codigo, razonSocial, Moneda, Email, domicilio, rfc, telefono, contacto){
        var editaProveedor = 0;
        if(editaProveedor == 0){
            //P1765	XINOVA SA DE CV	
            //XIN100304FV5	
            //juancarlos.verduzco@gmail.com	
            //(442) 2175452	
            //MXP	
            //NULL	
            //PINO SUAREZ, SANTA CLARA, MEXICO, MEX, MX. CP:50090   
            $('#btnBuscarProveedores').text(codigo + ' - ' + razonSocial);
            //MonId = MonedaId;
            //document.getElementById('cboMoneda').value = MonedaId;
            document.getElementById('nombreProveedor').innerText = codigo+' '+razonSocial;
            document.getElementById('direccionProveedor').innerText = domicilio;
            document.getElementById('codigoPostalProveedor').innerHTML = '<i class="fa fa-envelope" aria-hidden="true"></i> '+ ((Email==null)?'-':Email);
            document.getElementById('rfcProveedor').innerText = 'RFC: '+rfc;
            document.getElementById('telefonicosProveedor').innerHTML ='<i class="fa fa-phone" aria-hidden="true"></i> '+ ((telefono==null)?'-':telefono);
            document.getElementById('contactoProveedor').innerHTML = '<i class="fa fa-vcard" aria-hidden="true"></i> '+ ((contacto==null)?'-':contacto);

            // $("#ordenesCompraOC #cboSucursal").removeAttr('disabled');
            // $("#ordenesCompraOC #cboSucursal").selectpicker('refresh');
            // $("#ordenesCompraOC #cboMoneda").removeAttr('disabled');
            // $("#ordenesCompraOC #cboMoneda").selectpicker('refresh');
            // $("#ordenesCompraOC #cboTipoOC").removeAttr('disabled');
            // $("#ordenesCompraOC #cboTipoOC").selectpicker('refresh');
            // $("#ordenesCompraOC #cboAlmacen").removeAttr('disabled');
            // $("#ordenesCompraOC #cboAlmacen").selectpicker('refresh');
            // //$("#ordenesCompraOC #boton-datos-adicionales").removeAttr('disabled');
            // $("#ordenesCompraOC #cboAgente").removeAttr('disabled');
            // $("#ordenesCompraOC #cboAgente").selectpicker('refresh');
            // $("#ordenesCompraOC #cboSucursalAgente").removeAttr('disabled');
            // $("#ordenesCompraOC #cboSucursalAgente").selectpicker('refresh');

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

        // $('#' + nombreInputId).val(id);
        // $('#' + nombreInputId).change();

        // $('#cboMoneda').change();
        // $('#modalBuscadorProveedores').modal('hide');
        //$('#modalBuscadorProveedores').on('show.bs.modal', function () { }).modal("show");
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
                beforeSend: function() {
                
                },
                complete: function() {
                // setTimeout($.unblockUI, 500);
                },
                success: function(data){
                    if(data.rates.length > 0){
                        $("#div-tipo-cambio").show();
                        $("#input_tc").val(data.rates[0].Rate);
                        $("#input_tc_anterior").val(data.rates[0].Rate);
                                        
                    }else{
                        $("#div-tipo-cambio").hide();
                        $("#input_tc").val(1);
                        $("#input_tc_anterior").val(1);
                    }
                }
            });
        }
    }
    window.onload = function () { 
        tableOC.columns.adjust().draw();     
    }
    $('#tblArticulosExistentesNueva').on('change','input#input-cantidadAE',function (e) {
        e.preventDefault();
        if($(this).val() == '' || $(this).val() < 0){
            $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
        }

        $(this).val(parseFloat(this.value).toFixed(CANTIDAD_DECIMALES));

        if ($("#sel-proveedor").val() != ""){
            var tabla = $('#tblArticulosExistentesNueva').DataTable();
            var fila = $(this).closest('tr');
            var index = tabla.row(fila).index();

            var datos = tabla.row(fila).data();
            var precio = 'input-precioAE';
            var cantidad = 'input-cantidadAE';
            var descuento = 'input-descuentoAE';
            var referenciaId = datos['ID_AUX'];
            var Cantidad_anterior = datos['CANTIDAD'];
            var Precio_anterior = datos['PRECIO'];
            var Descuento_anterior = datos['DESCUENTO'];
            var IVA_anterior = datos['IVA'];
            var ID_IVA_anterior = datos['ID_IVA'];
            var cantidadNueva = $(this).val();

            if(JSON_PARTIDA.hasOwnProperty(referenciaId)) {
                bootbox.dialog({
                    message: "¿Estas seguro de modificar la cantidad?.",
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
                                    type: "POST",
                                    async: false,
                                    data: {
                                        detalleId: referenciaId
                                        ,cantidad: cantidadNueva
                                        ,precio: Precio_anterior
                                        ,descuento: Descuento_anterior
                                        ,iva: IVA_anterior
                                        ,idIva: ID_IVA_anterior
                                    },
                                    dataType: "json",
                                    url: "compras/editarPartida",
                                    success: function () {
                                        //delete JSON_PARTIDA[referenciaId];
                                        datos['CANTIDAD'] = cantidadNueva;
                                        RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                                        calculaTotalOrdenCompra();
                                        calculaTipoCambio();
                                        if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                                            PartidaResumenAE(index);
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
                        },
                        default: {
                            label: "No",
                            className: "btn-default m-r-5 m-b-5",
                            callback: function () {
                                tabla.row(fila).nodes(fila,COL_CANTIDAD).to$().find('input#' + cantidad).val(parseFloat(Cantidad_anterior).toFixed(CANTIDAD_DECIMALES));
                            }
                        }
                    }
                });
            }
            else{

                datos['CANTIDAD'] = $(this).val();

                if (datos['CODIGO_ARTICULO'] != ""){
                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                    console.log(['precio ' + precio, 'cantidad ' +cantidad, 'descuento ' + descuento]);
                    calculaTotalOrdenCompra();
                    //calculaTipoCambio();
                    // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                    //     PartidaResumenAE(index);
                    // }
                }
                else{
                    bootbox.dialog({
                        message: "No se ha elegido un articulo, elige uno por favor para continuar.",
                        title: "Ordenes de Compra",
                        buttons: {
                            success: {
                                label: "Si",
                                className: "btn-success"
                            }
                        }
                    });
                    $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
                }
            }
        }else{
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
            $(this).val("");
        }
});

$('#tblArticulosExistentesNueva').on('change','input#input-precioAE',function (e) {
    e.preventDefault();
    if($(this).val() == '' || $(this).val() < 0){
        $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
    }

    $(this).val(parseFloat(this.value).toFixed(CANTIDAD_DECIMALES));
    if ($("#sel-proveedor").val() != ""){
        var tabla = $('#tblArticulosExistentesNueva').DataTable();
        var fila = $(this).closest('tr');
        var index = tabla.row(fila).index();

        var datos = tabla.row(fila).data();
        var precio = 'input-precioAE';
        var cantidad = 'input-cantidadAE';
        var descuento = 'input-descuentoAE';
        var referenciaId = datos['ID_AUX'];
        var Cantidad_anterior = datos['CANTIDAD'];
        var Precio_anterior = datos['PRECIO'];
        var Descuento_anterior = datos['DESCUENTO'];
        var IVA_anterior = datos['IVA'];
        var ID_IVA_anterior = datos['ID_IVA'];
        var precioNuevo = $(this).val();

        if(JSON_PARTIDA.hasOwnProperty(referenciaId)) {

            bootbox.dialog({
                message: "¿Estas seguro de modificar el precio?.",
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
                                type: "POST",
                                async: false,
                                data: {
                                    detalleId: referenciaId
                                    ,precio: precioNuevo
                                    ,cantidad: Cantidad_anterior
                                    ,descuento: Descuento_anterior
                                    ,iva: IVA_anterior
                                    ,idIva: ID_IVA_anterior
                                },
                                dataType: "json",
                                url: "compras/editarPartida",
                                success: function () {
                                    //delete JSON_PARTIDA[referenciaId];
                                    datos['PRECIO'] = precioNuevo;
                                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                                    calculaTotalOrdenCompra();
                                    calculaTipoCambio();
                                    if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                                        PartidaResumenAE(index);
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
                    },
                    default: {
                        label: "No",
                        className: "btn-default m-r-5 m-b-5",
                        callback: function () {
                            tabla.row(fila).nodes(fila,COL_PRECIO).to$().find('input#' + precio).val(parseFloat(Precio_anterior).toFixed(CANTIDAD_DECIMALES));
                        }
                    }
                }
            });

        }
        else{

            datos['PRECIO'] = $(this).val();

            if (datos['ID_ARTICULO'] != ""){
                RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                calculaTotalOrdenCompra();
                // calculaTipoCambio();
                // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                //     PartidaResumenAE(index);
                // }
            }
            else{
                bootbox.dialog({
                    message: "No se ha elegido un articulo, elige uno por favor para continuar.",
                    title: "Ordenes de Compra",
                    buttons: {
                        success: {
                            label: "Si",
                            className: "btn-success"
                        }
                    }
                });
                $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
            }

        }

    }
    else{
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
        $(this).val("");
    }
});

$('#tblArticulosExistentesNueva').on('change','input#input-descuentoAE',function (e) {

    if($(this).val() == '' || $(this).val() < 0){
        $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
    }

    if ($(this).val() >= 100){
        $(this).val(parseFloat('100.00').toFixed(CANTIDAD_DECIMALES));
    }

    $(this).val(parseFloat(this.value).toFixed(CANTIDAD_DECIMALES));
    if ($("#sel-proveedor").val() != ""){
        var tabla = $('#tblArticulosExistentesNueva').DataTable();
        var fila = $(this).closest('tr');
        var index = tabla.row(fila).index();

        var datos = tabla.row(fila).data();
        var precio = 'input-precioAE';
        var cantidad = 'input-cantidadAE';
        var descuento = 'input-descuentoAE';
        var referenciaId = datos['ID_AUX'];
        var Cantidad_anterior = datos['CANTIDAD'];
        var Precio_anterior = datos['PRECIO'];
        var Descuento_anterior = datos['DESCUENTO'];
        var IVA_anterior = datos['IVA'];
        var ID_IVA_anterior = datos['ID_IVA'];
        var descuentoNuevo = $(this).val();

        if(JSON_PARTIDA.hasOwnProperty(referenciaId)) {

            bootbox.dialog({
                message: "¿Estas seguro de modificar el descuento?.",
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
                                type: "POST",
                                async: false,
                                data: {
                                    detalleId: referenciaId
                                    ,precio: Precio_anterior
                                    ,cantidad: Cantidad_anterior
                                    ,descuento: descuentoNuevo
                                    ,iva: IVA_anterior
                                    ,idIva: ID_IVA_anterior
                                },
                                dataType: "json",
                                url: "compras/editarPartida",
                                success: function () {
                                    //delete JSON_PARTIDA[referenciaId];
                                    datos['DESCUENTO'] = descuentoNuevo;
                                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                                    calculaTotalOrdenCompra();
                                    calculaTipoCambio();
                                    if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                                        PartidaResumenAE(index);
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
                    },
                    default: {
                        label: "No",
                        className: "btn-default m-r-5 m-b-5",
                        callback: function () {
                            tabla.row(fila).nodes(fila,COL_DESCUENTO).to$().find('input#' + descuento).val(parseFloat(Descuento_anterior).toFixed(CANTIDAD_DECIMALES));
                        }
                    }
                }
            });

        }
        else{

            datos['DESCUENTO'] = $(this).val();

            if (datos['ID_ARTICULO'] != ""){
                RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                calculaTotalOrdenCompra();
                // calculaTipoCambio();
                // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                //     PartidaResumenAE(index);
                // }
            }
            else{
                bootbox.dialog({
                    message: "No se ha elegido un articulo, elige uno por favor para continuar.",
                    title: "Ordenes de Compra",
                    buttons: {
                        success: {
                            label: "Si",
                            className: "btn-success"
                        }
                    }
                });
                $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
            }

        }

    }
    else{
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
        $(this).val("");
    }
});
$('#tblArticulosExistentesNueva').on('change','select#cboIVAAE',function (e) {
    e.preventDefault();
    if ($("#sel-proveedor").val() != ""){
        var tabla = $('#tblArticulosExistentesNueva').DataTable();
        var fila = $(this).closest('tr');
        var index = tabla.row(fila).index();

        var datos = tabla.row(fila).data();
        var precio = 'input-precioAE';
        var cantidad = 'input-cantidadAE';
        var descuento = 'input-descuentoAE';
        var iva = 'cboIVAAM';
        var referenciaId = datos['ID_AUX'];
        var Cantidad_anterior = datos['CANTIDAD'];
        var Precio_anterior = datos['PRECIO'];
        var Descuento_anterior = datos['DESCUENTO'];
        var IVA_anterior = datos['IVA'];
        var ID_IVA_anterior = datos['ID_IVA'];
        var id_iva_nuevo = $(this).val();
        var iva_nuevo = $(this).val() == '' ? '0' : $(this).find('option:selected').text();

        if(JSON_PARTIDA.hasOwnProperty(referenciaId)) {

            bootbox.dialog({
                message: "¿Estas seguro de modificar el IVA?.",
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
                                type: "POST",
                                async: false,
                                data: {
                                    detalleId: referenciaId
                                    ,precio: Precio_anterior
                                    ,cantidad: Cantidad_anterior
                                    ,descuento: Descuento_anterior
                                    ,iva: iva_nuevo
                                    ,idIva: id_iva_nuevo
                                },
                                dataType: "json",
                                url: "compras/editarPartida",
                                success: function () {
                                    //delete JSON_PARTIDA[referenciaId];
                                    datos["ID_IVA"] = id_iva_nuevo;
                                    datos["IVA"] = iva_nuevo;
                                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                                    calculaTotalOrdenCompra();
                                    calculaTipoCambio();
                                    if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                                        PartidaResumenAE(index);
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
                    },
                    default: {
                        label: "No",
                        className: "btn-default m-r-5 m-b-5",
                        callback: function () {
                            tabla.row(fila).nodes(fila,COL_IVA).to$().find('select#'+iva).val(ID_IVA_anterior);
                        }
                    }
                }
            });

        }
        else{

            datos["ID_IVA"] = id_iva_nuevo;
            datos["IVA"] = iva_nuevo;

            if (datos['ID_ARTICULO'] != ""){
                RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                calculaTotalOrdenCompra();
               // calculaTipoCambio();
                // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                //     PartidaResumenAE(index);
                // }
            }
            else{
                bootbox.dialog({
                    message: "No se ha elegido un articulo, elige uno por favor para continuar.",
                    title: "Ordenes de Compra",
                    buttons: {
                        success: {
                            label: "Si",
                            className: "btn-success"
                        }
                    }
                });
                $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
            }

        }

    }
});
$('#guardar').off().on('click', function(e) {

    var estadoOC = $('#estadoOC').text();
    if(estadoOC == 'Completa'){
    
         bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'>No puedes editar la Orden de Compra porque ya esta Completa.</div>",
            buttons: {
            success: {
            label: "Ok",
            className: "btn-success m-r-5 m-b-5"
            }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

    }
    else if(estadoOC == 'Correspondida Completa'){

        bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'>No puedes editar la Orden de Compra porque ya esta Correspondida Completa.</div>",
            buttons: {
            success: {
            label: "Ok",
            className: "btn-success m-r-5 m-b-5"
            }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

    }
    else{

        validarCampos();
        if(bandera == 0){

            registraOC();
            InicializaComponentesOC();
        }

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
        
      
        InicializaComponentesOC();
        $("#tblArticulosExistentesNueva").DataTable().clear().draw();
        $("#tblArticulosMiscelaneosNueva").DataTable().clear().draw();
        // $("#tblArticulosExistentesResumenNueva").DataTable().clear().draw();
        // $("#tblArticulosMiscelaneosResumenNueva").DataTable().clear().draw();
        calculaTotalOrdenCompra();
        let val = $('option:selected', this).val();
        console.log('sel-tipoOC: '+ val);
        if (val == 0) {
            $("#articulosOC").show();
            $("#miscelaneosOC").hide();
            BanderaOC = 0;
            set_columns_index(0);
        } else {
            $("#articulosOC").hide();
            $("#miscelaneosOC").show();
            BanderaOC = 1;
            set_columns_index(1);
        }
        console.log('sel-tipo-oc: '+BanderaOC)
        setTimeout($.unblockUI, 2000);
    });
    function set_columns_index(BanderaOC){
        if (BanderaOC == 0) {
            //Tabla Articulos
            COL_PARTIDA = 0;
            COL_CODIGO_ART = 1;
            COL_NOMBRE_ART = 2;
            COL_NOMBRE_ART_MISC = 1;
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
            COL_PARTIDA_CERRADA = 14;
            COL_BTN_ELIMINAR_COMPRA = 15;
            COL_ID_IVA = 16;
            COL_ID_PARTIDA = 17;

            // COL_FECHA_REQUERIDA_COMPRA = 14;
            // COL_CODIGO_OT = 17;
            // COL_ID_OT = 18;
            // COL_ID_ARTICULO = 19;
            // COL_ID_AUX = 21;
            // COL_ID_UMI = 22;
            // COL_ID_UMC = 23;
            // COL_ESTATUS_PARTIDA = 25;
        } else {
            //tabla Miscelaneos
            COL_PARTIDA = 0;
            //COL_CODIGO_ART = 1;
            COL_NOMBRE_ART = 1;
            COL_NOMBRE_ART_MISC = 1;

            // COL_UNIDAD_MEDIDA_INV = 3;
            COL_CTA_MAYOR = 2;       
            // COL_FACTOR_CONVERSION = 4;
            // COL_UNIDAD_MEDIDA_COMPRAS = 5;
            COL_CANTIDAD = 3;
            COL_PRECIO = 4;
            COL_SUBTOTAL = 5;
            COL_DESCUENTO = 6;
            COL_MONTO_DESCUENTO = 7;
            COL_IVA = 8;
            COL_MONTO_IVA = 9;
            COL_TOTAL = 10;
            COL_PARTIDA_CERRADA = 11;
            COL_BTN_ELIMINAR_COMPRA = 12;
            COL_ID_IVA = 13;
            COL_ID_PARTIDA = 14;
        }
    }
    $('#tblArticulosMiscelaneosNueva').on('click', 'span#boton-otAM', function (e) {
    e.preventDefault();
    if ($("#sel-proveedor").val() != ""){
        var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
        var fila = $(this).closest('tr');
        fila = tabla.row(fila).index();
        banderaFilaOC = 2;
        $('#modal-ordenesTrabajo #input-fila').val(fila);
        $('#modal-ordenesTrabajo').modal('show');
    }
    else{
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

$('#tblArticulosMiscelaneosNueva').on('change','input#input-nombreART-miselaneos',function (e) {

    e.preventDefault();

    if ($("#sel-proveedor").val() == ""){

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
        $(this).val("");

    }

});

$('#tblArticulosMiscelaneosNueva').on('change','select#cboUMAM',function (e) {

    e.preventDefault();

    if ($("#sel-proveedor").val() == "") {
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
        $(this).val("");
    }
});

$('#tblArticulosMiscelaneosNueva').on('change','input#input-cantidadAM',function (e) {
    e.preventDefault();
    if($(this).val() == '' || $(this).val() < 0){
        $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
    }

    $(this).val(parseFloat(this.value).toFixed(CANTIDAD_DECIMALES));

    if ($("#sel-proveedor").val() != ""){
        var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
        var fila = $(this).closest('tr');
        var index = tabla.row(fila).index();
        //VARIFICAR EN LOS DEMAS CAMPOS Y VERIFICAR CAMPO DE CTASMAYOR        
        var datos = tabla.row(fila).data();
        datos['NOMBRE_ARTICULO'] = tabla.row(fila).nodes(fila, 1).to$().find("input#input-nombreART-miselaneos").val();
        
        console.log(datos)
        var precio = 'input-precioAM';
        var cantidad = 'input-cantidadAM';
        var descuento = 'input-descuentoAM';
        var referenciaId = datos['ID_AUX'];
        var Cantidad_anterior = datos['CANTIDAD'];
        var Precio_anterior = datos['PRECIO'];
        var Descuento_anterior = datos['DESCUENTO'];
        var IVA_anterior = datos['IVA'];
        var ID_IVA_anterior = datos['ID_IVA'];
        var cantidadNueva = $(this).val();

        /* if(registroNuevo == 1){

            bootbox.dialog({
                message: "¿Estas seguro de modificar la cantidad?.",
                title: "Ordenes de Compra",
                buttons: {
                    success: {
                        label: "Si",
                        className: "btn-success",
                        callback: function () {
                            $.ajax({
                                type: "POST",
                                async: false,
                                data: {
                                    detalleId: referenciaId
                                    ,cantidad: cantidadNueva
                                    ,precio: Precio_anterior
                                    ,descuento: Descuento_anterior
                                    ,iva: IVA_anterior
                                    ,idIva: ID_IVA_anterior
                                },
                                dataType: "json",
                                url: "compras/editarPartida",
                                success: function () {
                                    //delete JSON_PARTIDA[referenciaId];
                                    datos['CANTIDAD'] = cantidadNueva;
                                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                                    calculaTotalOrdenCompra();
                                    //calculaTipoCambio();
                                    if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                                        PartidaResumenAE(index);
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
                    },
                    default: {
                        label: "No",
                        className: "btn-default m-r-5 m-b-5",
                        callback: function () {
                            tabla.row(fila).nodes(fila,COL_CANTIDAD).to$().find('input#' + cantidad).val(parseFloat(Cantidad_anterior).toFixed(CANTIDAD_DECIMALES));
                        }
                    }
                }
            });

        }
        else{ */
            if (datos['NOMBRE_ARTICULO'] != ""){
                datos['CANTIDAD'] = $(this).val();
                RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                calculaTotalOrdenCompra();
                //calculaTipoCambio();
                // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                //     PartidaResumenAM(index);
                // }
            }
            else{
                bootbox.dialog({
                    message: "No se ha capturado articulo, captura para continuar.",
                    title: "Ordenes de Compra",
                    buttons: {
                        success: {
                            label: "Si",
                            className: "btn-success"
                        }
                    }
                });
                $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
            }
        //}

    }else{
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
        $(this).val("");
    }
});
$('#tblArticulosMiscelaneosNueva').on('change','input#input-precioAM',function (e) {
    e.preventDefault();
    if($(this).val() == '' || $(this).val() < 0){
        $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
    }

    $(this).val(parseFloat(this.value).toFixed(CANTIDAD_DECIMALES));
    if ($("#sel-proveedor").val() != ""){

        var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
        var fila = $(this).closest('tr');
        var index = tabla.row(fila).index();

        var datos = tabla.row(fila).data();
        var precio = 'input-precioAM';
        var cantidad = 'input-cantidadAM';
        var descuento = 'input-descuentoAM';
        var referenciaId = datos['ID_AUX'];
        var Cantidad_anterior = datos['CANTIDAD'];
        var Precio_anterior = datos['PRECIO'];
        var Descuento_anterior = datos['DESCUENTO'];
        var IVA_anterior = datos['IVA'];
        var ID_IVA_anterior = datos['ID_IVA'];
        var precioNuevo = $(this).val();

        /* if(registroNuevo == 1){

            bootbox.dialog({
                message: "¿Estas seguro de modificar el precio?.",
                title: "Ordenes de Compra",
                buttons: {
                    success: {
                        label: "Si",
                        className: "btn-success",
                        callback: function () {
                            $.ajax({
                                type: "POST",
                                async: false,
                                data: {
                                    detalleId: referenciaId
                                    ,precio: precioNuevo
                                    ,cantidad: Cantidad_anterior
                                    ,descuento: Descuento_anterior
                                    ,iva: IVA_anterior
                                    ,idIva: ID_IVA_anterior
                                },
                                dataType: "json",
                                url: "compras/editarPartida",
                                success: function () {
                                    //delete JSON_PARTIDA[referenciaId];
                                    datos['PRECIO'] = precioNuevo;
                                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                                    calculaTotalOrdenCompra();
                                    //calculaTipoCambio();
                                    if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                                        PartidaResumenAE(index);
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
                    },
                    default: {
                        label: "No",
                        className: "btn-default m-r-5 m-b-5",
                        callback: function () {
                            tabla.row(fila).nodes(fila,COL_PRECIO).to$().find('input#' + precio).val(parseFloat(Precio_anterior).toFixed(CANTIDAD_DECIMALES));
                        }
                    }
                }
            });

        }
        else{ */
             if (datos['NOMBRE_ARTICULO'] != ""){
                    datos['PRECIO'] = $(this).val();
                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                    calculaTotalOrdenCompra();
                    //calculaTipoCambio();
                    // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                    //     PartidaResumenAM(index);
                    // }
            }
            else{
                bootbox.dialog({
                    message: "No se ha capturado articulo, captura para continuar.",
                    title: "Ordenes de Compra",
                    buttons: {
                        success: {
                            label: "Si",
                            className: "btn-success"
                        }
                    }
                });
                $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
            }
        //}

    }
    else{
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
        $(this).val("");
    }
});

$('#tblArticulosMiscelaneosNueva').on('change','input#input-descuentoAM',function (e) {

    if($(this).val() == '' || $(this).val() < 0){
        $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
    }

    if ($(this).val() >= 100){
        $(this).val(parseFloat('100.00').toFixed(CANTIDAD_DECIMALES));
    }

    $(this).val(parseFloat(this.value).toFixed(CANTIDAD_DECIMALES));
    if ($("#sel-proveedor").val() != ""){

        var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
        var fila = $(this).closest('tr');
        var index = tabla.row(fila).index();

        var datos = tabla.row(fila).data();
        var precio = 'input-precioAM';
        var cantidad = 'input-cantidadAM';
        var descuento = 'input-descuentoAM';
        var referenciaId = datos['ID_AUX'];
        var Cantidad_anterior = datos['CANTIDAD'];
        var Precio_anterior = datos['PRECIO'];
        var Descuento_anterior = datos['DESCUENTO'];
        var IVA_anterior = datos['IVA'];
        var ID_IVA_anterior = datos['ID_IVA'];
        var descuentoNuevo = $(this).val();

        /* if(registroNuevo == 1){

            bootbox.dialog({
                message: "¿Estas seguro de modificar el descuento?.",
                title: "Ordenes de Compra",
                buttons: {
                    success: {
                        label: "Si",
                        className: "btn-success",
                        callback: function () {
                            $.ajax({
                                type: "POST",
                                async: false,
                                data: {
                                    detalleId: referenciaId
                                    ,precio: Precio_anterior
                                    ,cantidad: Cantidad_anterior
                                    ,descuento: descuentoNuevo
                                    ,iva: IVA_anterior
                                    ,idIva: ID_IVA_anterior
                                },
                                dataType: "json",
                                url: "compras/editarPartida",
                                success: function () {
                                    //delete JSON_PARTIDA[referenciaId];
                                    datos['DESCUENTO'] = descuentoNuevo;
                                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                                    calculaTotalOrdenCompra();
                                    //calculaTipoCambio();
                                    if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                                        PartidaResumenAE(index);
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
                    },
                    default: {
                        label: "No",
                        className: "btn-default m-r-5 m-b-5",
                        callback: function () {
                            tabla.row(fila).nodes(fila,COL_DESCUENTO).to$().find('input#' + descuento).val(parseFloat(Descuento_anterior).toFixed(CANTIDAD_DECIMALES));
                        }
                    }
                }
            });

        }
        else{ */
            if (datos['NOMBRE_ARTICULO'] != ""){    
                datos['DESCUENTO'] = $(this).val();
                RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                calculaTotalOrdenCompra();
                //calculaTipoCambio();
                // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                //     PartidaResumenAM(index);
                // }
            }
            else{
                bootbox.dialog({
                    message: "No se ha capturado articulo, captura para continuar.",
                    title: "Ordenes de Compra",
                    buttons: {
                        success: {
                            label: "Si",
                            className: "btn-success"
                        }
                    }
                });
                $(this).val(parseFloat('0.00').toFixed(CANTIDAD_DECIMALES));
            }
    
        //}
    }
    else{
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
        $(this).val("");
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
        var iva = 'cboIVAAM';
        var referenciaId = datos['ID_AUX'];
        var Cantidad_anterior = datos['CANTIDAD'];
        var Precio_anterior = datos['PRECIO'];
        var Descuento_anterior = datos['DESCUENTO'];
        var IVA_anterior = datos['IVA'];
        var ID_IVA_anterior = datos['ID_IVA'];
        var id_iva_nuevo = $(this).val();
        var iva_nuevo = $(this).val() == '' ? '0' : $(this).find('option:selected').text();

//        if(registroNuevo == 1){
        if(false){

            bootbox.dialog({
                message: "¿Estas seguro de modificar el IVA?.",
                title: "Ordenes de Compra",
                buttons: {
                    success: {
                        label: "Si",
                        className: "btn-success",
                        callback: function () {
                            $.ajax({
                                type: "POST",
                                async: false,
                                data: {
                                    detalleId: referenciaId
                                    ,precio: Precio_anterior
                                    ,cantidad: Cantidad_anterior
                                    ,descuento: Descuento_anterior
                                    ,iva: iva_nuevo
                                    ,idIva: id_iva_nuevo
                                },
                                dataType: "json",
                                url: "compras/editarPartida",
                                success: function () {
                                    //delete JSON_PARTIDA[referenciaId];
                                    datos["ID_IVA"] = id_iva_nuevo;
                                    datos["IVA"] = iva_nuevo;
                                    RealizaCalculos(fila, tabla, precio, cantidad, descuento);
                                    calculaTotalOrdenCompra();
                                    //calculaTipoCambio();
                                    if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
                                        PartidaResumenAE(index);
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
                    },
                    default: {
                        label: "No",
                        className: "btn-default m-r-5 m-b-5",
                        callback: function () {
                            tabla.row(fila).nodes(fila,COL_IVA).to$().find('select#'+iva).val(ID_IVA_anterior);
                        }
                    }
                }
            });

        }
        else{

            datos["ID_IVA"] = id_iva_nuevo;
            datos["IVA"] = iva_nuevo;

            RealizaCalculos(fila, tabla, precio, cantidad, descuento);
            calculaTotalOrdenCompra();
            //calculaTipoCambio();
            // if(datos['CANTIDAD'] != "" && datos['PRECIO'] != ""){
            //     PartidaResumenAE(index);
            // }

        }

    }
});

$('#tblArticulosMiscelaneosNueva').on('click', 'button#boton-eliminarAM', function (e) {
    e.preventDefault();

    var tabla = $('#tblArticulosMiscelaneosNueva').DataTable();
    var fila = $(this).closest('tr');
    var datos = tabla.row(fila).data();
    var index = tabla.row(fila).index();
    var id = datos['ID_PARTIDA'];
    if(id == ""){
        tabla.row(fila).remove().draw(false);
    }
    else{
        if (registroNuevo == 1){
            eliminarPartidaArtMisc(fila);
        }
    }
    calculaTotalOrdenCompra();
    actualizaLineaPartidaAM();
    //calculaTipoCambio();
   //PartidaResumenAM();
});

}  //fin js_iniciador               
function val_btn(val) { 

        $('#btn_enviar').attr('data-operacion', val);                                                     
} 
                   