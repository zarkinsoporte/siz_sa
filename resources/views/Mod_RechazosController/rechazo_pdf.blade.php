<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid rgb(212, 200, 201);
            padding-bottom: 10px;
        }
        
        .header h1 {
            color:rgb(201, 196, 197);
            font-size: 18px;
            margin: 0;
            font-weight: bold;
        }
        
        .header h2 {
            color: #666;
            font-size: 14px;
            margin: 5px 0 0 0;
            font-weight: normal;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: bold;
            font-size: 13px;
            padding: 8px 12px;
            border-left: 4px solid rgb(78, 75, 75);
            margin-bottom: 10px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .info-table th {
            background-color: #e9ecef;
            color: #495057;
            font-weight: bold;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-size: 11px;
            width: 30%;
        }
        
        .info-table td {
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            font-size: 11px;
        }
        
        .info-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .material-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .checklist-item {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .checklist-title {
            background-color:rgb(161, 159, 160);
            color: white;
            font-weight: bold;
            padding: 6px 10px;
            font-size: 12px;
        }
        
        .checklist-observation {
            background-color: #f8f9fa;
            padding: 8px 10px;
            border-left: 3px solid #dc3545;
            margin: 5px 0;
            font-size: 11px;
        }
        
        .checklist-images {
            margin-top: 8px;
        }
        
        .image-item {
            display: inline-block;
            margin: 5px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .image-placeholder {
            width: 80px;
            height: 60px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3px;
        }
        
        .observations {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .recipients {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .recipients ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        
        .recipients li {
            margin: 2px 0;
            font-size: 11px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .urgent {
            color: #dc3545;
            font-weight: bold;
        }
        
        .no-data {
            color: #999;
            font-style: italic;
        }
    </style>
</head>   
<body>
    
    <div class="header">
        <h1> REPORTE DE RECHAZO DE MATERIAL</h1>
        <h2>Control de Calidad - Sistema SIZ</h2>
        <p>Fecha de Generación: {{ date("d/m/Y H:i:s") }}</p>
    </div>

    <!-- Datos del Proveedor -->
    <div class="section">
        <div class="section-title"> DATOS DEL PROVEEDOR</div>
        <table class="info-table">
            <tr>
                <th>Proveedor:</th>
                <td>{{ $proveedor_nombre ?? "N/A" }}</td>
            </tr>
            <tr>
                <th>Código:</th>
                <td>{{ $proveedor_codigo ?? "N/A" }}</td>
            </tr>
            
            <tr>
                <th>Nota de Entrada:</th>
                <td>{{ $inspeccion->INC_docNum ?? "N/A" }}</td>
            </tr>
            <tr>
                <th>Número de Factura:</th>
                <td>{{ $inspeccion->INC_numFactura ?? "N/A" }}</td>
            </tr>
        </table>
    </div>

    <!-- Información del Material Rechazado -->
    <div class="section">
        <div class="section-title"> MATERIAL RECHAZADO</div>
        <div class="material-info">
            <table class="info-table">
                <tr>
                    <th>Código de Material:</th>
                    <td class="urgent">{{ $inspeccion->INC_codMaterial ?? "N/A" }}</td>
                </tr>
                <tr>
                    <th>Descripción:</th>
                    <td>{{ $inspeccion->INC_nomMaterial ?? "N/A" }}</td>
                </tr>
                <tr>
                    <th>Cantidad Rechazada:</th>
                    <td class="urgent">{{ number_format($inspeccion->INC_cantRechazada ?? 0, 2) }} {{ $inspeccion->INC_unidadMedida ?? "N/A" }}</td>
                </tr>
                <tr>
                    <th>Cantidad Total Recibida:</th>
                    <td>{{ number_format($inspeccion->INC_cantRecibida ?? 0, 2) }} {{ $inspeccion->INC_unidadMedida ?? "N/A" }}</td>
                </tr>
               
                <tr>
                    <th>Fecha de Inspección:</th>
                    <td>{{ $inspeccion->INC_fechaInspeccion ? date("d/m/Y H:i:s", strtotime($inspeccion->INC_fechaInspeccion)) : "N/A" }}</td>
                </tr>
                <tr>
                    <th>Inspector:</th>
                    <td>{{ $inspeccion->INC_nomInspector ?? "N/A" }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Rubros del Checklist que No Cumplen -->
    <div class="section">
       
        @if(isset($checklistNoCumple) && count($checklistNoCumple) > 0)
            @foreach($checklistNoCumple as $item)
                <div class="checklist-item">
                    <div class="checklist-title">
                        {{ $item["descripcion"] }}
                    </div>
                    
                    @if(!empty($item["observacion"]))
                        <div class="checklist-observation">
                            <strong>Observación:</strong> {{ $item["observacion"] }}
                        </div>
                    @endif
                    
                    @if(!empty($item["cantidad"]))
                        <div class="checklist-observation">
                            <strong>Cantidad Afectada:</strong> {{ $item["cantidad"] }}
                        </div>
                    @endif
                    
                    @if(isset($item["imagenes"]) && count($item["imagenes"]) > 0)
                        <div class="checklist-images">
                            <strong>Evidencias Fotográficas:</strong>
                            <div style="margin-top: 5px;">
                                @foreach($item["imagenes"] as $imagen)
                                    <div class="image-item">
                                        <div class="image-placeholder">
                                            <img src="{{ url('/incoming/imagen/' . $imagen['id']) }}" 
                                                 alt="{{ $imagen['archivo'] }}" 
                                                 style="width: 80px; height: 60px; object-fit: cover;">
                                        </div>
                                        <div>{{ basename($imagen['archivo']) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="no-data">No se encontraron rubros que no cumplan en el checklist.</div>
        @endif
    </div>

    <!-- Observaciones Generales -->
    <div class="section">
        <div class="section-title"> OBSERVACIONES GENERALES</div>
        <div class="observations">
            @if(!empty($rechazo->IR_notasGenerales))
                {{ $rechazo->IR_notasGenerales }}
            @elseif(!empty($inspeccion->INC_notas))
                {{ $inspeccion->INC_notas }}
            @else
                <div class="no-data">No se registraron observaciones generales.</div>
            @endif
        </div>
    </div>
</body>
</html>