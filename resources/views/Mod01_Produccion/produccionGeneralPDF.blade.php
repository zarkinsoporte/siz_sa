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
                #content {position: relative; top:10%}
                tr:nth-child(even) {background-color: #f2f2f2;}
            </style>
        </head>
<body>
<div id="header" >
<img src="images/Mod01_Produccion/siz1.png" >
<!--empieza encabezado, continua cuerpo-->
            <table border="1px">
               
                        <tr>
                         <td colspan="6" align="center" bgcolor="#fff">
                         <b><?php echo 'SALOTTO S.A. de C.V.'; ?></b><br>
                         <b>Producción</b>
                         <h3>Reporte de Producción General</h3>
                         </tr>
                      
</table>
</div>

<div id="content">
     <table id="usuarios" border="1px" class="table table-bordered">
            <thead>
                    <tr>
                            <th style="font-size: 9px; width: 9%">Fecha</th>
                            <th style="font-size: 9px; width: 6%">Orden</th> 
                            <th style="font-size: 9px; width: 4%">Pedido</th>
                            <th style="font-size: 9px; width: 11%">Código</th>
                            <th style="font-size: 9px; width: 51%">Modelo</th> 
                            <th style="font-size: 9px; width: 8%">VS</th>
                            <th style="font-size: 9px; width: 3%">Cant</th> 
                            <th style="font-size: 9px; width: 8%">TVS</th>
                     </tr>                   
                </thead>    
            <?php foreach ($array as $val) {
             // $tcant=$tcant+$val->Cantidad;
              //  $tval=$tval+$val->TVS;?>
                             
<!--Cuerpo o datos de la tabla
<div class="row">
<div style="overflow-x:auto;" class="col-md-12"> </div>-->

                 <tr>
                    <td style="font-size: 9px; width: 9% padding-bottom: 0px; padding-top: 0px; padding-left: 2px; padding-right: 2px;" > {{substr($val->fecha,0,10)}} </td>
                    <td style="font-size: 9px; width: 6% padding-bottom: 0px; padding-top: 0px; padding-left: 2px; padding-right: 2px;"> {{$val->orden}} </td>
                    <td style="font-size: 9px; width: 4% padding-bottom: 0px; padding-top: 0px; padding-left: 2px; padding-right: 2px;"> {{$val->Pedido}} </td>
                    <td style="font-size: 9px; width: 11% padding-bottom: 0px; padding-top: 0px; padding-left: 2px; padding-right: 2px;"> {{$val->Codigo}} </td>
                    <td style="font-size: 9px; width: 51% padding-bottom: 0px; padding-top: 0px; padding-left: 2px; padding-right: 2px;"> {{$val->modelo}} </td>
                    <td style="font-size: 9px; width: 8% padding-bottom: 0px; padding-top: 0px; padding-left: 2px; padding-right: 2px;"> {{$val->VS}} </td>
                    <td style="font-size: 9px; width: 3% padding-bottom: 0px; padding-top: 0px; padding-left: 2px; padding-right: 2px;"> {{$val->Cantidad}} </td>
                    <td style="font-size: 9px; width: 8% padding-bottom: 0px; padding-top: 0px; padding-left: 2px; padding-right: 2px;"> {{$val->TVS}} </td>
                    </tr>
             <?php }?>
            </table>
            <br>
    <table  border= "1px"  style="width: auto;" align="right">
    <tr>
    <th style="text-align: center;">Total Cantidad</th>
    <th style="text-align: center;">Total VS</th></tr>
    <tr>
    <td style="text-align: center;">0</td>  
    <td style="text-align: center;">0</td></tr>  
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