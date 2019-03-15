@extends('home') 
@section('homecontent')


<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header">
                Reporte de Materia Prima
                <small>Entradas / Devoluciones / Notas Crédito</small>
            </h3>
            <h3></h3>
            <h4>Del: {{$fi.' al: '.$ff}}</h4>

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
                <h4>Entradas (Lerma)</h4>
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
                    @if(count($data)>0) @foreach ($data as $rep)
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
                    @endforeach @endif
                </tbody>
            </table>
        </div>
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
                        @if(count($data)>0) @foreach ($data as $rep)
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
                                {{$rep->Price}}
                            </td>
                            <td scope="row">
                                {{$rep->LineTotal}}
                            </td>
                            <td scope="row">
                                {{$rep->VatSum}}
                            </td>
                            <td scope="row">
                                {{$rep->LineTotal+$rep->VatSum }} {{$rep->DocCur}}
                            </td>
                            
                        </tr>
                        @endforeach @endif
                    </tbody>
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