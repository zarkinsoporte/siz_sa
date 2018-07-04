<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ 'Historial OP' }}</title>
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
            <td colspan="2" align="center" bgcolor="#ccc"><h3><?php echo $data[0]->CompanyName ?></h3></td>
            <td colspan="5" align="left" bgcolor="#ccc"><h3>Historial OP</h3></td>           
 </tr>
            <tr>
            <th align="center">Orden de fabricación:<hr/></th>
            <td colspan="2"><?php echo $op ?><hr/></td>         
            <td align="center">V S:<hr/></td>
            <td colspan="2"><?php echo number_format($data[0]->VS, 2, '.', ','); ?><hr/></td>
            </tr>
            <tr>
            <th align="center">Descripción:<hr/></th>
            <td colspan="2"><?php echo $data[0]->ItemCode ?> - <?php echo $data[0]->ItemName ?><hr/></td>    
        </tbody>
    </thead> 
<style>
.table-blockquote {
  padding: 3px 10px;
  border: PowderBlue 5px solid;
  border-radius: 20px;
}
</style>
     <div class="row">
        <div class="col-6">
             <table  border="1px"class="table table-striped">
                    <thead class="table table-striped table-bordered table-condensed" >
                        <tr>
                        <th bgcolor="8D8D8D" scope="col">FechaI</th>
                        <th bgcolor="8D8D8D" scope="col">FechaF</th>
                        <th bgcolor="8D8D8D" scope="col">Estación</th>
                        <th bgcolor="8D8D8D" scope="col">Empleado</th>
                        <th bgcolor="8D8D8D" scope="col">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $rep)
                        <tr>
                            <td scope="row">
                               <?php echo date('d-m-Y', strtotime($rep->FechaI));  ?>
                            </td>
                            <td scope="row">
                                <?php echo date('d-m-Y', strtotime($rep->FechaF));  ?> 
                            </td>
                            <td scope="row">
                                {{$rep->NAME}}
                            </td>
                            <td scope="row">
                                {{ $rep->Empleado }}
                            </td>
                            <td scope="row">
                                {{ $rep->U_CANTIDAD }}
                            </td>
                        </tr>    
                    @endforeach 
                    </tbody>
                </table>
        </div>
     </div>
     @yield('subcontent-01')
</div>
<!-- /.container-fluid -->

</div>
<!-- /#page-wrapper -->
</div>
<footer>
<script type="text/php">
 $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}';
 $date = 'Fecha de impresion : <?php echo $hoy = date("d-m-Y H:i:s");?>';
 $tittle = 'Reporte OP.Pdf';
 $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
 $pdf->page_text(40, 740, $text, $font, 9);
 $pdf->page_text(420, 23, $date, $font, 9);
 $pdf->page_text(420, 740, $tittle, $font, 9);


</script> 
</footer>
</div>

</body>
</html>