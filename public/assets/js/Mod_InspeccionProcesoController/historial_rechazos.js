// Función para renderizar el historial de rechazos
function renderHistorialRechazos() {
    if (!opData || !centroInspeccionData) {
        swal({
            title: 'Error',
            text: 'No hay información de OP o centro de inspección',
            type: 'error',
            confirmButtonText: 'Aceptar'
        });
        return;
    }
    
    // Mostrar blockUI
    $.blockUI({
        message: '<h1>Cargando historial de rechazos...</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
            color: '#000000',
            baseZ: 2000
        }
    });
    
    // Obtener historial de rechazos
    $.ajax({
        url: routeapp + '/home/inspeccion-proceso/historial-completo',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            op: opData.OP,
            centro: centroInspeccionData.id
        },
        success: function(response) {
            $.unblockUI();
            
            if (response.success && response.inspecciones && response.inspecciones.length > 0) {
                var html = '';
                
                response.inspecciones.forEach(function(insp, index) {
                    // Formatear fecha
                    var fechaInspeccion = new Date(insp.IPR_fechaInspeccion);
                    var fechaFormateada = fechaInspeccion.getDate().toString().padStart(2, '0') + '/' + 
                                         (fechaInspeccion.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                                         fechaInspeccion.getFullYear() + ' ' +
                                         fechaInspeccion.getHours().toString().padStart(2, '0') + ':' + 
                                         fechaInspeccion.getMinutes().toString().padStart(2, '0');
                    
                    var panelClass = 'panel-danger';
                    var estadoBadge = '<span class="label label-danger">RECHAZADA</span>';
                    
                    html += '<div class="panel ' + panelClass + '" style="margin-bottom: 15px;">' +
                        '<div class="panel-heading" style="background-color: #dc3545; color: white;">' +
                            '<h4 class="panel-title">' +
                                '<strong>Rechazo #' + insp.IPR_id + '</strong> - ' + estadoBadge + ' - ' + fechaFormateada +
                            '</h4>' +
                        '</div>' +
                        '<div class="panel-body">' +
                            '<div class="row">' +
                                '<div class="col-md-6">' +
                                    '<p><strong>Fecha de Inspección:</strong> ' + fechaFormateada + '</p>' +
                                    '<p><strong>Inspector:</strong> ' + (insp.IPR_nomInspector || 'N/A') + '</p>' +
                                '</div>' +
                                '<div class="col-md-6">' +
                                    '<p><strong>Cantidad Inspeccionada:</strong> ' + parseFloat(insp.IPR_cantInspeccionada || 0).toFixed(2) + '</p>' +
                                    '<p><strong>Estado:</strong> ' + estadoBadge + '</p>' +
                                '</div>' +
                            '</div>';
                    
                    if (insp.IPR_observaciones) {
                        html += '<div class="row" style="margin-top: 10px;">' +
                            '<div class="col-md-12">' +
                                '<p><strong>Observaciones Generales:</strong></p>' +
                                '<div style="background-color: #f5f5f5; padding: 10px; border-radius: 5px; border-left: 4px solid #dc3545;">' +
                                    insp.IPR_observaciones +
                                '</div>' +
                            '</div>' +
                        '</div>';
                    }
                    
                    // Tabla de detalles del checklist
                    if (insp.detalles && insp.detalles.length > 0) {
                        html += '<div class="row" style="margin-top: 15px;">' +
                            '<div class="col-md-12">' +
                                '<p><strong>Detalle del Checklist:</strong></p>' +
                                '<div style="max-height: 300px; overflow-y: auto;">' +
                                '<table class="table table-bordered table-striped table-condensed" style="font-size: 12px;">' +
                                    '<thead style="background-color: #f5f5f5;">' +
                                        '<tr>' +
                                            '<th>Punto de Inspección</th>' +
                                            '<th>Estado</th>' +
                                            '<th>Empleado Responsable</th>' +
                                            '<th>Observaciones</th>' +
                                        '</tr>' +
                                    '</thead>' +
                                    '<tbody>';
                        
                        insp.detalles.forEach(function(detalle) {
                            var estadoTexto = '';
                            var estadoClass = '';
                            
                            if (detalle.IPD_estado === 'C') {
                                estadoTexto = 'Cumple';
                                estadoClass = 'success';
                            } else if (detalle.IPD_estado === 'N') {
                                estadoTexto = 'No Cumple';
                                estadoClass = 'danger';
                            } else {
                                estadoTexto = 'No Aplica';
                                estadoClass = 'default';
                            }
                            
                            html += '<tr>' +
                                '<td>' + (detalle.CHK_descripcion || 'N/A') + '</td>' +
                                '<td><span class="label label-' + estadoClass + '">' + estadoTexto + '</span></td>' +
                                '<td>' + (detalle.empleado_nombre || 'N/A') + '</td>' +
                                '<td>' + (detalle.IPD_observacion || '-') + '</td>' +
                            '</tr>';
                        });
                        
                        html += '</tbody>' +
                            '</table>' +
                            '</div>' +
                            '</div>' +
                        '</div>';
                    }
                    
                    html += '</div>' +
                    '</div>';
                });
                
                $('#modal_op_numero').text(opData.OP);
                $('#contenido_historial_rechazos').html(html);
                $('#modalHistorialRechazos').modal('show');
            } else {
                swal({
                    title: 'Sin rechazos',
                    text: 'No se encontraron rechazos previos para esta OP en este centro de inspección',
                    type: 'info',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function() {
            $.unblockUI();
            swal({
                title: 'Error',
                text: 'Error al cargar el historial de rechazos',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

