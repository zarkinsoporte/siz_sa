<<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ 'Reporte de Rechazos' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
    <style>
    /*
	Generic Styling, for Desktops/Laptops 
	*/
    img {
    display: block;
    margin-left:50px;
    margin-right:50px;
    width: 700%;
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
            position: absolute;right:10%;
            align-content:;
        }
        h3{
            font-family: 'Helvetica';
        }
</style>
</head>
<body>
<header>
<div id="app">
        <div id="wrapper">
<div class="container" >  
<img src="images/Mod01_Produccion/siz1.png" >
<br><br>
</header>
<div class="col-6">
     <table  border="1px" class="table table-striped">
         <thead class="thead-dark">
     <tr>
      <td colspan="5" align="center" bgcolor="#ccc"><h3>Reporte de Rechazos</font></h3></td>
      </tr>
      <tbody>
     <br>
      <th>De la fecha</th>
      <td>{{$fechaIni}}</td>
      <th>A la fecha:</th>
      <td>{{$fechaFin}}</td>
      </tbody>
</thead>
</table>
<br><br>

<div>
    <table border="1px" class="table table-striped" >
        <thead class="thead-dark">
            <tbody>
            <tr>
            <td rowspan="2" align="center" bgcolor="#474747" style="color:white"; scope="col">Fecha de Revisión</td>
            <td rowspan="2" align="center" bgcolor="#474747" style="color:white"; scope="col">Proveedor</td>
            <td rowspan="2"  align="center" bgcolor="#474747" style="color:white"; scope="col">Descripcion de Marerial</td>
            <td colspan="3" align="center" bgcolor="#000" style="color:white"; scope="col">Cantidad</td>
            <td rowspan="2" align="center" bgcolor="#474747" style="color:white"; scope="col">Nombre del Inspector</td>
            <td rowspan="2" align="center" bgcolor="#474747" style="color:white"; scope="col"   >No.Factura</td>
             </tr>
             <tr>
             <td bgcolor="#000" style="color:white";>Aceptada</td>
             <td bgcolor="#000" style="color:white";>Rechazada</td>
             <td bgcolor="#000" style="color:white";>Revisada</td>
             </tr>
            </tbody>
            @foreach($rechazo as $rep)
                        <tr>
                            <td scope="row"align="center">
                            <?php echo date('d-m-Y', strtotime($rep->fechaRevision));  ?>
                            </td>

                            <td scope="row"align="center">
                            {{$rep->proveedorNombre}}
                            </td>

                            <td scope="row"align="center">
                            {{$rep->materialDescripcion}}
                            </td>

                            <td scope="row"align="center">
                            {{$rep->cantidadAceptada}}
                            </td>

                            <td scope="row"align="center">
                            {{$rep->cantidadRechazada}}
                            </td>

                           <td scope="row"align="center">
                            {{$rep->cantidadRevisada}}
                            </td>

                            <td scope="row"align="center">
                            {{$rep->InspectorNombre}}
                            </td>
                            
                            <td scope="row"align="center">
                            {{$rep->DocumentoNumero}}
                            </td>
                        </tr>    
                    @endforeach 
        </thead>
    </table>
<footer>
<script type="text/php">
 $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}';
 $date = 'Fecha de impresion : <?php echo $hoy = date("d-m-Y H:i:s");?>';
 $tittle = 'Reporte_Rechazo.Pdf';
 $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
 $pdf->page_text(40, 580, $text, $font, 9);
 $pdf->page_text(603, 23, $date, $font, 9);
 $pdf->page_text(680, 580, $tittle, $font, 9);


</script> 
</footer>

</div>
  
</body>

</html>
