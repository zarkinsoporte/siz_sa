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
    
    var evidencias = [];
    var dataTable = null;
    
    // Establecer fechas por defecto (3 meses atrás hasta hoy)
    var fechaHasta = new Date();
    var fechaDesde = new Date();
    fechaDesde.setMonth(fechaDesde.getMonth() - 3);
    
    $("#filtro_fecha_hasta").val(fechaHasta.toISOString().split('T')[0]);
    $("#filtro_fecha_desde").val(fechaDesde.toISOString().split('T')[0]);
    
    // Función para cargar la tabla de evidencias con filtros
    function cargarEvidencias() {
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
            url: routeapp + "/home/evidencia-cliente/buscar",
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $("meta[name=\"csrf-token\"]").attr("content")
            },
            data: {
                fecha_desde: fechaDesde,
                fecha_hasta: fechaHasta
            },
            success: function(data) {
                evidencias = data;
                renderTablaEvidencias();
                $.unblockUI();
            },
            error: function(xhr) {
                $.unblockUI();
                var mensaje = "Error al cargar los datos de evidencias. Intente nuevamente.";
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
    
    // Renderizar tabla de evidencias
    function renderTablaEvidencias() {
        // Verificar si DataTable ya está inicializado y destruirlo
        if ($.fn.DataTable.isDataTable('#tabla_evidencias')) {
            $('#tabla_evidencias').DataTable().destroy();
        }
        
        // Limpiar completamente la tabla
        $("#tabla_evidencias tbody").empty();
        
        var tbody = "";
        
        evidencias.forEach(function(evidencia) {
            var acciones = "<button class=\"btn btn-danger btn-sm btn-ver-pdf\" data-op=\"" + evidencia.OP + "\" title=\"Ver Reporte PDF Cliente\"><i class=\"fa fa-file-pdf-o\"></i> Cliente</button> " +
                          "<button class=\"btn btn-danger btn-sm btn-ver-pdf-interno\" data-op=\"" + evidencia.OP + "\" title=\"Ver Reporte PDF Interno\"><i class=\"fa fa-file-pdf-o\"></i> Interno</button>";
            
            // Solo mostrar botón de video si hay videos disponibles
            if (evidencia.tiene_video) {
                acciones += " <button class=\"btn btn-info btn-sm btn-ver-video\" data-op=\"" + evidencia.OP + "\" title=\"Ver Video de Evidencia\"><i class=\"fa fa-video-camera\"></i> Video</button>";
            }
            
            // Formatear fecha
            var fechaFinalizacion = "";
            if (evidencia.FechaFinalizacion) {
                var fecha = new Date(evidencia.FechaFinalizacion);
                fechaFinalizacion = fecha.getFullYear() + "/" + 
                    (fecha.getMonth() + 1).toString().padStart(2, '0') + "/" + 
                    fecha.getDate().toString().padStart(2, '0');
            }
            
            var cliente = evidencia.Cliente || "N/A";
            var pedido = evidencia.Pedido || "N/A";
            
            tbody += "<tr>" +
                "<td>" + evidencia.OP + "</td>" +
                "<td style=\"text-align: left;\">" + evidencia.CodigoArticulo + " - " + evidencia.NombreArticulo + "</td>" +
                "<td>" + pedido + "</td>" +
                "<td style=\"text-align: left;\">" + cliente + "</td>" +
                "<td>" + fechaFinalizacion + "</td>" +
                "<td>" + (parseFloat(evidencia.Cantidad) || 0).toFixed(2) + "</td>" +
                "<td>" + acciones + "</td>" +
            "</tr>";
        });
        
        $("#tabla_evidencias tbody").html(tbody);
        
        // Inicializar DataTable solo si no está ya inicializado
        if (!$.fn.DataTable.isDataTable('#tabla_evidencias')) {
            dataTable = $("#tabla_evidencias").DataTable({
                order: [[4, "desc"]], // Ordenar por fecha de finalización descendente
                language: {
                    "url": assetapp + "assets/lang/Spanish.json"
                },
                pageLength: 25,
                responsive: true
            });
        }
    }
    
    // Evento para el botón buscar
    $("#btn_buscar_evidencias").off("click").on("click", function() {
        cargarEvidencias();
    });
    
    // Evento para el botón ver PDF Cliente
    $(document).on("click", ".btn-ver-pdf", function() {
        var op = $(this).data("op");
        if (op) {
            // Abrir PDF en nueva pestaña
            window.open(routeapp + "/home/evidencia-cliente/pdf/" + op, "_blank");
        }
    });
    
    // Evento para el botón ver PDF Interno
    $(document).on("click", ".btn-ver-pdf-interno", function() {
        var op = $(this).data("op");
        if (op) {
            // Abrir PDF interno en nueva pestaña
            window.open(routeapp + "/home/evidencia-cliente/pdf-interno/" + op, "_blank");
        }
    });
    
    // Evento para el botón ver video (abre en nueva pestaña)
    $(document).on("click", ".btn-ver-video", function() {
        var op = $(this).data("op");
        if (op) {
            abrirVideoEnNuevaPestaña(op);
        }
    });
    
    // Función para obtener el primer video y abrirlo en una nueva pestaña
    function abrirVideoEnNuevaPestaña(op) {
        $.blockUI({
            message: "<h1>Cargando video...</h1><h3>por favor espere un momento...<i class=\"fa fa-spin fa-spinner\"></i></h3>",
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
            url: routeapp + "/home/evidencia-cliente/videos/" + op,
            type: "GET",
            success: function(response) {
                $.unblockUI();
                if (response.success && response.videos && response.videos.length > 0) {
                    // Obtener el primer video de la primera inspección
                    var primeraInspeccion = response.videos[0];
                    var primerChkId = Object.keys(primeraInspeccion.videos)[0];
                    var primerVideo = primeraInspeccion.videos[primerChkId][0];
                    
                    // Construir URL del video
                    var videoUrl = routeapp + "/home/inspeccion-proceso/imagen/" + primerVideo.id;
                    
                    // Abrir en nueva pestaña
                    window.open(videoUrl, '_blank');
                } else {
                    swal({
                        title: "Información",
                        text: "No se encontraron videos de evidencia para esta OP",
                        type: "info",
                        confirmButtonText: "Aceptar"
                    });
                }
            },
            error: function(xhr) {
                $.unblockUI();
                var mensaje = "Error al cargar el video. Intente nuevamente.";
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    mensaje = xhr.responseJSON.msg;
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
    
    // Cargar datos al iniciar
    cargarEvidencias();
}

