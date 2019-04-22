array_get($fechas_entradas,'fi')


<!-- html5 no soporta <td align="right">$80</td> -->
<td class='alnright'>text to be aligned to right</td>
<style>
    .alnright { text-align: right; }
</style>

en cuidado con DOUBLE, porque no se maneja igual que DECIMAL. Double es un punto flotante, es decir un numero decimal por
aproximación, mientras que DECIMAL es de precisión. Esto puede hacer que se generen redondeos con el DOUBLE que terminen
ocasionado desfasajes en los cálculos, lo que no ocurrirá si usas DECIMAL. El manual recomienda DECIMAL para los valores
monetarios

{{date_format(date_create($rep->DocDate), 'd-m-Y')}}

$ {{number_format($totalEntrada,'2', '.',',')}} MXP



.table > tbody > tr > td
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {

    padding: 0px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;

}

table {

    width: 100%;
width: auto;
}


DocNum
                               DocDate
                               CardCode
                               CardName
                               NumAtCard}}
                              
                            ItemCode
                            Dscription
                                Quantity*$rep->NumPerMsr  => Cant
                                Price,'2', '.',',')
                                LineTotal,'2', '.',',')}}
                                VatSum,'2', '.',',')}}
                                LineTotal+$rep->VatSum,'2', '.',',')}} {{$rep->DocCur}} => TotalConIva


                                
$(".DTFC_LeftHeadWrapper").find("input").on( 'keyup change', function () {       
            
            if ( table.column(0).search() !== $(".DTFC_LeftHeadWrapper").find("input").val() ) {
                table
                    .column(0)
                    .search(this.value, true, false)
                    
                    .draw();
            } 
                
    } );

    alert(;

    $(".DTFC_LeftHeadWrapper").find("input").on( 'keyup change', function () {
        console.log($(".DTFC_LeftHeadWrapper").find("input").val());
            if ( table.column(1).search() !== $(".DTFC_LeftHeadWrapper").find("input").val() ) {
                table
                    .column(1)
                    .search(this.value, true, false)
                    
                    .draw();
            }
    });




    <style>
        th {
            font-size: 12px;
        }
    
        td {
            font-size: 11px;
        }
    
        th,
        td {
            white-space: nowrap;
        }
    
        div.container {
            min-width: 980px;
            margin: 0 auto;
        }
    
        th:first-child {
            position: -webkit-sticky;
            position: sticky;
            left: 0;
            z-index: 5;
        }
    
        table.dataTable thead .sorting_asc {
            position: sticky;
        }
    
        .DTFC_LeftBodyWrapper {
            margin-top: 83px;
        }
    
        .DTFC_LeftHeadWrapper {
            display: none;
        }
    
        .btn-group {
            //cuando es datatables y custom buttons
            margin-bottom: 0px;
            z-index: 5;
        }
    
        .btn-group>.btn {
            float: none;
        }
    
        .btn {
            border-radius: 4px;
        }
    
        .btn-group>.btn:not(:first-child):not(:last-child):not(.dropdown-toggle) {
            border-radius: 4px;
        }
    
        .btn-group>.btn:first-child:not(:last-child):not(.dropdown-toggle) {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
    
        .btn-group>.btn:last-child:not(:first-child),
        .btn-group>.dropdown-toggle:not(:first-child) {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
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
                <h5><b>Del:</b> {{\AppHelper::instance()->getHumanDate($fi)}} <b>al:</b> {{\AppHelper::instance()->getHumanDate($ff)}}</h5>
                <h5>Actualizado: {{date('d-m-Y h:i a', strtotime("now"))}}</h5>
                <!-- <h5>Fecha & hora: {{\AppHelper::instance()->getHumanDate(strtotime("now"))}}</h5> -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="dt-buttons btn-group">
                    <a class="btn btn-success" href="entradasalmacenXLS"><i class="fa fa-file-excel-o"></i> Excel</a>
                    <a href="../reporte/ENTRADAS ALMACEN/" target="_blank" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Pdf</a>
                </div>
            </div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-md-12" style="margin-top: -30px;">