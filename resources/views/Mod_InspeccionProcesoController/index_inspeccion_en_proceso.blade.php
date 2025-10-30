@extends('home')
@section('homecontent')

{!! Html::script('assets/js/Mod_InspeccionProcesoController/index_inspeccion_en_proceso.js?v='.time()) !!}

{!! Html::style('assets/css/customdt2.css') !!}

<script>
    // Variable global para el nombre del inspector
    var currentUser = '{{ Auth::user()->firstName." ".Auth::user()->lastName }}';
</script>

<style>
    input[type="radio"], input[type="checkbox"] {
        margin: 4px 0 0;
        margin-top: 1px \9;
        accent-color: #007bff;
        line-height: normal;
    }
    .mt7{
        margin-top: 7px
    }
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    .mt-4 {
        margin-top: 1.5rem !important;
    }
    .invoice-header {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .invoice-from {
        display: inline-block;
        margin-right: 40px;
    }
    .invoice-to {
        display: inline-block;
        margin-right: 40px;
    }
    .invoice-date {
        display: inline-block;
        float: right;
    }
    /* Contenedor principal con fondo oscuro */
    .data-summary-block {
    background: #2d353c;
    color: #fff;
    padding: 20px 15px;
    border-radius: 8px; /* Bordes redondeados */
    }
    
    /* Estilo para cada bloque de dato individual */
    .data-summary-item {
    text-align: right; /* Alinea todo a la derecha */
    margin-bottom: 15px; /* Espacio para móviles */
    }
    
    /* Estilo para la etiqueta pequeña (título) */
    .data-summary-item small {
    display: block; /* Para que ocupe toda la línea */
    font-weight: bold;
    text-transform: uppercase;
    opacity: 0.7;
    margin-bottom: 5px;
    font-size: 12px;
    }
    
    /* Estilo para los inputs dentro del bloque oscuro */
    .data-summary-item .form-control {
    background: transparent !important; /* Fondo transparente */
    border: none !important; /* Sin bordes */
    border-bottom: 1px solid #555 !important; /* Línea inferior sutil */
    color: #fff !important; /* Texto blanco */
    font-size: 26px; /* Letra grande para el valor */
    font-weight: 300; /* Letra más delgada */
    text-align: right;
    padding: 0;
    height: auto;
    box-shadow: none !important; /* Sin sombras de focus */
    border-radius: 0;
    }
    
    /* Estilo específico para los inputs que son de solo lectura */
    .data-summary-item .form-control[readonly] {
    border-bottom: none !important; /* Quita la línea inferior si no es editable */
    }
    
    /* Oculta las flechas de los input type="number" en navegadores WebKit */
    .data-summary-item input::-webkit-outer-spin-button,
    .data-summary-item input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }
    
    /* Oculta las flechas en Firefox */
    .data-summary-item input[type=number] {
    -moz-appearance: textfield;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                Inspección en Proceso
            </h3>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="invoice">
                    <div class="invoice-header" id="cabecera_nota" >
                        <div class="invoice-from">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="m-t-5 mb-3">
                                        <label><strong>
                                            <font size="2">Inspector</font>
                                        </strong></label>
                                        <input type="text" id="nombre_inspector" class="form-control" 
                                            value="{{ Auth::user()->firstName.' '.Auth::user()->lastName }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="m-t-5 mb-3">
                                        <label><strong>
                                            <font size="2">Orden de Producción (OP)</font>
                                        </strong></label>
                                        <input type="text" id="numero_op" class="form-control"
                                            placeholder="Ingrese el número de OP y presione Enter">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-to">
                            <small>INFORMACIÓN DE LA OP</small>
                            <address class="m-t-5 m-b-5">
                                <strong><span id="articulo_op" style="font-size: 16px;"></span></strong><br>
                                <span id="cantidad_op"></span>
                            </address>
                            <div id="centro_inspeccion_actual" style="margin-top: 10px;"></div>
                        </div>
                        <div class="invoice-date">
                            <small>INSPECCIÓN</small>
                            <p><span id="id_inspeccion"></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="inspeccion_container" class="mb-3 mt-4" style="overflow-x: auto; display: none;">
                <div class="row">
                    <div class="col-md-3" id="resumen_inspeccion">
                        <!-- Resumen lateral -->
                    </div>
                    <div class="col-md-9" id="checklist_container">
                        <!-- Checklist dinámico -->
                        <table class="table table-striped table-bordered" id="tabla_checklist">
                            <thead>
                                <tr>
                                    <th>Evidencias</th>
                                    <th>Punto Checklist</th>
                                    <th>Cumple</th>
                                    <th>No Cumple</th>
                                    <th>No Aplica</th>
                                    <th>Empleado Responsable</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody id="checklist_body">
                                <!-- Las filas se generarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para ver historial de inspecciones previas -->
    <div class="modal fade" id="modalHistorialInspecciones" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" style="width: 90%; max-width: 1200px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><i class="fa fa-history"></i> Historial de Inspecciones - OP <span id="modal_op_numero"></span></h4>
                </div>
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <div id="contenido_historial_inspecciones">
                        <!-- Se llenará dinámicamente -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

