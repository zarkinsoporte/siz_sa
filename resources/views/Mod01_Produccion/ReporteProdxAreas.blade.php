@extends('home') 
@section('homecontent')
<style>
    .table-scroll {
        position: relative;
        min-width: 90%;
        margin-left: 10px;
        z-index: 1;        
        overflow: scroll;
    }

    .table-scroll thead th {
        background: #333;
        color: #fff;
        position: -webkit-sticky;
        position: sticky;
        top: 0;
    }
    .table-scroll tfoot,
    .table-scroll tfoot th,
    .table-scroll tfoot td {
    position: -webkit-sticky;
    position: sticky;
    bottom: 0;
    background: #333;
    color: #fff;
    z-index:4;
    }  
    th:first-child {
  position: -webkit-sticky;
  position: sticky;
  left: 0;
  z-index: 2;
  background: #333;
}
    thead th:first-child,
    tfoot th:first-child {
        z-index: 5;
    }

    .pane {
        overflow: auto;
        max-height: 300px;
    }
    
</style>

<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <div class="visible-xs"><br><br></div>
            <h3 class="page-header">
                Reporte de Producción por Áreas
                <small>Producción</small>
            </h3>
            <h3></h3>
            <h4><b>Del:</b> {{\AppHelper::instance()->getHumanDate($fi)}} <b>al:</b> {{\AppHelper::instance()->getHumanDate($ff)}}</h4>

            <!-- <h5>Fecha & hora: {{date('d-m-Y h:i a', strtotime("now"))}}</h5> -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-11">
            <p align="right">
                <a href="../reporte/PRODUCCION POR AREAS" target="_blank" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Reporte PDF</a>
                <a class="btn btn-success" href="produccionxareasXLS"><i class="fa fa-file-excel-o"></i> Reporte XLS</a>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-11">
            <h4>Reporte de Fundas</h4>
        </div>
        <div id="t1" class="col-md-11 table-scroll">
            <div class="pane">
                <table id="main-table" class="table table-striped main-table" style="margin-bottom:0px">

                    <thead class="table-condensed">
                        <tr class="encabezado">
                            <th scope="col" style="min-width:150px;">Fecha</th>
                            <th scope="col">Ordenes en Planeación</th>
                            <th scope="col">Preparado Entrega</th>
                            <th scope="col">Anaquel Corte</th>
                            <th scope="col">Corte de Piel</th>
                            <th scope="col">Inspección de Corte</th>
                            <th scope="col">Pegado de Costura</th>
                            <th scope="col">Anaquel Costura</th>
                            <th scope="col">Costura Recta</th>
                            <th scope="col">Armado de Costura</th>
                            <th scope="col">Pespunte o Doble</th>
                            <th scope="col">Terminado de Costura</th>
                            <th scope="col">Inspeccionar Costura</th>
                            <th scope="col">Series Incompletas</th>
                            <th scope="col">Acojinado</th>
                            <th scope="col">Fundas Terminadas</th>
                            <th scope="col">Kitting</th>
                            <th scope="col">Enfundado Tapiz</th>
                            <th scope="col">Tapizar</th>
                            <th scope="col">Armado de Tapiz</th>
                            <th scope="col">Empaque</th>
                            <th scope="col">Inspeccion Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($data)>0) @foreach ($data as $rep)
                        <tr>
                            <th id="f0" scope="row" class="table-condensed">
                                {{\AppHelper::instance()->getHumanDate($rep->Fecha)}}
                            </th>
                            <td id="f1" scope="row">
                                {{number_format($rep->VST100,2)}}
                            </td>
                            <td id="f2" scope="row">
                                {{number_format($rep->VST106,2)}}
                            </td>
                            <td id="f3" scope="row">
                                {{number_format($rep->VST109,2)}}
                            </td>
                            <td id="f4" scope="row">
                                {{number_format($rep->VST112,2)}}
                            </td>
                            <td id="f5" scope="row">
                                {{number_format($rep->VST115,2)}}
                            </td>
                            <td id="f6" scope="row">
                                {{number_format($rep->VST118,2)}}
                            </td>
                            <td id="f7" scope="row">
                                {{number_format($rep->VST121,2)}}
                            </td>
                            <td id="f8" scope="row">
                                {{number_format($rep->VST124,2)}}
                            </td>
                            <td id="f9" scope="row">
                                {{number_format($rep->VST127,2)}}
                            </td>
                            <td id="f10" scope="row">
                                {{number_format($rep->VST130,2)}}
                            </td>
                            <td id="f11" scope="row">
                                {{number_format($rep->VST133,2)}}
                            </td>
                            <td id="f12" scope="row">
                                {{number_format($rep->VST136,2)}}
                            </td>
                            <td id="f13" scope="row">
                                {{number_format($rep->VST139,2)}}
                            </td>
                            <td id="f14" scope="row">
                                {{number_format($rep->VST145,2)}}
                            </td>
                            <td id="f15" scope="row">
                                {{number_format($rep->VST148,2)}}
                            </td>
                            <td id="f16" scope="row">
                                {{number_format($rep->VST151,2)}}
                            </td>
                            <td id="f17" scope="row">
                                {{number_format($rep->VST154,2)}}
                            </td>
                            <td id="f18" scope="row">
                                {{number_format($rep->VST157,2)}}
                            </td>
                            <td id="f19" scope="row">
                                {{number_format($rep->VST160,2)}}
                            </td>
                            <td id="f20" scope="row">
                                {{number_format($rep->VST172,2)}}
                            </td>
                            <td id="f21" scope="row">
                                {{number_format($rep->VST175,2)}}
                            </td>
                        </tr>
                        @endforeach @endif
                    </tbody>
                    <tfoot>
                        <tr class="total1">
                            <th scope="row" class="table-condensed">SUMA DE FUNDAS:</th>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr  class="encabezado">
                            <th scope="row" class="table-condensed">INVENTARIO:</th>
                            @foreach ($data2 as $item)                          
                                <td scope="row">
                                    {{number_format($item->SVS,2)}}
                                </td>                             
                            @endforeach                                          
                        </tr>
                    </tfoot>
                </table>
                <table class="table table-striped main-table" style="margin-bottom:0px">
                
                </table>
            
            </div>
        </div>
        <!-- /.col-md-8 -->
    </div>
    <!-- /.row -->
    <div class="row">
            <div class="col-md-11">
                <h4>Reporte de Cascos</h4>
            </div>
            <div id="t2" class="col-md-11 table-scroll">
                <div class="pane">
                    <table id="main-table" class="table table-striped main-table" style="margin-bottom:0px">
    
                        <thead class="table-condensed">
                            <tr class="encabezado">
                                <th scope="col" style="min-width:150px;">Fecha</th>
                                <th scope="col">Planeación</th>
                                <th scope="col">Habilitado</th>
                                <th scope="col">Armado</th>
                                <th scope="col">Tapado</th>
                                <th scope="col">Pegado</th>
                                <th scope="col">Entrega</th>                              
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($data3)>0) @foreach ($data3 as $rep3)
                            <tr>
                                <th id="f0" scope="row" class="table-condensed">
                                    {{\AppHelper::instance()->getHumanDate($rep3->Fecha)}}
                                </th>
                                <td id="f1" scope="row">
                                    {{number_format($rep3->VST400,2)}}
                                </td>
                                <td id="f2" scope="row">
                                    {{number_format($rep3->VST403,2)}}
                                </td>
                                <td id="f3" scope="row">
                                    {{number_format($rep3->VST406,2)}}
                                </td>
                                <td id="f4" scope="row">
                                    {{number_format($rep3->VST409,2)}}
                                </td>
                                <td id="f5" scope="row">
                                    {{number_format($rep3->VST415,2)}}
                                </td>                               
                                <td id="f6" scope="row">
                                    {{number_format($rep3->VST418,2)}}
                                </td>                               
                            </tr>
                            @endforeach @endif
                        </tbody>
                        <tfoot>
                            <tr class="total2">
                                <th scope="row" class="table-condensed">SUMA DE CASCOS:</th>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>                                
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- /.col-md-8 -->
        </div>
        <!-- /.row -->
        <div class="row">
                <div class="col-md-11">
                    <h4>Movimientos de Cascos</h4>
                </div>
                <div id="t3" class="col-md-11 table-scroll">
                    <div class="pane">
                        <table id="main-table" class="table table-striped main-table" style="margin-bottom:0px">
        
                            <thead class="table-condensed">
                                <tr class="encabezado">
                                    <th scope="col" style="min-width:150px;">Fecha</th>
                                    <th scope="col">Carpintería</th>
                                    <th scope="col">Almacén</th>
                                    <th scope="col">Camión</th>
                                    <th scope="col">Kitting</th>
                                    <th scope="col">Tapiz</th>
                                    <th scope="col">Ajuste</th>                                   
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($data4)>0) @foreach ($data4 as $rep4)
                                <tr>
                                    <th id="f0" scope="row" class="table-condensed">
                                        {{\AppHelper::instance()->getHumanDate($rep4->Fecha)}}
                                    </th>
                                    <td id="f1" scope="row">
                                        {{number_format($rep4->S_CARP,2)}}
                                    </td>
                                    <td id="f2" scope="row">
                                        {{number_format($rep4->S_TRAS,2)}}
                                    </td>
                                    <td id="f3" scope="row">
                                        {{number_format($rep4->S_KITT,2)}}
                                    </td>
                                    <td id="f4" scope="row">
                                        {{number_format($rep4->S_TAPI,2)}}
                                    </td>
                                    <td id="f5" scope="row">
                                        0.00
                                    </td>
                                    <td  scope="row">
                                        {{number_format((($rep4->S_TAPI + $rep4->S_KITT + $rep4->S_TRAS + $rep4->S_CARP)*-1) + $rep4->S_VST  ,2)}}
                                    </td>                                                                   
                                </tr>
                                @endforeach @endif
                            </tbody>
                            <tfoot>
                                <tr class="total3">
                                    <th scope="row" class="table-condensed">SUMA DE CASCOS:</th>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                   
                                </tr>
                                <tr  class="encabezado">
                                        <th scope="row" class="table-condensed">INVENTARIO CASCO:</th>
                                        @foreach ($data5 as $item)                          
                                            <td scope="row">
                                                {{number_format($item->T_CARP,2)}}
                                            </td>                             
                                            <td scope="row">
                                                {{number_format($item->T_ALMA,2)}}
                                            </td>                             
                                            <td scope="row">
                                                {{number_format($item->T_CAMI,2)}}
                                            </td>                             
                                            <td scope="row">
                                                {{number_format($item->T_KITT,2)}}
                                            </td>                             
                                            <td scope="row">
                                                {{number_format($item->T_TAPI,2)}}
                                            </td>                             
                                            <td scope="row">
                                               
                                            </td>                             
                                        @endforeach                                          
                                    </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <!-- /.col-md-8 -->
            </div><!-- /.row -->
</div>
<br>
<!-- /.container -->
@endsection
 
@section('homescript') 
CalcularTotal();
@endsection

<script>
    function CalcularTotal()
    {
        var totals = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        var $filas= $("#t1 tr:not('.total1, .encabezado')");
        $filas.each(function() {
            $(this).find('td').each(function(i) {       
                totals[i] += parseFloat($(this).html());        
            });
        });
        $(".total1 td").each(function(i) {
            $(this).html(totals[i].toFixed(2));
        });

        totals = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $filas= $("#t2 tr:not('.total2, .encabezado')");
        $filas.each(function() {
            $(this).find('td').each(function(i) {       
                totals[i] += parseFloat($(this).html());        
            });
        });
        $(".total2 td").each(function(i) {
            $(this).html(totals[i].toFixed(2));
        });

        totals = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $filas= $("#t3 tr:not('.total3, .encabezado')");
        $filas.each(function() {
            $(this).find('td').each(function(i) {       
                totals[i] += parseFloat($(this).html());        
            });
        });
        $(".total3 td").each(function(i) {
            $(this).html(totals[i].toFixed(2));
        });
    }
    function mostrar()
    {
        $("#hiddendiv").show();
    };
</script>