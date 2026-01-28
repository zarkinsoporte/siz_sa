@extends('home')
@section('homecontent')

{!! Html::script('assets/js/Reportes_IncomingController/index_rep05.js?v='.time()) !!}
{!! Html::style('assets/css/customdt2.css') !!}

<style>
    .titulo-reporte { font-size: 18px; font-weight: bold; margin-bottom: 15px; }
    .table-resumen { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
    .table-resumen th, .table-resumen td { border: 1px solid #dee2e6; padding: 8px; text-align: center; vertical-align: middle; }
    .table-resumen th { background-color: #f8f9fa; font-weight: bold; }
    .box-proveedor { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 15px; }
    #tablaDetalle th { background-color: #ff69b4; color: #fff; font-weight: bold; text-align: center; }
    #tablaDetalle td { vertical-align: middle; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="titulo-reporte">REP-05 HISTORIAL POR PROVEEDOR</div>

            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-3">
                    <label for="anoReporte">Año del Reporte:</label>
                    <select id="anoReporte" class="form-control">
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ $i == $anoActual ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="codProv">Código de Proveedor:</label>
                    <input id="codProv" type="text" class="form-control" placeholder="Ej: P2221">
                </div>
                <div class="col-md-5" style="padding-top: 25px;">
                    <button type="button" class="btn btn-primary" id="btnBuscar">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                    <button type="button" class="btn btn-success" id="btnImprimirPDF">
                        <i class="fa fa-file-pdf-o"></i> Imprimir PDF
                    </button>
                </div>
            </div>

            <div class="box-proveedor">
                <div><strong>Proveedor:</strong> <span id="txtProveedor">-</span></div>
                <div><strong>Periodo:</strong> <span id="txtPeriodo">-</span></div>
            </div>

            <h5>Calificación por mes</h5>
            <table class="table-resumen" id="tablaMes">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Calificación</th>
                    </tr>
                </thead>
                <tbody id="tbodyMes">
                    <tr><td colspan="2" class="text-center">Seleccione filtros y haga clic en "Buscar"</td></tr>
                </tbody>
            </table>

            <h5>Detalle</h5>
            <table class="table table-bordered table-striped" id="tablaDetalle">
                <thead>
                    <tr>
                        <th>Rechazo</th>
                        <th>NE</th>
                        <th>Código Material</th>
                        <th>Material</th>
                        <th>UDM</th>
                        <th>Recibido</th>
                        <th>Rechazada</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>
</div>

<script>
    var csrfToken = '{{ csrf_token() }}';
    var anoActual = '{{ $anoActual }}';
</script>

@endsection

