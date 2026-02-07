<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>REP-07 Indicadores de Calidad por Mes</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 3px 4px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left; }
        .row-label { text-align: left; padding-left: 15px; }
        .area-header { font-weight: bold; text-align: left; padding-left: 5px; }
        
        /* Colores por área */
        .area-total { background-color: #47855e !important; color: white; }
        .area-corte { background-color: #ffcccc !important; }
        .area-costura { background-color: #cce5ff !important; }
        .area-cojineria { background-color: #d4edda !important; }
        .area-tapiceria { background-color: #ffe4c4 !important; }
        .area-carpinteria { background-color: #fff3cd !important; }
        
        .pct-row { font-weight: bold; }
    </style>
</head>
<body>
<?php
    $mesesKeys = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
    $mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    
    function fmtNum($v) {
        if ($v === null || $v === '' || floatval($v) == 0) return '-';
        return number_format($v, 0, '.', ',');
    }
    
    function fmtDec($v) {
        if ($v === null || $v === '' || floatval($v) == 0) return '-';
        return number_format($v, 2, '.', ',');
    }
    
    function fmtPct($producido, $reprocesado) {
        if (!$producido || $producido == 0) return '-';
        $pct = ($reprocesado / $producido) * 100;
        return number_format($pct, 2) . '%';
    }
    
    function renderAreaBlock($nombreArea, $mesesData, $objetivo, $cssClass, $mesesKeys) {
        $html = '';
        
        // Fila de encabezado del área
        $html .= '<tr class="' . $cssClass . '"><td class="area-header">' . $nombreArea . '</td>';
        for ($i = 0; $i < 12; $i++) {
            $html .= '<td></td>';
        }
        $html .= '</tr>';
        
        // Ordenes producidas
        $html .= '<tr><td class="row-label">Ordenes producidas</td>';
        for ($i = 0; $i < 12; $i++) {
            $mes = $mesesKeys[$i];
            $val = isset($mesesData[$mes]) ? $mesesData[$mes]['PRO_TCANT'] : 0;
            $html .= '<td>' . fmtNum($val) . '</td>';
        }
        $html .= '</tr>';
        
        // Ordenes reprocesadas
        $html .= '<tr><td class="row-label">Ordenes reprocesadas</td>';
        for ($i = 0; $i < 12; $i++) {
            $mes = $mesesKeys[$i];
            $val = isset($mesesData[$mes]) ? $mesesData[$mes]['REC_TCANT'] : 0;
            $html .= '<td>' . fmtNum($val) . '</td>';
        }
        $html .= '</tr>';
        
        // % de incidencia
        $html .= '<tr class="' . $cssClass . ' pct-row"><td class="row-label">% de incidencia</td>';
        for ($i = 0; $i < 12; $i++) {
            $mes = $mesesKeys[$i];
            $prod = isset($mesesData[$mes]) ? $mesesData[$mes]['PRO_TCANT'] : 0;
            $reproc = isset($mesesData[$mes]) ? $mesesData[$mes]['REC_TCANT'] : 0;
            $html .= '<td>' . fmtPct($prod, $reproc) . '</td>';
        }
        $html .= '</tr>';
        
        // Valor sala producido
        $html .= '<tr><td class="row-label">Valor sala producido</td>';
        for ($i = 0; $i < 12; $i++) {
            $mes = $mesesKeys[$i];
            $val = isset($mesesData[$mes]) ? $mesesData[$mes]['PRO_TVS'] : 0;
            $html .= '<td>' . fmtDec($val) . '</td>';
        }
        $html .= '</tr>';
        
        // Valor sala reprocesado
        $html .= '<tr><td class="row-label">Valor sala reprocesado</td>';
        for ($i = 0; $i < 12; $i++) {
            $mes = $mesesKeys[$i];
            $val = isset($mesesData[$mes]) ? $mesesData[$mes]['REC_TVS'] : 0;
            $html .= '<td>' . fmtDec($val) . '</td>';
        }
        $html .= '</tr>';
        
        // % Valor sala
        $html .= '<tr class="' . $cssClass . ' pct-row"><td class="row-label">% Valor sala</td>';
        for ($i = 0; $i < 12; $i++) {
            $mes = $mesesKeys[$i];
            $prodVs = isset($mesesData[$mes]) ? $mesesData[$mes]['PRO_TVS'] : 0;
            $reprocVs = isset($mesesData[$mes]) ? $mesesData[$mes]['REC_TVS'] : 0;
            $html .= '<td>' . fmtPct($prodVs, $reprocVs) . '</td>';
        }
        $html .= '</tr>';
        
        // Objetivo por Mes
        $html .= '<tr><td class="row-label">Objetivo por Mes</td>';
        for ($i = 0; $i < 12; $i++) {
            $html .= '<td>' . ($objetivo * 100) . '%</td>';
        }
        $html .= '</tr>';
        
        return $html;
    }
?>

    <table>
        <thead>
            <tr>
                <th style="width: 140px;">AREA / MES</th>
                @foreach($mesesNombres as $mesNombre)
                    <th>{{ $mesNombre }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <?php
                // TOTAL
                echo renderAreaBlock('TOTAL', $totales, 0.07, 'area-total', $mesesKeys);
                
                // CORTE
                if (isset($areas['1 CORTE'])) {
                    echo renderAreaBlock('CORTE', $areas['1 CORTE']['meses'], $areas['1 CORTE']['objetivo'], 'area-corte', $mesesKeys);
                }
                
                // COSTURA
                if (isset($areas['2 COSTURA'])) {
                    echo renderAreaBlock('COSTURA', $areas['2 COSTURA']['meses'], $areas['2 COSTURA']['objetivo'], 'area-costura', $mesesKeys);
                }
                
                // COJINERIA
                if (isset($areas['3 COJINERIA'])) {
                    echo renderAreaBlock('COJINERIA', $areas['3 COJINERIA']['meses'], $areas['3 COJINERIA']['objetivo'], 'area-cojineria', $mesesKeys);
                }
                
                // TAPICERIA
                if (isset($areas['4 TAPICERIA'])) {
                    echo renderAreaBlock('TAPICERIA', $areas['4 TAPICERIA']['meses'], $areas['4 TAPICERIA']['objetivo'], 'area-tapiceria', $mesesKeys);
                }
                
                // CARPINTERIA
                if (isset($areas['6 CARPINTERIA'])) {
                    echo renderAreaBlock('CARPINTERIA', $areas['6 CARPINTERIA']['meses'], $areas['6 CARPINTERIA']['objetivo'], 'area-carpinteria', $mesesKeys);
                }
            ?>
        </tbody>
    </table>

</body>
</html>
