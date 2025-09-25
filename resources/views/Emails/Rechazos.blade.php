<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Notificación de Rechazo</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        
        .info-section {
            background-color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
        }
        
        .info-section h3 {
            color: #dc3545;
            margin-top: 0;
            font-size: 16px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .info-table th {
            background-color: #e9ecef;
            color: #495057;
            font-weight: bold;
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }
        
        .info-table td {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }
        
        .info-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .footer {
            background-color: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
        }
        
        .urgent {
            color: #dc3545;
            font-weight: bold;
        }
        
        .material-info {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚨 NOTIFICACIÓN DE RECHAZO DE MATERIAL</h1>
        <p>Control de Calidad - Sistema SIZ</p>
    </div>
    
    <div class="content">
        <div class="info-section">
            <h3>📋 Información del Rechazo</h3>
            <table class="info-table">
                <tr>
                    <th>Número de Rechazo:</th>
                    <td class="urgent">#{{ $id_rechazo }}</td>
                </tr>
                <tr>
                    <th>Fecha de Rechazo:</th>
                    <td>{{ $fecha_rechazo }}</td>
                </tr>
                <tr>
                    <th>Nota de Entrada:</th>
                    <td>{{ $nota_entrada }}</td>
                </tr>
            </table>
        </div>
        
        <div class="info-section">
            <h3>🏢 Información del Proveedor</h3>
            <table class="info-table">
                <tr>
                    <th>Proveedor:</th>
                    <td>{{ $proveedor_nombre }}</td>
                </tr>
                <tr>
                    <th>Código:</th>
                    <td>{{ $proveedor_codigo }}</td>
                </tr>
                <tr>
                    <th>Orden de Compra:</th>
                    <td>{{ $numero_oc }}</td>
                </tr>
                <tr>
                    <th>Fecha de OC:</th>
                    <td>{{ $fecha_oc }}</td>
                </tr>
            </table>
        </div>
        
        <div class="material-info">
            <h3> Material Rechazado</h3>
            <table class="info-table">
                <tr>
                    <th>Código de Material:</th>
                    <td>{{ $codigo_material }}</td>
                </tr>
                <tr>
                    <th>Descripción:</th>
                    <td>{{ $nombre_material }}</td>
                </tr>
                <tr>
                    <th>Cantidad Rechazada:</th>
                    <td class="urgent">{{ $cantidad_rechazada }} {{ $udm }}</td>
                </tr>
                <tr>
                    <th>Lote:</th>
                    <td>{{ $lote ?: 'N/A' }}</td>
                </tr>
            </table>
        </div>
        
        @if($notas_generales)
        <div class="info-section">
            <h3>📝 Notas Generales</h3>
            <div class="highlight">
                {{ $notas_generales }}
            </div>
        </div>
        @endif
        
        <div class="highlight">
            <h3>⚠️ Acción Requerida</h3>
            <p><strong>ATENCIÓN COMPRAS:</strong> Favor de notificar al proveedor <strong>{{ $proveedor_nombre }}</strong> para que se presente al almacén con previa cita para recoger los productos rechazados.</p>
            <p>El material rechazado se encuentra disponible en el almacén y debe ser retirado en un plazo máximo de 30 días.</p>
        </div>
        
        <div class="info-section">
            <h3>👤 Inspector Responsable</h3>
            <table class="info-table">
                <tr>
                    <th>Nombre:</th>
                    <td>{{ $inspector_nombre }}</td>
                </tr>
                <tr>
                    <th>Número de Nómina:</th>
                    <td>{{ $inspector_codigo }}</td>
                </tr>
                <tr>
                    <th>Correo:</th>
                    <td>{{ $inspector_correo }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>Control de Calidad - Sistema SIZ</strong></p>
        <p>Este es un correo automático del sistema. Por favor, no responder a este mensaje.</p>
        <p>Fecha de envío: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
    