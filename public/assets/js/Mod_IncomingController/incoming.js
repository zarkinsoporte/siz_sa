function js_iniciador() {
    $('.toggle').bootstrapSwitch();
    $('[data-toggle="tooltip"]').tooltip();
    $('.boot-select').selectpicker();
    $('.dropdown-toggle').dropdown();
    setTimeout(function() {
    $('#infoMessage').fadeOut('fast');
    }, 5000); // <-- time in milliseconds
    $("#sidebarCollapse").on("click", function() {
        $("#sidebar").toggleClass("active"); 
        $("#page-wrapper").toggleClass("content"); 
        $(this).toggleClass("active"); 
    });
    $("#sidebar").toggleClass("active"); 
    $("#page-wrapper").toggleClass("content"); 
    $(this).toggleClass("active"); 
    
    var materiales = [];
    var materialSeleccionado = null;
    var pielData = {};
    var checklist = [];
    var respuestas = {};
    var cantidadRecibida = 0;
    var dataTable = null; // Variable para almacenar la instancia de DataTable
    var idInspeccion = 0;
    var inspeccionConsultaData = null; // Variable global para el modo consulta
    
    // Función para buscar materiales
    function buscarMateriales() {
        var numero = $('#numero_entrada').val();
        if(!numero) {
            swal({
                title: 'Campo requerido',
                text: 'Ingrese un número de entrada',
                type: 'warning',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        // Mostrar blockUI
        $.blockUI({
            message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
        
        $.getJSON(routeapp+'/home/INSPECCION/buscar', {numero_entrada: numero}, function(data){
            materiales = data;
            if(materiales.length > 0) {
                renderCabecera(materiales[0]);
                renderTablaMateriales();
              
            } else {
                swal({
                    title: 'Sin resultados',
                    text: 'No se encontraron materiales para este número de entrada',
                    type: 'info',
                    confirmButtonText: 'Aceptar'
                });
                $('#materiales_container').hide();
                $('#inspeccion_container').hide();
            }
            // Ocultar blockUI
            $.unblockUI();
        }).fail(function() {
            // Ocultar blockUI en caso de error
            $.unblockUI();
            swal({
                title: 'Error',
                text: 'Error en la búsqueda. Intente nuevamente.',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    }
    
    // Evento para buscar con Enter
    $('#numero_entrada').keypress(function(e) {
        if(e.which == 13) { // Enter key
            $('#inspeccion_container').hide();
            buscarMateriales();
        }
    });
    
    // Mantener el botón buscar por compatibilidad
    $('#buscar_materiales').click(function(){
        $('#inspeccion_container').hide();
        buscarMateriales();
    });
    
    // Renderizar cabecera mejorada
    function renderCabecera(mat){
        $('#nombre_proveedor').text(mat.NOMBRE_PROVEEDOR || 'N/A');
        $('#fecha_recepcion').text(mat.FECHA_RECEPCION || 'N/A');
        $('#numero_factura').text(mat.NUM_FACTURA || 'N/A');
        // Quitar el ID de inspección de la cabecera - ahora solo aparece en el resumen
        
        $('#cabecera_nota').show();
    }
    
    // Renderizar tabla de materiales
    function renderTablaMateriales(){
        // Destruir DataTable existente si existe
        if (dataTable !== null) {
            dataTable.destroy();
            dataTable = null;
        }
        
        var tbody = '';
        console.log(materiales);
        materiales.forEach(function(mat, idx){
            var porRevisar = (parseFloat(mat.POR_REVISAR) || 0);
            var acciones = '';
            
            // Botones de inspecciones previas si existen
            if(mat.inspecciones && Array.isArray(mat.inspecciones) && mat.inspecciones.length > 0) {
                mat.inspecciones.forEach(function(inspeccion) {
                    if(inspeccion && inspeccion.INC_id) {
                        acciones += '<button class="btn btn-success btn-xs btnVerInspeccion" data-inspeccion-id="'+inspeccion.INC_id+'" title="Ver Inspección ID: '+inspeccion.INC_id+'"><i class="fa fa-eye"></i> '+inspeccion.INC_id+'</button> ';
                    }
                });
            }
            
            // Ícono de checklist - deshabilitar si por revisar es 0
            if(porRevisar > 0) {
                acciones += '<button class="btn btn-primary btn-sm btnChecklist" title="Abrir Checklist"><span class="glyphicon glyphicon-check"></span></button>';
            } else {
                acciones += '<button class="btn btn-default btn-sm" disabled title="Sin cantidad por revisar"><span class="glyphicon glyphicon-check"></span></button>';
            }
            
            tbody += '<tr data-idx="'+idx+'">'+
                '<td>'+(mat.LineNum || 'N/A')+'</td>'+
                '<td>'+acciones+'</td>'+
                '<td>'+mat.CODIGO_ARTICULO+'</td>'+
                '<td>'+mat.MATERIAL+'</td>'+
                '<td>'+mat.UDM+'</td>'+
                '<td>'+(parseFloat(mat.CANTIDAD) || 0).toFixed(2)+'</td>'+
                '<td>' + (parseFloat(mat.CAN_INSPECCIONADA) || 0).toFixed(2)+'</td>'+
                '<td>' + (parseFloat(mat.CAN_RECHAZADA) || 0).toFixed(2)+'</td>'+
                '<td>'+porRevisar.toFixed(2)+'</td>'+
            '</tr>';
        });
        $('#tabla_materiales tbody').html(tbody);
        
        // Inicializar nueva DataTable
        dataTable = $('#tabla_materiales').DataTable({
            order: [[0, 'asc']],
            language: {
                "url": assetapp + "assets/lang/Spanish.json"
            }
        });
        
        $('#materiales_container').show();
    }
    
    // Evento para abrir modal de piel
    $('#tabla_materiales').on('click', '.btnPiel', function(){
        var idx = $(this).closest('tr').data('idx');
        materialSeleccionado = materiales[idx];
        $('#modalPiel').modal('show');
    });
    
    // Guardar clases de piel
    $('#guardarPiel').click(function(){
        var total = parseFloat($('#claseA').val()||0)+parseFloat($('#claseB').val()||0)+parseFloat($('#claseC').val()||0)+parseFloat($('#claseD').val()||0);
        var cantidadAceptada = parseFloat($('#cantidad_aceptada').val()) || 0;
        
        if(total != cantidadAceptada){
            swal({
                title: 'Clases de piel incompletas',
                text: 'La suma de clases debe ser igual a la cantidad aceptada ('+cantidadAceptada.toFixed(2)+')',
                type: 'warning',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        pielData[materialSeleccionado.CODIGO_ARTICULO] = {
            claseA: ($('#claseA').val() == '' ? 0 : $('#claseA').val()),
            claseB: ($('#claseB').val() == '' ? 0 : $('#claseB').val()),
            claseC: ($('#claseC').val() == '' ? 0 : $('#claseC').val()),
            claseD: ($('#claseD').val() == '' ? 0 : $('#claseD').val())
        };
        $('#modalPiel').modal('hide');
        $('#alertPiel').hide();
        
        swal({
            title: 'Guardado exitoso',
            text: 'Las clases de piel han sido guardadas correctamente',
            type: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    });
    
    // Calcular porcentajes automáticamente en el modal de piel
    $(document).on('input', '.clase-piel', function(){
        calcularPorcentajesPiel();
    });
    
    // Función para calcular porcentajes de piel
    function calcularPorcentajesPiel() {
        var claseA = parseFloat($('#claseA').val()) || 0;
        var claseB = parseFloat($('#claseB').val()) || 0;
        var claseC = parseFloat($('#claseC').val()) || 0;
        var claseD = parseFloat($('#claseD').val()) || 0;
        
        var total = claseA + claseB + claseC + claseD;
        var cantidadAceptada = parseFloat($('#cantidad_aceptada').val()) || 0;
        
        // Calcular porcentajes
        var porcentajeA = cantidadAceptada > 0 ? (claseA / cantidadAceptada * 100) : 0;
        var porcentajeB = cantidadAceptada > 0 ? (claseB / cantidadAceptada * 100) : 0;
        var porcentajeC = cantidadAceptada > 0 ? (claseC / cantidadAceptada * 100) : 0;
        var porcentajeD = cantidadAceptada > 0 ? (claseD / cantidadAceptada * 100) : 0;
        
        // Actualizar porcentajes
        $('#porcentajeA').text(porcentajeA.toFixed(2) + '%');
        $('#porcentajeB').text(porcentajeB.toFixed(2) + '%');
        $('#porcentajeC').text(porcentajeC.toFixed(2) + '%');
        $('#porcentajeD').text(porcentajeD.toFixed(2) + '%');
        
        // Actualizar totales
        $('#totalClases').text(total.toFixed(2));
        $('#totalPorcentaje').text((porcentajeA + porcentajeB + porcentajeC + porcentajeD).toFixed(2) + '%');
        
        // Validar si la suma es correcta
        if(Math.abs(total - cantidadAceptada) < 0.01) {
            $('#alertPiel').hide();
            $('.clase-piel').removeClass('is-invalid').addClass('is-valid');
        } else {
            $('.clase-piel').removeClass('is-valid').addClass('is-invalid');
        }
    }
    
    // Evento para mostrar checklist
    $('#tabla_materiales').on('click', '.btnChecklist', function(){
        var idx = $(this).closest('tr').data('idx');
        materialSeleccionado = materiales[idx];
        cantidadRecibida = parseFloat(materialSeleccionado.CANTIDAD) || 0;
        
        // Mostrar blockUI
        $.blockUI({
            message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
            url: routeapp+'home/INSPECCION/checklist',
            type: 'POST',
            data: {
                inc_id: materialSeleccionado.ID_INSPECCION || 0,
                material: materialSeleccionado
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
                checklist = data.checklist;
                respuestas = {};
                if(data.respuestas){
                    data.respuestas.forEach(function(r){
                        respuestas[r.IND_chkId] = r;
                    });
                }
                idInspeccion = data.id_inspeccion || 0;
                
                // Para nuevas inspecciones, idInspeccion será 0 hasta que se guarde
                // No actualizar el material en el array hasta que se guarde
                
                renderChecklist();
                renderResumen();
                $('#inspeccion_container').show();
                // Ocultar blockUI
                $.unblockUI();
            },
            error: function() {
                // Ocultar blockUI en caso de error
                $.unblockUI();
                swal({
                    title: 'Error',
                    text: 'Error al cargar el checklist',
                    type: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });
    
    // Evento para ver inspección previa
    $('#tabla_materiales').on('click', '.btnVerInspeccion', function(){
        var inspeccionId = $(this).data('inspeccion-id');
        var idx = $(this).closest('tr').data('idx');
        materialSeleccionado = materiales[idx];
        
        // Mostrar blockUI
        $.blockUI({
            message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
        
        // Cargar datos de la inspección existente
        $.getJSON(routeapp+'/home/INSPECCION/ver-inspeccion', {
            inc_id: inspeccionId
        }, function(data){
            checklist = data.checklist;
            // Convertir array de respuestas en objeto indexado por IND_chkId
            respuestas = {};
            if(data.respuestas && Array.isArray(data.respuestas)) {
                data.respuestas.forEach(function(r) {
                    respuestas[r.IND_chkId] = r;
                });
            }
            idInspeccion = inspeccionId;
            
            // Pasar imágenes por CHK_id para mostrarlas en modo consulta
            window._imagenesPorChk = data.imagenes || {};
            renderChecklistSoloLectura();
            renderResumenSoloLectura(data.inspeccion);
            $('#inspeccion_container').show();
            // Ocultar blockUI
            $.unblockUI();
        }).fail(function() {
            // Ocultar blockUI en caso de error
            $.unblockUI();
            swal({
                title: 'Error',
                text: 'Error al cargar la inspección',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    });
    
    // Renderizar checklist
    function renderChecklist(){
        var html = '<table class="table table-bordered" style="width:100%"><thead><tr><th style="width:10%"></th><th style="width:20%">Punto</th><th style="width:8%; text-align:center">Cumple</th><th style="width:8%; text-align:center">No Cumple</th><th style="width:8%; text-align:center">No Aplica</th><th style="width:50%">Observación</th></tr></thead><tbody>';
        checklist.forEach(function(item){
            var r = respuestas[item.CHK_id]||{};
            html += '<tr data-chk="'+item.CHK_id+'">'+
                '<td>'+
                    '<button type="button" class="btn btn-primary btn-sm btnEvidencia" title="Adjuntar Evidencia"><span class="glyphicon glyphicon-camera"></span></button>'+
                    '<input type="file" name="img_'+item.CHK_id+'" accept=".jpg,.jpeg,.png" style="display:none;" class="inputEvidencia" multiple>'+
                    '<div class="imagenes-previas" id="imagenes_'+item.CHK_id+'" style="margin-top: 5px;"></div>'+
                '</td>'+
                '<td style="font-size: 14px;">'+item.CHK_descripcion+'</td>'+
                '<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="C" '+(r.IND_estado=='C'?'checked':'')+'></td>'+
                '<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="N" '+(r.IND_estado=='N'?'checked':'')+'></td>'+
                '<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="A" '+(r.IND_estado=='A'?'checked':'')+'></td>'+
                //'<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="A" '+(r.IND_estado=='A'?'checked':(!r.IND_estado?'checked':''))+'></td>'+
                '<td><textarea class="form-control textareaObservacion" name="obs_'+item.CHK_id+'" rows="2" style="resize:none; text-transform:uppercase;">'+(r.IND_observacion||'')+'</textarea></td>'+
                '</tr>';
                //'<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="A" '+(r.IND_estado=='A'?'checked':(!r.IND_estado?'checked':''))+'></td>'+
        });
        html += '</tbody></table>';
        $('#checklist_container').html(html);
        
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
                // Limpiar contenedor de imágenes previas
                contenedorImagenes.empty();
                
                for(var i = 0; i < files.length; i++) {
                    var file = files[i];
                    
                    // Validar tipo de archivo
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
                    
                    // Validar tamaño (máximo 5MB por archivo)
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
                    
                    // Agregar preview de la imagen
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var imgPreview = '<div style="display: inline-block; margin: 2px;">' +
                            '<img src="' + e.target.result + '" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #ccc;" title="' + file.name + '">' +
                            '<div style="font-size: 10px; text-align: center;">' + file.name + '</div>' +
                            '</div>';
                        contenedorImagenes.append(imgPreview);
                    };
                    reader.readAsDataURL(file);
                }
                
                // Cambiar el ícono del botón para indicar que hay archivos
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
        
        // Evento para convertir a mayúsculas en tiempo real
        $('.textareaObservacion').on('input', function(){
            this.value = this.value.toUpperCase();
        });
    }
    
    // Renderizar checklist en modo solo lectura
    function renderChecklistSoloLectura(){
        var html = '<table class="table table-bordered" style="width:100%"><thead><tr><th style="width:10%"></th><th style="width:20%">Punto</th><th style="width:8%; text-align:center">Cumple</th><th style="width:8%; text-align:center">No Cumple</th><th style="width:8%; text-align:center">No Aplica</th><th style="width:50%">Observación</th></tr></thead><tbody>';
        checklist.forEach(function(item){
            var r = respuestas[item.CHK_id]||{};
            var imgs = (window._imagenesPorChk && window._imagenesPorChk[item.CHK_id]) ? window._imagenesPorChk[item.CHK_id] : [];
            var imgsHtml = '';
            if (imgs.length > 0) {
                imgs.forEach(function(it){
                    var url = routeapp + 'home/INSPECCION/imagen/' + it.id;
                    imgsHtml += '<div style="font-size:11px; margin-top:3px;"><i class="fa fa-paperclip"></i> <a href="'+url+'" target="_blank">'+ it.archivo +'</a></div>';
                });
            }
            html += '<tr data-chk="'+item.CHK_id+'">'+
                '<td>'+
                    '<button type="button" class="btn btn-primary btn-sm btnEvidencia" title="Adjuntar Evidencia" disabled><span class="glyphicon glyphicon-camera"></span></button>'+ // deshabilitado
                    '<input type="file" name="img_'+item.CHK_id+'" accept=".jpg,.jpeg,.png" style="display:none;" class="inputEvidencia" multiple disabled>'+ // deshabilitado
                    '<div class="imagenes-previas" id="imagenes_'+item.CHK_id+'" style="margin-top: 5px;">'+imgsHtml+'</div>'+ // solo visualización
                '</td>'+
                '<td style="font-size: 14px;">'+item.CHK_descripcion+'</td>'+ // solo visualización
                '<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="C" '+(r.IND_estado=='C'?'checked':'')+' disabled></td>'+ // deshabilitado
                '<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="N" '+(r.IND_estado=='N'?'checked':'')+' disabled></td>'+ // deshabilitado
                '<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="A" '+(r.IND_estado=='A'?'checked':(!r.IND_estado?'checked':''))+' disabled></td>'+ // deshabilitado
                '<td><textarea class="form-control textareaObservacion" name="obs_'+item.CHK_id+'" rows="2" style="resize:none; text-transform:uppercase;" readonly>'+(r.IND_observacion||'')+'</textarea></td>'+ // readonly
            '</tr>';
        });
        html += '</tbody></table>';
        $('#checklist_container').html(html);
    }
    
    // Renderizar resumen lateral
    function renderResumen(){
        var porRevisar = parseFloat(materialSeleccionado.POR_REVISAR) || 0;
        var aceptadas = parseFloat(materialSeleccionado.CAN_INSPECCIONADA) || 0;
        var rechazadas = parseFloat(materialSeleccionado.CAN_RECHAZADA) || 0;
        var porcentaje = porRevisar > 0 ? (aceptadas / porRevisar * 100) : 0;
        
        // Para nuevas inspecciones, cantidad aceptada por defecto = cantidad por revisar
        var cantidadAceptadaDefault = porRevisar;
        
        // Obtener fecha actual para la inspección en formato YYYY-MM-DD para input type="date"
        var fechaActual = new Date();
        var fechaFormateada = fechaActual.getFullYear() + '-' + 
                             (fechaActual.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                             fechaActual.getDate().toString().padStart(2, '0');
        
        // Mostrar ID de inspección o "Por definir" si es nueva
        var idInspeccionMostrar = idInspeccion > 0 ? idInspeccion : 'Por definir';
        
        // Obtener nombre del inspector (usuario actual para nuevas inspecciones)
        var nomInspector = typeof currentUser !== 'undefined' ? currentUser : 'Usuario Actual';
        
        var html = '<div class="card">'+
            '<div class="card-body">'+
                '<h4 style="font-weight: bold; margin-bottom: 16px;">RESUMEN #'+materialSeleccionado.LineNum+'</h4>'+
            '<h4 style="margin-bottom: 10px;">' + materialSeleccionado.CODIGO_ARTICULO + ' - ' + materialSeleccionado.MATERIAL + '</h4>'+
                
                '<div class="row">'+
                    '<div class="col-sm-6 col-md-6">'+
                        '<div style="margin-top: 20px;">'+
                            '<label style="font-weight: bold; margin-bottom: 10px;">Reporte de Inspección N°:</label>'+
                            '<input type="text" id="id_inspeccion_resumen" class="form-control" value="'+idInspeccionMostrar+'" readonly style="margin-top: 5px;">'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-sm-6 col-md-6">'+
                        '<div style="margin-top: 20px;">'+
                            '<label style="font-weight: bold; margin-bottom: 10px;">Fecha de Inspección:</label>'+
                            '<input type="date" id="fecha_inspeccion" class="form-control" value="'+fechaFormateada+'" readonly style="margin-top: 5px;">'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col-sm-6 col-md-6">'+
                        '<div style="margin-top: 20px;">'+
                            '<label style="font-weight: bold; margin-bottom: 10px;">Nombre del Inspector:</label>'+
                            '<input type="text" id="nomInspector" class="form-control" value="'+nomInspector+'" readonly style="margin-top: 5px;">'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="data-summary-block" style="margin-top: 10px; margin-bottom: 20px;">'+
                    '<div class="row">'+
                        '<div class="col-sm-6 col-md-3">'+
                            '<div class="data-summary-item">'+
                                '<small>POR REVISAR</small>'+
                                '<input type="number" id="cantidad_por_revisar" class="form-control user-error" value="'+porRevisar.toFixed(2)+'" min="0" max="'+porRevisar+'" step="0.01">'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-3">'+
                            '<div class="data-summary-item">'+
                                '<small>ACEPTADA</small>'+
                                '<input type="number" id="cantidad_aceptada" class="form-control user-success" value="'+cantidadAceptadaDefault.toFixed(2)+'" min="0" max="'+porRevisar+'" step="0.01">'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-3">'+
                            '<div class="data-summary-item">'+
                                '<small>RECHAZADA</small>'+
                                '<input type="number" id="cantidad_rechazada" class="form-control" value="0.00" readonly="">'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-3">'+
                            '<div class="data-summary-item">'+
                                '<small>% ACEPTADO</small>'+
                                '<input type="text" id="porcentaje_aceptado" class="form-control" value="100.00%" readonly="">'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                
                '<div style="margin-top: 20px;">'+
                    '<label style="font-weight: bold; margin-bottom: 10px;">Observaciones Generales:</label>'+
                    '<textarea class="form-control textareaObservacionesGenerales" name="observaciones_generales" rows="5" style="resize:none; text-transform:uppercase; margin-top: 5px;" placeholder="INGRESE OBSERVACIONES GENERALES..."></textarea>'+
                '</div>'+
                
                // Botón de piel solo para materiales de piel (grupo 113)
                (materialSeleccionado.GRUPO == 113 ? 
                    '<div style="margin-top: 20px; margin-bottom: 20px;">'+
                        '<button id="btn_capturar_piel" class="btn btn-warning btn-lg btn-block"><i class="fa fa-tags"></i> Capturar Clases de Piel</button>'+
                    '</div>' : ''
                )+
                
                '<div style="margin-top: 20px; margin-bottom: 20px;">'+
                    '<button id="guardar_inspeccion" class="btn btn-success btn-lg btn-block">Guardar Inspección</button>'+
                '</div>'+
            '</div>'+
        '</div>';
        $('#resumen_material').html(html);
        
        // Evento para convertir a mayúsculas en tiempo real para observaciones generales
        $('.textareaObservacionesGenerales').on('input', function(){
            this.value = this.value.toUpperCase();
        });
        
        // Eventos para cálculos automáticos
        $('#cantidad_por_revisar, #cantidad_aceptada').on('input', function(){
            calcularCantidades();
            
            // Si es material de piel y cambia la cantidad por revisar, limpiar clases de piel
            if(materialSeleccionado.GRUPO == 113 && $(this).attr('id') == 'cantidad_por_revisar') {
                // Limpiar datos de piel en memoria
                if(pielData[materialSeleccionado.CODIGO_ARTICULO]) {
                    delete pielData[materialSeleccionado.CODIGO_ARTICULO];
                }
                
                // // Mostrar alerta al usuario
                // swal({
                //     title: 'Cantidad por revisar cambiada',
                //     text: 'Se han limpiado las clases de piel capturadas anteriormente. Debe volver a capturar las clases de piel.',
                //     type: 'warning',
                //     confirmButtonText: 'Entendido'
                // });
            }
        });
        
        // Evento para seleccionar todo el texto al hacer clic en cantidad por revisar
        $('#cantidad_por_revisar').on('click', function(){
            this.select();
        });
        
        // Evento para seleccionar todo el texto al hacer clic en cantidad aceptada
        $('#cantidad_aceptada').on('click', function(){
            this.select();
        });
        
        // Evento para hacer editable el campo de fecha con doble clic
        $('#fecha_inspeccion').on('dblclick', function(){
            $(this).prop('readonly', false);
            $(this).focus();
        });
        
        // Evento para hacer readonly el campo de fecha cuando pierde el foco
        $('#fecha_inspeccion').on('blur', function(){
            $(this).prop('readonly', true);
        });
        
        // Solo bloquear si realmente no hay por revisar (después de guardar)
        // No bloquear automáticamente al cargar el resumen
        
        // Evento para el botón de piel
        $(document).on('click', '#btn_capturar_piel', function(){
            abrirModalPiel();
        });
    }
    
    // Función para abrir modal de piel
    function abrirModalPiel() {
        var cantidadAceptada = parseFloat($('#cantidad_aceptada').val()) || 0;
        
        // Llenar información del modal
        $('#piel_articulo_info').text(materialSeleccionado.CODIGO_ARTICULO + ' - ' + materialSeleccionado.MATERIAL);
        //console.log(materialSeleccionado);
        $('#piel_lote_info').text(materialSeleccionado.LOTE || 'N/A');
        $('#piel_cantidad_total').text(cantidadAceptada.toFixed(2));
        
        // Limpiar campos y porcentajes
        $('#claseA, #claseB, #claseC, #claseD').val('');
        $('.porcentaje-clase').text('0.00%');
        $('#totalClases').text('0.00');
        $('#totalPorcentaje').text('0.00%');
        
        // Limpiar alertas previas
        $('#alertPiel').hide();
        
        // Mostrar modal
        $('#modalPiel').modal('show');
        
        // Habilitar campos para modo edición
        $('#claseA, #claseB, #claseC, #claseD').prop('disabled', false);
        $('#guardarPiel').prop('disabled', false);
    }
    
    // Evento para abrir modal de piel (mantener compatibilidad)
    $('#tabla_materiales').on('click', '.btnPiel', function(){
        var idx = $(this).closest('tr').data('idx');
        materialSeleccionado = materiales[idx];
        $('#modalPiel').modal('show');
    });

    // Limpiar modal cuando se cierre para evitar datos persistentes
    $('#modalPiel').on('hidden.bs.modal', function () {
        // Limpiar todos los campos del modal
        $('#piel_articulo_info').text('');
        $('#piel_lote_info').text('');
        $('#piel_cantidad_total').text('');
        $('#claseA, #claseB, #claseC, #claseD').val('');
        $('.porcentaje-clase').text('0.00%');
        $('#totalClases').text('0.00');
        $('#totalPorcentaje').text('0.00%');
        $('#alertPiel').hide();
        $('.clase-piel').removeClass('is-valid is-invalid');
        
        // Habilitar campos para modo edición (por si se abre en modo nueva inspección)
        $('#claseA, #claseB, #claseC, #claseD').prop('disabled', false);
        $('#guardarPiel').prop('disabled', false);
    });
    
    // Renderizar resumen en modo solo lectura
    function renderResumenSoloLectura(inspeccionData) {
        // Almacenar datos de la inspección en variable global
        inspeccionConsultaData = inspeccionData;

        var revisada = parseFloat(inspeccionData.CAN_INSPECCIONADA) + parseFloat(inspeccionData.CAN_RECHAZADA);
        var aceptadas = parseFloat(inspeccionData.CAN_INSPECCIONADA);
        var rechazadas = parseFloat(inspeccionData.CAN_RECHAZADA);
        var porcentaje = revisada > 0 ? (aceptadas / revisada * 100) : 0;
        
        // Obtener fecha de la inspección
        var fechaInspeccion = inspeccionData.INC_fechaInspeccion || new Date();
        var fechaFormateada = '';
        if (typeof fechaInspeccion === 'string') {
            // Si es string, formatear
            var fecha = new Date(fechaInspeccion);
            fechaFormateada = fecha.getDate().toString().padStart(2, '0') + '/' + 
                             (fecha.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                             fecha.getFullYear();
        } else {
            // Si es Date object
            fechaFormateada = fechaInspeccion.getDate().toString().padStart(2, '0') + '/' + 
                             (fechaInspeccion.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                             fechaInspeccion.getFullYear();
        }
        
        // Mostrar ID de inspección
        var idInspeccionMostrar = inspeccionData.INC_id || 'Por definir';
        
        // Obtener nombre del inspector desde los datos de la inspección
        var nomInspector = inspeccionData.INC_nomInspector || 'N/A';
        
        var html = '<div class="card">'+
            '<div class="card-body">'+
                '<h4 style="font-weight: bold; margin-bottom: 16px;">RESUMEN #'+inspeccionData.LINE_NUM+' - CONSULTA</h4>'+
            '<h4 style="margin-bottom: 10px;">' + inspeccionData.CODIGO_ARTICULO + ' - ' + inspeccionData.MATERIAL + '</h4>'+
                
                '<div class="row">'+
                    '<div class="col-sm-6 col-md-6">'+
                        '<div style="margin-top: 20px;">'+
                            '<label style="font-weight: bold; margin-bottom: 10px;">Reporte de Inspección N°:</label>'+
                            '<input type="text" id="id_inspeccion_consulta" class="form-control" value="'+idInspeccionMostrar+'" readonly style="margin-top: 5px;">'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-sm-6 col-md-6">'+
                        '<div style="margin-top: 20px;">'+
                            '<label style="font-weight: bold; margin-bottom: 10px;">Fecha de Inspección:</label>'+
                            '<input type="text" id="fecha_inspeccion_consulta" class="form-control" value="'+fechaFormateada+'" readonly style="margin-top: 5px;">'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col-sm-6 col-md-6">'+
                        '<div style="margin-top: 20px;">'+
                            '<label style="font-weight: bold; margin-bottom: 10px;">Inspector:</label>'+
                            '<input type="text" id="nomInspector_consulta" class="form-control" value="'+nomInspector+'" readonly style="margin-top: 5px;">'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                
                '<div class="data-summary-block" style="margin-top: 10px; margin-bottom: 20px;">'+
                    '<div class="row">'+
                        '<div class="col-sm-6 col-md-3">'+
                            '<div class="data-summary-item">'+
                                '<small>REVISADA</small>'+
                                '<input type="number" id="cantidad_revisada_consulta" class="form-control user-error" value="'+revisada.toFixed(2)+'" readonly style="margin-top: 5px;">'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-3">'+
                            '<div class="data-summary-item">'+
                                '<small>ACEPTADA</small>'+
                                '<input type="number" id="cantidad_aceptada_consulta" class="form-control user-success" value="'+aceptadas.toFixed(2)+'" readonly style="margin-top: 5px;">'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-3">'+
                            '<div class="data-summary-item">'+
                                '<small>RECHAZADA</small>'+
                                '<input type="number" id="cantidad_rechazada_consulta" class="form-control" value="'+rechazadas.toFixed(2)+'" readonly style="margin-top: 5px;">'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-3">'+
                            '<div class="data-summary-item">'+
                                '<small>% ACEPTADO</small>'+
                                '<input type="text" id="porcentaje_aceptado_consulta" class="form-control" value="'+porcentaje.toFixed(2)+'%" readonly style="margin-top: 5px;">'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                
                '<div style="margin-top: 20px;">'+
                    '<label style="font-weight: bold; margin-bottom: 10px;">Observaciones Generales:</label>'+
                    '<textarea class="form-control textareaObservacionesGenerales" name="observaciones_generales" rows="5" style="resize:none; text-transform:uppercase; margin-top: 5px;" placeholder="INGRESE OBSERVACIONES GENERALES..." readonly>'+inspeccionData.OBSERVACIONES_GENERALES+'</textarea>'+
                '</div>'+
                // Botón de piel solo para materiales de piel (grupo 113) - modo consulta
                (inspeccionData.CODIGO_ARTICULO && materialSeleccionado && materialSeleccionado.GRUPO == 113 ? 
                    '<div style="margin-top: 20px; margin-bottom: 20px;">'+
                        '<button id="btn_ver_piel_consulta" class="btn btn-warning btn-lg btn-block"><i class="fa fa-tags"></i> Ver Clases de Piel</button>'+
                    '</div>' : ''
                )+
                '<div style="margin-top: 20px; margin-bottom: 20px;">'+
                    '<button id="guardar_inspeccion" class="btn btn-success btn-lg btn-block" disabled>Guardar Inspección</button>'+
                '</div>'+
            '</div>'+
        '</div>';
        $('#resumen_material').html(html);
        
        // Evento para el botón de ver piel en modo consulta
        $(document).on('click', '#btn_ver_piel_consulta', function(){
            abrirModalPielConsulta();
        });
    }
    
    // Función para abrir modal de piel en modo consulta
    function abrirModalPielConsulta() {
        // Obtener el ID de inspección del campo del formulario
        var incId = $('#id_inspeccion_consulta').val();

        if (!incId || incId === 'Por definir') {
            swal({
                title: 'Error',
                text: 'No se puede obtener el ID de inspección.',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        // Verificar que tenemos los datos de la inspección
        if (!inspeccionConsultaData) {
            swal({
                title: 'Error',
                text: 'No se encontraron los datos de la inspección.',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        // Limpiar modal completamente antes de cargar nueva información
        $('#piel_articulo_info').text('');
        $('#piel_lote_info').text('');
        $('#piel_cantidad_total').text('');
        $('#claseA, #claseB, #claseC, #claseD').val('');
        $('.porcentaje-clase').text('0.00%');
        $('#totalClases').text('0.00');
        $('#totalPorcentaje').text('0.00%');
        $('.clase-piel').removeClass('is-valid is-invalid');

        // Mostrar blockUI mientras se cargan los datos
        $.blockUI({
            message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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

        // Cargar datos de piel desde la base de datos usando el ID específico de la inspección
        $.getJSON(routeapp+'/home/INSPECCION/ver-piel', {
            inc_id: incId
        }, function(data){
            $.unblockUI();

            if(data.success && data.piel) {
                // Llenar información del modal con datos específicos de esta inspección
                $('#piel_articulo_info').text(inspeccionConsultaData.CODIGO_ARTICULO + ' - ' + inspeccionConsultaData.MATERIAL);
                $('#piel_lote_info').text(inspeccionConsultaData.LOTE || 'N/A');
                $('#piel_cantidad_total').text((parseFloat(inspeccionConsultaData.CAN_INSPECCIONADA)).toFixed(2));

                // Llenar campos con datos específicos de la base de datos
                $('#claseA').val(data.piel.claseA || 0);
                $('#claseB').val(data.piel.claseB || 0);
                $('#claseC').val(data.piel.claseC || 0);
                $('#claseD').val(data.piel.claseD || 0);

                // Calcular porcentajes
                calcularPorcentajesPiel();

                // Deshabilitar todos los campos para modo consulta
                $('#claseA, #claseB, #claseC, #claseD').prop('disabled', true);
                $('#guardarPiel').prop('disabled', true);

                // Mostrar modal
                $('#modalPiel').modal('show');
            } else {
                swal({
                    title: 'Sin datos de piel',
                    text: 'No se encontraron clases de piel para esta inspección.',
                    type: 'info',
                    confirmButtonText: 'Aceptar'
                });
            }
        }).fail(function() {
            $.unblockUI();
            swal({
                title: 'Error',
                text: 'Error al cargar los datos de piel.',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    }
    
    // Función para calcular cantidades automáticamente
    function calcularCantidades() {
        var porRevisar = parseFloat($('#cantidad_por_revisar').val()) || 0;
        var aceptada = parseFloat($('#cantidad_aceptada').val()) || 0;
        var maxDisponible = parseFloat(materialSeleccionado.POR_REVISAR) || 0;
        
        // Validar que cantidad por revisar no sea mayor al disponible
        if(porRevisar > maxDisponible) {
            $('#cantidad_por_revisar').val(maxDisponible);
            porRevisar = maxDisponible;
        }
        
        // Validar que cantidad por revisar no sea cero
        if(porRevisar <= 0) {
            $('#cantidad_por_revisar').val(maxDisponible);
            porRevisar = maxDisponible;
        }
        
        // Validar que cantidad aceptada no sea mayor a por revisar
        if(aceptada > porRevisar) {
            $('#cantidad_aceptada').val(porRevisar);
            aceptada = porRevisar;
        }
        
        var rechazada = porRevisar - aceptada;
        var porcentaje = porRevisar > 0 ? (aceptada / porRevisar * 100) : 0;
        
        $('#cantidad_rechazada').val(rechazada.toFixed(2));
        $('#porcentaje_aceptado').val(porcentaje.toFixed(2) + '%');
        
        // Solo bloquear si por revisar es 0 DESPUÉS de guardar (no durante la edición)
        if(porRevisar <= 0 && maxDisponible <= 0) {
            bloquearElementos();
        } else {
            desbloquearElementos();
        }
    }
    
    // Función para bloquear elementos
    function bloquearElementos() {
        $('#cantidad_por_revisar, #cantidad_aceptada').prop('disabled', true);
        $('.textareaObservacion, .textareaObservacionesGenerales').prop('disabled', true);
        $('input[type="radio"]').prop('disabled', true);
        $('.btnEvidencia').prop('disabled', true);
        $('#guardar_inspeccion').prop('disabled', true);
    }
    
    // Función para desbloquear elementos
    function desbloquearElementos() {
        $('#cantidad_por_revisar, #cantidad_aceptada').prop('disabled', false);
        $('.textareaObservacion, .textareaObservacionesGenerales').prop('disabled', false);
        $('input[type="radio"]').prop('disabled', false);
        $('.btnEvidencia').prop('disabled', false);
        $('#guardar_inspeccion').prop('disabled', false);
    }
    
    // Guardar inspección
    $(document).on('click', '#guardar_inspeccion', function(){
        // Validar que todos los rubros del checklist tengan una opción seleccionada
        var rubrosSinSeleccionar = [];
        checklist.forEach(function(item){
            var estado = $('input[name="estado_'+item.CHK_id+'"]:checked').val();
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
        
        // Validar clases de piel para materiales de tipo piel (grupo 113)
        if(materialSeleccionado.GRUPO == 113) {
            var pielCapturada = pielData[materialSeleccionado.CODIGO_ARTICULO];
            if(!pielCapturada) {
                swal({
                    title: 'Clases de piel no capturadas',
                    text: 'Debe capturar las clases de piel antes de guardar la inspección.',
                    type: 'warning',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            
            var totalPiel = parseFloat(pielCapturada.claseA || 0) + parseFloat(pielCapturada.claseB || 0) + 
                           parseFloat(pielCapturada.claseC || 0) + parseFloat(pielCapturada.claseD || 0);
            var cantidadAceptada = parseFloat($('#cantidad_aceptada').val()) || 0;
            
            if(Math.abs(totalPiel - cantidadAceptada) > 0.01) {
                swal({
                    title: 'Clases de piel incompletas',
                    text: 'La suma de las clases de piel (' + totalPiel.toFixed(2) + ') debe coincidir con la cantidad aceptada (' + cantidadAceptada.toFixed(2) + ').',
                    type: 'warning',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
        }
        
        var datos = new FormData();
        datos.append('material', JSON.stringify(materialSeleccionado));
        datos.append('piel', JSON.stringify(pielData[materialSeleccionado.CODIGO_ARTICULO]||{}));
        datos.append('cantidad_por_revisar', $('#cantidad_por_revisar').val());
        datos.append('cantidad_aceptada', $('#cantidad_aceptada').val());
        datos.append('fecha_inspeccion', $('#fecha_inspeccion').val());
        datos.append('observaciones_generales', $('.textareaObservacionesGenerales').val());
        
        // Enviar lote correcto - usar el lote del material seleccionado
        var loteEnviar = materialSeleccionado.LOTE || 'N/A';
        datos.append('lote', loteEnviar);
        
        // Enviar número de línea del material
        datos.append('line_num', parseInt(materialSeleccionado.LineNum || 0));

        checklist.forEach(function(item){
            var estado = $('input[name="estado_'+item.CHK_id+'"]:checked').val()||'';
            var obs = $('textarea[name="obs_'+item.CHK_id+'"]').val()||'';
            datos.append('checklist['+item.CHK_id+'][estado]', estado);
            datos.append('checklist['+item.CHK_id+'][obs]', obs);
            var files = $('input[name="img_'+item.CHK_id+'"][type="file"]')[0].files;
            if(files.length > 0) {
                for(var i = 0; i < files.length; i++) {
                    datos.append('imagenes['+item.CHK_id+'][]', files[i]);
                }
            }
        });
        
        // Mostrar blockUI
        $.blockUI({
            message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
            url: routeapp + '/home/INSPECCION/guardar',
            type: 'POST',
            data: datos,
            processData: false,
            contentType: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(resp){
                // Ocultar blockUI
                $.unblockUI();
                
                if(resp.success) {
                    // Actualizar materiales con los datos recargados
                    if(resp.materiales) {
                        materiales = resp.materiales;
                        renderTablaMateriales();
                        renderCabecera(materiales[0]);
                        
                        // Verificar si el material actual ya no tiene por revisar
                        var materialActual = null;
                        for(var i = 0; i < materiales.length; i++) {
                            if(materiales[i].CODIGO_ARTICULO == materialSeleccionado.CODIGO_ARTICULO) {
                                materialActual = materiales[i];
                                break;
                            }
                        }
                        
                        // Si el material actual ya no tiene por revisar, bloquear todo
                        if(materialActual && parseFloat(materialActual.POR_REVISAR) <= 0) {
                            bloquearElementos();
                            swal({
                                title: 'Material completado',
                                text: 'Este material ya no tiene cantidad por revisar. La inspección ha sido completada.',
                                type: 'info',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    }
                    
                    // Obtener el ID de la inspección del mensaje de respuesta
                    var idInspeccionGuardada = resp.id_inspeccion || 'N/A';
                    
                    swal({
                        title: 'Guardado exitoso',
                        text: 'La inspección ha sido guardada correctamente con ID: ' + idInspeccionGuardada,
                        type: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                    
                    // Ocultar el resumen y checklist después de guardar exitosamente
                    $('#inspeccion_container').hide();
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
                // Ocultar blockUI en caso de error
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