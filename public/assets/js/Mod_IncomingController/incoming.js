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
                //$('#cabecera_nota').hide();
                $('#materiales_container').hide();
                $('#inspeccion_container').hide();
            }
        }).fail(function() {
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
        materiales.forEach(function(mat, idx){
            var porRevisar = (parseFloat(mat.CANTIDAD) || 0) - (parseFloat(mat.CAN_INSPECCIONADA) || 0) - (parseFloat(mat.CAN_RECHAZADA) || 0);
            var acciones = '';
            
            // Botón de piel si es grupo 113
            if(mat.GRUPO_MATERIAL == 113) {
                acciones += '<button class="btn btn-warning btn-xs btnPiel" title="Capturar Clases de Piel"><i class="fa fa-tags"></i></button> ';
            }
            
            // Ícono de checklist
            acciones += '<button class="btn btn-primary btn-sm btnChecklist" title="Abrir Checklist"><span class="glyphicon glyphicon-check"></span></button>';
            
            tbody += '<tr data-idx="'+idx+'">'+
                '<td>'+acciones+'</td>'+
                '<td>'+mat.CODIGO_ARTICULO+'</td>'+
                '<td>'+mat.MATERIAL+'</td>'+
                '<td>'+mat.UDM+'</td>'+
                //cantidad a 2 decimales
                '<td>'+(parseFloat(mat.CANTIDAD) || 0).toFixed(2)+'</td>'+
                '<td>'+(mat.CAN_INSPECCIONADA||0)+'</td>'+
                '<td>'+(mat.CAN_RECHAZADA||0)+'</td>'+
                '<td>'+porRevisar+'</td>'+
            '</tr>';
        });
        $('#tabla_materiales tbody').html(tbody);
        
        // Inicializar nueva DataTable
        dataTable = $('#tabla_materiales').DataTable({
            language: {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
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
        if(total != (parseFloat(materialSeleccionado.CANTIDAD) || 0)){
            $('#alertPiel').show().text('La suma de clases debe ser igual a la cantidad recibida ('+(parseFloat(materialSeleccionado.CANTIDAD) || 0)+')');
            return;
        }
        pielData[materialSeleccionado.COD_ARTICULO] = {
            claseA: $('#claseA').val(),
            claseB: $('#claseB').val(),
            claseC: $('#claseC').val(),
            claseD: $('#claseD').val()
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
    
    // Evento para mostrar checklist
    $('#tabla_materiales').on('click', '.btnChecklist', function(){
        var idx = $(this).closest('tr').data('idx');
        materialSeleccionado = materiales[idx];
        cantidadRecibida = parseFloat(materialSeleccionado.CANTIDAD) || 0;
        $.getJSON(routeapp+'/home/INSPECCION/checklist', {inc_id: materialSeleccionado.COD_ARTICULO}, function(data){
            checklist = data.checklist;
            respuestas = {};
            if(data.respuestas){
                data.respuestas.forEach(function(r){
                    respuestas[r.IND_chkId] = r;
                });
            }
            renderChecklist();
            renderResumen();
            $('#inspeccion_container').show();
        }).fail(function() {
            swal({
                title: 'Error',
                text: 'Error al cargar el checklist',
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
                '<td><button type="button" class="btn btn-primary btn-sm btnEvidencia" title="Adjuntar Evidencia"><span class="glyphicon glyphicon-camera"></span></button><input type="file" name="img_'+item.CHK_id+'" accept=".jpg,.jpeg,.png" style="display:none;" class="inputEvidencia"></td>'+
                '<td>'+item.CHK_descripcion+'</td>'+
                '<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="C" '+(r.IND_estado=='C'?'checked':'')+'></td>'+
                '<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="N" '+(r.IND_estado=='N'?'checked':'')+'></td>'+
                '<td style="text-align:center"><input style="accent-color:black;" type="radio" name="estado_'+item.CHK_id+'" value="A" '+(r.IND_estado=='A'?'checked':'')+'></td>'+
                '<td><textarea class="form-control textareaObservacion" name="obs_'+item.CHK_id+'" rows="2" style="resize:none; text-transform:uppercase;">'+(r.IND_observacion||'')+'</textarea></td>'+
            '</tr>';
        });
        html += '</tbody></table>';
        $('#checklist_container').html(html);
        
        // Evento para el botón de evidencia
        $('.btnEvidencia').click(function(){
            $(this).siblings('.inputEvidencia').click();
        });
        
        // Evento para cuando se selecciona un archivo
        $('.inputEvidencia').change(function(){
            var file = this.files[0];
            if(file) {
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
                
                // Validar tamaño (máximo 5MB)
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
                
                // Cambiar el ícono del botón para indicar que hay archivo
                $(this).siblings('.btnEvidencia').html('<span class="glyphicon glyphicon-ok text-success"></span>');
                
                swal({
                    title: 'Archivo adjuntado',
                    text: 'Archivo cargado correctamente: ' + file.name,
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
    
    // Renderizar resumen lateral
    function renderResumen(){
        var aceptadas = 500;//materialSeleccionado.CAN_INSPECCIONADA||0;
        var rechazadas = 0;//materialSeleccionado.CAN_RECHAZADA||0;
        var porcentaje = 100;//(aceptadas/cantidadRecibida*100);
        var html = '<div class="card">'+
            '<div class="card-body">'+
                '<h4 style="font-weight: bold; margin-bottom: 16px;">RESUMEN</h4>'+
                '<ul class="list-group">'+
                    '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                        'Cantidad Recibida'+
                        '<span class="badge badge-pill">500.00</span>'+
                    '</li>'+
                    '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                        'Cantidad Aceptada'+
                        '<span class="badge badge-pill">'+aceptadas.toFixed(2)+'</span>'+
                    '</li>'+
                    '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                        'Cantidad Rechazada'+
                        '<span class="badge badge-pill">'+rechazadas.toFixed(2)+'</span>'+
                    '</li>'+
                    '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                        '% Aceptado'+
                        '<span class="badge badge-pill">'+porcentaje.toFixed(2)+'%</span>'+
                    '</li>'+
                '</ul>'+
                '<div style="margin-top: 20px;">'+
                    '<label style="font-weight: bold; margin-bottom: 10px;">Observaciones Generales:</label>'+
                    '<textarea class="form-control textareaObservacionesGenerales" name="observaciones_generales" rows="5" style="resize:none; text-transform:uppercase; margin-top: 5px;" placeholder="INGRESE OBSERVACIONES GENERALES..."></textarea>'+
                '</div>'+
            '</div>'+
        '</div>';
        $('#resumen_material').html(html);
        
        // Evento para convertir a mayúsculas en tiempo real para observaciones generales
        $('.textareaObservacionesGenerales').on('input', function(){
            this.value = this.value.toUpperCase();
        });
    }
    
    // Guardar inspección
    $('#guardar_inspeccion').click(function(){
        var datos = new FormData();
        datos.append('material', JSON.stringify(materialSeleccionado));
        datos.append('piel', JSON.stringify(pielData[materialSeleccionado.COD_ARTICULO]||{}));
        checklist.forEach(function(item){
            var estado = $('input[name="estado_'+item.CHK_id+'"]:checked').val()||'';
            var obs = $('input[name="obs_'+item.CHK_id+'"').val()||'';
            datos.append('checklist['+item.CHK_id+'][estado]', estado);
            datos.append('checklist['+item.CHK_id+'][obs]', obs);
            var file = $('input[name="img_'+item.CHK_id+'"]')[0].files[0];
            if(file) datos.append('imagenes['+item.CHK_id+']', file);
        });
        
        $.ajax({
            url: routeapp+'/home/INSPECCION/guardar',
            type: 'POST',
            data: datos,
            processData: false,
            contentType: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(resp){
                swal({
                    title: 'Guardado exitoso',
                    text: resp.msg || 'La inspección ha sido guardada correctamente',
                    type: 'success',
                    confirmButtonText: 'Aceptar'
                });
            },
            error: function() {
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