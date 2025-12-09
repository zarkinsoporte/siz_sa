@extends('home')
@section('homecontent')

{!! Html::script('assets/js/Mod_InspeccionProcesoController/evidencia_cliente.js?v='.time()) !!}

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
    
    /* Estilos para la tabla de evidencias */
    .table-evidencias th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }
    
    .table-evidencias td {
        text-align: center;
        vertical-align: middle;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                Evidencia de Clientes
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filtro_fecha_desde">Fecha Desde:</label>
                                    <input type="date" id="filtro_fecha_desde" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filtro_fecha_hasta">Fecha Hasta:</label>
                                    <input type="date" id="filtro_fecha_hasta" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btn_buscar_evidencias" class="btn btn-primary btn-block">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="evidencias_container" class="mb-3" style="overflow-x: auto;">
            <table id="tabla_evidencias" class="table table-striped table-bordered table-evidencias" style="width:100%">
                <thead>
                    <tr>
                        <th>OP</th>
                        <th>MATERIAL</th>
                        <th>PEDIDO</th>
                        <th>CLIENTE</th>
                        <th>FECHA FINALIZACIÓN</th>
                        <th>CANTIDAD</th>
                        <th>ACCIONES</th>
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

