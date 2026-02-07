<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 5px;
        }
        
        .estacion-block {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .estacion-header {
            padding: 6px 12px;
            color: white;
            font-weight: bold;
            font-size: 12px;
            border-radius: 4px 4px 0 0;
            display: table;
            width: 100%;
        }
        
        .estacion-header .nombre { display: table-cell; text-align: left; }
        .estacion-header .objetivo { display: table-cell; text-align: right; }
        
        .header-corte { background-color: #c0392b; }
        .header-costura { background-color: #2980b9; }
        .header-cojineria { background-color: #27ae60; }
        .header-tapiceria { background-color: #e67e22; }
        
        .tabla-emp {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        
        .tabla-emp th {
            background-color: #f1f3f5;
            border: 1px solid #adb5bd;
            padding: 4px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }
        
        .tabla-emp td {
            border: 1px solid #dee2e6;
            padding: 3px 6px;
            text-align: center;
            font-size: 12px;
        }
        
        .tabla-emp tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .tabla-emp td.text-left { text-align: left; }
        
        .sem-cumple {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
        
        .sem-no-cumple {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }
        
        .sem-sin-datos {
            background-color: #f8f9fa;
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <?php
        $headerClasses = [
            'CORTE' => 'header-corte',
            'COSTURA' => 'header-costura',
            'COJINERIA' => 'header-cojineria',
            'TAPICERIA' => 'header-tapiceria',
        ];
        $areasOrden = ['CORTE', 'COSTURA', 'COJINERIA', 'TAPICERIA'];
    ?>

    @foreach($areasOrden as $areaNombre)
        <?php
            $estacion = isset($estaciones[$areaNombre]) ? $estaciones[$areaNombre] : null;
            if (!$estacion) continue;
            $headerClass = isset($headerClasses[$areaNombre]) ? $headerClasses[$areaNombre] : 'header-corte';
            $metaPct = $estacion['metaPct'];
            $metaNum = $estacion['meta'];
        ?>

        <div class="estacion-block">
            <div class="estacion-header {{ $headerClass }}">
                <span class="nombre">{{ $areaNombre }}</span>
                <span class="objetivo">Objetivo: {{ $metaPct }}%</span>
            </div>
            
            <table class="tabla-emp">
                <thead>
                    <tr>
                        <th style="width: 60px;">Código</th>
                        <th style="width: 180px; text-align: left;">Nombre del Empleado</th>
                        <th style="width: 130px; text-align: left;">Puesto</th>
                        <th style="width: 70px;">Bono</th>
                        <th style="width: 50px;">Meta</th>
                        @foreach($semanasISO as $semISO)
                            <th style="width: 60px;">SEM {{ $semISO }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if(count($estacion['empleados']) > 0)
                        @foreach($estacion['empleados'] as $emp)
                            <tr>
                                <td>{{ $emp['num_nom'] }}</td>
                                <td class="text-left">{{ $emp['nombre'] }}</td>
                                <td class="text-left">{{ $emp['opera'] }}</td>
                                <td>
                                    <?php
                                        $bono = $emp['bono'];
                                        if ($bono && is_numeric($bono)) {
                                            echo '$ ' . number_format(floatval($bono), 2);
                                        } else {
                                            echo $bono ?: '-';
                                        }
                                    ?>
                                </td>
                                <td><strong>{{ $metaPct }}%</strong></td>
                                @foreach($semanasISO as $semISO)
                                    <?php
                                        $val = isset($emp['semanas'][$semISO]) ? $emp['semanas'][$semISO] : null;
                                        if ($val === null) {
                                            $cls = 'sem-sin-datos';
                                            $txt = '-';
                                        } elseif ($val == 0) {
                                            $cls = 'sem-cumple';
                                            $txt = '0%';
                                        } else {
                                            $cls = ($val <= $metaNum) ? 'sem-cumple' : 'sem-no-cumple';
                                            $txt = round($val, 0) . '%';
                                        }
                                    ?>
                                    <td class="{{ $cls }}">{{ $txt }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ 5 + $nSemanas }}" style="text-align: center; padding: 10px; color: #999;">Sin datos para esta estación</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
