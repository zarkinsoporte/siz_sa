<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>REP-05 Historial por Proveedor</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left; }
        h4 { margin-top: 15px; margin-bottom: 10px; font-size: 11px; font-weight: bold; }
        .resumen-table td { font-size: 12px; padding: 6px; }
        .resumen-table td strong { font-size: 13px; }
        .proveedor-info { font-size: 13px; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="proveedor-info">
        Proveedor: {{$codProv}} @if($proveedorNombre) - {{$proveedorNombre}} @endif
    </div>

    <div style="display: table; width: 100%; margin-bottom: 15px;">
        <div style="display: table-cell; width: 50%; vertical-align: top; padding-right: 10px;">
            <h4>Distribución Aceptado / Rechazado</h4>
            <?php
                // Calcular porcentajes (solo Aceptado / Rechazado en la gráfica)
                $porcAceptado = $totalRecibido > 0 ? ($totalAceptado / $totalRecibido) * 100 : 0;
                $porcRechazado = $totalRecibido > 0 ? ($totalRechazado / $totalRecibido) * 100 : 0;
                
                // Normalizar para que la gráfica de 2 segmentos cierre al 100%
                $sumaPorcentajes = $porcAceptado + $porcRechazado;
                if ($sumaPorcentajes > 0) {
                    $porcAceptado = ($porcAceptado / $sumaPorcentajes) * 100;
                    $porcRechazado = ($porcRechazado / $sumaPorcentajes) * 100;
                }
                
                // Calcular ángulos para la gráfica de pastel (en grados)
                $anguloAceptado = ($porcAceptado / 100) * 360;
                $anguloRechazado = ($porcRechazado / 100) * 360;
                
                // Centro y radio del círculo
                $centroX = 150;
                $centroY = 150;
                $radio = 100;
                
                // Colores
                $colorAceptado = '#28a745'; // Verde
                $colorRechazado = '#dc3545'; // Rojo
                
                // Función helper para calcular coordenadas en el círculo
                function calcularCoordenadas($centroX, $centroY, $radio, $angulo) {
                    $x = $centroX + $radio * cos(deg2rad($angulo));
                    $y = $centroY + $radio * sin(deg2rad($angulo));
                    return ['x' => $x, 'y' => $y];
                }
            ?>
            <svg width="300" height="300" style="display: block; margin: 0 auto;">
                <!-- Círculo de Aceptado -->
                <?php if ($porcAceptado > 0): ?>
                    <?php
                        // Empezar desde arriba (-90 grados)
                        $startAngle = -90;
                        $endAngle = $startAngle + $anguloAceptado;
                        
                        $startCoords = calcularCoordenadas($centroX, $centroY, $radio, $startAngle);
                        $endCoords = calcularCoordenadas($centroX, $centroY, $radio, $endAngle);
                        
                        // Determinar si es un arco grande (>180 grados)
                        $largeArc = $anguloAceptado > 180 ? 1 : 0;
                        
                        // Si el ángulo es 360, dibujar círculo completo
                        if ($anguloAceptado >= 360) {
                            echo '<circle cx="' . $centroX . '" cy="' . $centroY . '" r="' . $radio . '" fill="' . $colorAceptado . '" stroke="#fff" stroke-width="2"/>';
                        } else {
                    ?>
                    <path d="M <?php echo $centroX; ?> <?php echo $centroY; ?> 
                            L <?php echo number_format($startCoords['x'], 4); ?> <?php echo number_format($startCoords['y'], 4); ?> 
                            A <?php echo $radio; ?> <?php echo $radio; ?> 0 <?php echo $largeArc; ?> 1 <?php echo number_format($endCoords['x'], 4); ?> <?php echo number_format($endCoords['y'], 4); ?> 
                            Z" 
                          fill="<?php echo $colorAceptado; ?>" 
                          stroke="#fff" 
                          stroke-width="2"/>
                    <?php } ?>
                <?php endif; ?>
                
                <!-- Círculo de Rechazado -->
                <?php if ($porcRechazado > 0): ?>
                    <?php
                        // Continuar desde donde terminó el aceptado
                        $startAngle = -90 + $anguloAceptado;
                        $endAngle = $startAngle + $anguloRechazado;
                        
                        $startCoords = calcularCoordenadas($centroX, $centroY, $radio, $startAngle);
                        $endCoords = calcularCoordenadas($centroX, $centroY, $radio, $endAngle);
                        
                        // Determinar si es un arco grande (>180 grados)
                        $largeArc = $anguloRechazado > 180 ? 1 : 0;
                        
                        // Si el ángulo es 360, dibujar círculo completo
                        if ($anguloRechazado >= 360) {
                            echo '<circle cx="' . $centroX . '" cy="' . $centroY . '" r="' . $radio . '" fill="' . $colorRechazado . '" stroke="#fff" stroke-width="2"/>';
                        } else {
                    ?>
                    <path d="M <?php echo $centroX; ?> <?php echo $centroY; ?> 
                            L <?php echo number_format($startCoords['x'], 4); ?> <?php echo number_format($startCoords['y'], 4); ?> 
                            A <?php echo $radio; ?> <?php echo $radio; ?> 0 <?php echo $largeArc; ?> 1 <?php echo number_format($endCoords['x'], 4); ?> <?php echo number_format($endCoords['y'], 4); ?> 
                            Z" 
                          fill="<?php echo $colorRechazado; ?>" 
                          stroke="#fff" 
                          stroke-width="2"/>
                    <?php } ?>
                <?php endif; ?>
                
                <!-- Texto en el centro -->
                <text x="<?php echo $centroX; ?>" y="<?php echo $centroY - 10; ?>" text-anchor="middle" font-size="14" font-weight="bold">Total</text>
                <text x="<?php echo $centroX; ?>" y="<?php echo $centroY + 10; ?>" text-anchor="middle" font-size="12"><?php echo number_format($totalRecibido, 2); ?></text>
            </svg>
            
            <!-- Leyenda -->
            <div style="text-align: center; margin-top: 10px;">
                <div style="display: inline-block; margin: 0 10px;">
                    <span style="display: inline-block; width: 20px; height: 20px; background-color: <?php echo $colorAceptado; ?>; vertical-align: middle; margin-right: 5px;"></span>
                    <span style="font-size: 11px;">Aceptado: <?php echo number_format($porcAceptado, 2); ?>%</span>
                </div>
                <div style="display: inline-block; margin: 0 10px;">
                    <span style="display: inline-block; width: 20px; height: 20px; background-color: <?php echo $colorRechazado; ?>; vertical-align: middle; margin-right: 5px;"></span>
                    <span style="font-size: 11px;">Rechazado: <?php echo number_format($porcRechazado, 2); ?>%</span>
                </div>
            </div>
        </div>
        
        <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 10px;">
            <h4>Resumen</h4>
            <table class="resumen-table">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-left"><strong>Total Recibido</strong></td>
                <td>{{ number_format($totalRecibido, 2) }}</td>
                <td>100.00%</td>
            </tr>
            <tr>
                <td class="text-left"><strong>Total Aceptado</strong></td>
                <td>{{ number_format($totalAceptado, 2) }}</td>
                <td>{{ $totalRecibido > 0 ? number_format(($totalAceptado / $totalRecibido) * 100, 2) : '0.00' }}%</td>
            </tr>
            <tr>
                <td class="text-left"><strong>Total Rechazado</strong></td>
                <td>{{ number_format($totalRechazado, 2) }}</td>
                <td>{{ $totalRecibido > 0 ? number_format(($totalRechazado / $totalRecibido) * 100, 2) : '0.00' }}%</td>
            </tr>
            <tr>
                <td class="text-left"><strong>Por Revisar</strong></td>
                <td>{{ number_format($totalPorRevisar, 2) }}</td>
                <td>{{ $totalRecibido > 0 ? number_format(($totalPorRevisar / $totalRecibido) * 100, 2) : '0.00' }}%</td>
            </tr>
        </tbody>
            </table>
        </div>
    </div>

    <h4>Detalle</h4>
    <table>
        <thead>
            <tr>
                <th>Rechazo</th>
                <th>NE</th>
                <th>Código Material</th>
                <th>Material</th>
                <th>UDM</th>
                <th>Recibido</th>
                <th>Rechazada</th>
            </tr>
        </thead>
        <tbody>
            @if($detalle && count($detalle) > 0)
                @foreach($detalle as $d)
                    <tr>
                        <td>{{ $d->RECHAZO }}</td>
                        <td>{{ $d->NE }}</td>
                        <td>{{ $d->COD_MAT }}</td>
                        <td class="text-left">{{ $d->MATERIAL }}</td>
                        <td>{{ $d->UDM }}</td>
                        <td>{{ number_format($d->RECIBIDO, 2) }}</td>
                        <td>{{ number_format($d->RECHAZADA, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="7">Sin datos</td></tr>
            @endif
        </tbody>
    </table>

</body>
</html>

