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
    
    var dataTable = null;
    
    // Función para formatear porcentaje
    function formatearPorcentaje(valor) {
        if (valor === null || valor === undefined || valor === 0 || valor === '') {
            return '-';
        }
        return (parseFloat(valor) * 100).toFixed(2) + '%';
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
        var mesesConDatos = meses.filter(function(valor) {
            return valor !== null && valor !== undefined && valor !== '' && valor > 0;
        });
        
        if (mesesConDatos.length === 0) {
            return 0;
        }
        
        var suma = mesesConDatos.reduce(function(a, b) { return a + b; }, 0);
        return suma / mesesConDatos.length;
    }
    
    // Función para cargar datos
    function cargarDatos() {
        var ano = $("#anoReporte").val();
        
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
            url: '/home/rep-04-confiabilidad-prov/buscar',
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
        
        // Calcular promedio anual
        var meses = [
            promedioAnual.ENERO, promedioAnual.FEBRERO, promedioAnual.MARZO,
            promedioAnual.ABRIL, promedioAnual.MAYO, promedioAnual.JUNIO,
            promedioAnual.JULIO, promedioAnual.AGOSTO, promedioAnual.SEPTIEMBRE,
            promedioAnual.OCTUBRE, promedioAnual.NOVIEMBRE, promedioAnual.DICIEMBRE
        ];
        
        var promedio = calcularPromedio(meses);
        $("#promedioAnual").text(formatearPorcentaje(promedio));
        
        $("#promedioEnero").text(promedioAnual.ENERO > 0 ? formatearPorcentaje(promedioAnual.ENERO) : '-');
        $("#promedioFebrero").text(promedioAnual.FEBRERO > 0 ? formatearPorcentaje(promedioAnual.FEBRERO) : '-');
        $("#promedioMarzo").text(promedioAnual.MARZO > 0 ? formatearPorcentaje(promedioAnual.MARZO) : '-');
        $("#promedioAbril").text(promedioAnual.ABRIL > 0 ? formatearPorcentaje(promedioAnual.ABRIL) : '-');
        $("#promedioMayo").text(promedioAnual.MAYO > 0 ? formatearPorcentaje(promedioAnual.MAYO) : '-');
        $("#promedioJunio").text(promedioAnual.JUNIO > 0 ? formatearPorcentaje(promedioAnual.JUNIO) : '-');
        $("#promedioJulio").text(promedioAnual.JULIO > 0 ? formatearPorcentaje(promedioAnual.JULIO) : '-');
        $("#promedioAgosto").text(promedioAnual.AGOSTO > 0 ? formatearPorcentaje(promedioAnual.AGOSTO) : '-');
        $("#promedioSeptiembre").text(promedioAnual.SEPTIEMBRE > 0 ? formatearPorcentaje(promedioAnual.SEPTIEMBRE) : '-');
        $("#promedioOctubre").text(promedioAnual.OCTUBRE > 0 ? formatearPorcentaje(promedioAnual.OCTUBRE) : '-');
        $("#promedioNoviembre").text(promedioAnual.NOVIEMBRE > 0 ? formatearPorcentaje(promedioAnual.NOVIEMBRE) : '-');
        $("#promedioDiciembre").text(promedioAnual.DICIEMBRE > 0 ? formatearPorcentaje(promedioAnual.DICIEMBRE) : '-');
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
                familia.ENERO, familia.FEBRERO, familia.MARZO,
                familia.ABRIL, familia.MAYO, familia.JUNIO,
                familia.JULIO, familia.AGOSTO, familia.SEPTIEMBRE,
                familia.OCTUBRE, familia.NOVIEMBRE, familia.DICIEMBRE
            ];
            
            var promedio = calcularPromedio(meses);
            
            var row = '<tr>' +
                '<td>' + formatearNumero(familia.ENTRADAS) + '</td>' +
                '<td>' + (familia.GRUPO || 'SIN GRUPO') + '</td>' +
                '<td class="promedio-cell">' + formatearPorcentaje(promedio) + '</td>' +
                '<td>' + (familia.ENERO > 0 ? formatearPorcentaje(familia.ENERO) : '-') + '</td>' +
                '<td>' + (familia.FEBRERO > 0 ? formatearPorcentaje(familia.FEBRERO) : '-') + '</td>' +
                '<td>' + (familia.MARZO > 0 ? formatearPorcentaje(familia.MARZO) : '-') + '</td>' +
                '<td>' + (familia.ABRIL > 0 ? formatearPorcentaje(familia.ABRIL) : '-') + '</td>' +
                '<td>' + (familia.MAYO > 0 ? formatearPorcentaje(familia.MAYO) : '-') + '</td>' +
                '<td>' + (familia.JUNIO > 0 ? formatearPorcentaje(familia.JUNIO) : '-') + '</td>' +
                '<td>' + (familia.JULIO > 0 ? formatearPorcentaje(familia.JULIO) : '-') + '</td>' +
                '<td>' + (familia.AGOSTO > 0 ? formatearPorcentaje(familia.AGOSTO) : '-') + '</td>' +
                '<td>' + (familia.SEPTIEMBRE > 0 ? formatearPorcentaje(familia.SEPTIEMBRE) : '-') + '</td>' +
                '<td>' + (familia.OCTUBRE > 0 ? formatearPorcentaje(familia.OCTUBRE) : '-') + '</td>' +
                '<td>' + (familia.NOVIEMBRE > 0 ? formatearPorcentaje(familia.NOVIEMBRE) : '-') + '</td>' +
                '<td>' + (familia.DICIEMBRE > 0 ? formatearPorcentaje(familia.DICIEMBRE) : '-') + '</td>' +
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
            pageLength: 25,
            order: [[2, 'asc']], // Ordenar por nombre de proveedor
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
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 3
            }
        });
    }
    
    // Evento para botón buscar
    $("#btnBuscar").on('click', function() {
        cargarDatos();
    });
    
    // Evento para botón imprimir PDF
    $("#btnImprimirPDF").on('click', function() {
        var ano = $("#anoReporte").val();
        
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
        var url = '/home/rep-04-confiabilidad-prov/pdf?ano=' + ano;
        window.open(url, '_blank');
    });
    
    // Cargar datos automáticamente al iniciar
    cargarDatos();
}
