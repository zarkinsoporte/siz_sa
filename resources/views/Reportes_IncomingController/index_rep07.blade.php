@extends('home')
@section('homecontent')

{!! Html::style('assets/css/customdt2.css') !!}
{!! Html::script('assets/js/Reportes_IncomingController/index_rep07.js?v='.time()) !!}

<style>
    .titulo-reporte { font-size: 18px; font-weight: bold; margin-bottom: 15px; }
    .tabla-indicadores { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
    .tabla-indicadores th, .tabla-indicadores td { 
        border: 1px solid #dee2e6; 
        padding: 4px 6px; 
        text-align: center; 
        vertical-align: middle; 
    }
    .tabla-indicadores th { background-color: #f8f9fa; font-weight: bold; }
    .tabla-indicadores .area-header { 
        font-weight: bold; 
        text-align: left; 
        padding-left: 10px;
    }
    .tabla-indicadores .row-label { text-align: left; padding-left: 20px; }
    .tabla-indicadores .pct-row { font-weight: bold; }
    
    /* Colores por área */
    .area-total { background-color: #47855e !important; color: white; }
    .area-corte { background-color: #ffcccc !important; }
    .area-costura { background-color: #cce5ff !important; }
    .area-cojineria { background-color: #d4edda !important; }
    .area-tapiceria { background-color: #ffe4c4 !important; }
    .area-carpinteria { background-color: #fff3cd !important; }
    
    .mes-header { min-width: 70px; cursor: pointer; }
    .mes-header:hover { background-color: #007bff !important; color: white; }
    .box-info { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 15px; }
    
    /* Modal styles */
    .modal-tabla-detalle { width: 100%; border-collapse: collapse; font-size: 14px; }
    .modal-tabla-detalle th, .modal-tabla-detalle td { 
        border: 1px solid #dee2e6; 
        padding: 8px 12px; 
        text-align: center; 
    }
    .modal-tabla-detalle th { background-color: #f8f9fa; font-weight: bold; }
    .modal-tabla-detalle .area-header { text-align: left; padding-left: 10px; font-weight: bold; }
    .modal-tabla-detalle .row-label { text-align: left; padding-left: 20px; }
    .modal-tabla-detalle .pct-row { font-weight: bold; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                REP-07 INDICADORES DE CALIDAD POR MES
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
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btnBuscar" class="btn btn-primary btn-block">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btnImprimirPDF" class="btn btn-danger btn-block">
                                        <i class="fa fa-file-pdf-o"></i> Imprimir PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-info" style="font-size: 14px;">
            <div><strong>Año:</strong> <span id="txtAno">-</span></div>
            <div><strong>Periodo:</strong> <span id="txtPeriodo">-</span></div>
        </div>

        <!-- Tabla de Indicadores -->
        <div class="table-responsive">
            <table class="tabla-indicadores" id="tablaIndicadores">
                <thead>
                    <tr>
                        <th style="width: 180px;">AREA / MES</th>
                        <th class="mes-header">Ene</th>
                        <th class="mes-header">Feb</th>
                        <th class="mes-header">Mar</th>
                        <th class="mes-header">Abr</th>
                        <th class="mes-header">May</th>
                        <th class="mes-header">Jun</th>
                        <th class="mes-header">Jul</th>
                        <th class="mes-header">Ago</th>
                        <th class="mes-header">Sep</th>
                        <th class="mes-header">Oct</th>
                        <th class="mes-header">Nov</th>
                        <th class="mes-header">Dic</th>
                    </tr>
                </thead>
                <tbody id="tbodyIndicadores">
                    <tr><td colspan="13" class="text-center">Seleccione un año y haga clic en "Buscar"</td></tr>
                </tbody>
            </table>
        </div>

        </div>
    </div>
</div>

<!-- Modal Detalle Mes -->
<div class="modal fade" id="modalDetalleMes" tabindex="-1" role="dialog" aria-labelledby="modalDetalleMesLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalDetalleMesLabel">Detalle del Mes</h4>
            </div>
            <div class="modal-body">
                <div id="modalDetalleMesContenido"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    var csrfToken = '{{ csrf_token() }}';
    var anoActual = '{{ date('Y') }}';
</script>

@endsection
