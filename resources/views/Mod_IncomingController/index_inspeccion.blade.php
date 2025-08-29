@extends('home')
@section('homecontent')

{!! Html::script('assets/js/Mod_IncomingController/incoming.js?v='.time()) !!}

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
    
    /* Estilos para el modal de piel */
    .clase-piel.is-valid {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
    }
    
    .clase-piel.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .porcentaje-clase {
        font-weight: bold;
        color: #495057;
    }
    
    .table-info {
        background-color: #d1ecf1 !important;
    }
    
    .form-control-static {
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 500;
        color: #495057;
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    /* Estilos para el campo de fecha editable */
    #fecha_inspeccion[readonly] {
        background-color: #e9ecef !important;
        cursor: default;
    }
    
    #fecha_inspeccion:not([readonly]) {
        background-color: #fff !important;
        cursor: text;
        border-color: #007bff !important;
    }
    
    /* Indicador visual de que el campo es editable */
    #fecha_inspeccion:hover {
        border-color: #007bff !important;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                Inspección de Materiales
            </h3>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="invoice">
                    <div class="invoice-header" id="cabecera_nota" >
                        <div class="invoice-from">
                            <div class="m-t-5 mb-3">
                              <label><strong>
                                  <font size="2">Número de Entrada</font>
                                </strong></label>
                              <input type="text" id="numero_entrada" class="form-control"
                                placeholder="Ingrese el número de entrada y presione Enter">
                            </div>
                            <small>PROVEEDOR</small>
                            <address class="m-t-5 m-b-5">
                                <strong><span id="nombre_proveedor" style="font-size: 16px;"></span></strong><br>
                                {{-- <span id="direccion_proveedor"></span><br>
                                <span id="email_proveedor"></span> --}}
                            </address>
                        </div>
                        <div class="invoice-to">
                            <small>INFORMACIÓN DE RECEPCIÓN</small>
                            <div class="date m-t-5">
                                <p><strong>Fecha:</strong> <span id="fecha_recepcion"></span></p>
                                <p><strong>Factura:</strong> <span id="numero_factura"></span></p>
                               
                            </div>
                        </div>
                        <div class="invoice-date">
                            <small>INSPECCIÓN</small>
                            <p><span id="id_inspeccion"></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="materiales_container" class="mb-3" style="overflow-x: auto; display: none;">
              <table id="tabla_materiales" class="table table-striped table-bordered" style="width:100%">
                <thead>
                  <tr>
                    <th>Acciones</th>
                    <th>Código</th>
                    <th>Material</th>
                    <th>UDM</th>
                    <th>Recibido</th>
                    <th>Aceptadas</th>
                    <th>Rechazadas</th>
                    <th>Por Revisar</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- Se llena por JS --}}
                </tbody>
              </table>
            </div>
            <div id="inspeccion_container" class="mb-3 mt-4" style="overflow-x: auto; display: none;">
              <div class="row">
                <div class="col-md-4" id="resumen_material">
                  <!-- Resumen lateral -->
                </div>
                <div class="col-md-8" id="checklist_container">
                  <!-- Checklist dinámico -->
                </div>
            
              </div>
              
            </div>
        </div>
    </div>

   

    <!-- Modal para clases de piel -->
    <div class="modal fade" id="modalPiel" tabindex="-1" role="dialog" aria-labelledby="modalPielLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="modalPielLabel">Captura de Clases de Piel</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <!-- Información del material -->
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="font-weight-bold">ARTÍCULO ACTIVO:</label>
                  <div class="form-control-static" style="font-size: 16px;" id="piel_articulo_info"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="font-weight-bold">LOTE:</label>
                  <div class="form-control-static" style="font-size: 16px;" id="piel_lote_info"></div>
                </div>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="font-weight-bold">CANTIDAD POR REVISAR:</label>
                  <div class="form-control-static" style="font-size: 16px;" id="piel_cantidad_total"></div>
                </div>
              </div>
            </div>
            
            <form id="formPiel">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead class="thead-dark">
                    <tr>
                      <th>CLASE</th>
                      <th>CANTIDAD</th>
                      <th>%</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><strong>CLASE A</strong></td>
                      <td>
                        <input type="number" min="0" step="0.01" class="form-control clase-piel" id="claseA" name="claseA" data-clase="A">
                      </td>
                      <td><span id="porcentajeA" class="porcentaje-clase">0.00%</span></td>
                    </tr>
                    <tr>
                      <td><strong>CLASE B</strong></td>
                      <td>
                        <input type="number" min="0" step="0.01" class="form-control clase-piel" id="claseB" name="claseB" data-clase="B">
                      </td>
                      <td><span id="porcentajeB" class="porcentaje-clase">0.00%</span></td>
                    </tr>
                    <tr>
                      <td><strong>CLASE C</strong></td>
                      <td>
                        <input type="number" min="0" step="0.01" class="form-control clase-piel" id="claseC" name="claseC" data-clase="C">
                      </td>
                      <td><span id="porcentajeC" class="porcentaje-clase">0.00%</span></td>
                    </tr>
                    <tr>
                      <td><strong>CLASE *</strong></td>
                      <td>
                        <input type="number" min="0" step="0.01" class="form-control clase-piel" id="claseD" name="claseD" data-clase="D">
                      </td>
                      <td><span id="porcentajeD" class="porcentaje-clase">0.00%</span></td>
                    </tr>
                    <tr class="table-info">
                      <td><strong>TOTAL</strong></td>
                      <td><span id="totalClases" class="font-weight-bold">0.00</span></td>
                      <td><span id="totalPorcentaje" class="font-weight-bold">0.00%</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <div class="alert alert-info" id="alertPiel" style="display:none"></div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="guardarPiel">Guardar Clases</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
