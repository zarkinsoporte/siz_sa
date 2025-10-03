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
    
    // Establecer fechas por defecto (3 meses atrás hasta hoy)
    var fechaHasta = new Date();
    var fechaDesde = new Date();
    fechaDesde.setMonth(fechaDesde.getMonth() - 3);
    
    $("#filtro_fecha_hasta").val(fechaHasta.toISOString().split('T')[0]);
    $("#filtro_fecha_desde").val(fechaDesde.toISOString().split('T')[0]);
    
    // Función para cargar inspecciones con filtros
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
            url: routeapp + "/home/INCOMING/buscar-inspecciones",
            type: "GET",
            data: {
                fecha_desde: fechaDesde,
                fecha_hasta: fechaHasta
            },
            success: function(data) {
                inspecciones = data;
                renderTablaInspecciones();
                $.unblockUI();
            },
            error: function() {
                $.unblockUI();
                swal({
                    title: "Error",
                    text: "Error al cargar los datos de inspecciones. Intente nuevamente.",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            }
        });
    }
    
    // Renderizar tabla de inspecciones
    function renderTablaInspecciones() {
        // Verificar si DataTable ya está inicializado y destruirlo
        if ($.fn.DataTable.isDataTable('#tabla_inspecciones')) {
            $('#tabla_inspecciones').DataTable().destroy();
        }
        
        // Limpiar completamente la tabla
        $("#tabla_inspecciones tbody").empty();
        
        var tbody = "";
        console.log(inspecciones);
        
        inspecciones.forEach(function(inspeccion, idx) {
            // Formatear fecha yyyy/mm/dd
            var fechaInspeccion = 'N/A';
            if (inspeccion.INC_fechaInspeccion) {
                var fecha = new Date(inspeccion.INC_fechaInspeccion);
                var año = fecha.getFullYear();
                var mes = String(fecha.getMonth() + 1).padStart(2, '0');
                var dia = String(fecha.getDate()).padStart(2, '0');
                fechaInspeccion = año + '/' + mes + '/' + dia;
            }
            
            var cantRecibida = parseFloat(inspeccion.INC_cantRecibida || 0).toFixed(2);
            var cantAceptada = parseFloat(inspeccion.INC_cantAceptada || 0).toFixed(2);
            var cantRechazada = parseFloat(inspeccion.INC_cantRechazada || 0).toFixed(2);
            
            tbody += "<tr data-idx=\"" + idx + "\">" +
                "<td>" + inspeccion.INC_id + "</td>" +
                "<td>" + fechaInspeccion + "</td>" +
                "<td>" + (inspeccion.INC_docNum || 'N/A') + "</td>" +
                "<td style=\"text-align: left;\">" + (inspeccion.INC_nomProveedor || 'N/A') + "</td>" +
                "<td style=\"text-align: left;\">" + (inspeccion.INC_codMaterial || '') + " - " + (inspeccion.INC_nomMaterial || '') + "</td>" +
                "<td>" + (inspeccion.INC_lote || 'N/A') + "</td>" +
                "<td>" + cantRecibida + "</td>" +
                "<td class=\"cantidad-aceptada\">" + cantAceptada + "</td>" +
                "<td class=\"cantidad-rechazada\">" + cantRechazada + "</td>" +
                "<td>" + (inspeccion.INC_nomInspector || 'N/A') + "</td>" +
                "<td>" +
                    "<button class=\"btn btn-ver-detalle btn-sm\" data-inc-id=\"" + inspeccion.INC_id + "\" title=\"Ver Detalle\">" +
                        "<i class=\"fa fa-eye\"></i> Ver Detalle" +
                    "</button>" +
                "</td>" +
            "</tr>";
        });
        
        $("#tabla_inspecciones tbody").html(tbody);
        
        // Inicializar DataTable solo si no está ya inicializado
        if (!$.fn.DataTable.isDataTable('#tabla_inspecciones')) {
            dataTable = $("#tabla_inspecciones").DataTable({
                order: [[0, "desc"]], // Ordenar por ID descendente
                language: {
                    "url": assetapp + "assets/lang/Spanish.json"
                },
                pageLength: 25,
                responsive: true
            });
        }
    }
    
    // Evento para el botón buscar
    $("#btn_buscar_inspecciones").off("click").on("click", function() {
        cargarInspecciones();
    });
    
    // Evento para ver detalle de inspección
    $("#tabla_inspecciones").off("click", ".btn-ver-detalle").on("click", ".btn-ver-detalle", function() {
        var incId = $(this).data("inc-id");
        
        // Mostrar modal
        $("#modalDetalleInspeccion").modal("show");
        
        // Cargar detalle de la inspección
        $.ajax({
            url: routeapp + "/home/INSPECCION/ver-inspeccion",
            type: "GET",
            data: { inc_id: incId },
            success: function(resp) {
                if (resp.success) {
                    renderDetalleInspeccion(resp);
                } else {
                    $("#detalle_inspeccion_content").html(
                        "<div class=\"alert alert-danger\">" +
                        "<i class=\"fa fa-exclamation-triangle\"></i> " +
                        "No se pudo cargar el detalle de la inspección" +
                        "</div>"
                    );
                }
            },
            error: function() {
                $("#detalle_inspeccion_content").html(
                    "<div class=\"alert alert-danger\">" +
                    "<i class=\"fa fa-exclamation-triangle\"></i> " +
                    "Error al cargar el detalle de la inspección" +
                    "</div>"
                );
            }
        });
    });
    
    // Función para renderizar el detalle de la inspección
    function renderDetalleInspeccion(data) {
        var inspeccion = data.inspeccion;
        var checklist = data.checklist;
        var respuestas = data.respuestas;
        var imagenes = data.imagenes;
        
        var html = "<div class=\"row\">";
        
        // Información general
        html += "<div class=\"col-md-12\">";
        html += "<h4><strong>Información General</strong></h4>";
        html += "<table class=\"table table-bordered\">";
        html += "<tr><th width=\"30%\">Código Artículo:</th><td>" + (inspeccion.CODIGO_ARTICULO || 'N/A') + "</td></tr>";
        html += "<tr><th>Material:</th><td>" + (inspeccion.MATERIAL || 'N/A') + "</td></tr>";
        html += "<tr><th>Lote:</th><td>" + (inspeccion.LOTE || 'N/A') + "</td></tr>";
        html += "<tr><th>Cantidad Inspeccionada:</th><td class=\"cantidad-aceptada\">" + 
                (parseFloat(inspeccion.CAN_INSPECCIONADA || 0).toFixed(2)) + "</td></tr>";
        html += "<tr><th>Cantidad Rechazada:</th><td class=\"cantidad-rechazada\">" + 
                (parseFloat(inspeccion.CAN_RECHAZADA || 0).toFixed(2)) + "</td></tr>";
        html += "<tr><th>Fecha de Inspección:</th><td>" + 
                (inspeccion.INC_fechaInspeccion || 'N/A') + "</td></tr>";
        html += "<tr><th>Inspector:</th><td>" + (inspeccion.INC_nomInspector || 'N/A') + "</td></tr>";
        
        if (inspeccion.OBSERVACIONES_GENERALES) {
            html += "<tr><th>Observaciones:</th><td>" + inspeccion.OBSERVACIONES_GENERALES + "</td></tr>";
        }
        
        html += "</table>";
        html += "</div>";
        
        // Checklist
        if (checklist && checklist.length > 0) {
            html += "<div class=\"col-md-12 mt-4\" style=\"margin-top: 20px;\">";
            html += "<h4><strong>Checklist de Inspección</strong></h4>";
            html += "<table class=\"table table-striped table-bordered\">";
            html += "<thead><tr><th>Descripción</th><th width=\"15%\">Estado</th><th width=\"25%\">Observación</th><th width=\"10%\">Cantidad</th></tr></thead>";
            html += "<tbody>";
            
            checklist.forEach(function(item) {
                var respuesta = respuestas[item.CHK_id] || 'No Aplica';
                var observacion = respuestas[item.CHK_id + '_observacion'] || '';
                var cantidad = respuestas[item.CHK_id + '_cantidad'] || '';
                
                var colorClass = '';
                if (respuesta === 'Cumple') colorClass = 'success';
                else if (respuesta === 'No Cumple') colorClass = 'danger';
                
                html += "<tr class=\"" + colorClass + "\">";
                html += "<td>" + item.CHK_descripcion + "</td>";
                html += "<td><strong>" + respuesta + "</strong></td>";
                html += "<td>" + observacion + "</td>";
                html += "<td>" + cantidad + "</td>";
                html += "</tr>";
                
                // Mostrar imágenes si existen
                if (imagenes[item.CHK_id] && imagenes[item.CHK_id].length > 0) {
                    html += "<tr><td colspan=\"4\">";
                    html += "<strong>Evidencias:</strong><br>";
                    imagenes[item.CHK_id].forEach(function(img) {
                        html += "<a href=\"" + routeapp + "/incoming/imagen/" + img.id + "\" target=\"_blank\">";
                        html += "<img src=\"" + routeapp + "/incoming/imagen/" + img.id + "\" style=\"max-width: 150px; margin: 5px; border: 1px solid #ddd; cursor: pointer;\">";
                        html += "</a>";
                    });
                    html += "</td></tr>";
                }
            });
            
            html += "</tbody></table>";
            html += "</div>";
        }
        
        html += "</div>";
        
        $("#detalle_inspeccion_content").html(html);
    }
    
    // Cargar datos al inicializar
    cargarInspecciones();
}

// Verificar si ya se inicializó para prevenir duplicados
if (!window.incomingInicializado) {
    window.incomingInicializado = true;
    $(document).ready(function() {
        js_iniciador();
    });
}
