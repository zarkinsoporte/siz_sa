<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Almacén</title>
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
            font-size: 12px;
            background-color: #333333;
        }

        td {
            font-family: 'Helvetica';
            font-size: 11px;
        }

        img {
            display: block;
            margin-top: 3.8%;
            width: 670;
            height: 45;
            position: absolute;
            right: 2%;
        }

        h5 {
            font-family: 'Helvetica';
            margin-bottom: 15;
        }

        .fz {
            font-size: 100%;
            margin-top: 7px;
        }

        #header {
            position: fixed;
            margin-top: 2px;

        }

        #content {
            position: relative;
            top: 20%
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
        <script type="text/php">
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif","normal"); 
            
            $codigos = '<?php echo $pKey ?>';
            $FACTOR_UM = '<?php echo 'Factor: '.$factor.$separador. ' UM: '.$um; ?>';
            $itemName = '<?php echo $itemName; ?>';
            $cardName = '<?php echo $cardCode.' '.$cardName; ?>';
            $size = 11;
            $size2 = 5;
            $size3 = 4;
             $pdf->page_text(18, 50, $codigos, $font, $size); 
            $pdf->page_text(3, 4, $itemName, $font, $size2); 
            $pdf->page_text(3, 9, $cardName, $font, $size2); 
           // $pdf->page_text(3, 20, $FACTOR_UM, $font, $size3); 
        </script>
    </div>
</body>

</html>