@extends('home') 
@section('homecontent')

<style>
    th, td{
        font-size: 12px;
    }
    
    .table{
        width: auto;
        margin-bottom:0px;
    }
    .detalle {
     margin-left: 3%;
    }
    .table > thead > tr > th, 
    .table > tbody > tr > th, 
    .table > tfoot > tr > th, 
    .table > thead > tr > td, 
    .table > tbody > tr > td,
    .table > tfoot > tr > td { 
        padding-bottom: 2px; padding-top: 2px; padding-left: 4px; padding-right: 0px;
    }
    .total{
        text-align: right; 
        padding-right:4px;
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
            <table class="table table-striped" style="margin-bottom:0px">
            @if(count($data)>0)
            
                <h4>Entradas (Lerma)</h4>
                <?php
                    $index = 0;
                    $totalEntrada = 0;
                    $moneda = 'MXP';
                     
                ?>
                    @foreach ($data as $rep) @if($index == 0)
                    <?php
                        $DocN = $rep->DocNum; 
                        $totalEntrada = $rep->LineTotal + $rep->VatSum;
                    ?>
                        <thead class="table-condensed">
                            <tr>
                                <th style="width:110px" class="zrk-gris" scope="col">Entrada</th>
                                <th style="width:120px" class="zrk-gris" scope="col">Fecha</th>
                                <th style="width:110px" class="zrk-gris" scope="col">Cliente</th>
                                <th style="width:457px" class="zrk-gris" scope="col" colspan="4">Razón Social</th>
                                <th style="width:120px" class="zrk-gris" scope="col">Num. Factura</th>

                            </tr>
                             <tr>
                                <th style="width:60px" class="zrk-gris-claro">Código</th>
                                <th style="width:450px" class="zrk-gris-claro" colspan="2">Descripción</th>
                                <th style="width:57px" class="zrk-gris-claro">Cantidad</th>
                                <th style="width:70px" class="zrk-gris-claro">Precio</th>
                                <th style="width:70px" class="zrk-gris-claro">Monto</th>
                                <th style="width:70px" class="zrk-gris-claro">IVA</th>
                                <th style="width:100px" class="zrk-gris-claro">Total</th>
                        </tr>
                        <tr><td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td></tr>
                        </thead>
                       
                        <tbody>
                            <tr>
                                <td style="width:110px" class="zrk-silver-w" scope="row">
                                    {{$rep->DocNum}}
                                </td>
                                <td style="width:120px" class="zrk-silver-w" scope="row">
                                    {{date_format(date_create($rep->DocDate), 'd-m-Y')}}
                                </td>
                                <td style="width:110px" class="zrk-silver-w" scope="row">
                                    {{$rep->CardCode}}
                                </td>
                                <td style="width:457px" class="zrk-silver-w" scope="row" colspan="4">
                                    {{$rep->CardName}}
                                </td>
                                <td style="width:120px" class="zrk-silver-w" scope="row">
                                    {{$rep->NumAtCard}}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:60px" class="zrk-gris-claro" scope="row">
                                    {{$rep->ItemCode}}
                                </td>
                                <td style="width:450px" class="zrk-gris-claro" scope="row" colspan="2">
                                    {{$rep->Dscription}}
                                </td>
                                <td style="width:57px" class="zrk-gris-claro" scope="row">
                                    {{$rep->Quantity*$rep->NumPerMsr}}
                                </td>
                                <td style="width:70px" class="zrk-gris-claro" scope="row">
                                    ${{number_format($rep->Price,'2', '.',',')}}
                                </td>
                                <td style="width:70px" class="zrk-gris-claro" scope="row">
                                    ${{number_format($rep->LineTotal,'2', '.',',')}}
                                </td>
                                <td style="width:70px" class="zrk-gris-claro" scope="row">
                                    ${{number_format($rep->VatSum,'2', '.',',')}}
                                </td>
                                <td style="width:100px" class="zrk-gris-claro" scope="row">
                                    ${{number_format($rep->LineTotal+$rep->VatSum,'2', '.',',')}} {{$rep->DocCur}}
                                </td>
                            </tr>
                       
                        
    @elseif($DocN == $rep->DocNum)
    <?php
        $totalEntrada += $rep->LineTotal + $rep->VatSum;
        $moneda = $rep->DocCur;
    ?>
                        <tr>
                           <td style="width:60px" class="zrk-gris-claro" scope="row">
                                {{$rep->ItemCode}}
                            </td>
                            <td style="width:450px" class="zrk-gris-claro" scope="row" colspan="2">
                                {{$rep->Dscription}}
                            </td>
                            <td style="width:57px" class="zrk-gris-claro" scope="row">
                                {{$rep->Quantity*$rep->NumPerMsr}}
                            </td>
                            <td style="width:70px" class="zrk-gris-claro" scope="row">
                                ${{number_format($rep->Price,'2', '.',',')}}
                            </td>
                            <td style="width:70px" class="zrk-gris-claro" scope="row">
                                ${{number_format($rep->LineTotal,'2', '.',',')}}
                            </td>
                            <td style="width:70px" class="zrk-gris-claro" scope="row">
                                ${{number_format($rep->VatSum,'2', '.',',')}}
                            </td>
                            <td style="width:100px" class="zrk-gris-claro" scope="row">
                                ${{number_format($rep->LineTotal+$rep->VatSum,'2', '.',',')}} {{$rep->DocCur}}
                            </td>
                        </tr>
@else
<tr>
    
    <td colspan="7" class="total zrk-gris-claro">Total:</td>
    <td class="zrk-gris-claro">${{number_format($totalEntrada,'2', '.',',')}} {{$moneda}}</td>
</tr>
<?php
    $DocN = $rep->DocNum;
    $totalEntrada = $rep->LineTotal + $rep->VatSum;
?>
         
                    <tr>
                        <td style="width:110px" class="zrk-silver-w" scope="row">
                            {{$rep->DocNum}}
                        </td>
                        <td style="width:120px" class="zrk-silver-w" scope="row">
                            {{date_format(date_create($rep->DocDate), 'd-m-Y')}}
                        </td>
                        <td style="width:110px" class="zrk-silver-w" scope="row">
                            {{$rep->CardCode}}
                        </td>
                        <td style="width:457px" class="zrk-silver-w" scope="row" colspan="4">
                            {{$rep->CardName}}
                        </td>
                        <td style="width:120px" class="zrk-silver-w" scope="row">
                            {{$rep->NumAtCard}}
                        </td>
                    </tr>
                    <tr>
                        <td style="width:60px" class="zrk-gris-claro" scope="row">
                            {{$rep->ItemCode}}
                        </td>
                        <td style="width:450px" class="zrk-gris-claro" scope="row" colspan="2">
                            {{$rep->Dscription}}
                        </td>
                        <td style="width:57px" class="zrk-gris-claro" scope="row">
                            {{$rep->Quantity*$rep->NumPerMsr}}
                        </td>
                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                            ${{number_format($rep->Price,'2', '.',',')}}
                        </td>
                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                            ${{number_format($rep->LineTotal,'2', '.',',')}}
                        </td>
                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                            ${{number_format($rep->VatSum,'2', '.',',')}}
                        </td>
                        <td style="width:100px" class="zrk-gris-claro" scope="row">
                            ${{number_format($rep->LineTotal+$rep->VatSum,'2', '.',',')}} {{$rep->DocCur}}
                        </td>
                    </tr>
                        @endif 
                        
                        @if($index == count($data)-1)
<tr>

    <td colspan="7" class="total">Total:</td>
    <td>${{number_format($totalEntrada,'2', '.',',')}} {{$moneda}}</td>
</tr>
                
                @endif
                <?php
$index++;
   ?>
                    @endforeach @endif
                    </table>
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