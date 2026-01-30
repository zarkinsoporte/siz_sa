<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>REP-06 Historial por Material</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left; }
        h4 { margin-top: 15px; margin-bottom: 10px; font-size: 11px; font-weight: bold; }
        .material-info { font-size: 13px; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="material-info">
        Material: {{$codMaterial}} @if($materialNombre) - {{$materialNombre}} @endif
    </div>
    <div class="material-info" style="font-size: 12px;">
        UDM: {{$udm}}
    </div>

    <h4>Detalle por Proveedor</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Código Proveedor</th>
                <th>Proveedor</th>
                <th>Aceptado</th>
                <th>Calificación</th>
            </tr>
        </thead>
        <tbody>
            @if($datos && count($datos) > 0)
                @foreach($datos as $index => $d)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $d->COD_PROV }}</td>
                        <td class="text-left">{{ $d->PROVEEDOR }}</td>
                        <td>{{ number_format($d->ACEPTADO, 2) }}</td>
                        <td>
                            @if($d->CALIFA !== null && $d->CALIFA > 0)
                                {{ number_format($d->CALIFA * 100, 2) }}%
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="5">Sin datos</td></tr>
            @endif
        </tbody>
    </table>

</body>
</html>
