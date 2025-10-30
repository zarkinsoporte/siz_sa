// Variables globales
var opData = null;
var centroInspeccionData = null;
var checklist = [];
var respuestas = {};
var idInspeccion = 0;
var inspeccionesPrevias = [];
var historial = [];
var estacionesCalidadAgregadas = []; // Controla qué estaciones de calidad ya se agregaron sus defectivos

// Función global para manejar cambios en el checklist
function manejarChecklist(chkId, valor) {
    var selectEmpleado = $('#empleado_' + chkId);
    var textareaObservacion = $('textarea[name="obs_' + chkId + '"]');
    var btnEvidencia = $('#imagenes_' + chkId).siblings('.btnEvidencia');
    
    if (valor === 'No Cumple') {
        textareaObservacion.prop('required', true);
        textareaObservacion.attr('placeholder', 'OBSERVACIÓN OBLIGATORIA');
        btnEvidencia.attr('title', 'Adjuntar Evidencia (OBLIGATORIO)');
    } else {
        textareaObservacion.prop('required', false);
        textareaObservacion.attr('placeholder', '');
        btnEvidencia.attr('title', 'Adjuntar Evidencia');
    }
    
    // El selectpicker de empleado siempre permanece habilitado y mantiene su valor
    
    // Actualizar respuestas
    respuestas[chkId] = valor;
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
                estacionesCalidadAgregadas = []; // Resetear estaciones agregadas para nueva OP
                
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
        //console.log('renderChecklist - checklist:', checklist);
        var tbody = '';
        
        if(!checklist || checklist.length === 0) {
            tbody = '<tr><td colspan="7" class="text-center"><strong>No hay checklist configurado para este centro de inspección</strong></td></tr>';
        } else {
            checklist.forEach(function(item) {
                var respuesta = respuestas[item.CHK_id] || '';
                var observacion = respuestas[item.CHK_id + '_observacion'] || '';
                
                // Construir select de empleados (siempre habilitado)
                var selectEmpleados = '<select id="empleado_'+item.CHK_id+'" name="empleado_'+item.CHK_id+'" class="form-control boot-select selectEmpleado" data-live-search="true">';
                selectEmpleados += '<option value="">Seleccione empleado...</option>';
                
                if(item.empleados_permitidos && item.empleados_permitidos.length > 0) {
                    item.empleados_permitidos.forEach(function(emp) {
                        var nombreCompleto = emp.firstName + ' ' + emp.lastName;
                        var selected = (emp.empID == item.empleado_responsable_default) ? 'selected' : '';
                        selectEmpleados += '<option value="'+emp.empID+'" '+selected+'>'+nombreCompleto+'</option>';
                    });
                }
                selectEmpleados += '</select>';
                
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
                    '<td>'+selectEmpleados+'</td>'+
                    '<td><textarea class="form-control textareaObservacion" name="obs_'+item.CHK_id+'" rows="2" style="resize:none; text-transform:uppercase;">'+observacion+'</textarea></td>'+
                '</tr>';
            });
        }
        
        $('#checklist_body').html(tbody);
        
        // Inicializar selectpickers
        $('.selectEmpleado').selectpicker();
        
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
        var cantidadDisponible = parseFloat(centroInspeccionData ? centroInspeccionData.cantidad_disponible : 0) || 0;
        var cantidadEnCentro = parseFloat(centroInspeccionData ? centroInspeccionData.cantidad_en_centro : 0) || 0;
        var cantidadAceptada = parseFloat(centroInspeccionData ? centroInspeccionData.cantidad_aceptada : 0) || 0;
        
        var fechaActual = new Date();
        var fechaFormateada = fechaActual.getFullYear() + '-' + 
                             (fechaActual.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                             fechaActual.getDate().toString().padStart(2, '0');
        
        var idInspeccionMostrar = idInspeccion > 0 ? idInspeccion : 'Por definir';
        var nomInspector = typeof currentUser !== 'undefined' ? currentUser : 'Usuario Actual';
        
        // Botón de inspecciones previas
        var htmlInspecciones = '';
        
        if(inspeccionesPrevias && inspeccionesPrevias.length > 0) {
            var totalInspecciones = inspeccionesPrevias.length;
            var totalAceptadas = inspeccionesPrevias.filter(function(i){ return i.IPR_estado === 'ACEPTADO'; }).length;
            var totalRechazadas = inspeccionesPrevias.filter(function(i){ return i.IPR_estado === 'RECHAZADO'; }).length;
            
            htmlInspecciones = '<div style="margin-top: 15px; margin-bottom: 15px;">'+
                '<small><strong>INSPECCIONES PREVIAS:</strong></small><br>'+
                '<button id="btn_ver_historial_inspecciones" class="btn btn-info btn-block" style="margin-top: 5px;">'+
                    '<i class="fa fa-history"></i> Ver Historial de Inspecciones ('+totalInspecciones+')'+
                '</button>'+
                '<div style="font-size: 11px; margin-top: 5px; text-align: center;">'+
                    '<span class="text-success"><strong>'+totalAceptadas+'</strong> Aceptadas</span> | '+
                    '<span class="text-danger"><strong>'+totalRechazadas+'</strong> Rechazadas</span>'+
                '</div>'+
            '</div>';
        } else {
            htmlInspecciones = '<div style="margin-top: 15px; margin-bottom: 15px;">'+
                '<small><strong>NO HAY INSPECCIONES PREVIAS</strong></small><br>';
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
                '<input type="hidden" id="cantidad_disponible" value="' + cantidadDisponible.toFixed(2) + '">'+
                
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
                        '<div class="col-sm-12">'+
                            '<div class="data-summary-item">'+
                                '<small>CANTIDAD A INSPECCIONAR</small>'+
                                '<input type="number" id="cantidad_inspeccionada" class="form-control user-success" value="'+cantidadDisponible.toFixed(3)+'" min="0" max="'+cantidadDisponible+'" step="0.001">'+
                            '</div>'+
                        '</div>'+
                        
                    '</div>'+
                '</div>'+
                
                tablaHistorial +
                
                // Agregar defectivos de otras estaciones de calidad
                '<div style="margin-top: 15px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">'+
                    '<label style="font-weight: bold;"><i class="fa fa-plus-circle"></i> Agregar Defectivos de Otras Estaciones:</label>'+
                    '<div class="row" style="margin-top: 10px;">'+
                        '<div class="col-sm-8">'+
                            '<select id="select_estacion_calidad" class="form-control boot-select" data-live-search="true" title="Seleccione una estación...">'+
                            '</select>'+
                        '</div>'+
                        '<div class="col-sm-4">'+
                            '<button id="btn_agregar_defectivos" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Agregar</button>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                
                '<div style="margin-top: 15px;">'+
                    '<label style="font-weight: bold;">Observaciones Generales:</label>'+
                    '<textarea class="form-control textareaObservacionesGenerales" name="observaciones_generales" rows="4" style="resize:none; text-transform:uppercase; font-size: 12px;" placeholder="INGRESE OBSERVACIONES..."></textarea>'+
                '</div>'+
                
                '<div style="margin-top: 15px;">'+
                   '<div class="row">'+
                        '<div class="col-sm-6 text-center col-md-6">'+
                            '<button id="guardar_rechazo" class="btn btn-danger btn-lg btn-block"><i class="fa fa-ban"></i> Rechazado</button>'+
                        '</div>'+
                        '<div class="col-sm-6 text-center col-md-6">'+
                            '<button id="guardar_inspeccion" class="btn btn-success btn-lg btn-block"><i class="fa fa-check"></i> Aceptado</button>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>';
        $('#resumen_inspeccion').html(html);
        
        // Poblar selectpicker con estaciones de calidad del historial
        var estacionesCalidad = historial.filter(function(item) {
            return item.EsCalidad === 'S' && item.U_CT !== centroInspeccionData.id;
        });
        
        var optionsHtml = '';
        estacionesCalidad.forEach(function(estacion) {
            optionsHtml += '<option value="'+estacion.U_CT+'">'+estacion.NombreEstacion+'</option>';
        });
        $('#select_estacion_calidad').html(optionsHtml);
        $('#select_estacion_calidad').selectpicker('refresh');
        
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
        
        // Evento para ver historial de inspecciones
        $('#btn_ver_historial_inspecciones').on('click', function(){
            abrirModalHistorialInspecciones();
        });
        
        // Evento para agregar defectivos de otras estaciones
        $('#btn_agregar_defectivos').on('click', function(){
            var estacionSeleccionada = $('#select_estacion_calidad').val();
            
            if (!estacionSeleccionada) {
                swal({
                    title: 'Estación no seleccionada',
                    text: 'Debe seleccionar una estación de calidad',
                    type: 'warning',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            
            // Verificar si ya se agregaron los defectivos de esta estación
            if (estacionesCalidadAgregadas.indexOf(estacionSeleccionada) !== -1) {
                swal({
                    title: 'Defectivos ya agregados',
                    text: 'Los defectivos de esta estación ya fueron agregados',
                    type: 'warning',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            
            // Mostrar blockUI
            $.blockUI({
                message: '<h1>Cargando defectivos...</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
            
            // Obtener defectivos de la estación seleccionada
            $.ajax({
                url: routeapp + '/home/inspeccion-proceso/defectivos-estacion',
                type: 'GET',
                data: { 
                    area: estacionSeleccionada,
                    op: opData.OP
                },
                success: function(response){
                    $.unblockUI();
                    
                    if (response.success && response.defectivos && response.defectivos.length > 0) {
                        // Agregar los defectivos al checklist
                        response.defectivos.forEach(function(defectivo){
                            checklist.push(defectivo);
                        });
                        
                        // Marcar la estación como agregada
                        estacionesCalidadAgregadas.push(estacionSeleccionada);
                        
                        // Deshabilitar la opción en el selectpicker
                        $('#select_estacion_calidad option[value="'+estacionSeleccionada+'"]').prop('disabled', true);
                        $('#select_estacion_calidad').selectpicker('refresh');
                        
                        // Re-renderizar el checklist
                        renderChecklist();
                        
                        swal({
                            title: 'Defectivos agregados',
                            text: 'Se agregaron ' + response.defectivos.length + ' defectivos al checklist',
                            type: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        swal({
                            title: 'Sin defectivos',
                            text: response.msg || 'No se encontraron defectivos para esta estación',
                            type: 'info',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function(){
                    $.unblockUI();
                    swal({
                        title: 'Error',
                        text: 'Error al cargar los defectivos',
                        type: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        });
    }
    
    // Función para guardar inspección (común para aceptado y rechazado)
    function guardarInspeccion(estado) {
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
                text: 'Debe seleccionar una opción para todos los rubros',
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
        
        // Validar cantidad a inspeccionar
        var cantidadInspeccionada = parseFloat($('#cantidad_inspeccionada').val()) || 0;
        var cantidadDisponible = parseFloat($('#cantidad_disponible').val()) || 0;
        
        if (cantidadInspeccionada <= 0) {
            swal({
                title: 'Cantidad inválida',
                text: 'Debe ingresar una cantidad mayor a cero',
                type: 'warning',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        if (cantidadInspeccionada > cantidadDisponible) {
            swal({
                title: 'Cantidad excedida',
                text: 'La cantidad a inspeccionar (' + cantidadInspeccionada + ') no puede ser mayor a la disponible (' + cantidadDisponible + ')',
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
        datos.append('cant_inspeccionada', cantidadInspeccionada);
        datos.append('cant_rechazada', 0); // Ya no se usa este campo separado
        datos.append('centro_inspeccion', centroInspeccionData.id);
        datos.append('nombre_centro', centroInspeccionData.nombre);
        datos.append('fecha_inspeccion', $('#fecha_inspeccion').val());
        datos.append('observaciones', $('.textareaObservacionesGenerales').val());
        datos.append('estado', estado); // 'ACEPTADO' o 'RECHAZADO'
        
        // Agregar respuestas del checklist
        Object.keys(respuestas).forEach(function(chkId) {
            if (respuestas[chkId] && respuestas[chkId] !== 'No Aplica') {
                datos.append('checklist[' + chkId + ']', respuestas[chkId]);
                
                // Agregar empleado responsable
                var empleadoId = $('#empleado_' + chkId).val();
                if (empleadoId) {
                    datos.append('checklist_empleado[' + chkId + ']', empleadoId);
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
                    var estadoTexto = estado === 'ACEPTADO' ? 'ACEPTADA' : 'RECHAZADA';
                    swal({
                        title: 'Inspección ' + estadoTexto,
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
    }
    
    // Evento para guardar como ACEPTADO
    $(document).on('click', '#guardar_inspeccion', function(){
        guardarInspeccion('ACEPTADO');
    });
    
    // Evento para guardar como RECHAZADO
    $(document).on('click', '#guardar_rechazo', function(){
        guardarInspeccion('RECHAZADO');
    });
    
    // Función para abrir modal de historial de inspecciones
    function abrirModalHistorialInspecciones() {
        $('#modal_op_numero').text(opData.OP);
        
        $.blockUI({
            message: '<h1>Cargando historial...</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
            url: routeapp + '/home/inspeccion-proceso/historial-completo',
            type: 'GET',
            data: { 
                op: opData.OP,
                centro: centroInspeccionData.id
            },
            success: function(response){
                $.unblockUI();
                
                if (response.success && response.inspecciones && response.inspecciones.length > 0) {
                    renderHistorialInspecciones(response.inspecciones);
                    $('#modalHistorialInspecciones').modal('show');
                } else {
                    swal({
                        title: 'Sin historial',
                        text: 'No se encontraron inspecciones previas',
                        type: 'info',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(){
                $.unblockUI();
                swal({
                    title: 'Error',
                    text: 'Error al cargar el historial de inspecciones',
                    type: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    }
    
    // Función para renderizar el historial de inspecciones en el modal
    function renderHistorialInspecciones(inspecciones) {
        var html = '';
        
        inspecciones.forEach(function(insp, index) {
            var estadoClass = insp.IPR_estado === 'RECHAZADO' ? 'panel-danger' : 'panel-success';
            var estadoIcono = insp.IPR_estado === 'RECHAZADO' ? 'fa-ban' : 'fa-check-circle';
            var estadoTexto = insp.IPR_estado === 'RECHAZADO' ? 'RECHAZADA' : 'ACEPTADA';
            var estadoColor = insp.IPR_estado === 'RECHAZADO' ? '#d9534f' : '#5cb85c';
            
            // Formatear fecha
            var fechaInsp = new Date(insp.IPR_fechaInspeccion);
            var fechaFormateada = fechaInsp.getDate().toString().padStart(2, '0') + '/' + 
                                 (fechaInsp.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                                 fechaInsp.getFullYear() + ' ' +
                                 fechaInsp.getHours().toString().padStart(2, '0') + ':' +
                                 fechaInsp.getMinutes().toString().padStart(2, '0');
            
            html += '<div class="panel '+estadoClass+'" style="margin-bottom: 20px;">'+
                '<div class="panel-heading" style="background-color: '+estadoColor+'; color: white;">'+
                    '<h4 class="panel-title">'+
                        '<i class="fa '+estadoIcono+'"></i> Inspección #'+insp.IPR_id+' - '+estadoTexto+
                    '</h4>'+
                '</div>'+
                '<div class="panel-body">'+
                    '<div class="row">'+
                        '<div class="col-md-3">'+
                            '<strong>Fecha:</strong><br>'+fechaFormateada+
                        '</div>'+
                        '<div class="col-md-3">'+
                            '<strong>Inspector:</strong><br>'+insp.IPR_nomInspector+
                        '</div>'+
                        '<div class="col-md-3">'+
                            '<strong>Cantidad Inspeccionada:</strong><br>'+parseFloat(insp.IPR_cantInspeccionada).toFixed(2)+
                        '</div>'+
                        '<div class="col-md-3">'+
                            '<strong>Estado:</strong><br><span style="color: '+estadoColor+'; font-weight: bold;">'+estadoTexto+'</span>'+
                        '</div>'+
                    '</div>';
            
            // Mostrar observaciones si existen
            if (insp.IPR_observaciones) {
                html += '<div class="row" style="margin-top: 10px;">'+
                    '<div class="col-md-12">'+
                        '<strong>Observaciones Generales:</strong><br>'+
                        '<div style="background-color: #f5f5f5; padding: 10px; border-radius: 3px;">'+
                            insp.IPR_observaciones+
                        '</div>'+
                    '</div>'+
                '</div>';
            }
            
            // Tabla de detalles del checklist
            if (insp.detalles && insp.detalles.length > 0) {
                html += '<div class="row" style="margin-top: 15px;">'+
                    '<div class="col-md-12">'+
                        '<strong>Detalle del Checklist:</strong>'+
                        '<div style="max-height: 300px; overflow-y: auto; margin-top: 5px;">'+
                        '<table class="table table-bordered table-condensed" style="font-size: 12px; margin-bottom: 0;">'+
                            '<thead style="background-color: #f5f5f5;">'+
                                '<tr>'+
                                    '<th style="width: 50%;">Punto de Inspección</th>'+
                                    '<th style="width: 15%; text-align: center;">Estado</th>'+
                                    '<th style="width: 20%;">Empleado Resp.</th>'+
                                    '<th style="width: 15%;">Observaciones</th>'+
                                '</tr>'+
                            '</thead>'+
                            '<tbody>';
                
                insp.detalles.forEach(function(det) {
                    var estadoDet = '';
                    var estadoColor = '';
                    if (det.IPD_estado === 'C') {
                        estadoDet = 'Cumple';
                        estadoColor = 'text-success';
                    } else if (det.IPD_estado === 'N') {
                        estadoDet = 'No Cumple';
                        estadoColor = 'text-danger';
                    } else {
                        estadoDet = 'No Aplica';
                        estadoColor = 'text-muted';
                    }
                    
                    html += '<tr>'+
                        '<td>'+det.CHK_descripcion+'</td>'+
                        '<td style="text-align: center;"><strong class="'+estadoColor+'">'+estadoDet+'</strong></td>'+
                        '<td>'+(det.empleado_nombre || '-')+'</td>'+
                        '<td>'+(det.IPD_observacion || '-')+'</td>'+
                    '</tr>';
                });
                
                html += '</tbody>'+
                    '</table>'+
                    '</div>'+
                    '</div>'+
                '</div>';
            }
            
            html += '</div>'+  // cierre panel-body
                '</div>';  // cierre panel
        });
        
        $('#contenido_historial_inspecciones').html(html);
    }
}
