@extends('home')
@section('homecontent')

{!! Html::script('assets/js/Mod_IncomingController/incoming.js?v='.time()) !!}

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
              <div class="text-end">
              <button id="guardar_inspeccion" class="btn btn-success mt-3 btn-lg pull-right">Guardar Inspección</button>
              </div>
            </div>
        </div>
    </div>

   

    <!-- Modal para clases de piel -->
    <div class="modal fade" id="modalPiel" tabindex="-1" role="dialog" aria-labelledby="modalPielLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="modalPielLabel">Captura de Clases de Piel</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <form id="formPiel">
              <div class="form-group">
                <label>Clase A</label>
                <input type="number" min="0" step="any" class="form-control" id="claseA" name="claseA">
              </div>
              <div class="form-group">
                <label>Clase B</label>
                <input type="number" min="0" step="any" class="form-control" id="claseB" name="claseB">
              </div>
              <div class="form-group">
                <label>Clase C</label>
                <input type="number" min="0" step="any" class="form-control" id="claseC" name="claseC">
              </div>
              <div class="form-group">
                <label>Clase D</label>
                <input type="number" min="0" step="any" class="form-control" id="claseD" name="claseD">
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
