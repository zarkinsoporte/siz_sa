<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo_pdf ?? 'Evidencia' }} - OP {{ $orden->OP }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background-color: white;
            color: #374151;
            font-size: 11px;
        }
        
        .document-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 15px;
            background-color: white;
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding-right: 15px;
            padding-bottom: 5px;
            width: 150px;
        }
        
        .info-value {
            display: table-cell;
            padding-bottom: 5px;
        }
        
        /* Inspección Section */
        .inspeccion-section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            page-break-inside: avoid;
        }
        
        .inspeccion-header {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .inspeccion-header h3 {
            margin: 0;
            font-size: 14px;
            color: #495057;
        }
        
        /* Checklist Table */
        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        
        .checklist-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .checklist-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: top;
        }
        
        .checklist-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .estado-cumple {
            color: #28a745;
            font-weight: bold;
        }
        
        .estado-no-cumple {
            color: #dc3545;
            font-weight: bold;
        }

        .estado-aceptado {
            color: #28a745;
            font-weight: bold;
        }

        .estado-rechazado {
            color: #dc3545;
            font-weight: bold;
        }
        
        /* Images Section */
        .images-section {
            margin-top: 15px;
        }
        
        .images-grid {
            width: 100%;
            margin-top: 10px;
        }
        
        .image-item {
            display: inline-block;
            width: 76%;
            padding: 8px;
            vertical-align: top;
            page-break-inside: avoid;
            box-sizing: border-box;
            margin-bottom: 15px;
        }
        
        .image-container {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 8px;
            text-align: center;
            background-color: #f8f9fa;
        }
        
        .image-container img {
            max-width: 100%;
            max-height: 450px;
            height: auto;
            border-radius: 3px;
            display: block;
            margin: 0 auto;
        }
        
        .image-label {
            font-size: 9px;
            color: #6b7280;
            margin-top: 5px;
            font-weight: bold;
            word-break: break-word;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                background-color: white;
            }
            .document-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="document-container">
        <!-- Información de la Orden -->
        <div class="info-section">
            <h2 style="margin-top: 0; margin-bottom: 15px; font-size: 14px; color: #1e293b;">Información de la Orden de Producción</h2>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Código de Artículo:</div>
                    <div class="info-value">{{ $orden->ItemCode }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nombre del Material:</div>
                    <div class="info-value">{{ $orden->NombreArticulo }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Número de Pedido:</div>
                    <div class="info-value">{{ $orden->Pedido ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Cliente:</div>
                    <div class="info-value">{{ $orden->Cliente ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de Finalización:</div>
                    <div class="info-value">{{ date('d/m/Y', strtotime($orden->FechaFinalizacion)) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Cantidad Planeada:</div>
                    <div class="info-value">{{ number_format($orden->Cantidad, 2) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Cantidad Completada:</div>
                    <div class="info-value">{{ number_format($orden->CantidadCompletada, 2) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Checklist de Liberación Final -->
        @if(isset($encabezadoChecklist) && isset($filasChecklist) && $filasChecklist->count() > 0)
            <div class="inspeccion-section" style="margin-bottom: 30px;">
                <div class="inspeccion-header">
                    <h3 style="margin: 0; font-size: 14px; color: #495057;">
                        {{ $encabezadoChecklist->LAB_descripcion ?? 'CHECK LIST DE LIBERACION FINAL' }}
                    </h3>
                </div>
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 70%;">Descripcion</th>
                            <th style="width: 25%;">Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filasChecklist as $fila)
                            <tr>
                                <td>{{ $fila->LAB_numOrden }}</td>
                                <td>{{ $fila->LAB_descripcion }}</td>
                                <td>
                                    @if($fila->LAB_estatus == 'CUMPLE')
                                        <span class="estado-cumple">{{ $fila->LAB_estatus }}</span>
                                    @elseif($fila->LAB_estatus == 'NO CUMPLE')
                                        <span class="estado-no-cumple">{{ $fila->LAB_estatus }}</span>
                                    @else
                                        {{ $fila->LAB_estatus ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        
        <!-- Inspecciones -->
        @foreach($inspecciones as $index => $inspeccion)
            <div class="inspeccion-section {{ ($index > 0 && $index < count($inspecciones) - 1) ? 'page-break' : '' }}">
                <div class="inspeccion-header">
                    <h3>
                        Estación: {{ substr($inspeccion->IPR_nombreCentro, 3) }} - 
                        Fecha: {{ date('d/m/Y H:i', strtotime($inspeccion->IPR_fechaInspeccion)) }}
                    </h3>
                    <p style="margin: 5px 0 0 0; font-size: 10px;">
                        Estado:
                        @if(($inspeccion->IPR_estado ?? '') === 'RECHAZADO')
                            <span class="estado-rechazado">RECHAZADO</span>
                        @else
                            <span class="estado-aceptado">ACEPTADO</span>
                        @endif
                        &nbsp;|&nbsp;
                        Inspector: {{ $inspeccion->IPR_nomInspector ?? 'N/A' }}
                        &nbsp;|&nbsp;
                        Cantidad Inspeccionada: {{ number_format($inspeccion->IPR_cantInspeccionada ?? 0, 2) }}
                        @if(isset($inspeccion->IPR_cantRechazada) && floatval($inspeccion->IPR_cantRechazada) > 0)
                            &nbsp;|&nbsp;
                            Cantidad Rechazada: {{ number_format($inspeccion->IPR_cantRechazada, 2) }}
                        @endif
                    </p>
                </div>
                
                @if($inspeccion->IPR_observaciones)
                    <div style="margin-bottom: 15px; padding: 10px; background-color: #fff3cd; border-radius: 5px;">
                        <strong>Observaciones Generales:</strong><br>
                        {{ $inspeccion->IPR_observaciones }}
                    </div>
                @endif
                
                <!-- Checklist -->
                @if($inspeccion->detalles && count($inspeccion->detalles) > 0)
                    <h4 style="font-size: 12px; margin-bottom: 10px;">Checklist de Inspección</h4>
                    <table class="checklist-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 50%;">Punto de Inspección</th>
                                <th style="width: 15%;">Estado</th>
                                <th style="width: 30%;">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inspeccion->detalles as $idx => $detalle)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $detalle->CHK_descripcion }}</td>
                                    <td>
                                        @if($detalle->IPD_estado == 'C')
                                            <span class="estado-cumple">Cumple</span>
                                        @elseif($detalle->IPD_estado == 'N')
                                            <span class="estado-no-cumple">No Cumple</span>
                                        @else
                                            <span>No Aplica</span>
                                        @endif
                                    </td>
                                    <td>{{ $detalle->IPD_observacion ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                
                <!-- Imágenes de Evidencia -->
                @if(isset($inspeccion->imagenes) && count($inspeccion->imagenes) > 0)
                    <div class="images-section">
                        <h4 style="font-size: 12px; margin-bottom: 10px;">Evidencias Fotográficas</h4>
                        <div class="images-grid">
                            @foreach($inspeccion->imagenes as $chkId => $imagenesChk)
                                @foreach($imagenesChk as $imagen)
                                    @if(!empty($imagen['base64']))
                                    <div class="image-item">
                                        <div class="image-container">
                                            <img src="{{ $imagen['base64'] }}" alt="Evidencia">
                                            <div class="image-label">
                                                {{ $imagen['chk_descripcion'] ?? 'Item ' . $chkId }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>

