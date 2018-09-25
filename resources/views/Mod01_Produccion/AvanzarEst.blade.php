@extends('home')

@section('homecontent')
<div class="container" >  
            <!-- Page Heading -->
            <div class="row">
                <div class="col-md-12">
                <div class="hidden-lg"><br><br></div>
                    <h3 class="page-header">
                 
                        <small>Traslados por Estación</small>
                    </h3>
                    <div class="visible-lg">
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard">  <a href="{!! url('home') !!}">Inicio</a></i>
                        </li>
                        <li>
                            <i class= "fa fa-archive"> <a href="{!! url('home/TRASLADO ÷ AREAS') !!}">Traslados</a></i>
                    </ol>
                    </div>
                </div>
            </div>  
        <div class="col-md-12"></div>
                <table style="border-collapse: separate;">
                    <thead>
                        <tr>
                        <th>Pedido</th>
                        <th>OP</th>
                        <th style="width:30%;" align="center"  scope="col">Descripción</th>
                        <th>Cantidad</th>
                        <th>Cliente</th>
                        <th>Fecha de Inicio</th>
                        <th>Fecha de Vencimiento</th>
                        <th>Dias en Estación</th>
                        <th>Avanzar OP</th>
                         </tr>
                    </thead>
                    @foreach ($EstacionOrden as $Avance)
                    <tbody>
            <td>{{$Avance->OriginNum}}</td>
            <td>{{$Avance->DocEntry}}</td>
            <td>{{$Avance->ItemName}}</td>
            <td>{{number_format($Avance->PlannedQty,0)}} </td>
            <td>{{$Avance->CardName}}</td>
            <td>{{$Avance->PostDate}}</td>
            <td>{{$Avance->DueDate}}</td>
            <td>{{(date_diff(new DateTime(date('Ymd h:m:s')), new DateTime($Avance->U_FechaHora)))->format('*98ji %a días')}}</td>
            <!--entra el boton borrar -->
            <td style="text-align:center;">
            <a href="" type="submit" class="btn btn-success btn-sm">
            <i class="fa fa-send-o" aria-hidden="true">   Avanzar</i></a>
            </td>
            
            </tbody>
@endforeach
            </table>
            
            </div>
</div>
</div>
</div>
@endsection 
