<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Rechazo de Material</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .document-container {
            max-width: 4xl;
            margin: 0 auto;
            padding: 1rem;
            background-color: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }
        
        .company-info {
            display: flex;
            align-items: center;
        }
        
        .company-logo {
            background-color: #1e293b;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            padding: 0.75rem;
            border-radius: 0.375rem;
            margin-right: 1rem;
        }
        
        .company-details h1 {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1e293b;
            margin: 0;
        }
        
        .company-details p {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }
        
        .report-info {
            text-align: right;
        }
        
        .report-info h2 {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1d4ed8;
            margin: 0;
        }
        
        .report-info p {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .info-block {
            margin-bottom: 15px;
        }

        .info-left {
            float: left;
            width: 48%;
            margin-right: 2%;
        }

        .info-right {
            float: right;
            width: 48%;
            margin-left: 2%;
            border-left: 2px solid #dee2e6;
            padding-left: 20px;
        }
        
        .info-block h3 {
            color: #495057;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .info-item {
            display: block;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        
        .info-label {
            font-weight: 600;
            color: #6c757d;
            display: inline-block;
            width: 140px;
            font-size: 12px;
        }
        
        .info-value {
            color: #495057;
            font-weight: 500;
            font-size: 12px;
        }
        
        .info-value.urgent {
            color: #dc3545;
            font-weight: 700;
            font-size: 13px;
        }
        
        /* Material Table */
        .material-section {
            margin-top: 1.5rem;
        }
        
        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .table-container {
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
        }
        
        .material-table {
            width: 100%;
            font-size: 0.875rem;
            border-collapse: collapse;
        }
        
        .material-table thead {
            background-color: #f9fafb;
        }
        
        .material-table th {
            padding: 0.5rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .material-table td {
            padding: 0.75rem 1rem;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .material-table tr:last-child td {
            border-bottom: none;
        }
        
        .material-table .urgent {
            color: #dc2626;
            font-weight: bold;
        }
        
        /* Observations */
        .observations-section {
            margin-top: 1.5rem;
        }
        
        .observations-content {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 1rem;
            font-size: 0.875rem;
            color: #92400e;
            border-radius: 0 0.375rem 0.375rem 0;
        }
        
        /* Checklist Details */
        .checklist-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            page-break-inside: avoid;
        }
        
        .checklist-item {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        
        .checklist-title {
            background-color: #334155;
            color: white;
            font-weight: bold;
            padding: 0.5rem;
            font-size: 0.875rem;
        }
        
        .checklist-content {
            padding: 0.75rem;
            background-color: #f9fafb;
            font-size: 0.875rem;
        }
        
        .checklist-observation {
            margin-bottom: 0.5rem;
        }
        
        .checklist-observation strong {
            color: #374151;
        }
        
        /* Images */
        .images-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .image-item {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            text-align: center;
            padding: 0.5rem;
            page-break-inside: avoid;
        }
        
        .large-image {
            width: 40%;
            
            object-fit: cover;
            margin-bottom: 0.5rem;
            border-radius: 0.25rem;
            border: 1px solid #d1d5db;
        }
        
        .image-caption {
            font-size: 0.75rem;
            color: #6b7280;
            word-break: break-all;
        }
        
        .image-placeholder {
            width: 40%;
           
            border: 2px dashed #d1d5db;
            background-color: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }
        
        /* Footer */
        .footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
            font-size: 1rem;
            color: #6b7280;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
        }
        
        .footer-left p {
            margin: 0.25rem 0;
        }
        
        .footer-right {
            text-align: right;
        }
        
        .footer-right p {
            margin: 0.25rem 0;
        }
        
        /* Print styles */
        @media print {
            body {
                font-size: 10px;
                background-color: white;
            }
            
            .document-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            .no-print {
                display: none;
            }
        }
        
        
    </style>
</head>
<body>
    <div class="document-container">
        
        

        <!-- Info Section -->
        <section class="info-section">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="width: 50%; vertical-align: top; padding-right: 20px;">
                        <div class="info-block">
                            <h3>Datos del Proveedor:</h3>
                            <div class="info-item">
                                <span class="info-label">Nombre:</span>
                                <span class="info-value">{{ $proveedor_nombre ?? "N/A" }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Código:</span>
                                <span class="info-value">{{ $proveedor_codigo ?? "N/A" }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Entrada:</span>
                                <span class="info-value">{{ $numero_entrada ?? "N/A" }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Fecha Ingreso:</span>
                                <span class="info-value">{{ $fecha_entrada ?? "N/A" }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding-left: 20px;">
                        <div class="info-block info-right">
                            <div class="info-item">
                                <span class="info-label">Número de Inspección:</span>
                                <span class="info-value urgent">#{{ $inspeccion->INC_id ?? "N/A" }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Fecha Inspección:</span>
                                <span class="info-value">{{ $inspeccion->INC_fechaInspeccion ? date("d/m/Y", strtotime($inspeccion->INC_fechaInspeccion)) : "N/A" }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Número de Rechazo:</span>
                                <span class="info-value urgent">#{{ $id_rechazo ?? "N/A" }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Inspector:</span>
                                <span class="info-value">{{ $inspector_nombre ?? "N/A" }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Lote:</span>
                                <span class="info-value">{{ $lote ?? "N/A" }}</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </section>

        <!-- Material Table -->
        <section class="material-section">
            <h3 class="section-title">Detalle del Material</h3>
            <div class="table-container">
                <table class="material-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Material</th>
                            <th>UdM</th>
                            <th>Recibido</th>
                            <th>Aceptado</th>
                            <th class="urgent">Rechazado</th>
                            <th>Motivo Rechazo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $codigo_material ?? "N/A" }}</td>
                            <td>{{ $nombre_material ?? "N/A" }}</td>
                            <td>{{ $udm ?? "N/A" }}</td>
                            <td>{{ number_format($inspeccion->INC_cantRecibida ?? 0, 3) }}</td>
                            <td>{{ number_format($inspeccion->INC_cantAceptada ?? 0, 3) }}</td>
                            <td class="urgent">{{ $cantidad_rechazada ?? "0.000" }}</td>
                            <td>Ver notas abajo</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Observations -->
        <section class="observations-section">
            <h3 class="section-title">Observaciones Generales / Motivo del Rechazo</h3>
            <div class="observations-content">
                @if(!empty($notas_generales))
                    {{ $notas_generales }}
                @elseif(!empty($inspeccion->INC_notas))
                    {{ $inspeccion->INC_notas }}
                @else
                    <p>No se registraron observaciones generales.</p>
                @endif
            </div>
        </section>

        <!-- Checklist Details -->
        <section class="checklist-section">
            <h3 class="section-title">Detalle de Inspección</h3>
            @if(isset($checklistNoCumple) && count($checklistNoCumple) > 0)
                <div class="space-y-4">
                    @foreach($checklistNoCumple as $item)
                        <div class="checklist-item">
                            <div class="checklist-title">
                                {{ $item["descripcion"] }}
                            </div>
                            <div class="checklist-content">
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
                                    <div class="images-section">
                                        <h4 style="font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Evidencias Fotográficas:</h4>
                                        <div class="images-grid">
                                            @foreach($item["imagenes"] as $imagen)
                                                <div class="image-item">
                                                    @if(!empty($imagen['base64']))
                                                        <img src="{{ $imagen['base64'] }}" 
                                                             alt="{{ $imagen['archivo'] }}" 
                                                             class="large-image">
                                                        <div class="image-caption">{{ basename($imagen['archivo']) }}</div>
                                                    @else
                                                        <div class="image-placeholder">
                                                            <span style="color: #9ca3af; font-size: 0.75rem;">Imagen no disponible</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="checklist-item">
                    <div class="checklist-content">
                        <p>No se encontraron rubros que no cumplan en el checklist.</p>
                    </div>
                </div>
            @endif
        </section>
        
        <!-- Footer -->
        <footer class="footer">
            <div class="footer-content">
                <div class="footer-left">
                    <p><strong>Inspector:</strong> {{ $inspector_nombre ?? "N/A" }}</p>
                    <p><strong>Email:</strong> {{ $inspector_correo ? $inspector_correo."@zarkin.com" : "N/A" }}</p>
                </div>
                <div class="footer-right">
                    <p><strong> SIZ - Control de Calidad</strong></p>
                </div>
            </div>
        </footer>

    </div>
</body>
</html>