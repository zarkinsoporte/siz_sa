@extends('home')
@section('homecontent')
        <div class="container" >

            <!-- Page Heading -->
            <div class="row">

                    <div class="visible-xs"><br><br></div>
                                      
                       <div class= "col-md-10 col-sm-7 hidden-xs hidden-sm">
                            <h3 class="page-header">
                                    Descarga de Orden de Compra 
                                    <small>Compras</small>   
                                  </h3> 
                        <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="{!! url('home') !!}">Inicio</a>
                        </li>
                    </ol>
                 </div>
            </div> 
{!! Form::open(['url' => 'home/PEDIDOS CSV', 'method' => 'POST']) !!} 
<div class="row">
    <div class="col-md-10">
            @include('partials.alertas')
      </div>       
 </div>                          
   
 <div class="row">
        <div class="col-md-2">
            <h4 class="">Número de O.C:</h4>
        </div>    
    <div class="col-md-2">
            <div class="">
            
            <input name="NumOC" type="number" class="form-control" required min ="1">                                                  
            
       </div> 
        </div>
      <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Consultar</button></div> 
 </div>          
    {!! Form::close() !!} 
    <br>
    @if (isset($pedido))
    <?php $date=date_create($pedido[0]->FechOC); 
     ?>
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title">Información de la Orden de Compra: {{$pedido[0]->NumOC}}</h1>
                </div>
                <div class="panel-body">
                    <h5>Fecha de Orden:  {{date_format($date, 'd-m-Y')}}</h5>
                    <h5>Proveedor:  {{$pedido[0]->CodeProv.'  '.$pedido[0]->NombProv}}</h5>
                    <h5>Elaboro:  {{$pedido[0]->Elaboro}}</h5>
                    <h5>Comentarios: {{$pedido[0]->Comments}}</h5>
                </div>
             </div>
         </div>
    </div>  
        <div class="row">
        <div  class= "col-md-11"> 
                 <div class="text-right">
                     <a  class="btn btn-warning btn-sm"  href="desPedidosCsv"><i class="fa fa-file-text"></i>  Descarga CSV</a>
                     <a class="btn btn-danger btn-sm"  href="PedidosCsvPDF" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
                    </div>
                 </div>
            </div>
             <br>
        <div class="row">
        <div class="col-md-11">
        <div class="table-responsive">
            <table class="table table-condensed">
                <thead class="thead-dark">
                    <tr>
                        <th style="text-align: center;">Código</th>
                        <th style="text-align: center;">Descripción</th>
                        <th style="text-align: center;">Versión</th>
                        <th style="text-align: center;">Cantidad Total</th>
                        <th style="text-align: center;">Cant. Pendiente</th>
                        <th style="text-align: center;">Entrega</th>
                        <th style="text-align: center;">Precio Unitario</th>      
                        <th style="text-align: center;">Moneda</th>     
                    </tr>
                 </thead>
               <tbody>     
            @foreach ($pedido as $pedi)
                    <tr>
                    <?php 
                     $dat=date_create($pedido[0]->FechEnt); 
                     ?>
                        <td style="text-align: center;">{{$pedi->Codigo}}</td>
                        <td>{{$pedi->Descrip}}</td>
                        <td style="text-align: center;" >{{$pedi->ValidComm}}</td>
                        <td style="text-align: center;">{{number_format($pedi->CantTl,2)}}</td>
                        <td style="text-align: center;">{{number_format($pedi->CantPend,2)}}</td>
                        <td style="text-align: center;">{{date_format($dat, 'd-m-Y')}}</td>
                        <td style="text-align: center;">{{"$ ".number_format($pedi->Price,4)}}</td>
                        <td style="text-align: center;">{{$pedi->Currency}}</td>                                                    
                    </tr>
            @endforeach
            </tbody>
    </table>
    </div>
   </div>  
</div>
            <br> 
             <!--<div class="row">
                    <div class="col-md-10">
                        <h3>Calidad</h3>
                            <table>
                                         <tr>
                                            <th>Empleado:</th>
                                            <td>{{0}}</td>
                                          </tr>
                                          <tr>
                                            <th>% de Calidad:</th>
                                            <td>{{0}}</td>
                                          </tr>
                                          <tr>
                                            <th>Bono:</th>
                                            <td>{{0}}</td>
                                          </tr>

                                </table>
                    </div>  /.col md 
                </div> /.row 
            <div class="row">
            <div class="col-md-10">
                <h3>Totales</h3>
                    <table>
                                 <tr>
                                    <th>Empleado:</th>
                                    <td>{{0}}</td>
                                     <td>{{0}}</td>
                                     <td>{{0}}</td> 
                                     <td>{{0}}</td>
                                      <td>{{0}}</td>
                                  </tr>
                                  <tr>
                                    <th>Total Bono:</th>
                                    <td>{{0}}</td>
                                  </tr>
                        </table>
            </div>
        </div>
        <h3></h3>-->
                    </div>
    </div>    
                   
           
 @endif

@endsection

@section('homescript')
window.TrelloBoards.load(document, { allAnchors: false });
@endsection
