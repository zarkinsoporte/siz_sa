<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{date('Ymd') . '_' . '_SIZ CodigosBarra.Pdf'}}</title>
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
            width: 670;
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
        @if(count($a)>0)
            <div class="row">
           
            <div class="col-md-8">
                <table class="table table-striped table-bordered" width="100%" style="table-layout:fixed;">
                    
                    <tr>
                                    <th style="widthy:100px" class="zrk-silver-w" scope="col">Código</th>
                                    <th style="widthy:120px" class="zrk-silver-w" scope="col">Descripción</th>
                                    <th style="widthy:110px" class="zrk-silver-w" scope="col">Precódigo</th>
                                    <th style="widthy:457px" class="zrk-silver-w" scope="col">Código Barra</th>
                                  
                    </tr>
                    <?php 
                        $codigo =\AppHelper::instance();                                           
                    ?>
                        @foreach ($a as $rep)
                        
                            
                            <tr>
                                <td style="widthy:60px" class="zrk-gris-claro" scope="row">
                                    {{$rep->ItemCode}}
                                </td>
                                <td style="widthy:450px" class="zrk-gris-claro" scope="row">
                                    {{$rep->ItemName }}
                                </td>
                                <td style="widthy:57px" class="zrk-gris-claro" scope="row">
                                    {{$rep->codibarr}}
                                </td>
                                <td style="widthy:70px" class="zrk-gris-claro" scope="row">
                                    {{$generator->output_image('svg', 'ean-13-nopad', $codigo->generateEAN($rep->codibarr), [])}}
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