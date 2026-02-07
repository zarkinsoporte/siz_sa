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

    // Variables globales para almacenar datos
    var datosAreas = null;
    var datosTotales = null;

    var mesesNombres = {
        '01': 'Enero', '02': 'Febrero', '03': 'Marzo', '04': 'Abril',
        '05': 'Mayo', '06': 'Junio', '07': 'Julio', '08': 'Agosto',
        '09': 'Septiembre', '10': 'Octubre', '11': 'Noviembre', '12': 'Diciembre'
    };

    function fmtNum(v) {
        if (v === null || v === undefined || v === '' || parseFloat(v) === 0) return '-';
        return Number(v).toLocaleString('es-MX', { maximumFractionDigits: 0 });
    }

    function fmtDec(v) {
        if (v === null || v === undefined || v === '' || parseFloat(v) === 0) return '-';
        return Number(v).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function fmtPct(producido, reprocesado) {
        if (!producido || producido === 0) return '-';
        var pct = (reprocesado / producido) * 100;
        return pct.toFixed(2) + '%';
    }

    function renderTabla(areas, totales) {
        var tbody = $("#tbodyIndicadores");
        tbody.empty();

        // Guardar datos globalmente para el modal
        datosAreas = areas;
        datosTotales = totales;

        var mesesKeys = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        // TOTAL
        renderAreaBlock(tbody, 'TOTAL', totales, 0.07, 'area-total', mesesKeys);
        
        // CORTE
        if (areas['1 CORTE']) {
            renderAreaBlock(tbody, 'CORTE', areas['1 CORTE'].meses, areas['1 CORTE'].objetivo, 'area-corte', mesesKeys);
        }
        
        // COSTURA
        if (areas['2 COSTURA']) {
            renderAreaBlock(tbody, 'COSTURA', areas['2 COSTURA'].meses, areas['2 COSTURA'].objetivo, 'area-costura', mesesKeys);
        }
        
        // COJINERIA
        if (areas['3 COJINERIA']) {
            renderAreaBlock(tbody, 'COJINERIA', areas['3 COJINERIA'].meses, areas['3 COJINERIA'].objetivo, 'area-cojineria', mesesKeys);
        }
        
        // TAPICERIA
        if (areas['4 TAPICERIA']) {
            renderAreaBlock(tbody, 'TAPICERIA', areas['4 TAPICERIA'].meses, areas['4 TAPICERIA'].objetivo, 'area-tapiceria', mesesKeys);
        }
        
        // CARPINTERIA
        if (areas['6 CARPINTERIA']) {
            renderAreaBlock(tbody, 'CARPINTERIA', areas['6 CARPINTERIA'].meses, areas['6 CARPINTERIA'].objetivo, 'area-carpinteria', mesesKeys);
        }
    }

    function renderAreaBlock(tbody, nombreArea, mesesData, objetivo, cssClass, mesesKeys) {
        // Fila de encabezado del área
        var rowHeader = '<tr class="' + cssClass + '"><td class="area-header">' + nombreArea + '</td>';
        for (var i = 0; i < 12; i++) {
            rowHeader += '<td></td>';
        }
        rowHeader += '</tr>';
        tbody.append(rowHeader);

        // Ordenes producidas
        var rowProd = '<tr><td class="row-label">Ordenes producidas</td>';
        for (var i = 0; i < 12; i++) {
            var mes = mesesKeys[i];
            var val = mesesData[mes] ? mesesData[mes].PRO_TCANT : 0;
            rowProd += '<td>' + fmtNum(val) + '</td>';
        }
        rowProd += '</tr>';
        tbody.append(rowProd);

        // Ordenes reprocesadas
        var rowReproc = '<tr><td class="row-label">Ordenes reprocesadas</td>';
        for (var i = 0; i < 12; i++) {
            var mes = mesesKeys[i];
            var val = mesesData[mes] ? mesesData[mes].REC_TCANT : 0;
            rowReproc += '<td>' + fmtNum(val) + '</td>';
        }
        rowReproc += '</tr>';
        tbody.append(rowReproc);

        // % de incidencia
        var rowPctInc = '<tr class="' + cssClass + ' pct-row"><td class="row-label">% de incidencia</td>';
        for (var i = 0; i < 12; i++) {
            var mes = mesesKeys[i];
            var prod = mesesData[mes] ? mesesData[mes].PRO_TCANT : 0;
            var reproc = mesesData[mes] ? mesesData[mes].REC_TCANT : 0;
            rowPctInc += '<td>' + fmtPct(prod, reproc) + '</td>';
        }
        rowPctInc += '</tr>';
        tbody.append(rowPctInc);

        // Valor sala producido
        var rowValProd = '<tr><td class="row-label">Valor sala producido</td>';
        for (var i = 0; i < 12; i++) {
            var mes = mesesKeys[i];
            var val = mesesData[mes] ? mesesData[mes].PRO_TVS : 0;
            rowValProd += '<td>' + fmtDec(val) + '</td>';
        }
        rowValProd += '</tr>';
        tbody.append(rowValProd);

        // Valor sala reprocesado
        var rowValReproc = '<tr><td class="row-label">Valor sala reprocesado</td>';
        for (var i = 0; i < 12; i++) {
            var mes = mesesKeys[i];
            var val = mesesData[mes] ? mesesData[mes].REC_TVS : 0;
            rowValReproc += '<td>' + fmtDec(val) + '</td>';
        }
        rowValReproc += '</tr>';
        tbody.append(rowValReproc);

        // % Valor sala
        var rowPctVal = '<tr class="' + cssClass + ' pct-row"><td class="row-label">% Valor sala</td>';
        for (var i = 0; i < 12; i++) {
            var mes = mesesKeys[i];
            var prodVs = mesesData[mes] ? mesesData[mes].PRO_TVS : 0;
            var reprocVs = mesesData[mes] ? mesesData[mes].REC_TVS : 0;
            rowPctVal += '<td>' + fmtPct(prodVs, reprocVs) + '</td>';
        }
        rowPctVal += '</tr>';
        tbody.append(rowPctVal);

        // Objetivo por Mes
        var rowObj = '<tr><td class="row-label">Objetivo por Mes</td>';
        for (var i = 0; i < 12; i++) {
            rowObj += '<td>' + (objetivo * 100).toFixed(0) + '%</td>';
        }
        rowObj += '</tr>';
        tbody.append(rowObj);

        // Fila vacía de separación
        var rowSep = '<tr style="height: 10px;"><td colspan="13"></td></tr>';
        tbody.append(rowSep);
    }

    // Función para mostrar el modal con detalle del mes
    function mostrarDetalleMes(mesKey) {
        if (!datosAreas || !datosTotales) {
            swal({ title: "Error", text: "No hay datos cargados", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        var mesNombre = mesesNombres[mesKey] || mesKey;
        $("#modalDetalleMesLabel").text('Indicadores de Calidad - ' + mesNombre);

        var html = '<table class="modal-tabla-detalle">';
        html += '<thead><tr><th style="width: 200px;">Área</th><th>Ord. Producidas</th><th>Ord. Reprocesadas</th><th>% Incidencia</th><th>Valor Producido</th><th>Valor Reprocesado</th><th>% Valor Sala</th><th>Objetivo</th></tr></thead>';
        html += '<tbody>';

        // TOTAL
        var totalData = datosTotales[mesKey] || { PRO_TCANT: 0, REC_TCANT: 0, PRO_TVS: 0, REC_TVS: 0 };
        html += '<tr class="area-total">';
        html += '<td class="area-header">TOTAL</td>';
        html += '<td>' + fmtNum(totalData.PRO_TCANT) + '</td>';
        html += '<td>' + fmtNum(totalData.REC_TCANT) + '</td>';
        html += '<td>' + fmtPct(totalData.PRO_TCANT, totalData.REC_TCANT) + '</td>';
        html += '<td>' + fmtDec(totalData.PRO_TVS) + '</td>';
        html += '<td>' + fmtDec(totalData.REC_TVS) + '</td>';
        html += '<td>' + fmtPct(totalData.PRO_TVS, totalData.REC_TVS) + '</td>';
        html += '<td>7%</td>';
        html += '</tr>';

        // Áreas
        var areasConfig = [
            { key: '1 CORTE', nombre: 'CORTE', objetivo: '7%', clase: 'area-corte' },
            { key: '2 COSTURA', nombre: 'COSTURA', objetivo: '7%', clase: 'area-costura' },
            { key: '3 COJINERIA', nombre: 'COJINERIA', objetivo: '6%', clase: 'area-cojineria' },
            { key: '4 TAPICERIA', nombre: 'TAPICERIA', objetivo: '8%', clase: 'area-tapiceria' },
            { key: '6 CARPINTERIA', nombre: 'CARPINTERIA', objetivo: '7%', clase: 'area-carpinteria' }
        ];

        areasConfig.forEach(function(area) {
            var areaData = datosAreas[area.key] && datosAreas[area.key].meses[mesKey] 
                ? datosAreas[area.key].meses[mesKey] 
                : { PRO_TCANT: 0, REC_TCANT: 0, PRO_TVS: 0, REC_TVS: 0 };
            
            html += '<tr class="' + area.clase + '">';
            html += '<td class="area-header">' + area.nombre + '</td>';
            html += '<td>' + fmtNum(areaData.PRO_TCANT) + '</td>';
            html += '<td>' + fmtNum(areaData.REC_TCANT) + '</td>';
            html += '<td>' + fmtPct(areaData.PRO_TCANT, areaData.REC_TCANT) + '</td>';
            html += '<td>' + fmtDec(areaData.PRO_TVS) + '</td>';
            html += '<td>' + fmtDec(areaData.REC_TVS) + '</td>';
            html += '<td>' + fmtPct(areaData.PRO_TVS, areaData.REC_TVS) + '</td>';
            html += '<td>' + area.objetivo + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';

        $("#modalDetalleMesContenido").html(html);
        $("#modalDetalleMes").modal('show');
    }

    // Evento click en encabezados de mes
    $(document).on('click', '.mes-header', function() {
        var mesKey = $(this).data('mes');
        if (mesKey) {
            mostrarDetalleMes(mesKey);
        }
    });

    function cargar() {
        var ano = $("#yearPicker").val();

        if (!ano) {
            swal({ title: "Error", text: "Seleccione un año", type: "error", confirmButtonText: "Aceptar" });
            return;
        }

        swal({ title: "Cargando...", text: "Por favor espere", type: "info", showConfirmButton: false, allowOutsideClick: false });

        $.ajax({
            url: routeapp + "/home/rep-07-indicadores/buscar",
            type: 'POST',
            data: { 
                _token: csrfToken, 
                ano: ano
            },
            success: function (resp) {
                swal.close();
                if (!resp.success) {
                    swal({ title: "Error", text: resp.msg || "Error al cargar", type: "error", confirmButtonText: "Aceptar" });
                    return;
                }

                $("#txtAno").text(resp.ano);
                $("#txtPeriodo").text('Del ' + resp.fechaIS + ' al ' + resp.fechaFS);
                
                // Actualizar encabezados de mes con data-mes
                var mesesKeys = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
                var mesesCortos = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                var headerRow = '<th style="width: 180px;">AREA / MES</th>';
                for (var i = 0; i < 12; i++) {
                    headerRow += '<th class="mes-header" data-mes="' + mesesKeys[i] + '" title="Click para ver detalle de ' + mesesNombres[mesesKeys[i]] + '">' + mesesCortos[i] + '</th>';
                }
                $("#tablaIndicadores thead tr").html(headerRow);
                
                renderTabla(resp.areas, resp.totales);
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
        
        if (!ano) {
            swal({ title: "Error", text: "Seleccione un año", type: "error", confirmButtonText: "Aceptar" });
            return;
        }
        
        var url = routeapp + "/home/rep-07-indicadores/pdf?ano=" + encodeURIComponent(ano);
        window.open(url, '_blank');
    });

    // Cargar datos automáticamente al iniciar
    cargar();
}
