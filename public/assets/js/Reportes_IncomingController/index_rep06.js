function js_iniciador() {
    $(".toggle").bootstrapSwitch();
    $("[data-toggle=\"tooltip\"]").tooltip();
    $(".boot-select").selectpicker();
    $(".dropdown-toggle").dropdown();

    setTimeout(function () {
        $("#infoMessage").fadeOut("fast");
    }, 5000);
    
    // Inicializar datepickers para fechas
    $("#fechaDesde").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        language: "es",
        todayHighlight: true
    });
    
    $("#fechaHasta").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        language: "es",
        todayHighlight: true
    });

    $("#sidebarCollapse").on("click", function () {
        $("#sidebar").toggleClass("active");
        $("#page-wrapper").toggleClass("content");
        $(this).toggleClass("active");
    });
    $("#sidebar").toggleClass("active");
    $("#page-wrapper").toggleClass("content");
    $(this).toggleClass("active");

    var dtDetalle = null;

    function fmtPct(v) {
        if (v === null || v === undefined || v === '' || parseFloat(v) === 0) return '-';
        var num = parseFloat(v);
        if (isNaN(num) || num === 0) return '-';
        // Los valores vienen como decimales (0.9871 = 98.71%), multiplicar por 100
        return (num * 100).toFixed(2) + '%';
    }

    function fmtNum(v) {
        if (v === null || v === undefined || v === '') return '0';
        return Number(v).toLocaleString('es-MX', { maximumFractionDigits: 2 });
    }

    function renderDetalle(datos) {
        if (dtDetalle) {
            dtDetalle.destroy();
            dtDetalle = null;
        }
        
        // Limpiar completamente la tabla
        $("#tablaDetalle").removeClass("dataTable");
        $("#tablaDetalle").removeData();
        $("#tablaDetalle tbody").empty();

        var rows = [];
        if (datos && datos.length > 0) {
            datos.forEach(function (d, index) {
                rows.push([
                    index + 1,
                    d.COD_PROV || '-',
                    d.PROVEEDOR || '-',
                    fmtNum(d.ACEPTADO),
                    fmtPct(d.CALIFA)
                ]);
            });
        }

        dtDetalle = $("#tablaDetalle").DataTable({
            data: rows,
            pageLength: 25,
            order: [[2, 'asc']], // Ordenar por nombre de proveedor
            autoWidth: false,
            columnDefs: [
                { width: "50px", targets: [0] }, // #
                { width: "120px", targets: [1] }, // Código Proveedor
                { width: "300px", targets: [2] }, // Proveedor
                { width: "120px", targets: [3] }, // Aceptado
                { width: "120px", targets: [4] }  // Calificación
            ],
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": { "sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior" }
            },
            scrollX: true,
            scrollCollapse: true
        });
    }

    function cargar() {
        var fechaDesde = $("#fechaDesde").val();
        var fechaHasta = $("#fechaHasta").val();
        var codMaterial = ($("#codMaterial").val() || '').trim();

        if (!codMaterial) {
            swal({ title: "Error", text: "Capture el código de material", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        if (!fechaDesde || !fechaHasta) {
            swal({ title: "Error", text: "Capture las fechas", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        // Validar que fecha desde sea menor o igual a fecha hasta
        if (new Date(fechaDesde) > new Date(fechaHasta)) {
            swal({ title: "Error", text: "La fecha desde debe ser menor o igual a la fecha hasta", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        swal({ title: "Cargando...", text: "Por favor espere", type: "info", showConfirmButton: false, allowOutsideClick: false });

        $.ajax({
            url: routeapp + "/home/rep-06-historial-material/buscar",
            type: 'POST',
            data: { 
                _token: csrfToken, 
                fecha_desde: fechaDesde, 
                fecha_hasta: fechaHasta, 
                cod_material: codMaterial 
            },
            success: function (resp) {
                swal.close();
                if (!resp.success) {
                    swal({ title: "Error", text: resp.msg || "Error al cargar", type: "error", confirmButtonText: "Aceptar" });
                    return;
                }

                $("#txtMaterial").text((resp.codMaterial || codMaterial) + (resp.materialNombre ? (' - ' + resp.materialNombre) : ''));
                $("#txtUDM").text(resp.udm || '-');
                $("#txtPeriodo").text('Del ' + resp.fechaIS + ' al ' + resp.fechaFS);
                
                renderDetalle(resp.datos);
            },
            error: function (xhr) {
                swal.close();
                swal({ title: "Error", text: "Error al cargar: " + (xhr.responseText || xhr.statusText), type: "error", confirmButtonText: "Aceptar" });
            }
        });
    }

    $("#btnBuscar").on('click', cargar);

    $("#btnImprimirPDF").on('click', function () {
        var fechaDesde = $("#fechaDesde").val();
        var fechaHasta = $("#fechaHasta").val();
        var codMaterial = ($("#codMaterial").val() || '').trim();
        
        if (!codMaterial) {
            swal({ title: "Error", text: "Capture el código de material", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        if (!fechaDesde || !fechaHasta) {
            swal({ title: "Error", text: "Capture las fechas", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        // Validar que fecha desde sea menor o igual a fecha hasta
        if (new Date(fechaDesde) > new Date(fechaHasta)) {
            swal({ title: "Error", text: "La fecha desde debe ser menor o igual a la fecha hasta", type: "error", confirmButtonText: "Aceptar" });
            return;
        }
        
        var url = routeapp + "/home/rep-06-historial-material/pdf?fecha_desde=" + encodeURIComponent(fechaDesde) + "&fecha_hasta=" + encodeURIComponent(fechaHasta) + "&cod_material=" + encodeURIComponent(codMaterial);
        window.open(url, '_blank');
    });
}
