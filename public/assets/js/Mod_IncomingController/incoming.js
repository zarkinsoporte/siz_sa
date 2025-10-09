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
        
        // Validar que ambas fechas estén presentes
        if (!fechaDesde || !fechaHasta) {
            swal({
                title: "Error",
                text: "Por favor seleccione ambas fechas",
                type: "error",
                confirmButtonText: "Aceptar"
            });
            return;
        }
        
        // Validar que fecha desde no sea mayor a fecha hasta
        if (new Date(fechaDesde) > new Date(fechaHasta)) {
            swal({
                title: "Error",
                text: "La fecha desde no puede ser mayor que la fecha hasta",
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
    
    // Renderizar tabla de inspecciones (agrupadas)
    function renderTablaInspecciones() {
        // Verificar si DataTable ya está inicializado y destruirlo
        if ($.fn.DataTable.isDataTable('#tabla_inspecciones')) {
            $('#tabla_inspecciones').DataTable().destroy();
        }
        
        // Limpiar completamente la tabla
        $("#tabla_inspecciones tbody").empty();
        
        var tbody = "";
        console.log("Inspecciones agrupadas:", inspecciones);
        
        inspecciones.forEach(function(inspeccion, idx) {
            var cantRecibida = parseFloat(inspeccion.CANT_RECIBIDA || 0).toFixed(2);
            var cantAceptada = parseFloat(inspeccion.CANT_ACEPTADA || 0).toFixed(2);
            var cantRechazada = parseFloat(inspeccion.CANT_RECHAZADA || 0).toFixed(2);
            
            // Botones de acciones
            var acciones = "<button class=\"btn btn-ver-detalle btn-sm\" " +
                "data-doc-num=\"" + inspeccion.INC_docNum + "\" " +
                "data-line-num=\"" + inspeccion.INC_lineNum + "\" " +
                "data-cod-material=\"" + inspeccion.INC_codMaterial + "\" " +
                "title=\"Ver Detalle de " + inspeccion.NUM_INSPECCIONES + " inspección(es)\">" +
                    "<i class=\"fa fa-eye\"></i> Ver Detalle (" + inspeccion.NUM_INSPECCIONES + ")" +
                "</button> ";
            
            // Si es piel, agregar botón para ver resumen de clases de piel
            if (inspeccion.INC_esPiel === 'S') {
                acciones += "<button class=\"btn btn-warning btn-sm btn-ver-piel\" " +
                    "data-doc-num=\"" + inspeccion.INC_docNum + "\" " +
                    "data-line-num=\"" + inspeccion.INC_lineNum + "\" " +
                    "data-cod-material=\"" + inspeccion.INC_codMaterial + "\" " +
                    "title=\"Ver Resumen de Clases de Piel\">" +
                        "<i class=\"fa fa-tags\"></i> Piel" +
                    "</button>";
            }
            
            tbody += "<tr data-idx=\"" + idx + "\">" +
                "<td>" + (inspeccion.INC_docNum || 'N/A') + "</td>" +
                "<td style=\"text-align: left;\">" + (inspeccion.INC_nomProveedor || 'N/A') + "</td>" +
                "<td style=\"text-align: left;\">" + (inspeccion.INC_codMaterial || '') + " - " + (inspeccion.INC_nomMaterial || '') + "</td>" +
                "<td>" + cantRecibida + " " + (inspeccion.INC_unidadMedida || '') + "</td>" +
                "<td class=\"cantidad-aceptada\">" + cantAceptada + "</td>" +
                "<td class=\"cantidad-rechazada\">" + cantRechazada + "</td>" +
                "<td>" + acciones + "</td>" +
            "</tr>";
        });
        
        $("#tabla_inspecciones tbody").html(tbody);
        
        // Inicializar DataTable solo si no está ya inicializado
        if (!$.fn.DataTable.isDataTable('#tabla_inspecciones')) {
            dataTable = $("#tabla_inspecciones").DataTable({
                order: [[0, "desc"]], // Ordenar por Nota de Entrada descendente
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
    
    // Evento para ver detalle de inspección agrupada
    $("#tabla_inspecciones").off("click", ".btn-ver-detalle").on("click", ".btn-ver-detalle", function() {
        var docNum = $(this).data("doc-num");
        var lineNum = $(this).data("line-num");
        var codMaterial = $(this).data("cod-material");
        
        // Mostrar modal con loading
        $("#modalDetalleInspeccion").modal("show");
        $("#detalle_inspeccion_content").html(
            "<div class=\"text-center\">" +
            "<i class=\"fa fa-spinner fa-spin fa-3x\"></i>" +
            "<p>Cargando detalle de inspecciones...</p>" +
            "</div>"
        );
        
        // Cargar detalle de la inspección agrupada
        $.ajax({
            url: routeapp + "/home/INCOMING/detalle-inspecciones",
            type: "GET",
            data: { 
                doc_num: docNum,
                line_num: lineNum,
                cod_material: codMaterial
            },
            success: function(resp) {
                if (resp.success) {
                    renderDetalleInspeccionAgrupada(resp);
                } else {
                    $("#detalle_inspeccion_content").html(
                        "<div class=\"alert alert-danger\">" +
                        "<i class=\"fa fa-exclamation-triangle\"></i> " +
                        (resp.msg || "No se pudo cargar el detalle de las inspecciones") +
                        "</div>"
                    );
                }
            },
            error: function(xhr) {
                $("#detalle_inspeccion_content").html(
                    "<div class=\"alert alert-danger\">" +
                    "<i class=\"fa fa-exclamation-triangle\"></i> " +
                    "Error al cargar el detalle de las inspecciones" +
                    "</div>"
                );
            }
        });
    });
    
    // Evento para ver resumen de clases de piel
    $("#tabla_inspecciones").off("click", ".btn-ver-piel").on("click", ".btn-ver-piel", function() {
        var docNum = $(this).data("doc-num");
        var lineNum = $(this).data("line-num");
        var codMaterial = $(this).data("cod-material");
        
        // Mostrar modal con loading
        $("#modalResumenPiel").modal("show");
        $("#resumen_piel_content").html(
            "<div class=\"text-center\">" +
            "<i class=\"fa fa-spinner fa-spin fa-3x\"></i>" +
            "<p>Cargando resumen de clases de piel...</p>" +
            "</div>"
        );
        
        // Cargar resumen de clases de piel
        $.ajax({
            url: routeapp + "/home/INCOMING/resumen-piel",
            type: "GET",
            data: { 
                doc_num: docNum,
                line_num: lineNum,
                cod_material: codMaterial
            },
            success: function(resp) {
                if (resp.success) {
                    renderResumenPiel(resp);
                } else {
                    $("#resumen_piel_content").html(
                        "<div class=\"alert alert-danger\">" +
                        "<i class=\"fa fa-exclamation-triangle\"></i> " +
                        (resp.msg || "No se pudo cargar el resumen de piel") +
                        "</div>"
                    );
                }
            },
            error: function(xhr) {
                $("#resumen_piel_content").html(
                    "<div class=\"alert alert-danger\">" +
                    "<i class=\"fa fa-exclamation-triangle\"></i> " +
                    "Error al cargar el resumen de piel" +
                    "</div>"
                );
            }
        });
    });
    
    // Función para renderizar el detalle de inspecciones agrupadas
    function renderDetalleInspeccionAgrupada(data) {
        var resumen = data.resumen;
        var inspecciones = data.inspecciones;
        var checklist = data.checklist;
        var respuestas = data.respuestas;
        var imagenes = data.imagenes;
        
        var html = "<div class=\"row\">";
        
        // Resumen general
        html += "<div class=\"col-md-12\">";
        html += "<h4><strong>Resumen de Inspecciones</strong></h4>";
        html += "<div class=\"alert alert-info\">";
        html += "<strong>Total de inspecciones realizadas:</strong> " + resumen.num_inspecciones;
        html += "</div>";
        html += "<table class=\"table table-bordered\">";
        html += "<tr><th width=\"30%\">Nota de Entrada:</th><td><strong>" + resumen.doc_num + "</strong></td></tr>";
        html += "<tr><th>Proveedor:</th><td>" + resumen.proveedor + "</td></tr>";
        html += "<tr><th>Código Material:</th><td>" + resumen.codigo_material + "</td></tr>";
        html += "<tr><th>Material:</th><td>" + resumen.material + "</td></tr>";
        html += "<tr><th>Cantidad Recibida:</th><td>" + parseFloat(resumen.cant_recibida || 0).toFixed(2) + "</td></tr>";
        html += "<tr><th>Total Aceptado:</th><td class=\"cantidad-aceptada\">" + 
                parseFloat(resumen.cant_aceptada || 0).toFixed(2) + "</td></tr>";
        html += "<tr><th>Total Rechazado:</th><td class=\"cantidad-rechazada\">" + 
                parseFloat(resumen.cant_rechazada || 0).toFixed(2) + "</td></tr>";
        html += "</table>";
        html += "</div>";
        
        // Detalle de cada inspección
        html += "<div class=\"col-md-12 mt-4\" style=\"margin-top: 20px;\">";
        html += "<h4><strong>Detalle de Inspecciones</strong></h4>";
        html += "<div class=\"panel-group\" id=\"accordionInspecciones\">";
        
        inspecciones.forEach(function(inspeccion, idx) {
            var fechaInsp = 'N/A';
            if (inspeccion.INC_fechaInspeccion) {
                var fecha = new Date(inspeccion.INC_fechaInspeccion);
                fechaInsp = fecha.getFullYear() + '/' + 
                           String(fecha.getMonth() + 1).padStart(2, '0') + '/' + 
                           String(fecha.getDate()).padStart(2, '0');
            }
            
            html += "<div class=\"panel panel-default\">";
            html += "<div class=\"panel-heading\" style=\"background-color: #f5f5f5;\">";
            html += "<h4 class=\"panel-title\">";
            html += "<a data-toggle=\"collapse\" data-parent=\"#accordionInspecciones\" href=\"#collapse" + idx + "\">";
            html += "<i class=\"fa fa-chevron-down\"></i> ";
            html += "Inspección #" + inspeccion.INC_id + " - Fecha: " + fechaInsp + " - Inspector: " + inspeccion.INC_nomInspector;
            html += " - Aceptada: <span class=\"cantidad-aceptada\">" + parseFloat(inspeccion.INC_cantAceptada || 0).toFixed(2) + "</span>";
            html += " - Rechazada: <span class=\"cantidad-rechazada\">" + parseFloat(inspeccion.INC_cantRechazada || 0).toFixed(2) + "</span>";
            html += "</a>";
            html += "</h4>";
            html += "</div>";
            html += "<div id=\"collapse" + idx + "\" class=\"panel-collapse collapse" + (idx === 0 ? " in" : "") + "\">";
            html += "<div class=\"panel-body\">";
            
            if (inspeccion.INC_notas) {
                html += "<div class=\"alert alert-warning\">";
                html += "<strong>Observaciones Generales:</strong> " + inspeccion.INC_notas;
                html += "</div>";
            }
            
            html += "</div>";
            html += "</div>";
            html += "</div>";
        });
        
        html += "</div>";
        html += "</div>";
        
        // Checklist consolidado (solo Cumple y No Cumple)
        if (checklist && checklist.length > 0 && Object.keys(respuestas).length > 0) {
            html += "<div class=\"col-md-12 mt-4\" style=\"margin-top: 20px;\">";
            html += "<h4><strong>Checklist Consolidado</strong></h4>";
            html += "<p class=\"text-muted\">Se muestran solo los rubros evaluados como <strong>Cumple</strong> o <strong>No Cumple</strong></p>";
            html += "<table class=\"table table-striped table-bordered\">";
            html += "<thead><tr><th>Descripción</th><th width=\"15%\">Estado</th><th>Observaciones</th></tr></thead>";
            html += "<tbody>";
            
            var hayRespuestas = false;
            checklist.forEach(function(item) {
                if (respuestas[item.CHK_id]) {
                    hayRespuestas = true;
                    var respuesta = respuestas[item.CHK_id];
                    var estado = respuesta.estado === 'C' ? 'Cumple' : 'No Cumple';
                    var colorClass = respuesta.estado === 'C' ? 'success' : 'danger';
                    
                    html += "<tr class=\"" + colorClass + "\">";
                    html += "<td>" + item.CHK_descripcion + "</td>";
                    html += "<td><strong>" + estado + "</strong></td>";
                    html += "<td>";
                    
                    // Mostrar observaciones de todas las inspecciones
                    if (respuesta.observaciones && respuesta.observaciones.length > 0) {
                        respuesta.observaciones.forEach(function(obs) {
                            var fechaObs = new Date(obs.fecha);
                            var fechaStr = fechaObs.getFullYear() + '/' + 
                                         String(fechaObs.getMonth() + 1).padStart(2, '0') + '/' + 
                                         String(fechaObs.getDate()).padStart(2, '0');
                            html += "<div style=\"margin-bottom: 5px;\">";
                            html += "<small class=\"text-muted\">" + fechaStr + " - " + obs.inspector + ":</small><br>";
                            html += obs.texto;
                            html += "</div>";
                        });
                    }
                    
                    // Mostrar cantidades si existen
                    if (respuesta.cantidades && respuesta.cantidades.length > 0) {
                        html += "<div style=\"margin-top: 10px;\">";
                        html += "<strong>Cantidades afectadas:</strong><br>";
                        respuesta.cantidades.forEach(function(cant) {
                            var fechaCant = new Date(cant.fecha);
                            var fechaStr = fechaCant.getFullYear() + '/' + 
                                         String(fechaCant.getMonth() + 1).padStart(2, '0') + '/' + 
                                         String(fechaCant.getDate()).padStart(2, '0');
                            html += "<small>" + fechaStr + ": " + parseFloat(cant.cantidad).toFixed(2) + "</small><br>";
                        });
                        html += "</div>";
                    }
                    
                    html += "</td>";
                    html += "</tr>";
                    
                    // Mostrar imágenes consolidadas si existen
                    if (imagenes[item.CHK_id] && imagenes[item.CHK_id].length > 0) {
                        html += "<tr><td colspan=\"3\">";
                        html += "<strong>Evidencias:</strong><br>";
                        imagenes[item.CHK_id].forEach(function(img) {
                            var fechaImg = new Date(img.fecha);
                            var fechaStr = fechaImg.getFullYear() + '/' + 
                                         String(fechaImg.getMonth() + 1).padStart(2, '0') + '/' + 
                                         String(fechaImg.getDate()).padStart(2, '0');
                            html += "<div style=\"display: inline-block; margin: 5px; text-align: center;\">";
                            html += "<a href=\"" + routeapp + "/incoming/imagen/" + img.id + "\" target=\"_blank\">";
                            html += "<img src=\"" + routeapp + "/incoming/imagen/" + img.id + "\" style=\"max-width: 150px; border: 1px solid #ddd; cursor: pointer;\">";
                            html += "</a>";
                            html += "<br><small class=\"text-muted\">" + fechaStr + "</small>";
                            html += "</div>";
                        });
                        html += "</td></tr>";
                    }
                }
            });
            
            if (!hayRespuestas) {
                html += "<tr><td colspan=\"3\" class=\"text-center\">No hay rubros evaluados como Cumple o No Cumple</td></tr>";
            }
            
            html += "</tbody></table>";
            html += "</div>";
        }
        
        html += "</div>";
        
        $("#detalle_inspeccion_content").html(html);
    }
    
    // Función para renderizar resumen de clases de piel
    function renderResumenPiel(data) {
        var resumen = data.resumen;
        var totales = data.totales;
        var porcentajes = data.porcentajes;
        var detalleInspecciones = data.detalle_inspecciones;
        
        var html = "<div class=\"row\">";
        
        // Información general
        html += "<div class=\"col-md-12\">";
        html += "<h4><strong>Resumen de Clases de Piel</strong></h4>";
        html += "<table class=\"table table-bordered\">";
        html += "<tr><th width=\"30%\">Nota de Entrada:</th><td><strong>" + resumen.doc_num + "</strong></td></tr>";
        html += "<tr><th>Proveedor:</th><td>" + resumen.proveedor + "</td></tr>";
        html += "<tr><th>Código Material:</th><td>" + resumen.codigo_material + "</td></tr>";
        html += "<tr><th>Material:</th><td>" + resumen.material + "</td></tr>";
        html += "<tr><th>Cantidad Recibida:</th><td>" + parseFloat(resumen.cant_recibida || 0).toFixed(3) + "</td></tr>";
        html += "<tr><th>Total Aceptado:</th><td class=\"cantidad-aceptada\">" + parseFloat(resumen.cant_aceptada || 0).toFixed(3) + "</td></tr>";
        html += "<tr><th>Total Rechazado:</th><td class=\"cantidad-rechazada\">" + parseFloat(resumen.cant_rechazada || 0).toFixed(3) + "</td></tr>";
        html += "<tr><th>Por Revisar:</th><td>" + parseFloat(resumen.por_revisar || 0).toFixed(3) + "</td></tr>";
        html += "</table>";
        html += "</div>";
        
        // Resumen consolidado de clases
        html += "<div class=\"col-md-12 mt-4\" style=\"margin-top: 20px;\">";
        html += "<h4><strong>Totales Consolidados</strong></h4>";
        
        if (resumen.es_parcial) {
            html += "<div class=\"alert alert-warning\">";
            html += "<i class=\"fa fa-exclamation-triangle\"></i> ";
            html += "<strong>INSPECCIÓN PARCIAL:</strong> La inspección aún no está completa. Quedan <strong>" + parseFloat(resumen.por_revisar || 0).toFixed(3) + "</strong> unidades por revisar.";
            html += "</div>";
        }
        
        html += "<table class=\"table table-striped table-bordered\">";
        html += "<thead>";
        html += "<tr>";
        html += "<th>Clase</th>";
        html += "<th>Cantidad</th>";
        html += "<th>Porcentaje</th>";
        html += "</tr>";
        html += "</thead>";
        html += "<tbody>";
        html += "<tr class=\"success\">";
        html += "<td><strong>Clase A</strong></td>";
        html += "<td>" + parseFloat(totales.clase_a || 0).toFixed(3) + "</td>";
        html += "<td>" + parseFloat(porcentajes.clase_a || 0).toFixed(2) + "% </td>";
        html += "</tr>";
        html += "<tr class=\"info\">";
        html += "<td><strong>Clase B</strong></td>";
        html += "<td>" + parseFloat(totales.clase_b || 0).toFixed(3) + "</td>";
        html += "<td>" + parseFloat(porcentajes.clase_b || 0).toFixed(2) + "% </td>";
        html += "</tr>";
        html += "<tr class=\"warning\">";
        html += "<td><strong>Clase C</strong></td>";
        html += "<td>" + parseFloat(totales.clase_c || 0).toFixed(3) + "</td>";
        html += "<td>" + parseFloat(porcentajes.clase_c || 0).toFixed(2) + "% </td>";
        html += "</tr>";
        html += "<tr class=\"danger\">";
        html += "<td><strong>Clase D</strong></td>";
        html += "<td>" + parseFloat(totales.clase_d || 0).toFixed(3) + "</td>";
        html += "<td>" + parseFloat(porcentajes.clase_d || 0).toFixed(2) + "% </td>";
        html += "</tr>";
        html += "<tr style=\"font-weight: bold; background-color: #f5f5f5;\">";
        html += "<td><strong>TOTAL</strong></td>";
        html += "<td>" + parseFloat(totales.total || 0).toFixed(3) + "</td>";
        
        // Calcular porcentaje total
        var porcentajeTotal = parseFloat(porcentajes.clase_a || 0) + 
                             parseFloat(porcentajes.clase_b || 0) + 
                             parseFloat(porcentajes.clase_c || 0) + 
                             parseFloat(porcentajes.clase_d || 0);
        
        html += "<td>" + porcentajeTotal.toFixed(2) + "% " + (resumen.es_parcial ? "<span class=\"label label-warning\">PARCIAL</span>" : "") + "</td>";
        html += "</tr>";
        html += "</tbody>";
        html += "</table>";
        html += "</div>";
        
        // Detalle por inspección
        if (detalleInspecciones && detalleInspecciones.length > 0) {
            html += "<div class=\"col-md-12 mt-4\" style=\"margin-top: 20px;\">";
            html += "<h4><strong>Detalle por Inspección</strong></h4>";
            html += "<table class=\"table table-striped table-bordered\">";
            html += "<thead>";
            html += "<tr>";
            html += "<th>ID Inspección</th>";
            html += "<th>Fecha</th>";
            html += "<th>Inspector</th>";
            html += "<th>Cant. Aceptada</th>";
            html += "<th>Clase A</th>";
            html += "<th>Clase B</th>";
            html += "<th>Clase C</th>";
            html += "<th>Clase D</th>";
            html += "</tr>";
            html += "</thead>";
            html += "<tbody>";
            
            detalleInspecciones.forEach(function(det) {
                var fechaDet = new Date(det.fecha);
                var fechaStr = fechaDet.getFullYear() + '/' + 
                             String(fechaDet.getMonth() + 1).padStart(2, '0') + '/' + 
                             String(fechaDet.getDate()).padStart(2, '0');
                
                html += "<tr>";
                html += "<td>" + det.inc_id + "</td>";
                html += "<td>" + fechaStr + "</td>";
                html += "<td>" + det.inspector + "</td>";
                html += "<td>" + parseFloat(det.cant_aceptada || 0).toFixed(3) + "</td>";
                html += "<td>" + parseFloat(det.clase_a || 0).toFixed(3) + "</td>";
                html += "<td>" + parseFloat(det.clase_b || 0).toFixed(3) + "</td>";
                html += "<td>" + parseFloat(det.clase_c || 0).toFixed(3) + "</td>";
                html += "<td>" + parseFloat(det.clase_d || 0).toFixed(3) + "</td>";
                html += "</tr>";
            });
            
            html += "</tbody>";
            html += "</table>";
            html += "</div>";
        }
        
        html += "</div>";
        
        $("#resumen_piel_content").html(html);
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

