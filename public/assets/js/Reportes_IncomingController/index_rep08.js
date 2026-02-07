function js_iniciador() {
    $(".toggle").bootstrapSwitch();
    $("[data-toggle=\"tooltip\"]").tooltip();
    $(".boot-select").selectpicker();
    $(".dropdown-toggle").dropdown();

    setTimeout(function () {
        $("#infoMessage").fadeOut("fast");
    }, 5000);
    
    // Inicializar yearPicker
    $("#yearPicker").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years",
        autoclose: true,
        language: "es"
    });

    $("#sidebarCollapse").on("click", function () {
        $("#sidebar").toggleClass("active");
        $("#page-wrapper").toggleClass("content");
        $(this).toggleClass("active");
    });
    $("#sidebar").toggleClass("active");
    $("#page-wrapper").toggleClass("content");
    $(this).toggleClass("active");

    // Variable global para almacenar datos
    var datosGlobal = null;

    var areaClasses = {
        'CORTE': 'area-corte',
        'COSTURA': 'area-costura',
        'COJINERIA': 'area-cojineria',
        'TAPICERIA': 'area-tapiceria',
        'CARPINTERIA': 'area-carpinteria'
    };

    function renderDatos(datos) {
        var container = $("#containerMeses");
        container.empty();

        // Guardar datos globalmente para el modal
        datosGlobal = datos;

        var mesesOrden = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        var areasOrden = ['CORTE', 'COSTURA', 'COJINERIA', 'TAPICERIA', 'CARPINTERIA'];

        mesesOrden.forEach(function(mesKey) {
            if (!datos[mesKey]) return;

            var mesData = datos[mesKey];
            var html = '<div class="bloque-mes">';
            html += '<div class="mes-header" data-mes="' + mesKey + '" title="Click para ver detalle">' + mesData.nombre + '</div>';
            html += '<div class="mes-content">';

            areasOrden.forEach(function(areaNombre) {
                var areaClass = areaClasses[areaNombre] || '';
                var defectos = mesData.areas[areaNombre] || [];

                html += '<div class="area-block">';
                html += '<div class="area-header ' + areaClass + '">' + areaNombre + '</div>';

                if (defectos.length > 0) {
                    defectos.forEach(function(defecto, idx) {
                        html += '<div class="defecto-item">';
                        html += '<span class="defecto-nombre" title="' + defecto.defectivo + '">' + (idx + 1) + '. ' + defecto.defectivo + '</span>';
                        html += '<span class="defecto-conteo">' + defecto.conteo + '</span>';
                        html += '</div>';
                    });
                } else {
                    html += '<div class="no-data">Sin datos</div>';
                }

                html += '</div>';
            });

            html += '</div></div>';
            container.append(html);
        });

        if (container.children().length === 0) {
            container.html('<p class="text-center text-muted" style="width: 100%;">No hay datos disponibles para el año seleccionado</p>');
        }
    }

    // Función para mostrar el modal con detalle del mes
    function mostrarDetalleMes(mesKey) {
        if (!datosGlobal || !datosGlobal[mesKey]) {
            swal({ title: "Error", text: "No hay datos para este mes", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        var mesData = datosGlobal[mesKey];
        $("#modalDetalleMesLabel").text('Top 3 Defectos - ' + mesData.nombre);

        var areasOrden = ['CORTE', 'COSTURA', 'COJINERIA', 'TAPICERIA', 'CARPINTERIA'];
        var html = '<div class="row">';

        areasOrden.forEach(function(areaNombre, idx) {
            var areaClass = areaClasses[areaNombre] || '';
            var defectos = mesData.areas[areaNombre] || [];

            // Dos columnas por fila
            if (idx > 0 && idx % 2 === 0) {
                html += '</div><div class="row" style="margin-top: 15px;">';
            }

            html += '<div class="col-md-6">';
            html += '<div class="modal-area-block">';
            html += '<div class="modal-area-header ' + areaClass + '">' + areaNombre + '</div>';

            if (defectos.length > 0) {
                defectos.forEach(function(defecto, defIdx) {
                    html += '<div class="modal-defecto-item">';
                    html += '<span class="modal-defecto-nombre">' + (defIdx + 1) + '. ' + defecto.defectivo + '</span>';
                    html += '<span class="modal-defecto-conteo">' + defecto.conteo + '</span>';
                    html += '</div>';
                });
            } else {
                html += '<div class="modal-no-data">Sin datos para este mes</div>';
            }

            html += '</div></div>';
        });

        html += '</div>';

        $("#modalDetalleMesContenido").html(html);
        $("#modalDetalleMes").modal('show');
    }

    // Evento click en encabezados de mes
    $(document).on('click', '.mes-header', function() {
        var mesKey = $(this).data('mes');
        if (mesKey) {
            mostrarDetalleMes(mesKey);
        }
    });

    function cargar() {
        var ano = $("#yearPicker").val();

        if (!ano) {
            swal({ title: "Error", text: "Seleccione un año", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        swal({ title: "Cargando...", text: "Por favor espere", type: "info", showConfirmButton: false, allowOutsideClick: false });

        $.ajax({
            url: routeapp + "/home/rep-08-top-defectivos/buscar",
            type: 'POST',
            data: { 
                _token: csrfToken, 
                ano: ano
            },
            success: function (resp) {
                swal.close();
                if (!resp.success) {
                    swal({ title: "Error", text: resp.msg || "Error al cargar", type: "error", confirmButtonText: "Aceptar" });
                    return;
                }

                $("#txtAno").text(resp.ano);
                $("#txtPeriodo").text('Del ' + resp.fechaIS + ' al ' + resp.fechaFS);
                
                renderDatos(resp.datos);
            },
            error: function (xhr) {
                swal.close();
                swal({ title: "Error", text: "Error al cargar: " + (xhr.responseText || xhr.statusText), type: "error", confirmButtonText: "Aceptar" });
            }
        });
    }

    $("#btnBuscar").on('click', cargar);

    $("#btnImprimirPDF").on('click', function () {
        var ano = $("#yearPicker").val();
        
        if (!ano) {
            swal({ title: "Error", text: "Seleccione un año", type: "error", confirmButtonText: "Aceptar" });
            return;
        }
        
        var url = routeapp + "/home/rep-08-top-defectivos/pdf?ano=" + encodeURIComponent(ano);
        window.open(url, '_blank');
    });

    // Cargar datos automáticamente al iniciar
    cargar();
}
