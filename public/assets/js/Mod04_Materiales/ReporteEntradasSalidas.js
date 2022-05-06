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
    $("#sidebar").toggleClass("active");
    $("#page-wrapper").toggleClass("content");
    $(this).toggleClass("active");
    var start = new Date(a_fstart[0], a_fstart[1] - 1, a_fstart[2])
    //var end = new Date(a_fend[0], a_fend[1] - 1, a_fend[2])
    var ignore = true;
    $('#sel_tipomat').selectpicker({
        noneSelectedText: 'Selecciona una opción',
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
    $('#sel_almacen').selectpicker({
        noneSelectedText: 'Selecciona una opción',
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
    $('#sel_articulos').selectpicker({
        noneSelectedText: 'Selecciona una opción',
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
    $("#fstart").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true
    }).on("change", function () {
        var selected = $(this).val();
        //var d_start = $('#fstart').datepicker('getDate');
        //var d_end = $('#fend').datepicker('getDate');

        //console.log(selected, d_end);
        $('#fend').datepicker('setStartDate', selected);
        //if (d_start > d_end && !ignore) {
            //$('#fend').datepicker('setDate', selected);
        //}
    });

    $("#fend").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true,
    }).on("change", function () {
        if (!ignore) {
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
            comboboxTipoMat_reload();
            comboboxArticulos_reload();
            setTimeout($.unblockUI, 2000);
        }
    });
    $('#fstart').datepicker('setDate', start);
    $('#fend').datepicker('setStartDate', start);
    ignore = false;
    //$('#fend').datepicker('setDate', end);    
    let todos_articulos = 0;
    let todos_tipomat = 0;
    

    function comboboxTipoMat_reload() {
        var options = [];
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                "_token": tokenapp,
                fstart: $('#fstart').val(),
                fend: $('#fend').val()
            },
            url: routeapp + "entradasSalidas_combobox_tipoMat",
            beforeSend: function () {

            },
            complete: function () {

                //$("#tcompras").DataTable().clear().draw();
            },
            success: function (data) {
                options = [];

                $("#sel_tipomat").empty();
                for (var i = 0; i < data.tipomat.length; i++) {
                    options.push('<option value="' + data.tipomat[i]['tipomaterial'] + '">' +
                        data.tipomat[i]['tipomaterial'] + '</option>');
                }
                if (data.tipomat.length <= 0) {
                    bootbox.dialog({
                        title: "Mensaje",
                        message: "<div class='alert alert-danger m-b-0'>No hay artículos dentro del intervalo de fechas</div>",
                        buttons: {
                            success: {
                                label: "Ok",
                                className: "btn-success m-r-5 m-b-5"
                            }
                        }
                    }).find('.modal-content').css({
                        'font-size': '14px'
                    });
                } else {
                    $('#sel_tipomat').append(options);
                    $('#sel_tipomat option').attr("selected", "selected");
                }
                todos_tipomat = data.tipomat.length;
                $('#sel_tipomat').selectpicker('refresh');

            }
        });
    }
    $("#sel_tipomat").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
        
        comboboxArticulos_reload();

    });
    $("#sel_almacen").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
        
        

    });
    function comboboxArticulos_reload() {
        
        var registros = $('#sel_tipomat').val() == null ? 0 : $('#sel_tipomat').val().length;
        var cadena = "";
        for (var x = 0; x < registros; x++) {
            if (x == registros - 1) {
                cadena += $($('#sel_tipomat option:selected')[x]).val();
            } else {
                cadena += $($('#sel_tipomat option:selected')[x]).val() + "','";
            }
        }
         
        var options = [];
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                "_token": tokenapp,
                fstart: $('#fstart').val(),
                fend: $('#fend').val(),
                tipomat: cadena,
                todos: (todos_tipomat == registros) ? true : false
            },
            url: routeapp + "entradasSalidas_combobox_articulos",
            beforeSend: function () {

            },
            complete: function () {

                //$("#tcompras").DataTable().clear().draw();
            },
            success: function (data) {
                options = [];

                $("#sel_articulos").empty();
                for (var i = 0; i < data.oitms.length; i++) {
                    options.push('<option value="' + data.oitms[i]['ItemCode'] + '">' +
                        data.oitms[i]['descr'] + '</option>');
                }
                if (data.oitms.length <= 0) {
                    
                } else {
                    $('#sel_articulos').append(options);
                    $('#sel_articulos option').attr("selected", "selected");
                }
                todos_articulos = data.oitms.length;
                $('#sel_articulos').selectpicker('refresh');

            }
        });
    }

    $('#tentradas thead tr').clone(true).appendTo('#tentradas thead');

    $('#tentradas thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        $(this).html('<input style="color: black;" type="text" placeholder="Filtro ' + title + '" />');

        $('input', this).on('keyup change', function () {

            if (table.column(i).search() !== this.value) {
                table
                    .column(i)
                    .search(this.value, true, false)
                    .draw();
            }

        });
    });
    
    var wrapper = $('#wrapper');
    var resizeStartHeight = wrapper.height();
    var height = (resizeStartHeight * 130) / 100;
    if (height < 200) {
        height = 200;
    }
    var table = $('#tentradas').DataTable({
        "order": [
            [1, "desc"],
            [0, "asc"],
            [2, "asc"]
        ],
        dom: "<'row'<'col-sm-9'B><'col-sm-3'l>>" + 'rtip',
        "pageLength": 100,
        "lengthMenu": [
            [100, 50, 25, -1],
            [100, 50, 25, "Todo"]
        ],
        orderCellsTop: true,
        scrollY: height,
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        fixedColumns: true,
        processing: true,
        deferRender: true,
        ajax: {
            url: routeapp + 'datatables_ioWhs',
            "type" : "POST",
            data: function (d) {
                d._token= tokenapp,
                d.fi = $('#fstart').val(),
                d.ff = $('#fend').val(),
                d.almacenes = getSel('sel_almacen'),
                d.todosalmacenes = todos_almacen,
                d.tipomat = getSel('sel_tipomat'),
                d.todostipomat = todos_tipomat,
                d.articulos = getSel('sel_articulos'),
                d.todosarticulos = todos_articulos
                
            }
        },
        columns: [
            // { data: 'action', name: 'action', orderable: false, searchable: false}
            {
                data: 'BASE_REF'
            },
            {
                data: 'DocDate',
                render: function (data) {
                    var d = new Date(data);
                    return moment(d).format("DD-MM-YYYY");
                }
            },
            {
                data: 'CreateDate',
                render: function (data) {
                    var d = new Date(data);
                    return moment(d).format("DD-MM-YYYY");
                }
            },
            {
                data: 'JrnlMemo'
            },

            {
                data: 'ItemCode',
                name: 'ItemCode'
            },
            {
                data: 'Dscription',
                name: 'Dscription'
            },
            {
                data: 'Movimiento',
                render: function (data) {
                    var val = new Intl.NumberFormat("es-MX", {
                        minimumFractionDigits: 2
                    }).format(data);
                    return val;
                }
            },
            {
                data: 'STDVAL',
                render: function (data) {
                    var val = new Intl.NumberFormat("es-MX", {
                        minimumFractionDigits: 2
                    }).format(data);
                    return val;
                }
            },
            {
                data: 'U_TipoMat'
            },

            {
                data: 'U_NAME'
            },
            {
                data: 'ALM_ORG'
            },
            {
                data: 'VSala',
                render: function (data) {
                    var val = new Intl.NumberFormat("es-MX", {
                        minimumFractionDigits: 2
                    }).format(data);
                    return val;
                }
            },


            {
                data: 'Comments'
            },

            {
                data: 'DocTime'
            },
        ],
        buttons: [{
                text: '<i class="fa fa-columns" aria-hidden="true"></i> Columna',
                className: "btn btn-primary",
                extend: 'colvis',
                postfixButtons: [{
                    text: 'Restaurar columnas',
                    extend: 'colvisRestore',
                }]
            },
            {
                text: '<i class="fa fa-copy" aria-hidden="true"></i> Copy',
                extend: 'copy',
                exportOptions: {
                    columns: ':visible',
                }
            },
            {
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                className: "btn-success",
                action: function (e, dt, node, config) {
                    var data = table.rows({
                        filter: 'applied'
                    }).data().toArray();
                    var json = JSON.stringify(data);
                    $.ajax({
                        type: 'POST',
                        url: routeapp + 'home/reporte/ajaxtosession/entradasysalidas',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function (params) {
                            $( "button:contains('Excel')").html('<i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i> Excel');
                        },
                        data: {
                            "_token": tokenapp,
                            "arr": json
                        },
                        success: function (data) {
                            window.location.href = routeapp + 'entradasysalidasXLS';
                        },
                        complete: function (params) {
                           setTimeout(function () {
                                $( "button:contains('Excel')").html('<span><i class="fa fa-file-excel-o"></i> Excel</span>');            
                            }, 2500);
                        }
                    });
                }
            },
            {
                text: '<i class="fa fa-file-pdf-o"></i> Pdf',
                className: "btn-danger",
                action: function (e, dt, node, config) {
                    var data = table.rows({
                        filter: 'applied'
                    }).data().toArray();
                    var json = JSON.stringify(data);
                    $.ajax({
                        type: 'POST',
                        url: routeapp + 'home/reporte/ajaxtosession/entradasysalidas',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "_token": tokenapp,
                            "arr": json
                        },
                        success: function (data) {
                            window.open(routeapp + 'entradasysalidasPDF', '_blank')
                        }
                    });
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json",
            buttons: {
                copyTitle: 'Copiar al portapapeles',
                copyKeys: 'Presiona <i>ctrl</i> + <i>C</i> para copiar o la tecla <i>Esc</i> para continuar.',
                copySuccess: {
                    _: '%d filas copiadas',
                    1: '1 fila copiada'
                }
            }
        },
        columnDefs: [

        ],
        "initComplete": function(settings, json) {
          
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api(),
                data;

            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                    i : 0;
            };

            // Total Cantidad
            pageTotal = api
                .column(6, {
                    page: 'current'
                })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var pageT = pageTotal.toLocaleString("es-MX", {
                minimumFractionDigits: 2
            })

            $(api.column(6).footer()).html(
                pageT
            );
            // Total Val Standar
            pageTotal = api
                .column(7, {
                    page: 'current'
                })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var pageT = pageTotal.toLocaleString("es-MX", {
                minimumFractionDigits: 2
            })

            $(api.column(7).footer()).html(
                '$ ' + pageT
            );
            // Total VS
            pageTotal = api
                .column(11, {
                    page: 'current'
                })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var pageT = pageTotal.toLocaleString("es-MX", {
                minimumFractionDigits: 2
            })

            $(api.column(11).footer()).html(
                pageT
            );
            // Update footer for VS
            //.toLocaleString("es-MX",{style:"currency", currency:"MXN"}) //example to format a number to Mexican Pesos
            //var n = 1234567.22
            //alert(n.toLocaleString("es-MX",{style:"currency", currency:"MXN"}))





        }
    });//end dattable
function getSel(id) {
    var registros = $('#'+id).val() == null ? 0 : $('#'+id).val().length;
    var cadena = "";
    for (var x = 0; x < registros; x++) {
        if (x == registros - 1) {
            cadena += $($('#'+id+' option:selected')[x]).val();
        } else {
            cadena += $($('#'+id+' option:selected')[x]).val() + "','";
        }
    }
    return cadena;
}
$('#boton-mostrar').on('click', function(e) {
    table.ajax.reload();
});

} //js_iniciador
document.onkeyup = function (e) {
    if (e.shiftKey && e.which == 112) {
        window.open(routeapp + "ayudas_pdf/AYM00_00.pdf", "_blank");
    }
}