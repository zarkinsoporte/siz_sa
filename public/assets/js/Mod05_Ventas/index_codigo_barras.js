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

    $('#tabla_index thead tr').clone(true).appendTo('#tabla_index thead');

    $('#tabla_index thead tr:eq(1) th').each(function (i) {
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
    console.log(resizeStartHeight)
    var height = (resizeStartHeight * 130) / 100;
    if (height < 200) {
        height = 200;
    }
    console.log(height)
    var table = $('#tabla_index').DataTable({
        "order": [
            [1, "desc"]
        ],
        dom: "<'row'<'col-sm-9'B><'col-sm-3'f>>" + 'rtip',
        select: true,
        orderCellsTop: true,
        scrollY: height,
        scrollX: true,
        scrollCollapse: true,
        paging: false,
        processing: true,
        deferRender: true,
        ajax: {
            url: routeapp + 'datatables_oitm_index_codigo_barras',
            "type" : "POST",
            data: function (d) {
                d._token= tokenapp
            }
        },
        columns: [
            {
                data: 'ItemCode'
            },
            {
                data: 'ItemName'
            },
            {
                data: 'codibarr'
            }
            // {
            //     data: 'codibarr_img',
            //     "render": function (data, type, row, meta) {
            //         return '<div>{!! $generator->output_image("svg", "ean-13-nopad", "'+data+'", []) !!}</div>';
            //     }
            // }
            
        ],
        buttons: [
            {
                text: '<i class="fa fa-file-pdf-o"></i> Pdf',
                className: "btn-danger",
                action: function (e, dt, node, config) {
                    var count = table.rows({
                        selected: true
                    }).count();
                    console.log(count)
                    if (count > 0) {
                        var data = table.rows({
                        selected: true
                        }).data().toArray();
                        
                        var json = JSON.stringify(data);
                        fileNamejs = '_SIZ CodigosBarra.Pdf';
                        $.ajax({
                            type: 'POST',
                            url: routeapp + 'home/pdf_codibarr',
                            data: {
                                "_token": tokenapp,
                                "data_t": json
                            },
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
                                window.open('pdf/' + data.fileName + '/'+ fileNamejs, '_blank');
                            
                            }
                        });
                    }else{
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>No ha seleccionado ningún código</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-success m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({
                            'font-size': '14px'
                        });
                    }
                    
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"        
        },
        columnDefs: [],
    
        "initComplete": function(settings, json) {
          
        }
    });//end dattable

$('#boton-mostrar').on('click', function(e) {
    table.ajax.reload();
});

}//js_iniciador
document.onkeyup = function (e) {
    if (e.shiftKey && e.which == 112) {
        window.open(routeapp + "ayudas_pdf/AYM00_00.pdf", "_blank");
    }
}