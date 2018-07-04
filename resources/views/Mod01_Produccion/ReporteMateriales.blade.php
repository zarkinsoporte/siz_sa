<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ 'Reporte de Materiales' }}</title>
    <style>
    /*
	Generic Styling, for Desktops/Laptops 
	*/
    img {
    display: block;
    margin-left:50px;
    margin-right:50px;
    width: 700%;
    margin-top:6%;
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
</style>

</head>

<body>

<div id="app">
        <div id="wrapper">
<div class="container" > 
<img src="images/Mod01_Produccion/siz1.png" >
<br><br>
<table>
    <thead>
        <tbody>
            <tr>
            <td colspan="5" align="center" bgcolor="#ccc"><h3>Reporte de Materiales</font></h3></td>
            </tr>
            <tr>
            <th align="center" >Codigo:</th>
            <td colspan="2"><?php echo $data[0]->ItemCode ?> - <?php echo $data[0]->ItemName ?></td>         
            <td align="center"><h3>O.P:</h3></td>
            <td colspan="2"><h3><?php echo $op ?></h3></td>
            </tr>
            <tr>
            <th align="center">Cliente:</th>
            <td colspan="2"><?php echo $data[0]->CardCode ?> - <?php echo $data[0]->CardName ?></td>         
            <td align="center">V.S:</td>
            <td colspan="2"><?php echo number_format($data[0]->VS, 2); ?></td>
            </tr>
            <tr>
            <th colspan="2"align="center">Fecha de Entrega: <hr></th>
            <td align='center'><?php echo date_create($data[0]->FechaEntrega)->format('Y-m-d'); ?><hr></td>
            <td align='center'>Cantidad Planeada:<hr></td>
            <td align='center'><?php echo number_format($data[0]->plannedqty,0); ?><hr></td>
            </tr>
        </tbody>
    </thead>    
     <div class="row">
     <div align="center"><h3>Materiales a utlizar</h3></div>   
        <div class="col-6">
             <table  border class="table table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th align="center" bgcolor="#474747" style="color:white"; scope="col">Fecha de entrega</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Código</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Descripción</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Unidad Medida</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Solicitada</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $bandera=false;
                    ?>
                    @foreach ($data as $rep)
                          <?php 
                          if($bandera==false){
                              $bandera=true;
                              $EstacionO=$rep->Estacion;
                              ?>
                              <tr><td colspan="5"align="center" bgcolor="#ccc"> <?php echo $EstacionO ?> </td></tr>

                              <?php
                          }
                          
                           $temporal=$rep->Estacion;
                          //dd($EstacionO);
                           if($EstacionO==$temporal){ 
                            ?>
                            <tr>
                            <td scope="row">
                               <?php echo date('d-m-Y', strtotime($rep->FechaEntrega));  ?>
                            </td>
                            
                            <td scope="row">
                                {{ $rep->Codigo }}
                            </td>
                            <td scope="row">
                                {{$rep->Descripcion}}
                            </td>
                            <td scope="row">
                                {{ $rep->InvntryUom }}
                            </td>
                            <td scope="row">
                            <?php echo number_format($rep->Cantidad,2); ?>
                            </td>
                        </tr>
                           <?php
                           }else{
                            $EstacionO=$temporal;
                            ?>  
                           <tr><td colspan="5"align="center" bgcolor="#ccc"> <?php echo $EstacionO ?> </td></tr>
                           <tr>
                            <td scope="row">
                               <?php echo date('d-m-Y', strtotime($rep->FechaEntrega));  ?>
                            </td>
                            
                            <td scope="row">
                                {{ $rep->Codigo }}
                            </td>
                            <td scope="row">
                                {{$rep->Descripcion}}
                            </td>
                            <td scope="row">
                                {{ $rep->InvntryUom }}
                            </td>
                            <td scope="row">
                                <?php echo number_format($rep->Cantidad,2); ?>

                            </td>
                        </tr>
                            <?php
                        }
                           ?>
                           
                    @endforeach 
                    </tbody>
                </table>        
        </div>
        <footer>
<script type="text/php">
 $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}';
 $date = 'Fecha de impresion : <?php echo $hoy = date("d-m-Y H:i:s");?>';
 $tittle = 'Reporte_Materiales.Pdf';
 $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
 $pdf->page_text(40, 740, $text, $font, 9);
 $pdf->page_text(420, 23, $date, $font, 9);
 $pdf->page_text(420, 740, $tittle, $font, 9);


</script> 
</footer>
     </div>
     @yield('subcontent-01')

</body>

</html>