@extends('home')

            @section('homecontent')
            
    {!! Html::script('assets/js/Mod03_Compras/OrdenesCompra.js') !!}
    {!! Html::style('assets/css/customdt2.css') !!}
                <div class="container" >

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
                                                                <button class="btn btn-success" id="boton-mostrar" type="button">
                                                                    <i class="fa fa-calendar"></i> </button>
                                                            </span>
                                                        </div><!-- /input-group -->
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                       <div class="table-scroll" id="registros-impresion">
                                                        <table id="tabla_impresion" class="table table-striped table-bordered hover" width="100%">
                                                            <thead>
                                                                <tr>
                                                                    
                                                                    <th>Acción</th>
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
                    
                    
                @endsection
