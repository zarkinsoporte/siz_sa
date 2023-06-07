@extends('home')

@section('homecontent')
            
{!! Html::script('assets/js/Mod03_Compras/OrdenesCompra.js') !!}
{!! Html::script('assets/js/Mod03_Compras/OrdenesCompraNueva.js') !!}

{!! Html::script('assets/js/Mod03_Compras/buscadores-proveedor.js') !!}

<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css">
<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"></script>
{!! Html::style('assets/css/invoice.css') !!}
{!! Html::style('assets/css/customdt2.css') !!}

<div class="container" id="btnBuscadorOC">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header">
                ORDENES DE COMPRA
            </h3>                                        
        </div>
            
        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
    </div>
    <div class="row" id="panel-body-datos">
        <input type="text" style="display: none" class="form-control input-sm" id="input-cliente-id">
        <div class="container">                                                                                                                  
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
            
                        <div class="panel-body">
                            <div>
                                @if (false)
                                <div class="alert alert-info" role="alert">
                                    si hay alerta personalizada
                                </div>
                                @endif
                            </div>
                            <div class="row" style="margin-bottom: 40px">
                                <div class="form-group">
                                    <div class="col-md-3 col-xs-12 col-sm-4">
                                       <label><strong>
                                            <font size="2">Estado</font>
                                        </strong></label>
                                        <div class="">
                                            <select class="form-control selectpicker" id="sel-tipo-oc" name="sel-tipo-oc" data-size="8"
                                                data-style="btn-success">
                                                <option value="0">Abiertas</option>
                                                <option value="1">Cerradas</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12 col-sm-4">
                                        <label><strong>
                                                <font size="2">Buscar por OC</font>
                                            </strong></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Ingresa OC" id="input_oc">
                                            <span class="input-group-btn">
                                                <button class="btn btn-success" id="boton-mostrar-oc" type="button"><i
                                                class="fa fa-search"></i></button>
                                            </span>
                                        </div><!-- /input-group -->
                                    </div>
                                    <div class="col-md-3 col-xs-12 col-sm-4" style="">
                                        <label><strong>
                                                <font size="2">Buscar por Rango Fecha</font>
                                            </strong></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" autocomplete="off" id="input_date">
                                        <span class="input-group-btn">
                                            <button class="btn btn-success" id="boton-mostrar-calendar" type="button">
                                                <i class="fa fa-calendar"></i> </button>
                                        </span>
                                    </div><!-- /input-group -->
                                </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-scroll" id="registros-impresion">
                                    <table id="tableOC" class="table table-striped table-bordered hover" width="100%">
                                        <thead>
                                            <tr>
                                                
                                                <th>Acciones</th>
                                                <th>Código</th>
                                                <th>Provedor</th>
                                                <th>Estado OC</th>
                                                <th>Total</th>
                                                <th>Moneda</th>
                                                <th>Fecha</th>
                                                <th>Comentario</th>
                                                
                                            </tr>
                                        </thead>
                                    </table>
                                </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>                                                                                                 
        </div>                     
    </div>  <!-- /.row -->      

</div>   <!-- /.container -->

@include('Mod03_Compras/OrdenesCompras/OC_crear')
                  
@endsection
