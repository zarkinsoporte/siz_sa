// Variables globales
var opData = null;
var centroInspeccionData = null;
var checklist = [];
var respuestas = {};
var idInspeccion = 0;
var inspeccionesPrevias = [];
var historial = [];
var estacionesCalidadAgregadas = []; // Controla qué estaciones de calidad ya se agregaron sus defectivos
var evidencias = {}; // Almacena las evidencias (archivos) por cada item del checklist: {chkId: [{file, dataUrl, tipo}]}

// Función global para manejar cambios en el checklist
function manejarChecklist(chkId, valor) {
    var selectEmpleado = $('#empleado_' + chkId);
    var textareaObservacion = $('textarea[name="obs_' + chkId + '"]');
    var btnEvidencia = $('#imagenes_' + chkId).siblings('.btnEvidencia');
    
    // Verificar si este item requiere evidencia (empieza con "Foto" o "Video" en centros 169 o 175)
    var requiereEvidencia = btnEvidencia.attr('data-requiere-foto') === '1';
    var tipoEvidencia = btnEvidencia.attr('data-tipo-evidencia') || 'imagen';
    
    // Verificar si hay al menos un "No Cumple" en el checklist
    var hayNoCumple = $('input[type="radio"][value="No Cumple"]:checked').length > 0;
    
    if (valor === 'No Cumple') {
        textareaObservacion.prop('required', true);
        textareaObservacion.attr('placeholder', 'OBSERVACIÓN OBLIGATORIA');
        if (tipoEvidencia === 'video') {
            btnEvidencia.attr('title', 'Adjuntar Video (OBLIGATORIO)');
        } else {
            btnEvidencia.attr('title', 'Adjuntar Evidencia (OBLIGATORIO)');
        }
    } else {
        textareaObservacion.prop('required', false);
        textareaObservacion.attr('placeholder', '');
        
        // Si requiere evidencia (Foto/Video), solo es obligatorio si NO hay ningún "No Cumple"
        // Si hay al menos un "No Cumple", las evidencias de Foto/Video ya no son obligatorias
        if (requiereEvidencia && !hayNoCumple) {
            if (tipoEvidencia === 'video') {
                btnEvidencia.attr('title', 'Adjuntar Video (OBLIGATORIO)');
            } else {
                btnEvidencia.attr('title', 'Adjuntar Evidencia (OBLIGATORIO)');
            }
        } else {
            btnEvidencia.attr('title', 'Adjuntar Evidencia');
        }
    }
    
    // El selectpicker de empleado siempre permanece habilitado y mantiene su valor
    
    // Actualizar respuestas
    respuestas[chkId] = valor;
    
    // Actualizar visibilidad de botones y estado de evidencias obligatorias según si hay "No Cumple"
    actualizarBotonesInspeccion();
}

// Función para actualizar la visibilidad de los botones según el estado del checklist
function actualizarBotonesInspeccion() {
    // Contar cuántos puntos están marcados como "No Cumple"
    var hayNoCumple = $('input[type="radio"][value="No Cumple"]:checked').length > 0;
    
    if (hayNoCumple) {
        // Si hay al menos un "No Cumple", mostrar solo el botón RECHAZADO
        $('#guardar_inspeccion').hide();
        $('#guardar_rechazo').show();
        
        // Actualizar títulos de evidencias de "Foto" y "Video" para que NO sean obligatorias
        $('.btnEvidencia[data-requiere-foto="1"]').each(function() {
            var chkId = $(this).siblings('.inputEvidencia').attr('name').replace('img_', '');
            var tipoEvidencia = $(this).attr('data-tipo-evidencia') || 'imagen';
            var radioSeleccionado = $('input[name="checklist_' + chkId + '"]:checked').val();
            
            // Solo cambiar el título si NO está marcado como "No Cumple"
            // Si está marcado como "No Cumple", ya se maneja en manejarChecklist
            if (radioSeleccionado !== 'No Cumple') {
                if (tipoEvidencia === 'video') {
                    $(this).attr('title', 'Adjuntar Video');
                } else {
                    $(this).attr('title', 'Adjuntar Evidencia');
                }
            }
        });
    } else {
        // Si no hay "No Cumple", mostrar solo el botón ACEPTADO
        $('#guardar_inspeccion').show();
        $('#guardar_rechazo').hide();
        
        // Restaurar títulos de evidencias de "Foto" y "Video" como obligatorias
        $('.btnEvidencia[data-requiere-foto="1"]').each(function() {
            var chkId = $(this).siblings('.inputEvidencia').attr('name').replace('img_', '');
            var tipoEvidencia = $(this).attr('data-tipo-evidencia') || 'imagen';
            var radioSeleccionado = $('input[name="checklist_' + chkId + '"]:checked').val();
            
            // Solo cambiar el título si NO está marcado como "No Cumple"
            if (radioSeleccionado !== 'No Cumple') {
                if (tipoEvidencia === 'video') {
                    $(this).attr('title', 'Adjuntar Video (OBLIGATORIO)');
                } else {
                    $(this).attr('title', 'Adjuntar Evidencia (OBLIGATORIO)');
                }
            }
        });
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
        
        $.ajax({
            url: routeapp+'/home/inspeccion-proceso/buscar',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                op: numeroOP
            },
            dataType: 'json',
            success: function(data){
                if(data.success) {
                    opData = data.op;
                    centroInspeccionData = data.centro_inspeccion;
                    checklist = data.checklist;
                    historial = data.historial;
                    inspeccionesPrevias = data.inspecciones_previas;
                    respuestas = {};
                    evidencias = {}; // Resetear evidencias para nueva OP
                    estacionesCalidadAgregadas = []; // Resetear estaciones agregadas para nueva OP
                    
                    // Renderizar información
                    renderCabeceraOP();
                    renderChecklist();
                    renderResumen();
                    $('#inspeccion_container').show();
                    $('#cabecera_nota').show();
                }
                $.unblockUI();
            },
            error: function(jqXHR) {
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
            }
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
            // Verificar si el centro es 169 o 175
            var centroId = centroInspeccionData ? centroInspeccionData.id : '';
            var esCentroFoto = (centroId === '169' || centroId === '175');
            
            checklist.forEach(function(item) {
                var respuesta = respuestas[item.CHK_id] || '';
                var observacion = respuestas[item.CHK_id + '_observacion'] || '';
                
                // Verificar si la descripción empieza con "Foto" o "Video"
                var descripcionUpper = item.CHK_descripcion ? item.CHK_descripcion.trim().toUpperCase() : '';
                var empiezaConFoto = esCentroFoto && descripcionUpper.startsWith('FOTO');
                var empiezaConVideo = esCentroFoto && descripcionUpper.startsWith('VIDEO');
                
                // Aplicar estilo de fondo si empieza con "Foto" o "Video"
                var rowStyle = '';
                var rowClass = '';
                if (empiezaConFoto) {
                    rowStyle = 'style="background-color: #fff3cd;"'; // Color amarillo claro
                    rowClass = 'class="foto-requerida"';
                } else if (empiezaConVideo) {
                    rowStyle = 'style="background-color: #d1ecf1;"'; // Color azul claro
                    rowClass = 'class="video-requerido"';
                }
                
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
                
                // Título del botón de evidencia: obligatorio si empieza con "Foto" o "Video"
                var tituloEvidencia = '';
                var iconoBoton = '';
                var acceptFiles = '';
                var tipoEvidencia = '';
                
                if (empiezaConVideo) {
                    tituloEvidencia = 'Adjuntar Video (OBLIGATORIO)';
                    iconoBoton = '<span class="glyphicon glyphicon-facetime-video"></span>';
                    acceptFiles = '.mp4,.mov,.avi,.wmv';
                    tipoEvidencia = 'video';
                } else if (empiezaConFoto) {
                    tituloEvidencia = 'Adjuntar Evidencia (OBLIGATORIO)';
                    iconoBoton = '<span class="glyphicon glyphicon-camera"></span>';
                    acceptFiles = '.jpg,.jpeg,.png';
                    tipoEvidencia = 'imagen';
                } else {
                    tituloEvidencia = 'Adjuntar Evidencia';
                    iconoBoton = '<span class="glyphicon glyphicon-camera"></span>';
                    acceptFiles = '.jpg,.jpeg,.png';
                    tipoEvidencia = 'imagen';
                }
                
                var requiereEvidencia = (empiezaConFoto || empiezaConVideo) ? '1' : '0';
                
                tbody += '<tr '+rowClass+' '+rowStyle+'>'+
                    '<td>'+
                        '<button type="button" class="btn btn-primary btn-sm btnEvidencia" title="'+tituloEvidencia+'" data-requiere-foto="'+requiereEvidencia+'" data-tipo-evidencia="'+tipoEvidencia+'">'+iconoBoton+'</button>'+
                        '<input type="file" name="img_'+item.CHK_id+'" accept="'+acceptFiles+'" style="display:none;" class="inputEvidencia" data-requiere-foto="'+requiereEvidencia+'" data-tipo-evidencia="'+tipoEvidencia+'" '+(empiezaConVideo ? '' : 'multiple')+'>'+
                        '<div class="imagenes-previas" id="imagenes_'+item.CHK_id+'" style="margin-top: 5px;"></div>'+
                    '</td>'+
                    '<td>'+item.CHK_descripcion+'</td>'+
                    '<td><input type="radio" name="checklist_'+item.CHK_id+'" value="Cumple" '+(respuesta === 'Cumple' ? 'checked' : '')+' onchange="manejarChecklist('+item.CHK_id+', this.value)" data-requiere-foto="'+requiereEvidencia+'"></td>'+
                    '<td><input type="radio" name="checklist_'+item.CHK_id+'" value="No Cumple" '+(respuesta === 'No Cumple' ? 'checked' : '')+' onchange="manejarChecklist('+item.CHK_id+', this.value)" data-requiere-foto="'+requiereEvidencia+'"></td>'+
                    '<td><input type="radio" name="checklist_'+item.CHK_id+'" value="No Aplica" '+(respuesta === 'No Aplica' ? 'checked' : '')+' onchange="manejarChecklist('+item.CHK_id+', this.value)" data-requiere-foto="'+requiereEvidencia+'"></td>'+
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
            var tipoEvidencia = $(this).attr('data-tipo-evidencia') || 'imagen';
         
            if(files.length > 0) {
                // Inicializar array de evidencias para este item
                evidencias[chkId] = [];
                contenedorImagenes.empty();
                
                var archivosProcesados = 0;
                var totalArchivos = files.length;
                
                for(var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var fileObj = file; // Guardar referencia del file para usar en el callback
                    
                    if (tipoEvidencia === 'video') {
                        // Validación para videos
                        var allowedVideoTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv', 'video/avi'];
                        var allowedExtensions = ['.mp4', '.mov', '.avi', '.wmv'];
                        var fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                        
                        if(allowedVideoTypes.indexOf(file.type) === -1 && allowedExtensions.indexOf(fileExtension) === -1) {
                            swal({
                                title: 'Tipo de archivo no válido',
                                text: 'Solo se permiten archivos de video: MP4, MOV, AVI, WMV',
                                type: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                            this.value = '';
                            return;
                        }
         
                        // Tamaño máximo para videos: 60MB
                        if(file.size > 100 * 1024 * 1024) {
                            swal({
                                title: 'Archivo demasiado grande',
                                text: 'El video no debe superar 100MB',
                                type: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                            this.value = '';
                            return;
                        }
     
                        // Crear Blob URL para el video (funciona mejor que data URL)
                        var blobUrl = URL.createObjectURL(file);
                        evidencias[chkId].push({
                            file: file,
                            dataUrl: blobUrl, // Usar Blob URL en lugar de data URL
                            tipo: 'video',
                            nombre: file.name
                        });
                        
                        archivosProcesados++;
                        if (archivosProcesados === totalArchivos) {
                            mostrarBotonEvidencias(chkId, tipoEvidencia);
                        }
                    } else {
                        // Validación para imágenes
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
     
                        if(file.size > 10 * 1024 * 1024) {
                            swal({
                                title: 'Archivo demasiado grande',
                                text: 'El archivo no debe superar 10MB',
                                type: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                            this.value = '';
                            return;
                        }
     
                        var reader = new FileReader();
                        reader.onload = (function(file, chkId, tipoEvidencia) {
                            return function(e) {
                                evidencias[chkId].push({
                                    file: file,
                                    dataUrl: e.target.result,
                                    tipo: 'imagen',
                                    nombre: file.name
                                });
                                
                                archivosProcesados++;
                                if (archivosProcesados === totalArchivos) {
                                    mostrarBotonEvidencias(chkId, tipoEvidencia);
                                }
                            };
                        })(fileObj, chkId, tipoEvidencia);
                        reader.readAsDataURL(file);
                    }
                }
                
                var iconoOk = tipoEvidencia === 'video' ? '<span class="glyphicon glyphicon-ok text-white"></span>' : '<span class="glyphicon glyphicon-ok text-white"></span>';
                $(this).siblings('.btnEvidencia').html(iconoOk);
                
                var tipoTexto = tipoEvidencia === 'video' ? 'video(s)' : 'archivo(s)';
                swal({
                    title: 'Archivos adjuntados',
                    text: 'Se han cargado ' + files.length + ' ' + tipoTexto + ' correctamente',
                    type: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
        
        $('.textareaObservacion').on('input', function(){
            this.value = this.value.toUpperCase();
        });
        
        // Agregar listeners para actualizar botones cuando cambien los radios
        $('input[type="radio"][name^="checklist_"]').on('change', function(){
            actualizarBotonesInspeccion();
        });
        
        // Inicializar items que empiezan con "Foto" o "Video" (centros 169 y 175)
        // Estos items requieren evidencia SOLO si NO hay ningún "No Cumple" en el checklist
        var hayNoCumple = $('input[type="radio"][value="No Cumple"]:checked').length > 0;
        
        $('.btnEvidencia[data-requiere-foto="1"]').each(function() {
            var chkId = $(this).siblings('.inputEvidencia').attr('name').replace('img_', '');
            var radioSeleccionado = $('input[name="checklist_' + chkId + '"]:checked').val();
            var tipoEvidencia = $(this).attr('data-tipo-evidencia') || 'imagen';
            
            // Si hay al menos un "No Cumple", las evidencias de "Foto" y "Video" ya NO son obligatorias
            // Solo son obligatorias si NO hay ningún "No Cumple" y el item no está marcado como "No Cumple"
            if (!hayNoCumple && radioSeleccionado !== 'No Cumple') {
                if (tipoEvidencia === 'video') {
                    $(this).attr('title', 'Adjuntar Video (OBLIGATORIO)');
                } else {
                    $(this).attr('title', 'Adjuntar Evidencia (OBLIGATORIO)');
                }
            } else {
                if (tipoEvidencia === 'video') {
                    $(this).attr('title', 'Adjuntar Video');
                } else {
                    $(this).attr('title', 'Adjuntar Evidencia');
                }
            }
            
            // Si hay un radio seleccionado, llamar a manejarChecklist para asegurar el estado correcto
            if (radioSeleccionado) {
                manejarChecklist(chkId, radioSeleccionado);
            }
        });
        
        // Actualizar botones inicialmente
        actualizarBotonesInspeccion();
    }
    
    // Función para mostrar botón de evidencias en lugar de vistas previas
    function mostrarBotonEvidencias(chkId, tipoEvidencia) {
        var contenedor = $('#imagenes_' + chkId);
        var cantidad = evidencias[chkId] ? evidencias[chkId].length : 0;
        var tipoTexto = tipoEvidencia === 'video' ? 'video' : 'imagen';
        var tipoTextoPlural = tipoEvidencia === 'video' ? 'videos' : 'imágenes';
        
        if (cantidad > 0) {
            var botonHtml = '<button type="button" class="btn btn-info btn-xs btnVerEvidencias" data-chk-id="' + chkId + '" style="margin-top: 5px;">' +
                '<i class="fa fa-eye"></i> Ver ' + (cantidad === 1 ? tipoTexto : tipoTextoPlural) + ' (' + cantidad + ')' +
                '</button>';
            contenedor.html(botonHtml);
        }
    }
    
    // Función para abrir modal de evidencias
    function abrirModalEvidencias(chkId) {
        if (!evidencias[chkId] || evidencias[chkId].length === 0) {
            swal({
                title: 'Sin evidencias',
                text: 'No hay evidencias para mostrar',
                type: 'info',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        // Separar videos e imágenes
        var videos = [];
        var imagenes = [];
        
        evidencias[chkId].forEach(function(evidencia) {
            if (evidencia.tipo === 'video') {
                videos.push(evidencia);
            } else {
                imagenes.push(evidencia);
            }
        });
        
        // Abrir videos en nuevas pestañas
        videos.forEach(function(video, index) {
            // Verificar y recrear Blob URL si es necesario
            var videoUrl = video.dataUrl;
            if (!videoUrl || (!videoUrl.startsWith('blob:') && !videoUrl.startsWith('data:'))) {
                if (video.file) {
                    videoUrl = URL.createObjectURL(video.file);
                    video.dataUrl = videoUrl;
                }
            }
            
            // Abrir video en nueva pestaña
            var nuevaVentana = window.open(videoUrl, '_blank');
            if (!nuevaVentana) {
                // Si el popup fue bloqueado, mostrar mensaje
                swal({
                    title: 'Popup bloqueado',
                    text: 'Por favor, permite ventanas emergentes para este sitio y vuelve a intentar, o descarga el video directamente.',
                    type: 'warning',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
        
        // Si solo hay videos, no mostrar el modal
        if (imagenes.length === 0) {
            return;
        }
        
        // Mostrar imágenes en el modal
        // Obtener el nombre del item del checklist
        var checklistItem = checklist.find(function(item) {
            return item.CHK_id == chkId;
        });
        var tituloItem = checklistItem ? checklistItem.CHK_descripcion : 'Item ' + chkId;
        
        $('#modal_evidencia_titulo').text(tituloItem);
        
        var html = '<div class="row">';
        imagenes.forEach(function(evidencia, index) {
            html += '<div class="col-md-6" style="margin-bottom: 20px;">' +
                '<h5><i class="fa fa-image"></i> Imagen ' + (index + 1) + ': ' + evidencia.nombre + '</h5>' +
                '<img src="' + evidencia.dataUrl + '" style="width: 100%; max-height: 400px; object-fit: contain; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onclick="window.open(this.src, \'_blank\')" title="Click para ver en tamaño completo">' +
                '</div>';
        });
        html += '</div>';
        
        $('#contenido_evidencias').html(html);
        
        // Limpiar el contenido del modal cuando se cierra
        $('#modalEvidencias').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            $('#contenido_evidencias').html('');
        });
        
        // Asegurar que los videos se carguen correctamente después de que el modal se muestre
        $('#modalEvidencias').off('shown.bs.modal').on('shown.bs.modal', function() {
            setTimeout(function() {
                $('#modalEvidencias video').each(function() {
                    var video = this;
                    
                    // Verificar que el src esté establecido
                    if (!video.src || video.src === '') {
                        console.error('Video sin src:', video.id);
                        return;
                    }
                    
                    // Establecer estilos explícitos para asegurar visibilidad
                    $(video).css({
                        'display': 'block',
                        'width': '100%',
                        'max-width': '800px',
                        'height': 'auto',
                        'min-height': '400px',
                        'max-height': '600px',
                        'background-color': '#000',
                        'margin': '0 auto',
                        'border': '2px solid #333',
                        'object-fit': 'contain'
                    });
                    
                    // Remover atributos que puedan interferir
                    video.removeAttribute('width');
                    video.removeAttribute('height');
                    
                    // Forzar carga
                    if (video.load) {
                        video.load();
                    }
                    
                    // Event listeners para debugging y ajuste de dimensiones
                    video.addEventListener('loadedmetadata', function() {
                        console.log('Metadatos del video cargados:', {
                            id: video.id,
                            videoWidth: video.videoWidth,
                            videoHeight: video.videoHeight,
                            duration: video.duration
                        });
                        
                        // Mostrar información del video
                        var infoDiv = $('#video_info_' + video.id);
                        if (infoDiv.length) {
                            infoDiv.html('Dimensiones: ' + video.videoWidth + 'x' + video.videoHeight + ' | Duración: ' + (video.duration ? video.duration.toFixed(2) + 's' : 'N/A'));
                        }
                        
                        // Si el video tiene dimensiones válidas, ajustar el contenedor
                        if (video.videoWidth > 0 && video.videoHeight > 0) {
                            var aspectRatio = video.videoHeight / video.videoWidth;
                            var containerWidth = Math.min(800, $(video).parent().width() - 40);
                            var calculatedHeight = containerWidth * aspectRatio;
                            
                            // Asegurar que el video tenga altura visible
                            if (calculatedHeight < 300) {
                                calculatedHeight = 300;
                            } else if (calculatedHeight > 600) {
                                calculatedHeight = 600;
                            }
                            
                            $(video).css({
                                'width': containerWidth + 'px',
                                'height': calculatedHeight + 'px',
                                'min-height': calculatedHeight + 'px'
                            });
                            
                            console.log('Dimensiones del video ajustadas:', {
                                width: containerWidth,
                                height: calculatedHeight,
                                aspectRatio: aspectRatio
                            });
                        }
                        
                        // Intentar cargar y mostrar el primer frame
                        video.muted = true;
                        video.currentTime = 0.01;
                        
                        // Esperar a que los metadatos se carguen antes de intentar reproducir
                        setTimeout(function() {
                            var playPromise = video.play();
                            if (playPromise !== undefined) {
                                playPromise.then(function() {
                                    // Video se está reproduciendo, esperar un poco más para que se renderice el frame
                                    setTimeout(function() {
                                        video.pause();
                                        video.muted = false;
                                        video.currentTime = 0;
                                        // Forzar actualización visual
                                        video.style.visibility = 'visible';
                                        video.style.opacity = '1';
                                    }, 500);
                                }).catch(function(err) {
                                    console.log('No se pudo reproducir automáticamente:', err);
                                    video.muted = false;
                                    // Intentar cargar el frame de otra manera
                                    video.currentTime = 0;
                                    if (video.load) {
                                        video.load();
                                    }
                                });
                            }
                        }, 200);
                    }, { once: true });
                    
                    video.addEventListener('loadeddata', function() {
                        console.log('Datos del video cargados:', video.id);
                        // Forzar repintado
                        video.style.display = 'none';
                        video.offsetHeight; // Trigger reflow
                        video.style.display = 'block';
                    }, { once: true });
                    
                    video.addEventListener('canplay', function() {
                        console.log('Video puede reproducirse:', video.id);
                        // Asegurar que el video sea visible
                        $(video).css('visibility', 'visible');
                        $(video).css('opacity', '1');
                    }, { once: true });
                    
                    video.addEventListener('playing', function() {
                        console.log('Video reproduciéndose:', video.id);
                        // Asegurar visibilidad cuando se reproduce
                        $(video).css('visibility', 'visible');
                        $(video).css('opacity', '1');
                    });
                    
                    video.addEventListener('error', function(e) {
                        console.error('Error al cargar video:', {
                            id: video.id,
                            error: e,
                            errorCode: video.error ? video.error.code : 'unknown'
                        });
                        var videoNombre = $(video).closest('.col-md-12').find('h5').text().replace(/Video \d+: /, '') || 'video';
                        var errorMsg = '<div class="alert alert-danger" style="margin-top: 10px;">' +
                            '<strong>Error al cargar el video.</strong><br>' +
                            '<a href="' + video.src + '" download="' + videoNombre + '" class="btn btn-sm btn-danger">' +
                            '<i class="fa fa-download"></i> Descargar Video' +
                            '</a>' +
                            '</div>';
                        $(video).parent().append(errorMsg);
                    }, { once: true });
                    
                    // Intentar cargar el primer frame
                    video.currentTime = 0.1;
                });
            }, 300);
        });
        
        $('#modalEvidencias').modal('show');
    }
    
    // Evento para abrir modal de evidencias
    $(document).on('click', '.btnVerEvidencias', function() {
        var chkId = $(this).attr('data-chk-id');
        abrirModalEvidencias(chkId);
    });
    
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
        
        // Botón de historial de rechazos
        var htmlInspecciones = '';
        
        if(inspeccionesPrevias && inspeccionesPrevias.length > 0) {
            var totalRechazadas = inspeccionesPrevias.filter(function(i){ return i.IPR_estado === 'RECHAZADO'; }).length;
            
            if(totalRechazadas > 0) {
                htmlInspecciones = '<div style="margin-top: 15px; margin-bottom: 15px;">'+
                    '<small><strong>HISTORIAL DE RECHAZOS:</strong></small><br>'+
                    '<button id="btn_ver_historial_rechazos" class="btn btn-info btn-block" style="margin-top: 5px;">'+
                        ' Ver Rechazos / Reprocesos ('+totalRechazadas+')'+
                    '</button>'+
                '</div>';
            } else {
                htmlInspecciones = '<div style="margin-top: 15px; margin-bottom: 15px;">'+
                    '<small><strong>NO HAY RECHAZOS PREVIOS</strong></small><br>'+
                    '<div style="font-size: 11px; margin-top: 5px; text-align: center; color: #28a745;">'+
                        '<strong>✓ No se han registrado rechazos en este centro</strong>'+
                    '</div>'+
                '</div>';
            }
        } else {
            htmlInspecciones = '<div style="margin-top: 15px; margin-bottom: 15px;">'+
                '<small><strong>NO HAY RECHAZOS PREVIOS</strong></small><br>';
            htmlInspecciones += '</div>';
        }
        
        // Botón para ver historial de OP en modal
        var botonHistorialOP = '';
        if(historial && historial.length > 0) {
            botonHistorialOP = '<div style="margin-top: 15px; margin-bottom: 15px;">'+
                '<button id="btn_ver_historial_op" class="btn btn-info btn-block">'+
                    '<i class="fa fa-list"></i> Ver Historial de la OP'+
                '</button>'+
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
                
                botonHistorialOP +
                
                // Agregar defectivos de otras estaciones de calidad
                '<div style="margin-top: 15px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">'+
                    '<label style="font-weight: bold;"><i class="fa fa-plus-circle"></i> Agregar Defectivos de Otras Estaciones:</label>'+
                    '<div class="row" style="margin-top: 10px;">'+
                        '<div class="col-sm-12">'+
                            '<select id="select_estacion_calidad" class="form-control boot-select" data-live-search="true" title="Seleccione una estación...">'+
                            '</select>'+
                        '</div>'+
                        '<div class="col-sm-12" style="margin-top: 5px;">'+
                            '<button id="btn_agregar_defectivos" class="btn btn-primary btn-block">Agregar</button>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                
                '<div style="margin-top: 15px;">'+
                    '<label style="font-weight: bold;">Observaciones Generales:</label>'+
                    '<textarea class="form-control textareaObservacionesGenerales" name="observaciones_generales" rows="4" style="resize:none; text-transform:uppercase; font-size: 12px;" placeholder="INGRESE OBSERVACIONES..."></textarea>'+
                '</div>'+
                
                '<div style="margin-top: 15px;">'+
                   '<div class="row">'+
                        '<div class="col-sm-12 text-center">'+
                            '<button id="guardar_rechazo" class="btn btn-danger btn-lg btn-block" style="display: none;"><i class="fa fa-ban"></i> Rechazado</button>'+
                            '<button id="guardar_inspeccion" class="btn btn-success btn-lg btn-block"><i class="fa fa-check"></i> Aceptado</button>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>';
        $('#resumen_inspeccion').html(html);
        
        // Poblar selectpicker con estaciones de calidad del historial o es la U_CT 115
        var estacionesCalidad = historial.filter(function(item) {
            return (item.EsCalidad === 'S' && item.U_CT !== centroInspeccionData.id) || item.U_CT === '115';
        });
        //
        
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
        
        // Actualizar botones inicialmente después de renderizar el resumen
        // (se actualizará nuevamente cuando se renderice el checklist)
        setTimeout(function(){
            actualizarBotonesInspeccion();
        }, 100);
        
        // Evento para ver historial de rechazos
        $(document).on('click', '#btn_ver_historial_rechazos', function(){
            abrirModalHistorialRechazos();
        });
        
        // Evento para ver historial de OP
        $(document).on('click', '#btn_ver_historial_op', function(){
            abrirModalHistorialOP();
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
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
            // Cerrar el SweetAlert de "Procesando..." si está abierto y mostrar error después
            swal.close();
            // Rehabilitar botones
            $('#guardar_inspeccion, #guardar_rechazo').prop('disabled', false).removeClass('disabled');
            
            // Usar setTimeout para asegurar que el SweetAlert anterior se cierre antes de mostrar el nuevo
            setTimeout(function() {
                swal({
                    title: 'Checklist incompleto',
                    text: 'Debe seleccionar una opción para todos los rubros',
                    type: 'warning',
                    confirmButtonText: 'Aceptar'
                });
            }, 300);
            return;
        }
        
        // Validar observaciones e imágenes cuando hay "No Cumple"
        var rubrosSinObservacion = [];
        var rubrosSinImagenNoCumple = [];
        var rubrosSinImagenFoto = [];
        
        // Validar "No Cumple"
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
                
                // Solo validar imagen si NO es un item que empieza con "Foto" (esos se validan por separado)
                var inputEvidencia = $('input[name="img_' + chkId + '"]')[0];
                var requiereFoto = $(inputEvidencia).attr('data-requiere-foto') === '1';
                
                if (!requiereFoto && (!inputEvidencia.files || inputEvidencia.files.length === 0)) {
                    var checklistItem = checklist.find(function(item) {
                        return item.CHK_id == chkId;
                    });
                    rubrosSinImagenNoCumple.push(checklistItem ? checklistItem.CHK_descripcion : 'Rubro ' + chkId);
                }
            }
        });
        
        // Validar evidencias obligatorias (Fotos y Videos) para items que empiezan con "Foto" o "Video" (centros 169 y 175)
        // IMPORTANTE: Si hay al menos un "No Cumple", estas evidencias ya NO son obligatorias
        var hayNoCumple = $('input[type="radio"][value="No Cumple"]:checked').length > 0;
        var rubrosSinVideo = [];
        
        // Solo validar evidencias de "Foto" y "Video" si NO hay ningún "No Cumple"
        if (!hayNoCumple) {
            $('input[type="file"].inputEvidencia[data-requiere-foto="1"]').each(function() {
                var chkId = $(this).attr('name').replace('img_', '');
                var tipoEvidencia = $(this).attr('data-tipo-evidencia') || 'imagen';
                
                if (!this.files || this.files.length === 0) {
                    var checklistItem = checklist.find(function(item) {
                        return item.CHK_id == chkId;
                    });
                    var descripcion = checklistItem ? checklistItem.CHK_descripcion : 'Rubro ' + chkId;
                    
                    if (tipoEvidencia === 'video') {
                        rubrosSinVideo.push(descripcion);
                    } else {
                        rubrosSinImagenFoto.push(descripcion);
                    }
                }
            });
        }
        
        var errores = [];
        if (rubrosSinObservacion.length > 0) {
            errores.push('Debe agregar observaciones para rubros "No Cumple":\n' + rubrosSinObservacion.join(', '));
        }
        if (rubrosSinImagenNoCumple.length > 0) {
            errores.push('Debe adjuntar imágenes para rubros "No Cumple":\n' + rubrosSinImagenNoCumple.join(', '));
        }
        if (rubrosSinImagenFoto.length > 0) {
            errores.push('Debe adjuntar imágenes obligatorias (items que empiezan con "Foto"):\n' + rubrosSinImagenFoto.join(', '));
        }
        if (rubrosSinVideo.length > 0) {
            errores.push('Debe adjuntar videos obligatorios (items que empiezan con "Video"):\n' + rubrosSinVideo.join(', '));
        }
        
        if (errores.length > 0) {
            // Cerrar el SweetAlert de "Procesando..." si está abierto y mostrar error después
            swal.close();
            // Rehabilitar botones
            $('#guardar_inspeccion, #guardar_rechazo').prop('disabled', false).removeClass('disabled');
            
            // Usar setTimeout para asegurar que el SweetAlert anterior se cierre antes de mostrar el nuevo
            setTimeout(function() {
                swal({
                    title: 'Campos obligatorios faltantes',
                    text: errores.join('\n\n'),
                    type: 'warning',
                    confirmButtonText: 'Aceptar'
                });
            }, 300);
            return;
        }
        
        // Validar cantidad a inspeccionar
        var cantidadInspeccionada = parseFloat($('#cantidad_inspeccionada').val()) || 0;
        var cantidadDisponible = parseFloat($('#cantidad_disponible').val()) || 0;
        
        if (cantidadInspeccionada <= 0) {
            // Cerrar el SweetAlert de "Procesando..." si está abierto y mostrar error después
            swal.close();
            // Rehabilitar botones
            $('#guardar_inspeccion, #guardar_rechazo').prop('disabled', false).removeClass('disabled');
            
            // Usar setTimeout para asegurar que el SweetAlert anterior se cierre antes de mostrar el nuevo
            setTimeout(function() {
                swal({
                    title: 'Cantidad inválida',
                    text: 'Debe ingresar una cantidad mayor a cero',
                    type: 'warning',
                    confirmButtonText: 'Aceptar'
                });
            }, 300);
            return;
        }
        
        if (cantidadInspeccionada > cantidadDisponible) {
            // Cerrar el SweetAlert de "Procesando..." si está abierto y mostrar error después
            swal.close();
            // Rehabilitar botones
            $('#guardar_inspeccion, #guardar_rechazo').prop('disabled', false).removeClass('disabled');
            
            // Usar setTimeout para asegurar que el SweetAlert anterior se cierre antes de mostrar el nuevo
            setTimeout(function() {
                swal({
                    title: 'Cantidad excedida',
                    text: 'La cantidad a inspeccionar (' + cantidadInspeccionada + ') no puede ser mayor a la disponible (' + cantidadDisponible + ')',
                    type: 'warning',
                    confirmButtonText: 'Aceptar'
                });
            }, 300);
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
        
        // Deshabilitar botones justo antes de enviar la petición (después de todas las validaciones)
        $('#guardar_inspeccion, #guardar_rechazo').prop('disabled', true).addClass('disabled');
        
        // El SweetAlert ya está mostrando el spinner, pero mantenemos blockUI como respaldo
        $.blockUI({
            message: '<h2>Guardando inspección...</h2><p>Por favor espere...</p><i class="fa fa-spinner fa-spin fa-2x" style="margin-top: 10px;"></i>',
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
                    //recargar la pagina
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                    // $('#inspeccion_container').hide();
                    // $('#numero_op').val('');
                    // $('#cabecera_nota').hide();
                } else {
                    // Rehabilitar botones en caso de error
                    $('#guardar_inspeccion, #guardar_rechazo').prop('disabled', false).removeClass('disabled');
                    
                    swal({
                        title: 'Error al guardar inspección',
                        text: resp.msg || 'Error al guardar la inspección. Por favor, intente nuevamente.',
                        type: 'error',
                        confirmButtonText: 'Aceptar',
                        html: true
                    });
                }
            },
            error: function(xhr, status, error) {
                $.unblockUI();
                
                // Rehabilitar botones en caso de error
                $('#guardar_inspeccion, #guardar_rechazo').prop('disabled', false).removeClass('disabled');
                
                // Determinar el tipo de error y mensaje específico
                var mensajeError = 'Error al guardar la inspección';
                var tituloError = 'Error al guardar inspección';
                
                // Manejar diferentes tipos de errores HTTP
                if (xhr.status === 0) {
                    // Error de red o timeout
                    mensajeError = 'Error de conexión: No se pudo conectar con el servidor. Verifique su conexión a internet e intente nuevamente.';
                    tituloError = 'Error de conexión';
                } else if (xhr.status === 419) {
                    // Token CSRF expirado
                    mensajeError = 'Su sesión ha expirado. Por favor, recargue la página e intente nuevamente.';
                    tituloError = 'Sesión expirada';
                } else if (xhr.status === 422) {
                    // Error de validación
                    tituloError = 'Error de validación';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errores = [];
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            if (Array.isArray(value)) {
                                errores.push(value.join('<br>'));
                            } else {
                                errores.push(value);
                            }
                        });
                        mensajeError = errores.join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.msg) {
                        mensajeError = xhr.responseJSON.msg;
                    } else {
                        mensajeError = 'Los datos enviados no son válidos. Por favor, verifique la información e intente nuevamente.';
                    }
                } else if (xhr.status === 403) {
                    // Sin permisos
                    mensajeError = 'No tiene permisos para realizar esta acción.';
                    tituloError = 'Acceso denegado';
                } else if (xhr.status === 404) {
                    // Recurso no encontrado
                    mensajeError = 'El recurso solicitado no fue encontrado. Por favor, verifique la información e intente nuevamente.';
                    tituloError = 'Recurso no encontrado';
                } else if (xhr.status === 500) {
                    // Error del servidor
                    tituloError = 'Error del servidor';
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        mensajeError = xhr.responseJSON.msg;
                    } else if (xhr.responseText) {
                        try {
                            var respuesta = JSON.parse(xhr.responseText);
                            if (respuesta.msg) {
                                mensajeError = respuesta.msg;
                            } else {
                                mensajeError = 'Ocurrió un error en el servidor. Por favor, contacte al administrador del sistema.';
                            }
                        } catch(e) {
                            mensajeError = 'Ocurrió un error en el servidor. Por favor, contacte al administrador del sistema.';
                        }
                    } else {
                        mensajeError = 'Ocurrió un error en el servidor. Por favor, contacte al administrador del sistema.';
                    }
                } else if (xhr.status >= 400 && xhr.status < 500) {
                    // Otros errores del cliente
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        mensajeError = xhr.responseJSON.msg;
                    } else {
                        mensajeError = 'Error en la solicitud. Por favor, verifique la información e intente nuevamente.';
                    }
                } else if (status === 'timeout') {
                    // Timeout
                    mensajeError = 'La solicitud tardó demasiado tiempo. Por favor, intente nuevamente.';
                    tituloError = 'Tiempo de espera agotado';
                } else if (status === 'parsererror') {
                    // Error al parsear la respuesta
                    tituloError = 'Error de respuesta';
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        mensajeError = xhr.responseJSON.msg;
                    } else {
                        mensajeError = 'El servidor devolvió una respuesta inválida. Por favor, intente nuevamente.';
                    }
                } else {
                    // Intentar obtener el mensaje de error del servidor
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        mensajeError = xhr.responseJSON.msg;
                    } else if (xhr.responseText) {
                        try {
                            var respuesta = JSON.parse(xhr.responseText);
                            if (respuesta.msg) {
                                mensajeError = respuesta.msg;
                            }
                        } catch(e) {
                            // Si no se puede parsear, usar el texto de respuesta si es corto
                            if (xhr.responseText.length < 200) {
                                mensajeError = xhr.responseText;
                            }
                        }
                    }
                }
                
                // Agregar información adicional para debugging (solo en desarrollo)
                if (xhr.status && xhr.status !== 200) {
                    console.error('Error HTTP:', xhr.status, '- Status:', status, '- Error:', error);
                    console.error('Respuesta del servidor:', xhr.responseText);
                }
                
                swal({
                    title: tituloError,
                    text: mensajeError,
                    type: 'error',
                    confirmButtonText: 'Aceptar',
                    html: true
                });
            }
        });
    }
    
    // Evento para guardar como ACEPTADO
    $(document).on('click', '#guardar_inspeccion', function(){
        var cantidadInspeccionada = parseFloat($('#cantidad_inspeccionada').val()) || 0;
        var cantidadDisponible = parseFloat($('#cantidad_disponible').val()) || 0;
        
        swal({
            title: '¿Está seguro de que desea Aceptar la inspección?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Aceptar Inspección',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                // Mostrar spinner en el SweetAlert
                swal({
                    title: 'Procesando...',
                    html: '<p>Guardando inspección y avanzando OP...</p><i class="fa fa-spinner fa-spin fa-3x" style="margin-top: 20px;"></i>',
                    type: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                
                // Llamar a la función de guardar (los botones se deshabilitarán después de las validaciones)
                guardarInspeccion('ACEPTADO');
            }
        });
    });
    
    // Evento para guardar como RECHAZADO
    $(document).on('click', '#guardar_rechazo', function(){
        var cantidadInspeccionada = parseFloat($('#cantidad_inspeccionada').val()) || 0;
        var cantidadDisponible = parseFloat($('#cantidad_disponible').val()) || 0;
        
        // Contar cuántos puntos están marcados como "No Cumple"
        var puntosNoCumple = 0;
        $('input[type="radio"][value="No Cumple"]:checked').each(function() {
            puntosNoCumple++;
        });
        
        swal({
            title: '¿Está seguro de que desea Rechazar la inspección?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Rechazar Inspección',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                // Mostrar spinner en el SweetAlert
                swal({
                    title: 'Procesando...',
                    html: '<p>Guardando inspección rechazada y enviando notificaciones...</p><i class="fa fa-spinner fa-spin fa-3x" style="margin-top: 20px;"></i>',
                    type: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                
                // Llamar a la función de guardar (los botones se deshabilitarán después de las validaciones)
                guardarInspeccion('RECHAZADO');
            }
        });
    });
    
    // Función para abrir modal de historial de rechazos
    function abrirModalHistorialRechazos() {
        $('#modal_op_numero').text(opData.OP);
        
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
            success: function(response){
                $.unblockUI();
                
                if (response.success && response.inspecciones && response.inspecciones.length > 0) {
                    renderHistorialRechazos(response.inspecciones);
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
            error: function(){
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
    
    // Función para abrir modal de historial de OP
    function abrirModalHistorialOP() {
        if (!opData || !historial) {
            swal({
                title: 'Error',
                text: 'No hay información de la OP o historial disponible',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        $('#modal_historial_op_numero').text(opData.OP);
        renderHistorialOP(historial);
        $('#modalHistorialOP').modal('show');
    }
    
    // Función para renderizar el historial de OP en el modal
    function renderHistorialOP(historialData) {
        if (!historialData || historialData.length === 0) {
            $('#contenido_historial_op').html(
                '<div class="alert alert-info">' +
                '<i class="fa fa-info-circle"></i> No hay historial disponible para esta OP.' +
                '</div>'
            );
            return;
        }
        
        var html = '<div style="margin-bottom: 15px;">' +
            '<table class="table table-bordered table-striped table-condensed" style="font-size: 12px;">' +
                '<thead style="background-color: #f5f5f5;">' +
                    '<tr>' +
                        '<th style="width: 40%;">Estación</th>' +
                        '<th style="width: 35%;">Empleado</th>' +
                        '<th style="width: 25%; text-align: right;">Cantidad Elaborada</th>' +
                    '</tr>' +
                '</thead>' +
                '<tbody>';
        
        historialData.forEach(function(item) {
            var empleado = item.Empleado || 'N/A';
            var cantidad = item.CantidadElaborada ? parseFloat(item.CantidadElaborada).toFixed(2) : '0.00';
            var esCalidad = item.EsCalidad === 'S';
            var rowClass = esCalidad ? 'success' : '';
            var calidadBadge = esCalidad ? '<span class="label label-success" style="margin-left: 5px;">Calidad</span>' : '';
            
            html += '<tr class="' + rowClass + '">' +
                '<td><strong>' + item.NombreEstacion + '</strong>' + calidadBadge + '</td>' +
                '<td>' + empleado + '</td>' +
                '<td class="text-right">' + cantidad + '</td>' +
            '</tr>';
        });
        
        html += '</tbody>' +
            '</table>' +
        '</div>';
        
        $('#contenido_historial_op').html(html);
    }
    
    // Función para renderizar el historial de rechazos en el modal
    function renderHistorialRechazos(inspecciones) {
        var html = '';
        
        inspecciones.forEach(function(insp, index) {
            // Todos son rechazos, así que siempre son rojos
            var estadoClass = 'panel-danger';
            var estadoIcono = 'fa-exclamation-triangle';
            var estadoTexto = 'RECHAZADA';
            var estadoColor = '#dc3545';
            
            // Formatear fecha
            var fechaInsp = new Date(insp.IPR_fechaInspeccion);
            var fechaFormateada = fechaInsp.getDate().toString().padStart(2, '0') + '/' + 
                                 (fechaInsp.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                                 fechaInsp.getFullYear() + ' ' +
                                 fechaInsp.getHours().toString().padStart(2, '0') + ':' +
                                 fechaInsp.getMinutes().toString().padStart(2, '0');
            
            html += '<div class="panel '+estadoClass+'" style="margin-bottom: 20px;">'+
                '<div class="panel-heading" >'+
                    '<h4 class="panel-title">'+
                        ' Rechazo #'+insp.IPR_id+' - '+estadoTexto+
                    '</h4>'+
                '</div>'+
                '<div class="panel-body">'+
                    '<div class="row">'+
                        '<div class="col-md-2">'+
                            '<strong>Fecha:</strong><br>'+fechaFormateada+
                        '</div>'+
                        '<div class="col-md-2">'+
                            '<strong>Inspector:</strong><br>'+insp.IPR_nomInspector+
                        '</div>'+
                        '<div class="col-md-2">'+
                            '<strong>Estación:</strong><br>'+insp.IPR_nombreCentro+
                        '</div>'+
                        '<div class="col-md-2">'+
                            '<strong>Cantidad Inspeccionada:</strong><br>'+parseFloat(insp.IPR_cantInspeccionada).toFixed(2)+
                        '</div>'+
                        '<div class="col-md-2">'+
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
                   /*  if (det.IPD_estado === 'C') {
                        estadoDet = 'Cumple';
                        estadoColor = 'text-success';
                    } else if (det.IPD_estado === 'N') {
                        estadoDet = 'No Cumple';
                        estadoColor = 'text-danger';
                    } else {
                        estadoDet = 'No Aplica';
                        estadoColor = 'text-muted';
                    } */
                    
                    if (det.IPD_estado === 'N') {
                        estadoDet = 'No Cumple';
                        estadoColor = 'text-danger';

                        html += '<tr>'+
                        '<td>'+det.CHK_descripcion+'</td>'+
                        '<td style="text-align: center;"><strong class="'+estadoColor+'">'+estadoDet+'</strong></td>'+
                        '<td>'+(det.empleado_nombre || '-')+'</td>'+
                        '<td>'+(det.IPD_observacion || '-')+'</td>'+
                        '</tr>';
                    }
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
        
        $('#contenido_historial_rechazos').html(html);
    }
    
    // Atajo de teclado: Ctrl+Shift+N para marcar todo el checklist como "No Aplica"
    $(document).on('keydown', function(e) {
        // Detectar Ctrl+Shift+N
        if (e.ctrlKey && e.shiftKey && e.keyCode === 81) { // 90 es el código de 'Z'
            // Verificar que no se esté escribiendo en un campo de texto o textarea
            var target = e.target;
            var isTextInput = false;
            
            if (target.tagName === 'INPUT') {
                var inputType = target.type.toLowerCase();
                // Solo ignorar inputs de texto, number, date, etc., pero permitir radio y checkbox
                isTextInput = (inputType === 'text' || inputType === 'number' || inputType === 'date' || 
                               inputType === 'email' || inputType === 'password' || inputType === 'search' ||
                               inputType === 'tel' || inputType === 'url');
            } else if (target.tagName === 'TEXTAREA') {
                isTextInput = true;
            }
            
            // Si no está escribiendo en un campo de texto y el checklist está visible
            if (!isTextInput && $('#checklist_container').is(':visible') && checklist.length > 0) {
                e.preventDefault(); // Prevenir comportamiento por defecto
                
                // Marcar todos los radio buttons de "No Aplica" como checked
                checklist.forEach(function(item) {
                    var radioNoAplica = $('input[name="checklist_' + item.CHK_id + '"][value="No Aplica"]');
                    radioNoAplica.prop('checked', true);
                    
                    // Desmarcar los otros radio buttons
                    $('input[name="checklist_' + item.CHK_id + '"][value="Cumple"]').prop('checked', false);
                    $('input[name="checklist_' + item.CHK_id + '"][value="No Cumple"]').prop('checked', false);
                    
                    // Llamar a manejarChecklist para actualizar el estado
                    manejarChecklist(item.CHK_id, 'No Aplica');
                    
                    // Actualizar el objeto respuestas
                    respuestas[item.CHK_id] = 'No Aplica';
                });
                
                // Actualizar botones después de marcar todo como "No Aplica"
                actualizarBotonesInspeccion();
                
                // Mostrar mensaje de confirmación
                swal({
                    title: 'Checklist actualizado',
                    text: 'Todos los puntos del checklist han sido marcados como "No Aplica"',
                    type: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
    });
}
