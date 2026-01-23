<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>R-143 Confiabilidad de Proveedores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .promedio-cell {
            background-color: #d4edda;
            font-weight: bold;
        }
        
        .meta-cell {
            background-color: #fff3cd;
            font-weight: bold;
        }
        
        .grupo-header {
            background-color: #e3f2fd;
            font-weight: bold;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-right {
            text-align: right;
        }
        
        h4 {
            margin-top: 15px;
            margin-bottom: 10px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    
    <!-- Tabla Promedio del Año -->
    <h4>Promedio del año</h4>
    <table>
        <thead>
            <tr>
                <th>Entradas</th>
                <th>Promedio del año</th>
                <th>Enero</th>
                <th>Febrero</th>
                <th>Marzo</th>
                <th>Abril</th>
                <th>Mayo</th>
                <th>Junio</th>
                <th>Julio</th>
                <th>Agosto</th>
                <th>Septiembre</th>
                <th>Octubre</th>
                <th>Noviembre</th>
                <th>Diciembre</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $promedioAnualObj ? number_format($promedioAnualObj->ENTRADAS, 0) : '0' }}</td>
                <td class="promedio-cell">
                    @if($promedioAnualObj && isset($promedioAnualObj->PROMEDIO))
                        {{ number_format($promedioAnualObj->PROMEDIO * 100, 2) }}%
                    @else
                        -
                    @endif
                </td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->ENERO > 0 ? number_format($promedioAnualObj->ENERO * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->FEBRERO > 0 ? number_format($promedioAnualObj->FEBRERO * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->MARZO > 0 ? number_format($promedioAnualObj->MARZO * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->ABRIL > 0 ? number_format($promedioAnualObj->ABRIL * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->MAYO > 0 ? number_format($promedioAnualObj->MAYO * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->JUNIO > 0 ? number_format($promedioAnualObj->JUNIO * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->JULIO > 0 ? number_format($promedioAnualObj->JULIO * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->AGOSTO > 0 ? number_format($promedioAnualObj->AGOSTO * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->SEPTIEMBRE > 0 ? number_format($promedioAnualObj->SEPTIEMBRE * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->OCTUBRE > 0 ? number_format($promedioAnualObj->OCTUBRE * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->NOVIEMBRE > 0 ? number_format($promedioAnualObj->NOVIEMBRE * 100, 2) . '%' : '-' }}</td>
                <td>{{ $promedioAnualObj && $promedioAnualObj->DICIEMBRE > 0 ? number_format($promedioAnualObj->DICIEMBRE * 100, 2) . '%' : '-' }}</td>
            </tr>
            <tr>
                <td>-</td>
                <td class="meta-cell">META GENERAL</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
                <td class="meta-cell">95.00%</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Tabla de FAMILIAS -->
    <h4>FAMILIAS</h4>
    <table>
        <thead>
            <tr>
                <th>Entradas</th>
                <th>FAMILIA</th>
                <th>PROME.</th>
                <th>Enero</th>
                <th>Febrero</th>
                <th>Marzo</th>
                <th>Abril</th>
                <th>Mayo</th>
                <th>Junio</th>
                <th>Julio</th>
                <th>Agosto</th>
                <th>Septiembre</th>
                <th>Octubre</th>
                <th>Noviembre</th>
                <th>Diciembre</th>
            </tr>
        </thead>
        <tbody>
            @if($familias && count($familias) > 0)
                @foreach($familias as $familia)
                    @php
                        $meses = [
                            $familia->ENERO, $familia->FEBRERO, $familia->MARZO,
                            $familia->ABRIL, $familia->MAYO, $familia->JUNIO,
                            $familia->JULIO, $familia->AGOSTO, $familia->SEPTIEMBRE,
                            $familia->OCTUBRE, $familia->NOVIEMBRE, $familia->DICIEMBRE
                        ];
                        $mesesConDatos = array_filter($meses, function($v) { return $v > 0; });
                        $promedio = count($mesesConDatos) > 0 ? (array_sum($mesesConDatos) / count($mesesConDatos)) : 0;
                    @endphp
                    <tr>
                        <td>{{ number_format($familia->ENTRADAS, 0) }}</td>
                        <td class="text-left">{{ $familia->GRUPO ?? 'SIN GRUPO' }}</td>
                        <td class="promedio-cell">{{ number_format($promedio * 100, 2) }}%</td>
                        <td>{{ $familia->ENERO > 0 ? number_format($familia->ENERO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->FEBRERO > 0 ? number_format($familia->FEBRERO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->MARZO > 0 ? number_format($familia->MARZO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->ABRIL > 0 ? number_format($familia->ABRIL * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->MAYO > 0 ? number_format($familia->MAYO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->JUNIO > 0 ? number_format($familia->JUNIO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->JULIO > 0 ? number_format($familia->JULIO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->AGOSTO > 0 ? number_format($familia->AGOSTO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->SEPTIEMBRE > 0 ? number_format($familia->SEPTIEMBRE * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->OCTUBRE > 0 ? number_format($familia->OCTUBRE * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->NOVIEMBRE > 0 ? number_format($familia->NOVIEMBRE * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $familia->DICIEMBRE > 0 ? number_format($familia->DICIEMBRE * 100, 2) . '%' : '-' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="15" class="text-center">No hay datos disponibles</td>
                </tr>
            @endif
        </tbody>
    </table>
    
    <!-- Tabla de Proveedores -->
    <h4>Proveedores</h4>
    <table>
        <thead>
            <tr>
                <th>Entradas</th>
                <th>Código del Prov.</th>
                <th>Nombre del Proveedor</th>
                <th>% Confiabilidad Proveedor</th>
                <th>Enero</th>
                <th>Febrero</th>
                <th>Marzo</th>
                <th>Abril</th>
                <th>Mayo</th>
                <th>Junio</th>
                <th>Julio</th>
                <th>Agosto</th>
                <th>Septiembre</th>
                <th>Octubre</th>
                <th>Noviembre</th>
                <th>Diciembre</th>
            </tr>
        </thead>
        <tbody>
            @if($proveedores && count($proveedores) > 0)
                @php
                    $grupoActual = '';
                @endphp
                @foreach($proveedores as $proveedor)
                    @if($proveedor->GRUPO != $grupoActual)
                        @php
                            $grupoActual = $proveedor->GRUPO;
                        @endphp
                        <tr class="grupo-header">
                            <td colspan="16" class="text-left"><strong>{{ $proveedor->GRUPO ?? 'SIN GRUPO' }}</strong></td>
                        </tr>
                    @endif
                    <tr>
                        <td>{{ number_format($proveedor->ENTRADAS, 0) }}</td>
                        <td>{{ $proveedor->COD_PRO ?? '-' }}</td>
                        <td class="text-left">{{ $proveedor->PROVEEDOR ?? '-' }}</td>
                        <td class="promedio-cell">{{ number_format(($proveedor->PROMEDIO ?? 0) * 100, 2) }}%</td>
                        <td>{{ $proveedor->ENERO > 0 ? number_format($proveedor->ENERO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->FEBRERO > 0 ? number_format($proveedor->FEBRERO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->MARZO > 0 ? number_format($proveedor->MARZO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->ABRIL > 0 ? number_format($proveedor->ABRIL * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->MAYO > 0 ? number_format($proveedor->MAYO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->JUNIO > 0 ? number_format($proveedor->JUNIO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->JULIO > 0 ? number_format($proveedor->JULIO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->AGOSTO > 0 ? number_format($proveedor->AGOSTO * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->SEPTIEMBRE > 0 ? number_format($proveedor->SEPTIEMBRE * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->OCTUBRE > 0 ? number_format($proveedor->OCTUBRE * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->NOVIEMBRE > 0 ? number_format($proveedor->NOVIEMBRE * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $proveedor->DICIEMBRE > 0 ? number_format($proveedor->DICIEMBRE * 100, 2) . '%' : '-' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="16" class="text-center">No hay datos disponibles</td>
                </tr>
            @endif
        </tbody>
    </table>
    
</body>
</html>
