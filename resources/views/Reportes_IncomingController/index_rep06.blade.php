@extends('home')
@section('homecontent')

{!! Html::style('assets/css/customdt2.css') !!}
{!! Html::script('assets/js/Reportes_IncomingController/index_rep06.js?v='.time()) !!}

<style>
    .titulo-reporte { font-size: 18px; font-weight: bold; margin-bottom: 15px; }
    .box-material { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 15px; }
    #tablaDetalle {
        width: 100% !important;
        table-layout: fixed;
    }
    
    #tablaDetalle th { 
        background-color: #f8f9fa; 
        color: #495057; 
        font-weight: bold; 
        text-align: center; 
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    #tablaDetalle td { 
        vertical-align: middle; 
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 12px;
    }
    
    #tablaDetalle_wrapper .dataTables_scrollHead {
        overflow: visible !important;
    }
    
    #tablaDetalle_wrapper .dataTables_scrollBody {
        overflow-x: auto !important;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                REP-06 HISTORIAL POR MATERIAL
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
                                    <label for="fechaDesde">Fecha Desde:</label>
                                    <input type="text" id="fechaDesde" class="form-control" readonly value="{{ date('Y-m-d', strtotime('-1 year')) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fechaHasta">Fecha Hasta:</label>
                                    <input type="text" id="fechaHasta" class="form-control" readonly value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="codMaterial">Código de Material:</label>
                                    <input id="codMaterial" type="text" class="form-control" placeholder="">
                                </div>
                            </div>
                            
                            <div class="col-md-1">
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

            <div class="box-material" style="font-size: 16px;">
                <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;"><strong>Material:</strong> <span id="txtMaterial">-</span></div>
                <div style="font-size: 16px;"><strong>UDM:</strong> <span id="txtUDM">-</span></div>
                <div style="font-size: 16px;"><strong>Periodo:</strong> <span id="txtPeriodo">-</span></div>
            </div>

            <h5>Detalle por Proveedor</h5>
            <table class="table table-bordered table-striped" id="tablaDetalle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Código Proveedor</th>
                        <th>Proveedor</th>
                        <th>Aceptado</th>
                        <th>Calificación</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>
    </div>
    </div>
</div>

<script>
    var csrfToken = '{{ csrf_token() }}';
    var fechaDesdeDefault = '{{ date('Y-m-d', strtotime('-1 year')) }}';
    var fechaHastaDefault = '{{ date('Y-m-d') }}';
</script>

@endsection
