function js_iniciador() {
    $(".toggle").bootstrapSwitch();
    $("[data-toggle=\"tooltip\"]").tooltip();
    $(".boot-select").selectpicker();
    $(".dropdown-toggle").dropdown();
    setTimeout(function() {
        $("#infoMessage").fadeOut("fast");
    }, 5000);
    
    $("#sidebarCollapse").on("click", function() {
        $("#sidebar").toggleClass("active"); 
        $("#page-wrapper").toggleClass("content"); 
        $(this).toggleClass("active"); 
    });
    $("#sidebar").toggleClass("active"); 
    $("#page-wrapper").toggleClass("content"); 
    $(this).toggleClass("active"); 
    
    var inspecciones = [];
    var dataTable = null;
    
    // Establecer fechas por defecto (desde ayer hasta hoy)
    var fechaHasta = new Date();
    var fechaDesde = new Date();
    fechaDesde.setDate(fechaDesde.getDate() - 1);// Restar un día

    $("#filtro_fecha_hasta").val(fechaHasta.toISOString().split('T')[0]);
    $("#filtro_fecha_desde").val(fechaDesde.toISOString().split('T')[0]);
    
    // Función para cargar la tabla con filtros
    function cargarInspecciones() {
        var fechaDesde = $("#filtro_fecha_desde").val();
        var fechaHasta = $("#filtro_fecha_hasta").val();
        
        // Validar fechas
        if (fechaDesde && fechaHasta && new Date(fechaDesde) > new Date(fechaHasta)) {
            swal({
                title: "Error",
                text: "La fecha desde no puede ser mayor a la fecha hasta",
                type: "error",
                confirmButtonText: "Aceptar"
            });
            return;
        }
        
        // Mostrar blockUI
        $.blockUI({
            message: "<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class=\"fa fa-spin fa-spinner\"></i></h3>",
            css: {
                border: "none",
                padding: "16px",
                width: "50%",
                top: "40%",
                left: "30%",
                backgroundColor: "#fefefe",
                "-webkit-border-radius": "10px",
                "-moz-border-radius": "10px",
                opacity: .7,
                color: "#000000",
                baseZ: 2000
            }
        });
        
        $.ajax({
            url: routeapp + "/home/rep-01-inspeccion-material/buscar",
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $("meta[name=\"csrf-token\"]").attr("content")
            },
            data: {
                fecha_desde: fechaDesde,
                fecha_hasta: fechaHasta
            },
            success: function(data) {
                inspecciones = data;
                renderTabla();
                $.unblockUI();
            },
            error: function(xhr) {
                $.unblockUI();
                var mensaje = "Error al cargar los datos. Intente nuevamente.";
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    mensaje = xhr.responseJSON.error;
                }
                swal({
                    title: "Error",
                    text: mensaje,
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            }
        });
    }
    
    // Renderizar tabla
    function renderTabla() {
        // Verificar si DataTable ya está inicializado y destruirlo
        if ($.fn.DataTable.isDataTable('#tabla_reporte')) {
            $('#tabla_reporte').DataTable().destroy();
        }
        
        // Limpiar completamente la tabla
        $("#tabla_reporte tbody").empty();
        
        var tbody = "";
        
        inspecciones.forEach(function(inspeccion) {
            
            
            // Formatear porcentaje
            var porcentaje = "";
            if (inspeccion.PORC !== null && inspeccion.PORC !== undefined) {
                porcentaje = parseFloat(inspeccion.PORC).toFixed(2) + "%";
            } else {
                porcentaje = "0.00%";
            }
            
            tbody += "<tr>" +
                "<td>" + (inspeccion.ID || "") + "</td>" +
                "<td>" + inspeccion.FE_REV + "</td>" +
                "<td style=\"text-align: left;\">" + (inspeccion.PROVEEDOR || "N/A") + "</td>" +
                "<td>" + (inspeccion.CODIGO || "") + "</td>" +
                "<td style=\"text-align: left;\">" + (inspeccion.MATERIAL || "") + "</td>" +
                "<td>" + (inspeccion.UDM || "") + "</td>" +
                "<td>" + (parseFloat(inspeccion.RECIBIDO) || 0).toFixed(2) + "</td>" +
                "<td>" + (parseFloat(inspeccion.REVISADA) || 0).toFixed(2) + "</td>" +
                "<td>" + (parseFloat(inspeccion.ACEPTADA) || 0).toFixed(2) + "</td>" +
                "<td>" + (parseFloat(inspeccion.RECHAZADA) || 0).toFixed(2) + "</td>" +
                "<td>" + porcentaje + "</td>" +
                "<td>" + (inspeccion.INSPECTOR || "") + "</td>" +
                "<td>" + (inspeccion.FACTURA || "") + "</td>" +
                "<td style=\"text-align: left;\">" + (inspeccion.MOT_RECHAZO || "") + "</td>" +
                "<td>" + (inspeccion.GRUPPLAN || "") + "</td>" +
            "</tr>";
        });
        
        $("#tabla_reporte tbody").html(tbody);
        
        // Inicializar DataTable solo si no está ya inicializado
        if (!$.fn.DataTable.isDataTable('#tabla_reporte')) {
            dataTable = $("#tabla_reporte").DataTable({
                order: [[1, "desc"]], // Ordenar por fecha de revisión descendente
                language: {
                    "url": assetapp + "assets/lang/Spanish.json"
                },
                paging: false, // Deshabilitar paginación para mostrar todos los registros
                info: true, // Mostrar información de registros
                scrollX: true,
                scrollY: '60vh', // Altura fija con scroll vertical
                scrollCollapse: true // Colapsar scroll si no hay suficientes datos
            });
        }
    }
    
    // Evento para el botón buscar
    $("#btn_buscar").off("click").on("click", function() {
        cargarInspecciones();
    });
    
    // Evento para el botón imprimir PDF
    $("#btn_imprimir_pdf").off("click").on("click", function() {
        var fechaDesde = $("#filtro_fecha_desde").val();
        var fechaHasta = $("#filtro_fecha_hasta").val();
        
        // Validar fechas
        if (fechaDesde && fechaHasta && new Date(fechaDesde) > new Date(fechaHasta)) {
            swal({
                title: "Error",
                text: "La fecha desde no puede ser mayor a la fecha hasta",
                type: "error",
                confirmButtonText: "Aceptar"
            });
            return;
        }
        
        // Construir URL con parámetros
        var url = routeapp + "/home/rep-01-inspeccion-material/pdf?fecha_desde=" + encodeURIComponent(fechaDesde) + "&fecha_hasta=" + encodeURIComponent(fechaHasta);
        
        // Abrir PDF en nueva pestaña
        window.open(url, "_blank");
    });
    
    // Cargar datos al iniciar
    cargarInspecciones();
}
