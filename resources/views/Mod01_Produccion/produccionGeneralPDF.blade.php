<!DOCTYPE html>
<html lang="en">

        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <!-- CSRF Token -->
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <title>{{ 'Plantilla de Personal' }}</title>
                <style>
                /*
                Generic Styling, for Desktops/Laptops
                */
                img {
                display: block;
                margin-left:50px;
                margin-right:50px;
                width: 700%;
                margin-top:4.5%;
            }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-family:arial;
                }

                th {
                    color: white;
                    font-weight: bold;
                    color: black;
                    font-family: 'Helvetica';
                    font-size:80%;
                }
                td{
                    font-family: 'Helvetica';
                    font-size:80%;
                }

                    img{
                    width:500;
                        height: 20;
                        position: absolute;right:-2%;
                        align-content:;
                    }
                    h3{
                        font-family: 'Helvetica';
                    }
                    b{
                        font-size:100%;
                    }
                #header  {position: fixed; margin-top:2px; }
                #content {position: relative; top:17%}

            </style>
        </head>
<body>
<div id="header" >
<img src="images/Mod01_Produccion/siz1.png" >
<!--empieza encabezado, continua cuerpo-->
            <table border="1px" class="table table-striped">
                <thead class="thead-dark">
                        <tr>
                         <td colspan="6" align="center" bgcolor="#fff">
                         <b><?php echo 'SALOTTO S.A. de C.V.'; ?></b><br>
                         <b>Producción</b>
                         <h3>Reporte de Producción General</h3>
                         </tr>
                         </thead>
</table>
</div>
<div id="content">
     <table id="usuarios" >
            <?php foreach ($array as $val) {?>
<!--Cuerpo o datos de la tabla
<div class="row">
<div style="overflow-x:auto;" class="col-md-12">
<table border="1px" class="table table-bordered">-->
                 <tr>
                    <td style="font-size: 10px; width: 8% padding-bottom: 0px; padding-top: 0px; padding-left: 0px; padding-right: 0px;" > {{substr($val->fecha,0,10)}} </td>
                    <td style="font-size: 10px; width: 6% padding-bottom: 0px; padding-top: 0px; padding-left: 0px; padding-right: 0px;"> {{$val->orden}} </td>
                    <td style="font-size: 10px; width: 4% padding-bottom: 0px; padding-top: 0px; padding-left: 0px; padding-right: 0px;"> {{$val->Pedido}} </td>
                    <td style="font-size: 10px; width: 11% padding-bottom: 0px; padding-top: 0px; padding-left: 0px; padding-right: 0px;"> {{$val->Codigo}} </td>
                    <td style="font-size: 10px; width: 52% padding-bottom: 0px; padding-top: 0px; padding-left: 0px; padding-right: 0px;"> {{$val->modelo}} </td>
                    <td style="font-size: 10px; width: 8% padding-bottom: 0px; padding-top: 0px; padding-left: 0px; padding-right: 0px;"> {{$val->VS}} </td>
                    <td style="font-size: 10px; width: 3% padding-bottom: 0px; padding-top: 0px; padding-left: 0px; padding-right: 0px;"> {{$val->Cantidad}} </td>
                    <td style="font-size: 10px; width: 8% padding-bottom: 0px; padding-top: 0px; padding-left: 0px; padding-right: 0px;"> {{$val->TVS}} </td>
             <?php }?>
 </div>
</table>
</table>
<table id="totales" >
        <thead >
        <h5>Totales</h5>
        <tr>
            <th>Total Cantidad</th>
            <th>Total VS</th>
        </tr>
        </thead>
<tbody>
     <tr>
            <td style="font-size: 10px">0</td>
            <td style="font-size: 10px">0</td>

        </tr>
</tbody>


    </table>
</div>


                <footer>
                <script type="text/php">
                $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}';
                $date = 'Fecha de impresion : <?php echo $hoy = date("d-m-Y H:i:s"); ?>';
                $tittle = 'Siz_Reporte_Produccion_General.Pdf';
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->page_text(35, 755, $text, $font, 9);
                $pdf->page_text(405, 23, $date, $font, 9);
                $pdf->page_text(420, 755, $tittle, $font, 9);
                $empresa = 'Sociedad: <?php echo 'SALOTTO S.A. de C.V.'; ?>';
                $pdf->page_text(40, 23, $empresa, $font, 9);
                </script>
        </footer>
     @yield('subcontent-01')

</body>
</html>