
@extends('home')

@section('homecontent')
<div class="container" >

<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12">
    <div class="hidden-lg"><br><br></div>
        <h3 class="page-header">
           Traslados
            <small>Producción</small>
        </h3>
        <div class="visible-lg">
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard">  <a href="{!! url('home') !!}">Inicio</a></i>
            </li>
            <li>
                <i class= "fa fa-archive"> <a href="traslados">Traslados</a></i>
        </ol>
        </div>
    </div>
</div>
<!-- /.row -->

    <style>
    th{
        font-family:'Helvetica';
        font-size:90%;
       
        text-align:center;
    }
    td{
        font-family:'Helvetica';
        font-size:80%;
        
    }

    </style>
<div class="container" >
<div class="row" scrolling="yes">
        <div class="col-md-12">
                <table border="1px">
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
            <td>{{$Avance->ItemName}}</td>
            <td>{{$Avance->DocEntry}}</td>
            <td>{{$Avance->U_Reproceso}}</td>
            <td>{{number_format($Avance->PlannedQty,0)}}</td>
            <td>{{$Avance->OriginNum}}</td>
            <td>{{$Avance->Status}}</td>
            <td>{{$Avance->U_Recibido}}</td>
            <td>{{$Avance->U_Procesado}}</td>
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

@endsection
