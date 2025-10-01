@extends('home')
@section('homecontent')

{!! Html::script('assets/js/Mod_RechazosController/rechazos.js?v='.time()) !!}

{!! Html::style('assets/css/customdt2.css') !!}

<script>
    // Variable global para el nombre del inspector
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
    
    /* Estilos para el botón de generar rechazo */
    .btn-generar-rechazo {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
        padding: 5px 10px;
        font-size: 12px;
    }
    
    .btn-generar-rechazo:hover {
        background-color: #c82333;
        border-color: #bd2130;
        color: white;
    }
    
    .btn-generar-rechazo:disabled {
        background-color: #6c757d;
        border-color: #6c757d;
        opacity: 0.65;
    }
    
    /* Estilos para el estado de procesamiento */
    #confirmarGenerarRechazo.procesando {
        background-color: #6c757d;
        border-color: #6c757d;
        cursor: not-allowed;
    }

    #confirmarGenerarRechazo.procesando i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    #procesando_rechazo {
        margin-top: 15px;
    }

    #procesando_rechazo i {
        margin-right: 8px;
    }
    
    /* Estilos para la tabla de rechazos */
    .table-rechazos th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }
    
    .table-rechazos td {
        text-align: center;
        vertical-align: middle;
    }
    
    .cantidad-rechazada {
        color: #dc3545;
        font-weight: bold;
    }
    
    .cantidad-inspeccionada {
        color: #28a745;
        font-weight: bold;
    }
    
    .cantidad-por-revisar {
        color: #ffc107;
        font-weight: bold;
    }
    
    .reporte-generado {
        color: #28a745;
        font-weight: bold;
    }
    
    .reporte-pendiente {
        color: #dc3545;
        font-weight: bold;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                Gestión de Rechazos de Materiales
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
                                    <label for="filtro_estado">Estado del Rechazo:</label>
                                    <select id="filtro_estado" class="form-control boot-select" data-live-search="false">
                                       
                                        <option value="0" selected>Pendientes</option>
                                        <option value="1">Generados</option>
                                    </select>
                                </div>
                            </div>
                            
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
                                    <button type="button" id="btn_buscar_rechazos" class="btn btn-primary btn-block">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="rechazos_container" class="mb-3" style="overflow-x: auto;">
            <table id="tabla_rechazos" class="table table-striped table-bordered table-rechazos" style="width:100%">
                <thead>
                    <tr>
                        <th>ID INSPECCIÓN</th>
                        <th>NOTA ENTRADA</th>
                        <th>MATERIAL</th>
                        <th>UDM</th>
                        <th>CANTIDAD</th>
                        <th>INSPECCIONADA</th>
                        <th>RECHAZADA</th>
                        <th>FECHA</th>
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

    <!-- Modal de confirmación para generar rechazo -->
    <div class="modal fade" id="modalConfirmarRechazo" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarRechazoLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalConfirmarRechazoLabel">Confirmar Generación de Rechazo</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>¿Está seguro que desea generar el reporte de rechazo?</strong>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nota de Entrada:</strong>
                            <p id="modal_nota_entrada"></p>
                        </div>
                        <div class="col-md-6">
                            <strong>Código de Artículo:</strong>
                            <p id="modal_codigo_articulo"></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Material:</strong>
                            <p id="modal_material"></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Cantidad Rechazada:</strong>
                            <p class="cantidad-rechazada" id="modal_cantidad_rechazada"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>ID Inspección:</strong>
                            <p id="modal_id_inspeccion"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Línea:</strong>
                            <p id="modal_linea"></p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="modal_notas_generales"><strong>Notas Generales del Rechazo:</strong></label>
                        <textarea class="form-control" id="modal_notas_generales" rows="3" 
                                  placeholder="Ingrese las notas generales del rechazo..." 
                                  style="text-transform: uppercase;"></textarea>
                    </div>
                    
                    <!-- Estado de procesamiento -->
                    <div id="procesando_rechazo" class="alert alert-info" style="display: none;">
                        <i class="fa fa-spinner fa-spin"></i>
                        <strong>Procesando rechazo...</strong> Por favor espere, no cierre esta ventana.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btn_cancelar_rechazo" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarGenerarRechazo">
                        <i class="fa fa-times"></i> <span id="btn_text">Generar Rechazo</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection