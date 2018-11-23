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
                margin-left:40px;
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
                        position: absolute;right:3%;
                        align-content:;
                    }
                    h3{
                        font-family: 'Helvetica';
                    }
                    b{
                        font-size:100%;
                    }
                #header  {position: fixed; margin-top:1px; }
                #content {position: relative; top:17%}

            </style>
        </head>
<body>
<div id="header" >
<img src="images/Mod01_Produccion/siz1.png" >
<!--empieza encabezado, continua cuerpo-->
            <table border="1px" class="table table-striped">
            <td style="text-align: center;">
                    <b>SALOTTO S.A. de C.V.</b>
                    <br><small>EMILIANO ZAPATA #7 INT 1-B</small>
                    <br><small>PARQUE INDUSTRIAL LERMA</small>
                    <br><small>LERMA, ESTADO DE MÉXICO</small>
                    <br><small>C.P. 52004</small>
                    <br><small>R.F.C. SAL1701094N6</small>
                    <h2>ORDEN DE COMPRA</h2>
                    </td>                                          
            </table>
@if (isset($pedido))
    <?php $date=date_create($pedido[0]->FechOC); 
     ?>
    
 <h4>Orden de Compra Número: {{$pedido[0]->NumOC}}</h4>
 
    <table border= "1px" style="width: auto;">
                <tbody>
                            <tr>
                                <td width="11%">Fecha de Orden:</td>
                                <td width="50%"> {{date_format($date, 'd-m-Y')}}</td>                              
                                </tr>
                            <tr>
                                <td>Proveedor:</td>
                                <td>{{$pedido[0]->CodeProv.'  '.$pedido[0]->NombProv}}</td>
                            </tr>
                            <tr>
                                <td>Contacto:</td>
                                <td>{{$pedido[0]->Elaboro}}</td>
                            </tr>
                            <tr>
                                <td>Comentarios:</td>
                                <td>{{$pedido[0]->Comments}}</td>                    
                            </tr>
                            <td>&nbsp;</td>
                        </tbody>                     
                    </table>                                            
    <table border= "1px">
                    <tr>
                        <th style="text-align: center;">Código</th>
                        <th style="text-align: center;">Descripción</th>
                        <th style="text-align: center;">Versión</th>
                        <th style="text-align: center;">Cantidad Total</th>
                        <th style="text-align: center;">Cant. Pendiente</th>
                        <th style="text-align: center;">Entrega</th>
                        <th style="text-align: center;">Precio Unitario</th>      
                        <th style="text-align: center;">Moneda</th>    
                    </tr>   
            @foreach ($pedido as $pedi)
                    <tr>
                    <?php 
                     $dat=date_create($pedido[0]->FechEnt); 
                     ?>
                        <td style="text-align: center;">{{$pedi->Codigo}}</td>
                        <td>{{$pedi->Descrip}}</td>
                        <td style="text-align: center;">{{$pedi->ValidComm}}</td>
                        <td style="text-align: center;">{{number_format($pedi->CantTl,2)}}</td>
                        <td style="text-align: center;">{{number_format($pedi->CantPend,2)}}</td>
                        <td style="text-align: center;">{{date_format($dat, 'd-m-Y')}}</td>
                        <td style="text-align: center;">{{"$ ".number_format($pedi->Price,4)}}</td>
                        <td style="text-align: center;">{{$pedi->Currency}}</td>                                                   
                    </tr>
            @endforeach
    </table>
    @endif
             <!--<div class="row">
                    <div class="col-md-10">
                        <h3>Calidad</h3>
                            <table>
                                         <tr>
                                            <th>Empleado:</th>
                                            <td>{{0}}</td>
                                          </tr>
                                          <tr>
                                            <th>% de Calidad:</th>
                                            <td>{{0}}</td>
                                          </tr>
                                          <tr>
                                            <th>Bono:</th>
                                            <td>{{0}}</td>
                                          </tr>

                                </table>
                    </div>  /.col md 
                </div> /.row 
            <div class="row">
            <div class="col-md-10">
                <h3>Totales</h3>
                    <table>
                                 <tr>
                                    <th>Empleado:</th>
                                    <td>{{0}}</td>
                                     <td>{{0}}</td>
                                     <td>{{0}}</td> 
                                     <td>{{0}}</td>
                                      <td>{{0}}</td>
                                  </tr>
                                  <tr>
                                    <th>Total Bono:</th>
                                    <td>{{0}}</td>
                                  </tr>
                        </table>
            </div>
        </div>
        <h3></h3>-->
                 
 
 <footer>
                <script type="text/php">
                $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}';
                $date = 'Fecha de impresión : <?php echo $hoy = date("d-m-Y H:i:s"); ?>';
                $tittle = 'Siz_Orden_de_Compra.Pdf';
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