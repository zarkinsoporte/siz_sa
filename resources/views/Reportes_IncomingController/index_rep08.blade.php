@extends('home')
@section('homecontent')

{!! Html::style('assets/css/customdt2.css') !!}
{!! Html::script('assets/js/Reportes_IncomingController/index_rep08.js?v='.time()) !!}

<style>
    .titulo-reporte { font-size: 18px; font-weight: bold; margin-bottom: 15px; }
    .box-info { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 15px; }
    
    .container-meses { 
        display: flex; 
        flex-wrap: wrap; 
        gap: 15px; 
    }
    
    .bloque-mes { 
        flex: 0 0 calc(25% - 15px); 
        min-width: 280px;
        border: 1px solid #dee2e6; 
        border-radius: 5px;
        margin-bottom: 15px;
    }
    
    .mes-header {
        background-color: #343a40;
        color: white;
        padding: 8px 12px;
        font-weight: bold;
        font-size: 14px;
        border-radius: 4px 4px 0 0;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .mes-header:hover {
        background-color: #007bff;
    }
    
    .mes-content {
        padding: 10px;
    }
    
    .area-block {
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
        border-radius: 4px;
    }
    
    .area-header {
        padding: 6px 10px;
        font-weight: bold;
        font-size: 12px;
    }
    
    .area-corte { background-color: #ffcccc; }
    .area-costura { background-color: #cce5ff; }
    .area-cojineria { background-color: #d4edda; }
    .area-tapiceria { background-color: #ffe4c4; }
    .area-carpinteria { background-color: #fff3cd; }
    
    .defecto-item {
        display: flex;
        justify-content: space-between;
        padding: 4px 10px;
        border-top: 1px solid #e9ecef;
        font-size: 12px;
    }
    
    .defecto-item:nth-child(odd) {
        background-color: #f8f9fa;
    }
    
    .defecto-nombre {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding-right: 10px;
    }
    
    .defecto-conteo {
        font-weight: bold;
        min-width: 40px;
        text-align: right;
    }
    
    .no-data {
        color: #999;
        font-style: italic;
        padding: 4px 10px;
        font-size: 11px;
    }
    
    /* Modal styles */
    .modal-area-block {
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
    }
    
    .modal-area-header {
        padding: 10px 15px;
        font-weight: bold;
        font-size: 14px;
        border-radius: 4px 4px 0 0;
    }
    
    .modal-defecto-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 15px;
        border-top: 1px solid #e9ecef;
        font-size: 13px;
    }
    
    .modal-defecto-item:nth-child(odd) {
        background-color: #f8f9fa;
    }
    
    .modal-defecto-nombre {
        flex: 1;
        padding-right: 15px;
    }
    
    .modal-defecto-conteo {
        font-weight: bold;
        min-width: 60px;
        text-align: right;
    }
    
    .modal-no-data {
        color: #999;
        font-style: italic;
        padding: 10px 15px;
        font-size: 13px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                REP-08 INSPECCIÓN TOP 3 DEFECTOS
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

        <!-- Contenedor de los meses -->
        <div class="container-meses" id="containerMeses">
            <p class="text-center text-muted" style="width: 100%;">Seleccione un año y haga clic en "Buscar"</p>
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
