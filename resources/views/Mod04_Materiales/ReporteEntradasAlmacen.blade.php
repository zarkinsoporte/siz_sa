@extends('home') 
@section('homecontent')

<style>
    th, td, th{
        font-size: 10px;
        padding-bottom: 0px; 
        padding-top: 0px; 
        padding-left: 2px; 
        padding-right: 2px;
        cellspacing="0"; border="0"; celpadding="0"
    }
    table{
        margin : 0 0 10px 0; padding : 0 0 0 0; border-spacing : 0 0;
    }
</style>
<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header">
                Reporte de Materia Prima
                <small>Entradas / Devoluciones / Notas Crédito</small>
            </h3>
            <h4><b>Del:</b> {{\AppHelper::instance()->getHumanDate($fi)}} <b>al:</b> {{\AppHelper::instance()->getHumanDate($ff)}}</h4>

            <!-- <h5>Fecha & hora: {{date('d-m-Y h:i a', strtotime("now"))}}</h5> -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p align="right">
                <a href="../reporte/ENTRADAS ALMACEN/" target="_blank" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Reporte PDF</a>
                <a class="btn btn-success" href="entradasalmacenXLS"><i class="fa fa-file-excel-o"></i> Reporte XLS</a>

            </p>
        </div>
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            @if(count($data)>0)
            <table class="table table-striped" style="margin-bottom:0px">
                <h4>Entradas (Lerma)</h4>
                <?php
                    $index = 0;
                    $totalEntrada = 0;
                    $moneda = 'MXP';
                    $cadena_numerica = 4239.57; 
                    setlocale(LC_MONETARY,"es_MX");
                    $moneyformat = ""; 
                     
                ?>
                    @foreach ($data as $rep) @if($index == 0)
                    <?php
                        $DocN = $rep->DocNum; 
                        $totalEntrada = $rep->LineTotal + $rep->VatSum;
                    ?>
                        <thead class="table-condensed">
                            <tr>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">Entrada</th>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">Fecha</th>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">Cliente</th>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">Razón Social</th>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">Num. Factura</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td scope="row">
                                    {{$rep->DocNum}}
                                </td>
                                <td scope="row">
                                    {{date_format(date_create($rep->DocDate), 'd-m-Y')}}
                                </td>
                                <td scope="row">
                                    {{$rep->CardCode}}
                                </td>
                                <td align="center" scope="row">
                                    {{$rep->CardName}}
                                </td>
                                <td align="center" scope="row">
                                    {{$rep->NumAtCard}}
                                </td>
                            </tr>
                        </tbody>
            </table>
            <div class="col-md-11 col-md-offset-1">
                <table class="table table-striped" style="margin-top:0px">
                    <thead class="table-condensed">

                        <tr>
                            <td align="center" bgcolor="#cccccc"><b>Código</b> </td>
                            <td align="center" bgcolor="#cccccc"><b>Descripción</b></td>
                            <td align="center" bgcolor="#cccccc"><b>Cantidad</b></td>
                            <td align="center" bgcolor="#cccccc"><b>Precio</b></td>
                            <td align="center" bgcolor="#cccccc"><b>Monto</b></td>
                            <td align="center" bgcolor="#cccccc"><b>IVA</b></td>
                            <td align="center" bgcolor="#cccccc"><b>Total</b></th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr>
                            <td scope="row">
                                {{$rep->ItemCode}}
                            </td>
                            <td scope="row">
                                {{$rep->Dscription}}
                            </td>
                            <td scope="row">
                                {{$rep->Quantity*$rep->NumPerMsr}}
                            </td>
                            <td align="center" scope="row">
                                $ {{number_format($rep->Price,'2', '.',',')}}
                            </td>
                            <td scope="row">
                                $ {{number_format($rep->LineTotal,'2', '.',',')}}
                            </td>
                            <td scope="row">
                                $ {{number_format($rep->VatSum,'2', '.',',')}}
                            </td>
                            <td scope="row">
                                $ {{number_format($rep->LineTotal+$rep->VatSum,'2', '.',',')}} {{$rep->DocCur}}
                            </td>
                        </tr>
    @elseif($DocN == $rep->DocNum)
    <?php
        $totalEntrada += $rep->LineTotal + $rep->VatSum;
        $moneda = $rep->DocCur;
    ?>
                        <tr>
                            <td scope="row">
                                {{$rep->ItemCode}}
                            </td>
                            <td scope="row">
                                {{$rep->Dscription}}
                            </td>
                            <td scope="row">
                                {{$rep->Quantity*$rep->NumPerMsr}}
                            </td>
                            <td align="center" scope="row">
                               $ {{number_format($rep->Price,'2', '.',',')}} 
                            </td>
                            <td scope="row">
                               $ {{number_format($rep->LineTotal,'2', '.',',')}}
                            </td>
                            <td scope="row">
                               $ {{number_format($rep->VatSum,'2', '.',',')}}
                            </td>
                            <td scope="row">
                              $ {{number_format($rep->LineTotal+$rep->VatSum,'2', '.',',')}} {{$rep->DocCur}}                         
                            </td>
                        </tr>
@else
<tr>
    
    <td colspan="6" style="text-align: right">Total:</td>
    <td>$ {{number_format($totalEntrada,'2', '.',',')}} {{$moneda}}</td>
</tr>
<?php
    $DocN = $rep->DocNum;
    $totalEntrada = $rep->LineTotal + $rep->VatSum;
?>
                    </tbody>
                </table>
            </div>
            <!-- col-md-offset-1 -->
            <table class="table table-striped" style="margin-bottom:0px">
                <thead class="table-condensed">
                    <tr>
                        <th align="center" bgcolor="#474747" style="color:white" ; scope="col">Entrada</th>
                        <th align="center" bgcolor="#474747" style="color:white" ; scope="col">Fecha</th>
                        <th align="center" bgcolor="#474747" style="color:white" ; scope="col">Cliente</th>
                        <th align="center" bgcolor="#474747" style="color:white" ; scope="col">Razón Social</th>
                        <th align="center" bgcolor="#474747" style="color:white" ; scope="col">Num. Factura</th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td scope="row">
                            {{$rep->DocNum}}
                        </td>
                        <td scope="row">
                            {{date_format(date_create($rep->DocDate), 'd-m-Y')}}
                        </td>
                        <td scope="row">
                            {{$rep->CardCode}}
                        </td>
                        <td align="center" scope="row">
                            {{$rep->CardName}}
                        </td>
                        <td align="center" scope="row">
                            {{$rep->NumAtCard}}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="col-md-11 col-md-offset-1">
                <table class="table table-striped" style="margin-top:0px">
                    <thead class="table-condensed">

                        <tr>
                            <td align="center" bgcolor="#cccccc"><b>Código</b> </td>
                            <td align="center" bgcolor="#cccccc"><b>Descripción</b></td>
                            <td align="center" bgcolor="#cccccc"><b>Cantidad</b></td>
                            <td align="center" bgcolor="#cccccc"><b>Precio</b></td>
                            <td align="center" bgcolor="#cccccc"><b>Monto</b></td>
                            <td align="center" bgcolor="#cccccc"><b>IVA</b></td>
                            <td align="center" bgcolor="#cccccc"><b>Total</b></th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr>
                            <td scope="row">
                                {{$rep->ItemCode}}
                            </td>
                            <td scope="row">
                                {{$rep->Dscription}}
                            </td>
                            <td scope="row">
                                {{$rep->Quantity*$rep->NumPerMsr}}
                            </td>
                            <td align="center" scope="row">
                                $ {{number_format($rep->Price,'2', '.',',')}}
                            </td>
                            <td scope="row">
                                $ {{number_format($rep->LineTotal,'2', '.',',')}}
                            </td>
                            <td scope="row">
                                $ {{number_format($rep->VatSum,'2', '.',',')}}
                            </td>
                            <td scope="row">
                                $ {{number_format($rep->LineTotal+$rep->VatSum,'2', '.',',')}} {{$rep->DocCur}}
                            </td>
                        </tr>
                        @endif 
                        
                        @if($index == count($data)-1)
<tr>

    <td colspan="6" style="text-align: right">Total:</td>
    <td>$ {{number_format($totalEntrada,'2', '.',',')}} {{$moneda}}</td>
</tr>
                </table>
                @endif
                <?php
$index++;
   ?>
                    @endforeach @endif
            </div>



        </div>

    </div>
    <!-- /.container -->
@endsection
 
@section('homescript')
@endsection





    <script>
        function mostrar(){
                            $("#hiddendiv").show();
                        };

    </script>