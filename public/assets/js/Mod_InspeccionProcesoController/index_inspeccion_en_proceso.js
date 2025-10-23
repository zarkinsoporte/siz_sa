// Variables globales
var opData = null;
var centroInspeccionData = null;
var checklist = [];
var respuestas = {};
var idInspeccion = 0;
var inspeccionesPrevias = [];
var historial = [];

// Función global para manejar cambios en el checklist
function manejarChecklist(chkId, valor) {
    var inputCantidad = $('#cantidad_' + chkId);
    var textareaObservacion = $('textarea[name="obs_' + chkId + '"]');
    var btnEvidencia = $('#imagenes_' + chkId).siblings('.btnEvidencia');
    
    if (valor === 'No Cumple') {
        inputCantidad.prop('disabled', false);
        inputCantidad.focus();
        textareaObservacion.prop('required', true);
        textareaObservacion.attr('placeholder', 'OBSERVACIÓN OBLIGATORIA');
        btnEvidencia.attr('title', 'Adjuntar Evidencia (OBLIGATORIO)');
    } else {
        inputCantidad.prop('disabled', true);
        inputCantidad.val('');
        textareaObservacion.prop('required', false);
        textareaObservacion.attr('placeholder', '');
        btnEvidencia.attr('title', 'Adjuntar Evidencia');
    }
    
    // Actualizar respuestas
    respuestas[chkId] = valor;
    if (valor !== 'No Cumple') {
        respuestas[chkId + '_cantidad'] = '';
    }
}

function js_iniciador() {
    $('.toggle').bootstrapSwitch();
    $('[data-toggle="tooltip"]').tooltip();
    $('.boot-select').selectpicker();
    $('.dropdown-toggle').dropdown();
    
    setTimeout(function() {
        $('#infoMessage').fadeOut('fast');
    }, 5000);
    
    $("#sidebarCollapse").on("click", function() {
        $("#sidebar").toggleClass("active"); 
        $("#page-wrapper").toggleClass("content"); 
        $(this).toggleClass("active"); 
    });
    
    $("#sidebar").toggleClass("active"); 
    $("#page-wrapper").toggleClass("content"); 
    $(this).toggleClass("active"); 
    
    // Función para buscar OP y cargar todo automáticamente
    function buscarOP() {
        var numeroOP = $('#numero_op').val();
        
        if(!numeroOP) {
            swal({
                title: 'Campo requerido',
                text: 'Ingrese un número de OP',
                type: 'warning',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        $.blockUI({
            message: '<h1>Su petición está siendo procesada...</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
        
        $.getJSON(routeapp+'/home/inspeccion-proceso/buscar', {
            op: numeroOP
        }, function(data){
            if(data.success) {
                opData = data.op;
                centroInspeccionData = data.centro_inspeccion;
                checklist = data.checklist;
                historial = data.historial;
                inspeccionesPrevias = data.inspecciones_previas;
                respuestas = {};
                
                // Renderizar información
                renderCabeceraOP();
                renderChecklist();
                renderResumen();
                $('#inspeccion_container').show();
                $('#cabecera_nota').show();
            }
            $.unblockUI();
        }).fail(function(jqXHR) {
            $.unblockUI();
            var mensaje = 'Error en la búsqueda. Intente nuevamente.';
            if(jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                mensaje = jqXHR.responseJSON.msg;
            }
            swal({
                title: 'Error',
                text: mensaje,
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    }
    
    // Evento para buscar con Enter
    $('#numero_op').keypress(function(e) {
        if(e.which == 13) {
            $('#inspeccion_container').hide();
            buscarOP();
        }
    });
    
    // Renderizar cabecera de OP
    function renderCabeceraOP() {
        $('#articulo_op').text(opData.ItemCode + ' - ' + opData.ItemName);
        $('#cantidad_op').html('Cantidad Planeada: ' + parseFloat(opData.CantidadPlaneada).toFixed(2) + '<br>Pedido: ' + (opData.Pedido || 'N/A'));
        $('#centro_inspeccion_actual').html('<i class="fa fa-check-circle text-success"></i> Centro de Inspección: <strong>' + centroInspeccionData.nombre + '</strong>');
    }
    
    // Función para renderizar checklist
    function renderChecklist() {
        console.log('renderChecklist - checklist:', checklist);
        var tbody = '';
        
        if(!checklist || checklist.length === 0) {
            tbody = '<tr><td colspan="7" class="text-center"><strong>No hay checklist configurado para este centro de inspección</strong></td></tr>';
        } else {
            checklist.forEach(function(item) {
                var respuesta = respuestas[item.CHK_id] || '';
                var cantidadNoCumple = respuestas[item.CHK_id + '_cantidad'] || '';
                var observacion = respuestas[item.CHK_id + '_observacion'] || '';
                
                tbody += '<tr>'+
                    '<td>'+
                        '<button type="button" class="btn btn-primary btn-sm btnEvidencia" title="Adjuntar Evidencia"><span class="glyphicon glyphicon-camera"></span></button>'+
                        '<input type="file" name="img_'+item.CHK_id+'" accept=".jpg,.jpeg,.png" style="display:none;" class="inputEvidencia" multiple>'+
                        '<div class="imagenes-previas" id="imagenes_'+item.CHK_id+'" style="margin-top: 5px;"></div>'+
                    '</td>'+
                    '<td>'+item.CHK_descripcion+'</td>'+
                    '<td><input type="radio" name="checklist_'+item.CHK_id+'" value="Cumple" '+(respuesta === 'Cumple' ? 'checked' : '')+' onchange="manejarChecklist('+item.CHK_id+', this.value)"></td>'+
                    '<td><input type="radio" name="checklist_'+item.CHK_id+'" value="No Cumple" '+(respuesta === 'No Cumple' ? 'checked' : '')+' onchange="manejarChecklist('+item.CHK_id+', this.value)"></td>'+
                    '<td><input type="radio" name="checklist_'+item.CHK_id+'" value="No Aplica" '+(respuesta === 'No Aplica' ? 'checked' : '')+' onchange="manejarChecklist('+item.CHK_id+', this.value)"></td>'+
                    '<td><input type="number" id="cantidad_'+item.CHK_id+'" class="form-control cantidad-no-cumple" value="'+cantidadNoCumple+'" step="0.001" min="0" max="999999.999" onblur="if(this.value) this.value = parseFloat(this.value).toFixed(3)" disabled></td>'+
                    '<td><textarea class="form-control textareaObservacion" name="obs_'+item.CHK_id+'" rows="2" style="resize:none; text-transform:uppercase;">'+observacion+'</textarea></td>'+
                '</tr>';
            });
        }
        
        $('#checklist_body').html(tbody);
        
        // Evento para el botón de evidencia
        $('.btnEvidencia').click(function(){
            $(this).siblings('.inputEvidencia').click();
        });
        
        // Evento para cuando se seleccionan archivos
        $('.inputEvidencia').change(function(){
            var files = this.files;
            var chkId = $(this).attr('name').replace('img_', '');
            var contenedorImagenes = $('#imagenes_' + chkId);
         
            if(files.length > 0) {
                contenedorImagenes.empty();
                
                for(var i = 0; i < files.length; i++) {
                    var file = files[i];
                    
                    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if(allowedTypes.indexOf(file.type) === -1) {
                        swal({
                            title: 'Tipo de archivo no válido',
                            text: 'Solo se permiten archivos JPG y PNG',
                            type: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                        this.value = '';
                        return;
                    }
     
                    if(file.size > 5 * 1024 * 1024) {
                        swal({
                            title: 'Archivo demasiado grande',
                            text: 'El archivo no debe superar 5MB',
                            type: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                        this.value = '';
                        return;
                    }
     
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var imgPreview = '<div style="display: inline-block; margin: 2px; position: relative;">' +
                            '<img src="' + e.target.result + '" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #ccc;" title="' + file.name + '">' +
                            '<div style="font-size: 10px; text-align: center; max-width: 50px; overflow: hidden; text-overflow: ellipsis;">' + file.name + '</div>' +
                            '</div>';
                        contenedorImagenes.append(imgPreview);
                    };
                    reader.readAsDataURL(file);
                }
                
                $(this).siblings('.btnEvidencia').html('<span class="glyphicon glyphicon-ok text-white"></span>');
                
                swal({
                    title: 'Archivos adjuntados',
                    text: 'Se han cargado ' + files.length + ' archivo(s) correctamente',
                    type: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
        
        $('.textareaObservacion').on('input', function(){
            this.value = this.value.toUpperCase();
        });
    }
    
    // Renderizar resumen lateral
    function renderResumen() {
        var cantidadDisponible = centroInspeccionData ? centroInspeccionData.cantidad_disponible : 0;
        
        var fechaActual = new Date();
        var fechaFormateada = fechaActual.getFullYear() + '-' + 
                             (fechaActual.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                             fechaActual.getDate().toString().padStart(2, '0');
        
        var idInspeccionMostrar = idInspeccion > 0 ? idInspeccion : 'Por definir';
        var nomInspector = typeof currentUser !== 'undefined' ? currentUser : 'Usuario Actual';
        
        // Botones de inspecciones previas
        var htmlInspecciones = '';
        if(inspeccionesPrevias && inspeccionesPrevias.length > 0) {
            htmlInspecciones = '<div style="margin-top: 15px; margin-bottom: 15px;">'+
                '<small><strong>INSPECCIONES PREVIAS:</strong></small><br>';
            inspeccionesPrevias.forEach(function(insp) {
                htmlInspecciones += '<button class="btn btn-success btn-xs btnVerInspeccionProceso" data-inspeccion-id="'+insp.IPR_id+'" title="Ver Inspección ID: '+insp.IPR_id+'" style="margin: 2px;"><i class="fa fa-eye"></i> ID '+insp.IPR_id+'</button> ';
            });
            htmlInspecciones += '</div>';
        }
        
        // Tabla de historial
        var tablaHistorial = '';
        if(historial && historial.length > 0) {
            tablaHistorial = '<div style="margin-top: 20px; margin-bottom: 20px;">'+
                '<h5 style="font-weight: bold; margin-bottom: 10px;"><i class="fa fa-list"></i> HISTORIAL DE LA OP</h5>'+
                '<div style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd;">'+
                '<table class="table table-bordered table-striped table-condensed" style="font-size: 11px; margin-bottom: 0;">'+
                    '<thead style="background-color: #f5f5f5;">'+
                        '<tr>'+
                            '<th>Estación</th>'+
                            '<th>Empleado</th>'+
                            '<th class="text-right">Cantidad</th>'+
                        '</tr>'+
                    '</thead>'+
                    '<tbody>';
            
            historial.forEach(function(item) {
                var empleado = item.Empleado || 'N/A';
                var cantidad = item.CantidadElaborada ? parseFloat(item.CantidadElaborada).toFixed(2) : '0.00';
                var esCalidad = item.EsCalidad === 'S';
                var rowClass = esCalidad ? 'success' : '';
                tablaHistorial += '<tr class="'+rowClass+'">'+
                    '<td><strong>'+item.NombreEstacion+'</strong></td>'+
                    '<td>'+empleado+'</td>'+
                    '<td class="text-right">'+cantidad+'</td>'+
                '</tr>';
            });
            
            tablaHistorial += '</tbody>'+
                '</table>'+
                '</div>'+
            '</div>';
        }
        
        var html = '<div class="card">'+
            '<div class="card-body">'+
                '<h4 style="font-weight: bold; margin-bottom: 16px;"><i class="fa fa-clipboard"></i> RESUMEN INSPECCIÓN</h4>'+
            '<h4 style="margin-bottom: 10px;">' + (opData ? opData.ItemCode : '') + '</h4>'+
                '<p style="margin: 5px 0;"><strong>OP:</strong> ' + (opData ? opData.OP : '') + '</p>'+
                '<p style="margin: 5px 0;"><strong>Centro:</strong> ' + (centroInspeccionData ? centroInspeccionData.nombre : '') + '</p>'+
                '<p style="margin: 5px 0;"><strong>Cantidad en Centro:</strong> <span class="label label-info">' + parseFloat(cantidadDisponible).toFixed(2) + '</span></p>'+
                
                htmlInspecciones +
                
                '<hr>'+
                
                '<div class="row">'+
                    '<div class="col-sm-12">'+
                        '<div style="margin-top: 10px;">'+
                            '<label style="font-weight: bold;">ID Inspección:</label>'+
                            '<input type="text" id="id_inspeccion_resumen" class="form-control input-sm" value="'+idInspeccionMostrar+'" readonly>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col-sm-12">'+
                        '<div style="margin-top: 10px;">'+
                            '<label style="font-weight: bold;">Fecha Inspección:</label>'+
                            '<input type="date" id="fecha_inspeccion" class="form-control input-sm" value="'+fechaFormateada+'" readonly>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col-sm-12">'+
                        '<div style="margin-top: 10px;">'+
                            '<label style="font-weight: bold;">Inspector:</label>'+
                            '<input type="text" id="nomInspector" class="form-control input-sm" value="'+nomInspector+'" readonly>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                
                '<div class="data-summary-block" style="margin-top: 15px; margin-bottom: 15px;">'+
                    '<div class="row">'+
                        '<div class="col-sm-6">'+
                            '<div class="data-summary-item">'+
                                '<small>INSPECCIONADA</small>'+
                                '<input type="number" id="cantidad_inspeccionada" class="form-control user-success" value="0.000" min="0" max="'+cantidadDisponible+'" step="0.001">'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-6">'+
                            '<div class="data-summary-item">'+
                                '<small>RECHAZADA</small>'+
                                '<input type="number" id="cantidad_rechazada" class="form-control user-error" value="0.000" min="0" step="0.001">'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                
                tablaHistorial +
                
                '<div style="margin-top: 15px;">'+
                    '<label style="font-weight: bold;">Observaciones Generales:</label>'+
                    '<textarea class="form-control textareaObservacionesGenerales" name="observaciones_generales" rows="4" style="resize:none; text-transform:uppercase; font-size: 12px;" placeholder="INGRESE OBSERVACIONES..."></textarea>'+
                '</div>'+
                
                '<div style="margin-top: 15px;">'+
                    '<button id="guardar_inspeccion" class="btn btn-success btn-lg btn-block"><i class="fa fa-save"></i> Guardar Inspección</button>'+
                '</div>'+
            '</div>'+
        '</div>';
        $('#resumen_inspeccion').html(html);
        
        $('.textareaObservacionesGenerales').on('input', function(){
            this.value = this.value.toUpperCase();
        });
        
        $('#fecha_inspeccion').on('dblclick', function(){
            $(this).prop('readonly', false);
            $(this).focus();
        });
        
        $('#fecha_inspeccion').on('blur', function(){
            $(this).prop('readonly', true);
        });
        
        $('#cantidad_inspeccionada, #cantidad_rechazada').on('click', function(){
            this.select();
        });
    }
    
    // Guardar inspección
    $(document).on('click', '#guardar_inspeccion', function(){
        // Validaciones...
        var rubrosSinSeleccionar = [];
        checklist.forEach(function(item){
            var estado = $('input[name="checklist_'+item.CHK_id+'"]:checked').val();
            if(!estado) {
                rubrosSinSeleccionar.push(item.CHK_descripcion);
            }
        });
        
        if(rubrosSinSeleccionar.length > 0) {
            swal({
                title: 'Checklist incompleto',
                text: 'Debe seleccionar una opción para los siguientes rubros:\n\n' + rubrosSinSeleccionar.join('\n'),
                type: 'warning',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        // Validar observaciones e imágenes cuando hay "No Cumple"
        var rubrosSinObservacion = [];
        var rubrosSinImagen = [];
        
        $('input[type="radio"][value="No Cumple"]').each(function() {
            var chkId = $(this).attr('name').replace('checklist_', '');
            
            if ($(this).is(':checked')) {
                var observacion = $('textarea[name="obs_' + chkId + '"]').val().trim();
                if (!observacion) {
                    var checklistItem = checklist.find(function(item) {
                        return item.CHK_id == chkId;
                    });
                    rubrosSinObservacion.push(checklistItem ? checklistItem.CHK_descripcion : 'Rubro ' + chkId);
                }
                
                var inputEvidencia = $('input[name="img_' + chkId + '"]')[0];
                if (!inputEvidencia.files || inputEvidencia.files.length === 0) {
                    var checklistItem = checklist.find(function(item) {
                        return item.CHK_id == chkId;
                    });
                    rubrosSinImagen.push(checklistItem ? checklistItem.CHK_descripcion : 'Rubro ' + chkId);
                }
            }
        });
        
        var errores = [];
        if (rubrosSinObservacion.length > 0) {
            errores.push('Debe agregar observaciones para rubros "No Cumple":\n' + rubrosSinObservacion.join(', '));
        }
        if (rubrosSinImagen.length > 0) {
            errores.push('Debe adjuntar imágenes para rubros "No Cumple":\n' + rubrosSinImagen.join(', '));
        }
        
        if (errores.length > 0) {
            swal({
                title: 'Campos obligatorios faltantes',
                text: errores.join('\n\n'),
                type: 'warning',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        var datos = new FormData();
        datos.append('op', opData.OP);
        datos.append('doc_entry', opData.DocEntry);
        datos.append('cod_articulo', opData.ItemCode);
        datos.append('nom_articulo', opData.ItemName);
        datos.append('cant_planeada', opData.CantidadPlaneada);
        datos.append('cant_inspeccionada', $('#cantidad_inspeccionada').val());
        datos.append('cant_rechazada', $('#cantidad_rechazada').val());
        datos.append('centro_inspeccion', centroInspeccionData.id);
        datos.append('nombre_centro', centroInspeccionData.nombre);
        datos.append('fecha_inspeccion', $('#fecha_inspeccion').val());
        datos.append('observaciones', $('.textareaObservacionesGenerales').val());
        
        // Agregar respuestas del checklist
        Object.keys(respuestas).forEach(function(chkId) {
            if (respuestas[chkId] && respuestas[chkId] !== 'No Aplica') {
                datos.append('checklist[' + chkId + ']', respuestas[chkId]);
                
                if (respuestas[chkId] === 'No Cumple') {
                    var cantidad = $('#cantidad_' + chkId).val() || 0;
                    datos.append('checklist_cantidad[' + chkId + ']', cantidad);
                }
                
                var observacion = $('textarea[name="obs_' + chkId + '"]').val() || '';
                datos.append('checklist_observacion[' + chkId + ']', observacion);
                
                var inputEvidencia = $('input[name="img_' + chkId + '"]')[0];
                if (inputEvidencia && inputEvidencia.files && inputEvidencia.files.length > 0) {
                    for (var i = 0; i < inputEvidencia.files.length; i++) {
                        datos.append('checklist_evidencias[' + chkId + '][]', inputEvidencia.files[i]);
                    }
                }
            }
        });
        
        $.blockUI({
            message: '<h1>Guardando inspección...</h1>',
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
        
        $.ajax({
            url: routeapp + '/home/inspeccion-proceso/guardar',
            type: 'POST',
            data: datos,
            processData: false,
            contentType: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(resp){
                $.unblockUI();
                
                if(resp.success) {
                    swal({
                        title: 'Guardado exitoso',
                        text: 'La inspección ha sido guardada con ID: ' + resp.id_inspeccion,
                        type: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                    
                    // Limpiar y recargar
                    $('#inspeccion_container').hide();
                    $('#numero_op').val('');
                    $('#cabecera_nota').hide();
                } else {
                    swal({
                        title: 'Error',
                        text: resp.msg || 'Error al guardar la inspección',
                        type: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function() {
                $.unblockUI();
                swal({
                    title: 'Error',
                    text: 'Error al guardar la inspección',
                    type: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });
}
