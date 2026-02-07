<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>REP-08 Inspección Top 3 Defectos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8px; }
        
        .container-meses {
            display: table;
            width: 100%;
        }
        
        .row-meses {
            display: table-row;
        }
        
        .bloque-mes {
            display: table-cell;
            width: 25%;
            padding: 3px;
            vertical-align: top;
        }
        
        .mes-inner {
            border: 1px solid #000;
        }
        
        .mes-header {
            background-color: #343a40;
            color: white;
            padding: 4px 6px;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }
        
        .mes-content {
            padding: 4px;
        }
        
        .area-block {
            margin-bottom: 4px;
            border: 1px solid #ccc;
        }
        
        .area-header {
            padding: 3px 5px;
            font-weight: bold;
            font-size: 8px;
        }
        
        .area-corte { background-color: #ffcccc; }
        .area-costura { background-color: #cce5ff; }
        .area-cojineria { background-color: #d4edda; }
        .area-tapiceria { background-color: #ffe4c4; }
        .area-carpinteria { background-color: #fff3cd; }
        
        .defecto-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .defecto-table td {
            padding: 2px 4px;
            border-top: 1px solid #eee;
            font-size: 7px;
        }
        
        .defecto-nombre {
            width: 80%;
        }
        
        .defecto-conteo {
            width: 20%;
            text-align: right;
            font-weight: bold;
        }
        
        .no-data {
            color: #999;
            font-style: italic;
            padding: 2px 4px;
            font-size: 7px;
        }
    </style>
</head>
<body>
<?php
    $mesesOrden = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
    $areasOrden = ['CORTE', 'COSTURA', 'COJINERIA', 'TAPICERIA', 'CARPINTERIA'];
    $areaClasses = [
        'CORTE' => 'area-corte',
        'COSTURA' => 'area-costura',
        'COJINERIA' => 'area-cojineria',
        'TAPICERIA' => 'area-tapiceria',
        'CARPINTERIA' => 'area-carpinteria'
    ];
?>

<div class="container-meses">
    <?php 
    $mesCount = 0;
    foreach ($mesesOrden as $mesKey): 
        if (!isset($datos[$mesKey])) continue;
        $mesData = $datos[$mesKey];
        
        if ($mesCount % 4 == 0) {
            if ($mesCount > 0) echo '</div>';
            echo '<div class="row-meses">';
        }
        $mesCount++;
    ?>
    <div class="bloque-mes">
        <div class="mes-inner">
            <div class="mes-header"><?php echo $mesData['nombre']; ?></div>
            <div class="mes-content">
                <?php foreach ($areasOrden as $areaNombre): 
                    $areaClass = isset($areaClasses[$areaNombre]) ? $areaClasses[$areaNombre] : '';
                    $defectos = isset($mesData['areas'][$areaNombre]) ? $mesData['areas'][$areaNombre] : [];
                ?>
                <div class="area-block">
                    <div class="area-header <?php echo $areaClass; ?>"><?php echo $areaNombre; ?></div>
                    <?php if (count($defectos) > 0): ?>
                    <table class="defecto-table">
                        <?php foreach ($defectos as $idx => $defecto): ?>
                        <tr>
                            <td class="defecto-nombre"><?php echo ($idx + 1) . '. ' . $defecto['defectivo']; ?></td>
                            <td class="defecto-conteo"><?php echo $defecto['conteo']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php else: ?>
                    <div class="no-data">Sin datos</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if ($mesCount > 0) echo '</div>'; ?>
</div>

</body>
</html>
