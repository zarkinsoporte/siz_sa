@extends('home')
@section('homecontent')

{!! Html::style('assets/css/customdt2.css') !!}
{!! Html::script('assets/js/Reportes_IncomingController/index_rep09.js?v='.time()) !!}

<style>
    .box-info { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 15px; }
    
    .tarjeta-estacion {
        margin-bottom: 25px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .tarjeta-header {
        padding: 10px 15px;
        font-weight: bold;
        font-size: 15px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .tarjeta-header .meta-badge {
        background: rgba(255,255,255,0.25);
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 13px;
    }
    
    .tarjeta-body { padding: 0; }
    
    .tabla-incentivos {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        margin: 0;
    }
    
    .tabla-incentivos th {
        background-color: #f1f3f5;
        border: 1px solid #dee2e6;
        padding: 6px 8px;
        text-align: center;
        font-weight: bold;
        white-space: nowrap;
        font-size: 12px;
    }
    
    .tabla-incentivos td {
        border: 1px solid #dee2e6;
        padding: 5px 8px;
        text-align: center;
        vertical-align: middle;
        font-size: 12px;
    }
    
    .tabla-incentivos tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    .tabla-incentivos td.text-left { text-align: left; }
    
    .tabla-incentivos .sem-cumple {
        background-color: #d4edda !important;
        color: #155724;
        font-weight: bold;
    }
    
    .tabla-incentivos .sem-no-cumple {
        background-color: #f8d7da !important;
        color: #721c24;
        font-weight: bold;
    }
    
    .tabla-incentivos .sem-sin-datos {
        background-color: #f8f9fa;
        color: #adb5bd;
    }
    
    /* Colores de cabecera por estación */
    .header-corte { background-color: #c0392b; }
    .header-costura { background-color: #2980b9; }
    .header-cojineria { background-color: #27ae60; }
    .header-tapiceria { background-color: #e67e22; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header" id="titulo">
                REP-09 RESUMEN DE INCENTIVOS
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="yearPicker">Año:</label>
                                    <input type="text" id="yearPicker" class="form-control" readonly value="{{ $anoActual }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="mesPicker">Mes:</label>
                                    <select id="mesPicker" class="form-control">
                                        <option value="1" {{ $mesActual == '01' ? 'selected' : '' }}>Enero</option>
                                        <option value="2" {{ $mesActual == '02' ? 'selected' : '' }}>Febrero</option>
                                        <option value="3" {{ $mesActual == '03' ? 'selected' : '' }}>Marzo</option>
                                        <option value="4" {{ $mesActual == '04' ? 'selected' : '' }}>Abril</option>
                                        <option value="5" {{ $mesActual == '05' ? 'selected' : '' }}>Mayo</option>
                                        <option value="6" {{ $mesActual == '06' ? 'selected' : '' }}>Junio</option>
                                        <option value="7" {{ $mesActual == '07' ? 'selected' : '' }}>Julio</option>
                                        <option value="8" {{ $mesActual == '08' ? 'selected' : '' }}>Agosto</option>
                                        <option value="9" {{ $mesActual == '09' ? 'selected' : '' }}>Septiembre</option>
                                        <option value="10" {{ $mesActual == '10' ? 'selected' : '' }}>Octubre</option>
                                        <option value="11" {{ $mesActual == '11' ? 'selected' : '' }}>Noviembre</option>
                                        <option value="12" {{ $mesActual == '12' ? 'selected' : '' }}>Diciembre</option>
                                    </select>
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
            <div><strong>Mes:</strong> <span id="txtMes">-</span></div>
            <div><strong>Periodo:</strong> <span id="txtPeriodo">-</span></div>
        </div>

        <!-- Contenedor de tarjetas de estaciones -->
        <div id="containerEstaciones">
            <p class="text-center text-muted">Seleccione año y mes, luego haga clic en "Buscar"</p>
        </div>

        </div>
    </div>
</div>

<script>
    var csrfToken = '{{ csrf_token() }}';
    var anoActual = '{{ $anoActual }}';
    var mesActual = '{{ intval($mesActual) }}';
</script>

@endsection
