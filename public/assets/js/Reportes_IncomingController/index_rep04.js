function js_iniciador() {
    $(".toggle").bootstrapSwitch();
    $("[data-toggle=\"tooltip\"]").tooltip();
    $(".boot-select").selectpicker();
    $(".dropdown-toggle").dropdown();
    setTimeout(function() {
        $("#infoMessage").fadeOut("fast");
    }, 5000);
    
    // Inicializar yearPicker
    $("#yearPicker").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years",
        autoclose: true,
        language: "es"
    });
    
    $("#sidebarCollapse").on("click", function() {
        $("#sidebar").toggleClass("active"); 
        $("#page-wrapper").toggleClass("content"); 
        $(this).toggleClass("active"); 
    });
    $("#sidebar").toggleClass("active"); 
    $("#page-wrapper").toggleClass("content"); 
    $(this).toggleClass("active"); 
    
    var dataTable = null;
    
    // Función para formatear porcentaje
    function formatearPorcentaje(valor) {
        if (valor === null || valor === undefined || valor === '' || valor === 0) {
            return '-';
        }
        var num = parseFloat(valor);
        if (isNaN(num) || num === 0) {
            return '-';
        }
        // Los valores vienen como decimales (0.9871 = 98.71%), multiplicar por 100
        // Permitir valores mayores a 1.0 (100%) ya que pueden existir promedios superiores
        return (num * 100).toFixed(2) + '%';
    }
    
    // Función para formatear número
    function formatearNumero(valor) {
        if (valor === null || valor === undefined || valor === '') {
            return '-';
        }
        return parseInt(valor);
    }
    
    // Función para calcular promedio de meses con datos
    function calcularPromedio(meses) {
        var mesesConDatos = meses.map(function(valor) {
            // Convertir a número y manejar null/undefined
            var num = parseFloat(valor);
            return isNaN(num) ? null : num;
        }).filter(function(valor) {
            return valor !== null && valor !== undefined && !isNaN(valor) && valor > 0;
        });
        
        if (mesesConDatos.length === 0) {
            return 0;
        }
        
        var suma = mesesConDatos.reduce(function(a, b) { return a + b; }, 0);
        return suma / mesesConDatos.length;
    }
    
    // Función para cargar datos
    function cargarDatos() {
        var ano = $("#yearPicker").val();
        
        if (!ano) {
            swal({
                title: "Error",
                text: "Por favor seleccione un año",
                type: "error",
                confirmButtonText: "Aceptar"
            });
            return;
        }
        
        // Mostrar loading
        swal({
            title: "Cargando...",
            text: "Por favor espere",
            type: "info",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        
        $.ajax({
            url: routeapp + "/home/rep-04-confiabilidad-prov/buscar",
            type: 'POST',
            data: {
                _token: csrfToken,
                ano: ano
            },
            success: function(response) {
                swal.close();
                
                if (response.success) {
                    // Actualizar tabla de promedio anual
                    actualizarPromedioAnual(response.promedioAnual);
                    
                    // Actualizar tabla de familias
                    actualizarFamilias(response.familias);
                    
                    // Actualizar DataTable de proveedores
                    actualizarProveedores(response.proveedores);
                } else {
                    swal({
                        title: "Error",
                        text: response.msg || "Error al cargar los datos",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                }
            },
            error: function(xhr, status, error) {
                swal.close();
                swal({
                    title: "Error",
                    text: "Error al cargar los datos: " + error,
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            }
        });
    }
    
    // Función para actualizar tabla de promedio anual
    function actualizarPromedioAnual(promedioAnual) {
        if (!promedioAnual) {
            $("#promedioEntradas").text('0');
            $("#promedioAnual").text('-');
            $("#promedioEnero").text('-');
            $("#promedioFebrero").text('-');
            $("#promedioMarzo").text('-');
            $("#promedioAbril").text('-');
            $("#promedioMayo").text('-');
            $("#promedioJunio").text('-');
            $("#promedioJulio").text('-');
            $("#promedioAgosto").text('-');
            $("#promedioSeptiembre").text('-');
            $("#promedioOctubre").text('-');
            $("#promedioNoviembre").text('-');
            $("#promedioDiciembre").text('-');
            return;
        }
        
        $("#promedioEntradas").text(formatearNumero(promedioAnual.ENTRADAS));
        
        // Usar el promedio calculado en el servidor si está disponible
        if (promedioAnual.PROMEDIO !== undefined && promedioAnual.PROMEDIO !== null) {
            $("#promedioAnual").text(formatearPorcentaje(promedioAnual.PROMEDIO));
        } else {
            // Calcular promedio anual si no viene del servidor
            var meses = [
                parseFloat(promedioAnual.ENERO) || 0,
                parseFloat(promedioAnual.FEBRERO) || 0,
                parseFloat(promedioAnual.MARZO) || 0,
                parseFloat(promedioAnual.ABRIL) || 0,
                parseFloat(promedioAnual.MAYO) || 0,
                parseFloat(promedioAnual.JUNIO) || 0,
                parseFloat(promedioAnual.JULIO) || 0,
                parseFloat(promedioAnual.AGOSTO) || 0,
                parseFloat(promedioAnual.SEPTIEMBRE) || 0,
                parseFloat(promedioAnual.OCTUBRE) || 0,
                parseFloat(promedioAnual.NOVIEMBRE) || 0,
                parseFloat(promedioAnual.DICIEMBRE) || 0
            ];
            
            var promedio = calcularPromedio(meses);
            if (isNaN(promedio) || promedio === 0) {
                $("#promedioAnual").text('-');
            } else {
                $("#promedioAnual").text(formatearPorcentaje(promedio));
            }
        }
        
        var ene = parseFloat(promedioAnual.ENERO) || 0;
        var feb = parseFloat(promedioAnual.FEBRERO) || 0;
        var mar = parseFloat(promedioAnual.MARZO) || 0;
        var abr = parseFloat(promedioAnual.ABRIL) || 0;
        var may = parseFloat(promedioAnual.MAYO) || 0;
        var jun = parseFloat(promedioAnual.JUNIO) || 0;
        var jul = parseFloat(promedioAnual.JULIO) || 0;
        var ago = parseFloat(promedioAnual.AGOSTO) || 0;
        var sep = parseFloat(promedioAnual.SEPTIEMBRE) || 0;
        var oct = parseFloat(promedioAnual.OCTUBRE) || 0;
        var nov = parseFloat(promedioAnual.NOVIEMBRE) || 0;
        var dic = parseFloat(promedioAnual.DICIEMBRE) || 0;
        
        $("#promedioEnero").text(ene > 0 ? formatearPorcentaje(ene) : '-');
        $("#promedioFebrero").text(feb > 0 ? formatearPorcentaje(feb) : '-');
        $("#promedioMarzo").text(mar > 0 ? formatearPorcentaje(mar) : '-');
        $("#promedioAbril").text(abr > 0 ? formatearPorcentaje(abr) : '-');
        $("#promedioMayo").text(may > 0 ? formatearPorcentaje(may) : '-');
        $("#promedioJunio").text(jun > 0 ? formatearPorcentaje(jun) : '-');
        $("#promedioJulio").text(jul > 0 ? formatearPorcentaje(jul) : '-');
        $("#promedioAgosto").text(ago > 0 ? formatearPorcentaje(ago) : '-');
        $("#promedioSeptiembre").text(sep > 0 ? formatearPorcentaje(sep) : '-');
        $("#promedioOctubre").text(oct > 0 ? formatearPorcentaje(oct) : '-');
        $("#promedioNoviembre").text(nov > 0 ? formatearPorcentaje(nov) : '-');
        $("#promedioDiciembre").text(dic > 0 ? formatearPorcentaje(dic) : '-');
    }
    
    // Función para actualizar tabla de familias
    function actualizarFamilias(familias) {
        var tbody = $("#tbodyFamilias");
        tbody.empty();
        
        if (!familias || familias.length === 0) {
            tbody.append('<tr><td colspan="15" class="text-center">No hay datos disponibles</td></tr>');
            return;
        }
        
        familias.forEach(function(familia) {
            var meses = [
                parseFloat(familia.ENERO) || 0,
                parseFloat(familia.FEBRERO) || 0,
                parseFloat(familia.MARZO) || 0,
                parseFloat(familia.ABRIL) || 0,
                parseFloat(familia.MAYO) || 0,
                parseFloat(familia.JUNIO) || 0,
                parseFloat(familia.JULIO) || 0,
                parseFloat(familia.AGOSTO) || 0,
                parseFloat(familia.SEPTIEMBRE) || 0,
                parseFloat(familia.OCTUBRE) || 0,
                parseFloat(familia.NOVIEMBRE) || 0,
                parseFloat(familia.DICIEMBRE) || 0
            ];
            
            var promedio = calcularPromedio(meses);
            if (isNaN(promedio) || promedio === 0) {
                promedio = 0;
            }
            
            var ene = parseFloat(familia.ENERO) || 0;
            var feb = parseFloat(familia.FEBRERO) || 0;
            var mar = parseFloat(familia.MARZO) || 0;
            var abr = parseFloat(familia.ABRIL) || 0;
            var may = parseFloat(familia.MAYO) || 0;
            var jun = parseFloat(familia.JUNIO) || 0;
            var jul = parseFloat(familia.JULIO) || 0;
            var ago = parseFloat(familia.AGOSTO) || 0;
            var sep = parseFloat(familia.SEPTIEMBRE) || 0;
            var oct = parseFloat(familia.OCTUBRE) || 0;
            var nov = parseFloat(familia.NOVIEMBRE) || 0;
            var dic = parseFloat(familia.DICIEMBRE) || 0;
            
            var row = '<tr>' +
                '<td>' + formatearNumero(familia.ENTRADAS) + '</td>' +
                '<td>' + (familia.GRUPO || 'SIN GRUPO') + '</td>' +
                '<td class="promedio-cell">' + formatearPorcentaje(promedio) + '</td>' +
                '<td>' + (ene > 0 ? formatearPorcentaje(ene) : '-') + '</td>' +
                '<td>' + (feb > 0 ? formatearPorcentaje(feb) : '-') + '</td>' +
                '<td>' + (mar > 0 ? formatearPorcentaje(mar) : '-') + '</td>' +
                '<td>' + (abr > 0 ? formatearPorcentaje(abr) : '-') + '</td>' +
                '<td>' + (may > 0 ? formatearPorcentaje(may) : '-') + '</td>' +
                '<td>' + (jun > 0 ? formatearPorcentaje(jun) : '-') + '</td>' +
                '<td>' + (jul > 0 ? formatearPorcentaje(jul) : '-') + '</td>' +
                '<td>' + (ago > 0 ? formatearPorcentaje(ago) : '-') + '</td>' +
                '<td>' + (sep > 0 ? formatearPorcentaje(sep) : '-') + '</td>' +
                '<td>' + (oct > 0 ? formatearPorcentaje(oct) : '-') + '</td>' +
                '<td>' + (nov > 0 ? formatearPorcentaje(nov) : '-') + '</td>' +
                '<td>' + (dic > 0 ? formatearPorcentaje(dic) : '-') + '</td>' +
                '</tr>';
            
            tbody.append(row);
        });
    }
    
    // Función para actualizar DataTable de proveedores
    function actualizarProveedores(proveedores) {
        if (dataTable) {
            dataTable.destroy();
        }
        
        var datos = [];
        
        if (proveedores && proveedores.length > 0) {
            proveedores.forEach(function(proveedor) {
                datos.push([
                    formatearNumero(proveedor.ENTRADAS),
                    proveedor.COD_PRO || '-',
                    proveedor.PROVEEDOR || '-',
                    formatearPorcentaje(proveedor.PROMEDIO || 0),
                    proveedor.ENERO > 0 ? formatearPorcentaje(proveedor.ENERO) : '-',
                    proveedor.FEBRERO > 0 ? formatearPorcentaje(proveedor.FEBRERO) : '-',
                    proveedor.MARZO > 0 ? formatearPorcentaje(proveedor.MARZO) : '-',
                    proveedor.ABRIL > 0 ? formatearPorcentaje(proveedor.ABRIL) : '-',
                    proveedor.MAYO > 0 ? formatearPorcentaje(proveedor.MAYO) : '-',
                    proveedor.JUNIO > 0 ? formatearPorcentaje(proveedor.JUNIO) : '-',
                    proveedor.JULIO > 0 ? formatearPorcentaje(proveedor.JULIO) : '-',
                    proveedor.AGOSTO > 0 ? formatearPorcentaje(proveedor.AGOSTO) : '-',
                    proveedor.SEPTIEMBRE > 0 ? formatearPorcentaje(proveedor.SEPTIEMBRE) : '-',
                    proveedor.OCTUBRE > 0 ? formatearPorcentaje(proveedor.OCTUBRE) : '-',
                    proveedor.NOVIEMBRE > 0 ? formatearPorcentaje(proveedor.NOVIEMBRE) : '-',
                    proveedor.DICIEMBRE > 0 ? formatearPorcentaje(proveedor.DICIEMBRE) : '-'
                ]);
            });
        }
        
        dataTable = $("#tablaProveedores").DataTable({
            data: datos,
            //pageLength: 25,
            //sin length menu
            pageLength: -1,
            order: [[2, 'asc']], // Ordenar por nombre de proveedor
            columnDefs: [
                { orderable: false, targets: [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15] }, // Deshabilitar ordenamiento desde la columna 4 (% Confiabilidad) en adelante
                { width: "80px", targets: [0] }, // Entradas
                { width: "100px", targets: [1] }, // Código del Prov.
                { width: "200px", targets: [2] }, // Nombre del Proveedor
                { width: "120px", targets: [3] }, // % Confiabilidad Proveedor
                { width: "80px", targets: [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15] } // Meses
            ],
            autoWidth: false,
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
            scrollX: true,
            scrollCollapse: true
        });
    }
    
    // Evento para botón buscar
    $("#btnBuscar").on('click', function() {
        cargarDatos();
    });
    
    // Evento para botón imprimir PDF
    $("#btnImprimirPDF").on('click', function() {
        var ano = $("#yearPicker").val();
        
        if (!ano) {
            swal({
                title: "Error",
                text: "Por favor seleccione un año",
                type: "error",
                confirmButtonText: "Aceptar"
            });
            return;
        }
        
        // Abrir PDF en nueva pestaña
        var url = routeapp + "/home/rep-04-confiabilidad-prov/pdf?ano=" + ano;
        window.open(url, '_blank');
    });
    
    // Cargar datos automáticamente al iniciar
    cargarDatos();
}
