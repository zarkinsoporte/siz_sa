function js_iniciador() {
    $(".toggle").bootstrapSwitch();
    $("[data-toggle=\"tooltip\"]").tooltip();
    $(".boot-select").selectpicker();
    $(".dropdown-toggle").dropdown();

    setTimeout(function () {
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

    $("#sidebarCollapse").on("click", function () {
        $("#sidebar").toggleClass("active");
        $("#page-wrapper").toggleClass("content");
        $(this).toggleClass("active");
    });
    $("#sidebar").toggleClass("active");
    $("#page-wrapper").toggleClass("content");
    $(this).toggleClass("active");

    var headerClasses = {
        'CORTE': 'header-corte',
        'COSTURA': 'header-costura',
        'COJINERIA': 'header-cojineria',
        'TAPICERIA': 'header-tapiceria'
    };

    function renderEstaciones(estaciones, nSemanas, semanasISO) {
        var container = $("#containerEstaciones");
        container.empty();

        var areasOrden = ['CORTE', 'COSTURA', 'COJINERIA', 'TAPICERIA'];

        areasOrden.forEach(function (areaNombre) {
            var estacion = estaciones[areaNombre];
            if (!estacion) return;

            var headerClass = headerClasses[areaNombre] || 'header-corte';
            var metaPct = estacion.metaPct || '7%';
            var metaNum = estacion.meta ? parseFloat(estacion.meta) : 7;

            var html = '<div class="tarjeta-estacion">';
            html += '<div class="tarjeta-header ' + headerClass + '">';
            html += '<span><i class="fa fa-industry"></i> ' + areaNombre + '</span>';
            html += '<span class="meta-badge">Objetivo: ' + metaPct + '</span>';
            html += '</div>';
            html += '<div class="tarjeta-body">';

            // Tabla
            html += '<table class="tabla-incentivos">';
            html += '<thead><tr>';
            html += '<th style="width: 80px;">Código</th>';
            html += '<th style="width: 200px; text-align: left;">Nombre del Empleado</th>';
            html += '<th style="width: 160px; text-align: left;">Puesto</th>';
            html += '<th style="width: 90px;">Bono</th>';
            html += '<th style="width: 70px;">Meta</th>';
            for (var s = 0; s < semanasISO.length; s++) {
                html += '<th style="width: 80px;">SEM ' + semanasISO[s] + '</th>';
            }
            html += '</tr></thead>';
            html += '<tbody>';

            if (estacion.empleados && estacion.empleados.length > 0) {
                estacion.empleados.forEach(function (emp) {
                    html += '<tr>';
                    html += '<td>' + (emp.num_nom || '-') + '</td>';
                    html += '<td class="text-left">' + (emp.nombre || '-') + '</td>';
                    html += '<td class="text-left">' + (emp.opera || '-') + '</td>';
                    html += '<td>' + formatBono(emp.bono) + '</td>';
                    html += '<td><strong>' + metaPct + '</strong></td>';

                    for (var s = 0; s < semanasISO.length; s++) {
                        var val = emp.semanas[s];
                        if (val === null || val === undefined) {
                            html += '<td class="sem-sin-datos">-</td>';
                        } else if (val === 0) {
                            html += '<td class="sem-cumple">0%</td>';
                        } else {
                            var cumple = val <= metaNum;
                            var cls = cumple ? 'sem-cumple' : 'sem-no-cumple';
                            html += '<td class="' + cls + '">' + val.toFixed(0) + '%</td>';
                        }
                    }
                    html += '</tr>';
                });
            } else {
                var totalCols = 5 + semanasISO.length;
                html += '<tr><td colspan="' + totalCols + '" class="text-center" style="padding: 15px; color: #999;">Sin datos para esta estación</td></tr>';
            }

            html += '</tbody></table>';
            html += '</div></div>';

            container.append(html);
        });

        if (container.children().length === 0) {
            container.html('<p class="text-center text-muted">No hay datos disponibles para el período seleccionado</p>');
        }
    }

    function formatBono(bono) {
        if (!bono || bono === '') return '-';
        var num = parseFloat(bono);
        if (isNaN(num)) return bono;
        return '$ ' + num.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function cargar() {
        var ano = $("#yearPicker").val();
        var mes = $("#mesPicker").val();

        if (!ano || !mes) {
            swal({ title: "Error", text: "Seleccione año y mes", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        swal({ title: "Cargando...", text: "Por favor espere", type: "info", showConfirmButton: false, allowOutsideClick: false });

        $.ajax({
            url: routeapp + "/home/rep-09-resumen-incentivos/buscar",
            type: 'POST',
            data: {
                _token: csrfToken,
                ano: ano,
                mes: mes
            },
            success: function (resp) {
                swal.close();
                if (!resp.success) {
                    swal({ title: "Error", text: resp.msg || "Error al cargar", type: "error", confirmButtonText: "Aceptar" });
                    return;
                }

                $("#txtMes").text(resp.mesNombre + ' ' + resp.ano);
                $("#txtPeriodo").text('Del ' + resp.fechaIS + ' al ' + resp.fechaFS);

                renderEstaciones(resp.estaciones, resp.nSemanas, resp.semanasISO);
            },
            error: function (xhr) {
                swal.close();
                swal({ title: "Error", text: "Error al cargar: " + (xhr.responseText || xhr.statusText), type: "error", confirmButtonText: "Aceptar" });
            }
        });
    }

    $("#btnBuscar").on('click', cargar);

    $("#btnImprimirPDF").on('click', function () {
        var ano = $("#yearPicker").val();
        var mes = $("#mesPicker").val();

        if (!ano || !mes) {
            swal({ title: "Error", text: "Seleccione año y mes", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        var url = routeapp + "/home/rep-09-resumen-incentivos/pdf?ano=" + encodeURIComponent(ano) + "&mes=" + encodeURIComponent(mes);
        window.open(url, '_blank');
    });

    // Cargar datos automáticamente
    cargar();
}
