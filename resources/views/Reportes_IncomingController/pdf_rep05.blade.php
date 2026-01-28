<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>REP-05 Historial por Proveedor</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left; }
        h4 { margin-top: 15px; margin-bottom: 10px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>

    <h4>Calificación por mes</h4>
    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th>Calificación</th>
            </tr>
        </thead>
        <tbody>
            @if($resumenMes && count($resumenMes) > 0)
                @foreach($resumenMes as $r)
                    @php
                        $mm = str_pad((string)$r->MES, 2, '0', STR_PAD_LEFT);
                        $mesMap = [
                            '01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril',
                            '05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto',
                            '09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'
                        ];
                        $mesNombre = $mesMap[$mm] ?? $mm;
                    @endphp
                    <tr>
                        <td class="text-left">{{ $mesNombre }}</td>
                        <td>
                            @if($r->CALIFA && $r->CALIFA > 0)
                                {{ number_format($r->CALIFA * 100, 2) }}%
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="2">Sin datos</td></tr>
            @endif
        </tbody>
    </table>

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

