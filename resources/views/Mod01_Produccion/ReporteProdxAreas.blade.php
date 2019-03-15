@extends('home') 
@section('homecontent')
<style>
    
.table-scroll {
  position: relative;
  min-width: 90%;
  margin-left: 10px; 
}
.table-scroll thead th {
  background: #333;
  color: #fff;
  position: -webkit-sticky;
  position: sticky;
  top: 0;
}
th:first-child {
  position: -webkit-sticky;
  position: sticky;
  left: 0;
  z-index: 2;
  background:#333;
}
thead th:first-child,
tfoot th:first-child {
  z-index: 5;
}
.pane { 
    overflow: auto;
    max-height:250px;
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
            <h4>Del: {{date_format(date_create($fi), 'd-m-Y').' al: '.date_format(date_create($ff), 'd-m-Y')}}</h4>

            <!-- <h5>Fecha & hora: {{date('d-m-Y h:i a', strtotime("now"))}}</h5> -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-11">
            <p align="right">
                <a href="../ReporteMaterialesPDF" target="_blank" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Reporte PDF</a>
                <a class="btn btn-success" href="materialesXLS"><i class="fa fa-file-excel-o"></i> Reporte XLS</a>
            </p>
        </div>
    </div>
    <div class="row"> 
        <div class="col-md-11">
        <h4>Reporte de Fundas (Planeación-Corte)</h4>
        </div>
        <div  id="table-scroll" class="col-md-11 table-scroll">
            <div class="pane">
                    <table id="main-table" class="table table-striped main-table" style="margin-bottom:0px">
               
                            <thead class="table-condensed">
                                <tr>
                                    <th scope="col" style="min-width:150px;">Fecha</th>
                                    <th scope="col" style="width: 200px;">Ordenes en Planeaciòn</th>  
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
                                    <th scope="row"  class="table-condensed">
                                        {{\AppHelper::instance()->getHumanDate($rep->Fecha)}}
                                    </th>
                                    <td scope="row">
                                        {{number_format($rep->VST100,2) }}
                                    </td>
                                    <td scope="row">
                                        {{number_format($rep->VST106,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST109,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST112,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST115,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST118,2)}}
                                    </td>
                                    <td scope="row">
                                        {{number_format($rep->VST121,2)}} 
                                    </td>
                                    <td scope="row">
                                        {{number_format($rep->VST124,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST127,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST130,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST133,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST136,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST139,2)}}
                                    </td>
                                    <td scope="row">
                                        {{number_format($rep->VST145,2)}} 
                                    </td>
                                    <td scope="row">
                                        {{number_format($rep->VST148,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST151,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST154,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST157,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST160,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST172,2)}}
                                    </td>
                                    <td  scope="row">
                                        {{number_format($rep->VST175,2)}}
                                    </td>
                                </tr>
                                @endforeach @endif
                            </tbody>
                        </table>
            </div>
        </div> <!-- /.col-md-8 -->
    </div> <!-- /.row -->
    <div class="row">
        <div class="col-md-8">
            <table class="table table-striped" style="margin-bottom:0px">
                <h4>Reporte de Fundas (Costura)</h4>
                <thead class="table-condensed">
                    <tr>						

                       

                    </tr>
                </thead>
                <tbody>
                    @if(count($data)>0) @foreach ($data as $rep)
                    <tr>
                        <td scope="row">
                            {{\AppHelper::instance()->getHumanDate($rep->Fecha)}}
                        </td>
                        
                    </tr>
                    @endforeach @endif
                </tbody>
            </table>
        </div> <!-- /.col-md-8 -->
    </div> <!-- /.row -->
    <div class="row">
        <div class="col-md-8">
            <table class="table table-striped" style="margin-bottom:0px">
                <h4>Reporte de Fundas (Cojinería y Tapicería)</h4>
                <thead class="table-condensed">
                    <tr>						                        							
                        <th>Fecha</th>
                       
                    </tr>
                </thead>
                <tbody>
                    @if(count($data)>0) @foreach ($data as $rep)
                    <tr>
                        <td scope="row">
                            {{\AppHelper::instance()->getHumanDate($rep->Fecha)}}
                        </td>
                     
                    </tr>
                    @endforeach @endif
                </tbody>
            </table>
        </div> <!-- /.col-md-8 -->
    </div> <!-- /.row -->
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