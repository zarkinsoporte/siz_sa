@extends('home')
@section('homecontent')

{!! Html::script('assets/js/Reportes_IncomingController/index_rep04.js?v='.time()) !!}

{!! Html::style('assets/css/customdt2.css') !!}

<style>
    .mt7{
        margin-top: 7px
    }
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    .mt-4 {
        margin-top: 1.5rem !important;
    }
    
    /* Estilos para las tablas de resumen */
    .table-resumen {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }
    
    .table-resumen th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    .table-resumen td {
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    .table-resumen .promedio-cell {
        background-color: #d4edda;
        font-weight: bold;
    }
    
    .table-resumen .meta-cell {
        background-color: #fff3cd;
        font-weight: bold;
    }
    
    /* Estilos para la DataTable */
    #tablaProveedores_wrapper {
        margin-top: 20px;
    }
    
    #tablaProveedores {
        width: 100% !important;
        table-layout: fixed;
    }
    
    #tablaProveedores th {
        background-color: #ff69b4;
        color: white;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    #tablaProveedores td {
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    #tablaProveedores_wrapper .dataTables_scrollHead {
        overflow: visible !important;
    }
    
    #tablaProveedores_wrapper .dataTables_scrollBody {
        overflow-x: auto !important;
    }
    
    .porcentaje-cell {
        font-weight: bold;
    }
    
    .btn-actualizar {
        margin-bottom: 15px;
    }
    
    .btn-pdf {
        margin-bottom: 15px;
        margin-left: 10px;
    }
    
    .filtro-ano {
        margin-bottom: 20px;
    }
    
    .titulo-reporte {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                R-143 CONFIABILIDAD DE PROVEEDOR
            </h3>
        </div>
    </div>
    
    <div class="panel panel-default">
    <div class="panel-body">
        
        <!-- Sección de Filtros -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                        <h4 class="panel-title" style="color: #495057; font-size: 14px; font-weight: 600;">
                            <i class="fa fa-filter"></i> Filtros de Búsqueda
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="yearPicker">Año del Reporte:</label>
                                    <input type="text" id="yearPicker" class="form-control" readonly value="{{ date('Y') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btnBuscar" class="btn btn-primary btn-block">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btnImprimirPDF" class="btn btn-danger btn-block">
                                        <i class="fa fa-file-pdf-o"></i> Imprimir PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
            <!-- Tabla Promedio del Año -->
            <div class="row">
                <div class="col-md-12">
                    <h5>Promedio del año</h5>
                    <table class="table table-bordered table-resumen" id="tablaPromedioAnual">
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
                                <td id="promedioEntradas">0</td>
                                <td class="promedio-cell" id="promedioAnual">-</td>
                                <td id="promedioEnero">-</td>
                                <td id="promedioFebrero">-</td>
                                <td id="promedioMarzo">-</td>
                                <td id="promedioAbril">-</td>
                                <td id="promedioMayo">-</td>
                                <td id="promedioJunio">-</td>
                                <td id="promedioJulio">-</td>
                                <td id="promedioAgosto">-</td>
                                <td id="promedioSeptiembre">-</td>
                                <td id="promedioOctubre">-</td>
                                <td id="promedioNoviembre">-</td>
                                <td id="promedioDiciembre">-</td>
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
                </div>
            </div>
            
            <!-- Tabla de FAMILIAS -->
            <div class="row">
                <div class="col-md-12">
                    <h5>FAMILIAS</h5>
                    <table class="table table-bordered table-resumen" id="tablaFamilias">
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
                        <tbody id="tbodyFamilias">
                            <tr>
                                <td colspan="15" class="text-center">Seleccione un año y haga clic en "Buscar"</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- DataTable de Proveedores -->
            <div class="row">
                <div class="col-md-12">
                    <h5>Proveedores</h5>
                    <table class="table table-bordered table-striped" id="tablaProveedores">
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</div>

<script>
    var csrfToken = '{{ csrf_token() }}';
    var anoActual = '{{ $anoActual }}';
</script>

@endsection
