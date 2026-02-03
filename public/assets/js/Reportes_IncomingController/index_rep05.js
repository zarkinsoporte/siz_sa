function js_iniciador() {
    $(".toggle").bootstrapSwitch();
    $("[data-toggle=\"tooltip\"]").tooltip();
    $(".boot-select").selectpicker();
    $(".dropdown-toggle").dropdown();

    setTimeout(function () {
        $("#infoMessage").fadeOut("fast");
    }, 5000);
    
    // Inicializar datepickers para fechas
    $("#fechaDesde").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        language: "es",
        todayHighlight: true
    });
    
    $("#fechaHasta").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        language: "es",
        todayHighlight: true
    });

    $("#sidebarCollapse").on("click", function () {
        $("#sidebar").toggleClass("active");
        $("#page-wrapper").toggleClass("content");
        $(this).toggleClass("active");
    });
    $("#sidebar").toggleClass("active");
    $("#page-wrapper").toggleClass("content");
    $(this).toggleClass("active");

    var dtDetalle = null;

    function fmtPct(v) {
        if (v === null || v === undefined || v === '' || parseFloat(v) === 0) return '-';
        var num = parseFloat(v);
        if (isNaN(num) || num === 0) return '-';
        // Los valores vienen como decimales (0.9871 = 98.71%), multiplicar por 100
        // Permitir valores mayores a 1.0 (100%) ya que pueden existir promedios superiores
        return (num * 100).toFixed(2) + '%';
    }

    function fmtNum(v) {
        if (v === null || v === undefined || v === '') return '0';
        return Number(v).toLocaleString('es-MX', { maximumFractionDigits: 2 });
    }

    // Variable global para la gráfica
    var graficaAceptadoRechazado = null;
    
    // Función para renderizar gráfica de pastel
    function renderGraficaAceptadoRechazado(totalAceptado, totalRechazado, porcAceptado, porcRechazado) {
        var container = $("#graficaAceptadoRechazado");
        container.empty();
        
        if (totalAceptado === 0 && totalRechazado === 0) {
            container.html('<p class="text-center text-muted">No hay datos disponibles</p>');
            return;
        }
        
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Estado', 'Cantidad'],
                ['Aceptado', totalAceptado],
                ['Rechazado', totalRechazado]
            ]);

            var options = {
                title: 'Distribución Aceptado / Rechazado',
                pieHole: 0.4,
                colors: ['#28a745', '#dc3545'],
                legend: {
                    position: 'bottom'
                },
                pieSliceText: 'percentage',
                tooltip: {
                    text: 'value'
                }
            };

            graficaAceptadoRechazado = new google.visualization.PieChart(container[0]);
            graficaAceptadoRechazado.draw(data, options);
        }
        
        // Verificar si Google Charts está disponible y cargado
        if (typeof google === 'undefined' || typeof google.charts === 'undefined') {
            container.html('<p class="text-center text-muted">Cargando gráfica...</p>');
            return;
        }
        
        // Verificar si visualization está disponible
        if (typeof google.visualization === 'undefined') {
            // Esperar a que se cargue
            google.charts.setOnLoadCallback(drawChart);
        } else {
            // Ya está cargado, dibujar directamente
            drawChart();
        }
    }

    function renderDetalle(detalle) {
        if (dtDetalle) {
            dtDetalle.destroy();
            dtDetalle = null;
        }
        
        // Limpiar completamente la tabla
        $("#tablaDetalle").removeClass("dataTable");
        $("#tablaDetalle").removeData();
        $("#tablaDetalle tbody").empty();

        var rows = [];
        if (detalle && detalle.length > 0) {
            detalle.forEach(function (d) {
                // Generar el contenido de la columna Rechazo
                var rechazoCell = '';
                if (d.RECHAZO && d.RECHAZO > 0 && d.INC_ID) {
                    // Si hay rechazo, mostrar botón con icono PDF
                    var urlPdf = routeapp + '/home/RECHAZOS/pdf/' + d.INC_ID;
                    rechazoCell = '<a href="' + urlPdf + '" target="_blank" class="btn btn-danger btn-sm" title="Ver PDF del Rechazo">' +
                                 '<i class="fa fa-file-pdf-o"></i></a>';
                } else {
                    // Si no hay rechazo, mostrar guión
                    rechazoCell = '-';
                }
                
                rows.push([
                    rechazoCell,  // Rechazo (botón o guión)
                    d.NE,
                    d.COD_MAT,
                    d.MATERIAL,
                    d.UDM,
                    fmtNum(d.RECIBIDO),
                    fmtNum(d.RECHAZADA)
                ]);
            });
        }

        dtDetalle = $("#tablaDetalle").DataTable({
            data: rows,
            pageLength: 25,
            order: [[1, 'desc']],
            autoWidth: false,
            columnDefs: [
                { 
                    width: "80px", 
                    targets: [0],
                    className: "text-center"  // Centrar el botón
                }, // Rechazo
                { width: "100px", targets: [1] }, // NE
                { width: "120px", targets: [2] }, // Código Material
                { width: "250px", targets: [3] }, // Material
                { width: "80px", targets: [4] }, // UDM
                { width: "100px", targets: [5] }, // Recibido
                { width: "100px", targets: [6] }  // Rechazada
            ],
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": { "sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior" }
            },
            scrollX: true,
            scrollCollapse: true
        });
    }

    function cargar() {
        var fechaDesde = $("#fechaDesde").val();
        var fechaHasta = $("#fechaHasta").val();
        var codProv = ($("#codProv").val() || '').trim();

        if (!codProv) {
            swal({ title: "Error", text: "Capture el código de proveedor", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        if (!fechaDesde || !fechaHasta) {
            swal({ title: "Error", text: "Capture las fechas", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        // Validar que fecha desde sea menor o igual a fecha hasta
        if (new Date(fechaDesde) > new Date(fechaHasta)) {
            swal({ title: "Error", text: "La fecha desde debe ser menor o igual a la fecha hasta", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        swal({ title: "Cargando...", text: "Por favor espere", type: "info", showConfirmButton: false, allowOutsideClick: false });

        $.ajax({
            url: routeapp + "/home/rep-05-historial-proveedor/buscar",
            type: 'POST',
            data: { 
                _token: csrfToken, 
                fecha_desde: fechaDesde, 
                fecha_hasta: fechaHasta, 
                cod_prov: codProv 
            },
            success: function (resp) {
                swal.close();
                if (!resp.success) {
                    swal({ title: "Error", text: resp.msg || "Error al cargar", type: "error", confirmButtonText: "Aceptar" });
                    return;
                }

                $("#txtProveedor").text((resp.codProv || codProv) + (resp.proveedorNombre ? (' - ' + resp.proveedorNombre) : ''));
                $("#txtPeriodo").text('Del ' + resp.fechaIS + ' al ' + resp.fechaFS);

                // Actualizar resumen
                $("#totalRecibido").text(fmtNum(resp.totalRecibido || 0));
                $("#totalAceptado").text(fmtNum(resp.totalAceptado || 0));
                $("#totalRechazado").text(fmtNum(resp.totalRechazado || 0));
                $("#totalPorRevisar").text(fmtNum(resp.totalPorRevisar || 0));
                $("#porcAceptado").text((resp.porcAceptado || 0).toFixed(2) + '%');
                $("#porcRechazado").text((resp.porcRechazado || 0).toFixed(2) + '%');
                $("#porcPorRevisar").text((resp.porcPorRevisar || 0).toFixed(2) + '%');
                
                // Renderizar gráfica
                renderGraficaAceptadoRechazado(
                    parseFloat(resp.totalAceptado || 0),
                    parseFloat(resp.totalRechazado || 0),
                    parseFloat(resp.porcAceptado || 0),
                    parseFloat(resp.porcRechazado || 0)
                );
                
                renderDetalle(resp.detalle);
            },
            error: function (xhr) {
                swal.close();
                swal({ title: "Error", text: "Error al cargar: " + (xhr.responseText || xhr.statusText), type: "error", confirmButtonText: "Aceptar" });
            }
        });
    }

    $("#btnBuscar").on('click', cargar);

    $("#btnImprimirPDF").on('click', function () {
        var fechaDesde = $("#fechaDesde").val();
        var fechaHasta = $("#fechaHasta").val();
        var codProv = ($("#codProv").val() || '').trim();
        
        if (!codProv) {
            swal({ title: "Error", text: "Capture el código de proveedor", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        if (!fechaDesde || !fechaHasta) {
            swal({ title: "Error", text: "Capture las fechas", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        // Validar que fecha desde sea menor o igual a fecha hasta
        if (new Date(fechaDesde) > new Date(fechaHasta)) {
            swal({ title: "Error", text: "La fecha desde debe ser menor o igual a la fecha hasta", type: "error", confirmButtonText: "Aceptar" });
            return;
        }
        
        var url = routeapp + "/home/rep-05-historial-proveedor/pdf?fecha_desde=" + encodeURIComponent(fechaDesde) + "&fecha_hasta=" + encodeURIComponent(fechaHasta) + "&cod_prov=" + encodeURIComponent(codProv);
        window.open(url, '_blank');
    });
}

