@extends('home')
@section('homecontent')

{!! Html::script('assets/js/Reportes_IncomingController/index.js?v='.time()) !!}

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
    
    /* Estilos para la tabla */
    .table-reporte th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }
    
    .table-reporte td {
        text-align: center;
        vertical-align: middle;
    }
    
    /* Estilos para DataTable con scroll - Header fijo */
    #tabla_reporte_wrapper .dataTables_scrollHead {
        position: relative;
    }
    
    #tabla_reporte_wrapper .dataTables_scrollHead thead th {
        background-color: #f8f9fa !important;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    #tabla_reporte_wrapper .dataTables_scrollBody {
        border-top: 2px solid #dee2e6;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                R-140 INCOMING - Inspección de Materiales
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
                                    <label for="filtro_fecha_desde">Fecha Desde:</label>
                                    <input type="date" id="filtro_fecha_desde" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filtro_fecha_hasta">Fecha Hasta:</label>
                                    <input type="date" id="filtro_fecha_hasta" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btn_buscar" class="btn btn-primary btn-block">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                            <label>&nbsp;</label>
                                <button type="button" id="btn_imprimir_pdf" class="btn btn-danger btn-block">
                                    <i class="fa fa-file-pdf-o"></i> Imprimir PDF
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="reporte_container" class="mb-3" style="overflow-x: auto;">
            <table id="tabla_reporte" class="table table-striped table-bordered table-reporte" style="width:100%">
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
                    {{-- Se llena por JS --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
