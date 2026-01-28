function js_iniciador() {
    $(".toggle").bootstrapSwitch();
    $("[data-toggle=\"tooltip\"]").tooltip();
    $(".boot-select").selectpicker();
    $(".dropdown-toggle").dropdown();

    setTimeout(function () {
        $("#infoMessage").fadeOut("fast");
    }, 5000);

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
        return (parseFloat(v) * 100).toFixed(2) + '%';
    }

    function fmtNum(v) {
        if (v === null || v === undefined || v === '') return '0';
        return Number(v).toLocaleString('es-MX', { maximumFractionDigits: 2 });
    }

    function mesNombre(m) {
        var map = {
            '01': 'Enero', '02': 'Febrero', '03': 'Marzo', '04': 'Abril',
            '05': 'Mayo', '06': 'Junio', '07': 'Julio', '08': 'Agosto',
            '09': 'Septiembre', '10': 'Octubre', '11': 'Noviembre', '12': 'Diciembre'
        };
        var mm = ('' + m).padStart(2, '0');
        return map[mm] || mm;
    }

    function renderMes(resumenMes) {
        var tbody = $("#tbodyMes");
        tbody.empty();
        if (!resumenMes || resumenMes.length === 0) {
            tbody.append('<tr><td colspan="2" class="text-center">Sin datos</td></tr>');
            return;
        }
        resumenMes.forEach(function (r) {
            var mm = ('' + r.MES).padStart(2, '0');
            var cal = r.CALIFA;
            tbody.append(
                '<tr>' +
                '<td>' + mesNombre(mm) + '</td>' +
                '<td>' + (cal && parseFloat(cal) > 0 ? fmtPct(cal) : '-') + '</td>' +
                '</tr>'
            );
        });
    }

    function renderDetalle(detalle) {
        if (dtDetalle) dtDetalle.destroy();

        var rows = [];
        if (detalle && detalle.length > 0) {
            detalle.forEach(function (d) {
                rows.push([
                    d.RECHAZO,
                    d.NE,
                    d.COD_MAT,
                    d.MATERIAL,
                    d.UDM,
                    fmtNum(d.RECIBIDO),
                    fmtNum(d.RECHAZADA)
                ]);
            });
        }

        dtDetalle = $("#tablaDetalle").DataTable({
            data: rows,
            pageLength: 25,
            order: [[1, 'desc']],
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
        var ano = $("#anoReporte").val();
        var codProv = ($("#codProv").val() || '').trim();

        if (!codProv) {
            swal({ title: "Error", text: "Capture el código de proveedor", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        swal({ title: "Cargando...", text: "Por favor espere", type: "info", showConfirmButton: false, allowOutsideClick: false });

        $.ajax({
            url: '/home/rep-05-historial-proveedor/buscar',
            type: 'POST',
            data: { _token: csrfToken, ano: ano, cod_prov: codProv },
            success: function (resp) {
                swal.close();
                if (!resp.success) {
                    swal({ title: "Error", text: resp.msg || "Error al cargar", type: "error", confirmButtonText: "Aceptar" });
                    return;
                }

                $("#txtProveedor").text((resp.codProv || codProv) + (resp.proveedorNombre ? (' - ' + resp.proveedorNombre) : ''));
                $("#txtPeriodo").text('Del ' + resp.fechaIS + ' al ' + resp.fechaFS);

                renderMes(resp.resumenMes);
                renderDetalle(resp.detalle);
            },
            error: function (xhr) {
                swal.close();
                swal({ title: "Error", text: "Error al cargar: " + (xhr.responseText || xhr.statusText), type: "error", confirmButtonText: "Aceptar" });
            }
        });
    }

    $("#btnBuscar").on('click', cargar);

    $("#btnImprimirPDF").on('click', function () {
        var ano = $("#anoReporte").val();
        var codProv = ($("#codProv").val() || '').trim();
        if (!codProv) {
            swal({ title: "Error", text: "Capture el código de proveedor", type: "error", confirmButtonText: "Aceptar" });
            return;
        }
        window.open('/home/rep-05-historial-proveedor/pdf?ano=' + encodeURIComponent(ano) + '&cod_prov=' + encodeURIComponent(codProv), '_blank');
    });
}

