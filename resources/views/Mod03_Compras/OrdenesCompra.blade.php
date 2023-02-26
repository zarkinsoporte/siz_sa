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
                                    <div class="col-md-1 col-xs-12 col-sm-6">
                                        <div class="input-group">
                                            <button type="button" class="form-control btn btn-primary m-r-5 m-b-5" id="boton-nuevo">
                                                <i class="fa fa-plus"></i>
                                                Nuevo</button>
                                        </div><!-- /input-group -->
                                    </div>
                                    
                                    <div class="col-md-3 col-xs-12 col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Ingresa OC" id="input_oc">
                                        <span class="input-group-btn">
                                            <button class="btn btn-success" id="boton-mostrar-oc" type="button"><i
                                            class="fa fa-search"></i></button>
                                        </span>
                                        </div><!-- /input-group -->
                                    </div>
                                    <div class="col-md-3 col-xs-12 col-sm-6" style="">
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
                                                <th>Elaboro</th>
                                                <th>Estado OC</th>
                                                <th>Total</th>
                                                <th>Moneda</th>
                                                <th>Fecha</th>
                                                
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

<div class="container" id="ordenesCompraOC" style="display:none">
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header">
                Orden de Compra Nueva
            </h3>
        </div>
    </div>
    <div class="panel panel-default">
      
        <div class="panel-body">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-2" id="selecpicker-cliente">
                        <label><strong>
                                <font size="2">Tipo OC</font>
                            </strong></label>
                        <select class="form-control selectpicker" id="sel-tipo-oc" name="sel-tipo-oc"
                        data-size = "8",
                        data-style="btn-success"
                        >
                           
                            <option value="0">Artículos</option>
                            <option value="1">Miscelaneos</option>
                           
                           
                        </select>
                    </div>
                    <div class="col-md-4" id="selecpicker-cliente">
                        <label><strong>
                                <font size="2">Proveedor</font>
                            </strong></label>
                       
                        <select data-live-search="true" class="form-control selectpicker" id="sel-proveedor" name="proveedor"
                        data-size = "8",
                        data-style="btn-success"
                        >
                           
                            <option value="">Selecciona una opción</option>
                            @foreach ($proveedores as $proveedor)
                            <option data-moneda="{{$proveedor->Currency}}" value="{{old('proveedor',$proveedor->CardCode)}}">
                                <span>{{$proveedor->CardCode}} &nbsp;&nbsp;&nbsp; {{$proveedor->CardName}}</span>
                            </option>
                            @endforeach
                           
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label><strong>
                                <font size="2">Fecha</font>
                            </strong></label>
                        <input type="text" class="form-control" id="input-fecha" placeholder="Selecciona una fecha" disabled
                            onblur="blurFecha()" />
                    </div>
                    <div class="col-md-2">
                        <label><strong>
                                <font size="2">Moneda</font>
                            </strong></label>
                        {!! Form::select('MON_MonedaId', (["" => "Selecciona una opción"] ), null, ['id' =>
                        'cboMoneda', 'class' => 'form-control selectpicker', "data-size" => "8",
                        "data-style"=>"btn-success", "data-live-search"=>"true"]) !!}
                    </div>
                    <div class="col-md-2" id="div-tipo-cambio">
                        <label><strong>
                        <font size="2">Tipo Cambio</font>
                            </strong></label>
                       <input type="number" class="form-control" name="" value="1" id="input_tc">
                       <input type="number" class="form-control" name="" value="1" id="input_tc_anterior" style="display: none">
                    </div>
                </div>
            </div>
           
            <div class="row">
                <div class="invoice">
                    <div class="invoice-header">
                        <div class="invoice-from">
                            <small>PROVEEDOR</small>
                            <address class="m-t-5 m-b-5">
                                <strong><span id="nombreProveedor"></span></strong></br>
                                <span id="direccionProveedor"></span></br>
                                <span id="codigoPostalProveedor"></span></br>
                                <span id="rfcProveedor"></span></br>
                                <span id="telefonicosProveedor"></span></br>
                                <span id="contactoProveedor"></span>
                            </address>
                        </div>
                        <div class="invoice-to">
                           
                        </div>
                        <div class="invoice-date">
                            <small>CÓDIGO</small>
                            <div class="date m-t-5"><span id="codigoOC">POR DEFINIR</span></div>
                            <small>ESTADO OC</small>
                            <div class="date m-t-5"><span id="estadoOC">Abierta</span></div>
                        </div>
                    </div>
                    <div class="invoice-content">
                        <ul id="menuPrincipal" class="nav nav-tabs nav-justified nav-justified-mobile"
                            data-sortable-id="index-2">
                            <li class="active">
                                <a id="liArtExist" href="#articulosExistentes" data-toggle="tab">
                                    <i class="fa fa-shopping-cart m-r-5"></i> 
                                    <span class="hidden-xs">Artículos Orden de Compra</span>
                                </a>
                            </li>
                           
                        </ul>
                        <div class="tab-content" data-sortable-id="index-3">
                            <!-- Inicio Artiulos Existentes -->
                            <div class="tab-pane fade active in" id="articulosExistentes">
                                <div id="articulosOC">
                                    <div data-scrollbar="true" style="height: 600px">
                                        @include('Mod03_Compras.TablaArticulosExistentesNueva')
                                    </div>
                                </div>
                            <!--</div>-->
                            <!-- Fin Artiulos Existentes -->
    
                            <!-- Inicio Artiulos Miscelaneos -->
                            <!--<div class="tab-pane fade" id="articulosMiscelaneos">-->
                                <div id="miscelaneosOC" style="">
                                    <div data-scrollbar="true" style="height: 600px">
                                        @include('Mod03_Compras.TablaArticulosMiscelaneosNueva')
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Artiulos Miscelaneos -->
                            
                            <!-- Inicio Resumen -->
                            <div class="tab-pane fade" id="resumen">
                                <div data-scrollbar="true" style="height: 600px">
                                    @include('Mod03_Compras.TablaResumenNueva')
                                </div>
                            </div>
                            <!-- Fin Resumen -->
                        </div>
                    </div>
    
                    <div class="invoice-price">
                        <div class="invoice-price-left">
                            <div class="invoice-price-row">
                                <div class="sub-price">
                                    <small># ARTICULOS</small>
                                    <div id="ordenCompra-articulos">0</div>
                                </div>
                            </div>
                            <div class="invoice-price-row pull-right">
                                <div class="sub-price" style="text-align: right;">
                                    <small>SUBTOTAL</small>
                                    <div id="ordenCompra-subtotal">0.00</div>
                                </div>
                                <div class="sub-price" id="oculta_signo_descuento">
                                    <i class="fa fa-minus"></i>
                                </div>
                                <div class="sub-price" id="oculta_descuento" style="text-align: right;">
                                    <small>DESCUENTO</small>
                                    <div id="ordenCompra-descuento">0.00</div>
                                </div>
                                <div class="sub-price">
                                    <i class="fa fa-plus"></i>
                                </div>
                                <div class="sub-price" style="text-align: right;">
                                    <small>I.V.A</small>
                                    <div id="ordenCompra-iva">0.00</div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-price-right">
                            <small>TOTAL</small>
                            <div id="ordenCompra-total">0.00</div>
                        </div>
                    </div>
    
                    <!--<div class="invoice-price" id="tc-totales">
                        <div class="invoice-price-left">
                            <div class="invoice-price-row">
                                <div class="sub-price">
                                    <small id="lbl-tc">(TC. 0.00)</small>
                                </div>
                            </div>
                            <div class="invoice-price-row pull-right">
                                <div class="sub-price">
                                    <small id="lbl-tc-subtotal">( 0.00)</small>
                                </div>
                                <div class="sub-price">
                                    <small><i class="fa fa-minus"></i></small>
                                </div>
                                <div class="sub-price">
                                    <small id="lbl-tc-descuento">( 0.00)</small>
                                </div>
                                <div class="sub-price">
                                    <small><i class="fa fa-plus"></i></small>
                                </div>
                                <div class="sub-price">
                                    <small id="lbl-tc-iva">( 0.00)</small>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-price-right">
                            <small style="position: inherit" id="lbl-tc-total">( 0.00)</small>
                        </div>
                    </div>-->
    
                </div>
    
            </div>
    
            <div class="invoice-footer text-muted right">
                <div class="pull-right">
                    <button type="button" class="btn btn-success m-r-5 m-b-5" id="guardar">Guardar</button>
                    <button type="button" class="btn btn-default m-r-5 m-b-5" id="boton-cerrar">Cerrar</button>
                </div>
            </div>
            
        </div>
    </div>
    <div class="row">
        @include('Mod03_Compras.OrdenesCompras.modalDetalles')
    </div>
    
    <div class="row">
        @include('Mod03_Compras.OrdenesCompras.modalBuscadorArticulo')
    </div>
</div>
                  
@endsection
