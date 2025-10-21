// Variables globales
var opData = null;
var centroInspeccionData = null;
var checklist = [];
var respuestas = {};
var idInspeccion = 0;

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
    
    // Función para buscar OP
    function buscarOP() {
        var numeroOP = $('#numero_op').val();
        var centroInspeccion = $('#centro_inspeccion').val();
        
        // Validaciones
        if(!numeroOP) {
            swal({
                title: 'Campo requerido',
                text: 'Ingrese un número de OP',
                type: 'warning',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        if(!centroInspeccion) {
            swal({
                title: 'Campo requerido',
                text: 'Seleccione un Centro de Inspección',
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
        
        $.getJSON(routeapp+'/home/inspeccion-proceso/buscar', {
            op: numeroOP, 
            centro_inspeccion: centroInspeccion
        }, function(data){
            if(data.success) {
                opData = data.op;
                centroInspeccionData = data.centro_inspeccion;
                renderCabeceraOP();
                // TODO: Cargar checklist y mostrar formulario de inspección
                $('#inspeccion_container').show();
            } else {
                swal({
                    title: 'Sin resultados',
                    text: data.msg || 'No se encontró información para esta OP',
                    type: 'info',
                    confirmButtonText: 'Aceptar'
                });
                $('#inspeccion_container').hide();
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
    
    // Evento para buscar con Enter en OP
    $('#numero_op').keypress(function(e) {
        if(e.which == 13) { // Enter key
            $('#inspeccion_container').hide();
            buscarOP();
        }
    });
    
    // Evento para detectar cambio en centro de inspección
    $('#centro_inspeccion').on('change', function() {
        $('#inspeccion_container').hide();
        $('#articulo_op').text('');
        $('#cantidad_op').text('');
        //$('#fecha_entrega_op').text('');
    });
    
    // Renderizar cabecera con información de la OP
    function renderCabeceraOP() {
        if(opData) {
            $('#articulo_op').text(opData.ItemCode + ' - ' + opData.ItemName);
            $('#cantidad_op').text('Cantidad Planeada: ' + parseFloat(opData.CantidadPlaneada).toFixed(2));
            
            /* // Formatear fecha de entrega
            if(opData.FechaEntrega) {
                var fecha = new Date(opData.FechaEntrega);
                var fechaFormateada = fecha.getDate().toString().padStart(2, '0') + '/' + 
                                     (fecha.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                                     fecha.getFullYear();
                $('#fecha_entrega_op').text('Fecha Entrega: ' + fechaFormateada);
            } */
            
            $('#cabecera_nota').show();
        }
    }
    
    // TODO: Implementar las funciones para:
    // - Cargar checklist específico del centro de inspección
    // - Renderizar formulario de inspección
    // - Guardar inspección
    // - Ver inspecciones previas
}

