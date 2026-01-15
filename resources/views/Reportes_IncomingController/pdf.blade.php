<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>R-140 INCOMING - Inspección de Materiales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
        }
        
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 8px;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-right {
            text-align: right;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>FECHA REVISION</th>
                <th>PROVEEDOR</th>
                <th>CODIGO</th>
                <th>MATERIAL</th>
                <th>UDM</th>
                <th>RECIBIDO</th>
                <th>REVISADA</th>
                <th>ACEPTADA</th>
                <th>RECHAZADA</th>
                <th>POR CIENTO</th>
                <th>INSPECTOR</th>
                <th>FACTURA</th>
                <th>MOTIVO RECHAZO</th>
                <th>GRUPO PLANEACION</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inspecciones as $index => $inspeccion)
                <tr>
                    <td>{{ $inspeccion->ID ?? '' }}</td>
                    <td>{{ $inspeccion->FE_REV ? date('Y/m/d', strtotime($inspeccion->FE_REV)) : '' }}</td>
                    <td class="text-left">{{ $inspeccion->PROVEEDOR ?? 'N/A' }}</td>
                    <td>{{ $inspeccion->CODIGO ?? '' }}</td>
                    <td class="text-left">{{ $inspeccion->MATERIAL ?? '' }}</td>
                    <td>{{ $inspeccion->UDM ?? '' }}</td>
                    <td class="text-right">{{ number_format($inspeccion->RECIBIDO ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($inspeccion->REVISADA ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($inspeccion->ACEPTADA ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($inspeccion->RECHAZADA ?? 0, 2) }}</td>
                    <td class="text-right">{{ $inspeccion->PORC !== null ? number_format($inspeccion->PORC, 2) . '%' : '0.00%' }}</td>
                    <td>{{ $inspeccion->INSPECTOR ?? '' }}</td>
                    <td>{{ $inspeccion->FACTURA ?? '' }}</td>
                    <td class="text-left">{{ $inspeccion->MOT_RECHAZO ?? '' }}</td>
                    <td>{{ $inspeccion->GRUPPLAN ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
