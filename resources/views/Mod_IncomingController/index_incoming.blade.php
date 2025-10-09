@extends('home')
@section('homecontent')

{!! Html::script('assets/js/Mod_IncomingController/incoming.js?v='.time()) !!}

{!! Html::style('assets/css/customdt2.css') !!}

<script>
    var currentUser = '{{ Auth::user()->firstName." ".Auth::user()->lastName }}';
</script>

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
    
    /* Estilos para la tabla de inspecciones */
    .table-inspecciones th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }
    
    .table-inspecciones td {
        text-align: center;
        vertical-align: middle;
    }
    
    .cantidad-aceptada {
        color: #28a745;
        font-weight: bold;
    }
    
    .cantidad-rechazada {
        color: #dc3545;
        font-weight: bold;
    }
    
    .estado-inspeccionado {
        color: #28a745;
        font-weight: bold;
    }
    
    .estado-pendiente {
        color: #ffc107;
        font-weight: bold;
    }
    
    .btn-ver-detalle {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        padding: 5px 10px;
        font-size: 12px;
    }
    
    .btn-ver-detalle:hover {
        background-color: #0056b3;
        border-color: #004085;
        color: white;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                Resumen de Inspecciones de Materiales
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
                                        <button type="button" id="btn_buscar_inspecciones" class="btn btn-primary btn-block">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="inspecciones_container" class="mb-3" style="overflow-x: auto;">
                <table id="tabla_inspecciones" class="table table-striped table-bordered table-inspecciones" style="width:100%">
                    <thead>
                        <tr>
                            <th>NOTA ENTRADA</th>
                            <th>PROVEEDOR</th>
                            <th>MATERIAL</th>
                            <th>CANT. RECIBIDA</th>
                            <th>ACEPTADA</th>
                            <th>RECHAZADA</th>
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

    <!-- Modal para ver detalle de inspección (modal-lg) -->
    <div class="modal fade" id="modalDetalleInspeccion" tabindex="-1" role="dialog" aria-labelledby="modalDetalleInspeccionLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalDetalleInspeccionLabel">
                        <i class="fa fa-list-alt"></i> Detalle de Inspecciones Agrupadas
                    </h4>
                </div>
                <div class="modal-body" id="detalle_inspeccion_content">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p>Cargando...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para ver resumen de clases de piel (modal-lg) -->
    <div class="modal fade" id="modalResumenPiel" tabindex="-1" role="dialog" aria-labelledby="modalResumenPielLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalResumenPielLabel">
                        <i class="fa fa-tags"></i> Resumen Consolidado de Clases de Piel
                    </h4>
                </div>
                <div class="modal-body" id="resumen_piel_content">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p>Cargando...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


