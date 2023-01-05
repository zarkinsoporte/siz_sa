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
        reloadOrdenesImpresion(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
    });
    
    document.onkeyup = function(e) {
        if (e.shiftKey && e.which == 112) {
            var namefile= 'RG_'+$('#btn_pdf').attr('ayudapdf')+'.pdf';
            //console.log(namefile)
            $.ajax({
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

    function click_impresion() {
        var ordvta = tabla_impresion.rows('.selected').data();
        //var ordvtac = table.rows('.selected').node();
        //console.log(ordvtac[0])
        var ops = '';
        var registros = ordvta == null ? 0 : ordvta.length;
        for(var i=0; i < registros; i++){
            if (i == registros - 1) {
                ops += ordvta[i].OP;
            } else {
                ops += ordvta[i].OP + ",";
            }
            //console.log(ordvta[i]);         
        }
        
        if(registros > 0){
                $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                "_token": "{{ csrf_token() }}",
                ordenes: ops,
            
                },
                //url: '{!! route('impresionOP_empaque') !!}',
                url: routeapp + "",
                beforeSend: function() {
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
                complete: function() {
                    //reloadOrdenesImpresion();
                    setTimeout($.unblockUI, 1500);
                },
                success: function(data){   
                    if (data.mensajeErrr.includes('Error')) {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>Error al generar el PDF"+data.mensajeErrr+"</div>",
                            buttons: {
                            success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                            }
                            }
                            }).find('.modal-content').css({'font-size': '14px'} );
                    }else{
                        if (data.mensajeErrr.includes('SAP')) {
                            bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>Error, "+data.mensajeErrr+"</div>",
                            buttons: {
                            success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                            }
                            }
                            }).find('.modal-content').css({'font-size': '14px'} );
                        }
                        window.open('{{url()}}'+data.file,"_blank");
                    }
                }
                }); 
        }else{
              bootbox.dialog({
                title: "Mensaje",
                message: "<div class='alert alert-danger m-b-0'>No hay registros seleccionados.</div>",
                buttons: {
                success: {
                label: "Ok",
                className: "btn-success m-r-5 m-b-5"
                }
                }
                }).find('.modal-content').css({'font-size': '14px'} );
        }           
    }

// FIN LIBERACION
// INICIO IMPRESION
var tabla_impresion = $("#tabla_impresion").DataTable({
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

                        //return 
                        //'<button type="button" class="btn btn-primary" id="btnEditar"> <span class="glyphicon glyphicon-pencil"></span> </button>'
                        //+ '<button type="button" class="btn btn-danger" id="btnEliminar"> <span class="glyphicon glyphicon-trash"></span></button>'
                        return '' + '<button type="button" class="btn btn-danger btn-outline-danger" style="" id="boton-pdf"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>';

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

$('#tabla_impresion thead tr').clone(true).appendTo( '#tabla_impresion thead' );
$('#tabla_impresion thead tr:eq(1) th').each( function (i) {
    var title = $(this).text();
    $(this).html( '<input style="color: black;"  type="text" placeholder="Filtro '+title+'" />' );

    $( 'input', this ).on( 'keyup change', function () {       
            
            if ( tabla_impresion.column(i).search() !== this.value ) {
                tabla_impresion
                    .column(i)
                    .search(this.value, true, false)
                    
                    .draw();
            } 
                
    } );    
} );    
//tabla_impresion.columns.adjust().draw();
$('#tabla_impresion tbody').on( 'click', 'tr', function () {
} );

$('#tabla_impresion').on( 'click', 'button#boton-pdf', function (e) {
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
    var tblOC = $('#tabla_impresion').DataTable();
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
 
//reloadOrdenesImpresion();
function reloadOrdenesImpresion(fi, ff){
    
    $("#tabla_impresion").DataTable().clear().draw();
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
                $("#tabla_impresion").dataTable().fnAddData(data.data);           
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
                $("#tabla_impresion").DataTable().clear().draw();
                $("#tabla_impresion").dataTable().fnAddData((data.data));
                
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
$('#boton-mostrar').on('click', function(e) {
    //getop_empaque();
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
    window.onload = function () { 
        tabla_impresion.columns.adjust().draw();     
    }
}  //fin js_iniciador               
function val_btn(val) { 

        $('#btn_enviar').attr('data-operacion', val);                                                     
} 
                   