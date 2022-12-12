<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIZ Ultimos Precios.Pdf</title>
    <style>
        /*
                Generic Styling, for Desktops/Laptops
        */

        table {
            width: 100%;
            border-collapse: collapse;
            font-family: arial;
        }

        th {
            color: white;
            font-weight: bold;
            font-family: 'Helvetica';
            font-size: 14px;
            background-color: #333333;
        }

        td {
            font-family: 'Helvetica';
            font-size: 14px;
        }

        img {
            display: block;
            margin-top: 3.8%;
            width: 800px;
            height: 45;
            position: absolute;
            right: 2%;
        }

        h3 {
            font-family: 'Helvetica';
            margin-bottom: 2px;
            margin-top:3px
        }
        h5{
            margin-top:2px
        }
        .fz {
            font-size: 100%;
            margin-top: 7px;
        }

        #header {
            position: fixed;
            margin-top: 2px;
            margin-bottom:40px;
        }

        #content {
            position: relative;
            margin-top: 40%
        }

        table,
        th,
        td {
            text-align: center;
            border: 1px solid black;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .zrk-silver{
            background-color: #AFB0AE;
            color: black;
        }
        .zrk-dimgray{
            background-color: #514d4a;
            color: white;
        }
        .zrk-gris-claro{
            background-color: #eeeeee;
            color: black;
        }
        .zrk-silver-w{
            background-color: #656565;
            color: white;
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
</head>

<body>
    
    <!--Cuerpo o datos de la tabla-->
    <div id="content">
        @if(count($data_row)>0)
            <div class="row">
           
            <div class="col-md-8">
                <table class="table table-striped table-bordered" width="100%" style="table-layout:fixed;">
                    
                    <tr>
                                    <th style="width:4%" class="zrk-silver-w" scope="col">#</th>
                                    <th style="width:6%" class="zrk-silver-w" scope="col">OC</th>
                                    <th style="width:10%" class="zrk-silver-w" scope="col">ENTRADA</th>
                                    <th style="width:13%" class="zrk-silver-w" scope="col">FECHAF</th>
                                    <th style="width:45%" class="zrk-silver-w" scope="col">PROVEEDOR</th>
                                    <th style="width:13%" class="zrk-silver-w" scope="col">PRECIOF</th>
                                    <th style="width:9%" class="zrk-silver-w" scope="col">MONEDA</th>
                                  
                    </tr>
                    <?php 
                        $i = 1;                                         
                    ?>

                        @foreach ($data_row as $rep)
                        
                            
                            <tr>
                                <td  class="zrk-gris-claro" scope="row">
                                    {{$i++}}
                                </td>
                                <td  class="zrk-gris-claro" scope="row">
                                    {{$rep->ORDEN_C}}
                                </td>
                                <td  class="zrk-gris-claro" scope="row">
                                    {{$rep->COMPRA }}
                                </td>
                                <td style="vertical-align: middle;" class="zrk-gris-claro" scope="row">
                                    {{$rep->FECHAF}}
                                </td>
                                <td style=" text-align: left;" class="zrk-gris-claro" scope="row">
                                    {{$rep->PROVEEDOR}}
                                </td>
                                <td  class="zrk-gris-claro" scope="row">
                                    {{$rep->PRECIOF}}
                                </td>
                                <td  class="zrk-gris-claro" scope="row">
                                    {{$rep->MONEDA}}
                                </td>
                                   
                            </tr>         
                        @endforeach 
                </table>
            </div>

        </div>
       @endif
    </div>

</body>

</html>