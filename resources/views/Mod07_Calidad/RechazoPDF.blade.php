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
<div class="col-6">
     <table  border="1px" class="table table-striped">
         <thead class="thead-dark">
     <tr>
      <td colspan="5" align="center" bgcolor="#ccc"><h3>Reporte de Rechazos</font></h3></td>
      </tr>
      <tbody>
     <br>
      <th>De la fecha</th>
      <td>06 junio 2016</td>
      <th>A la fecha:</th>
      <td>06 junio 2016</td>
      </tbody>
</thead>
</table>
<br><br>

<div>
    <table border class="table table-striped">
        <thead>
            <tbody>
            <tr>
            <td rowspan="2" align="center" bgcolor="#474747" style="color:white"; scope="col">Fecha de Recepción</td>
            <td rowspan="2" align="center" bgcolor="#474747" style="color:white"; scope="col">Proveedor</td>
            <td rowspan="2"  align="center" bgcolor="#474747" style="color:white"; scope="col">Descripcion de Marerial</td>
            <td colspan="3" align="center" bgcolor="#474747" style="color:white"; scope="col">Cantidad</td>
            <td rowspan="2" align="center" bgcolor="#474747" style="color:white"; scope="col">Nombre del Inspector</td>
            <td rowspan="2" align="center" bgcolor="#474747" style="color:white"; scope="col"   >No.Factura</td>
             </tr>
             <tr>
             <td></td>
             <td></td>
             <td></td>
             <td bgcolor="#474747" style="color:white";>Aceptada</td>
             <td bgcolor="#474747" style="color:white";>Rechazada</td>
             <td bgcolor="#474747" style="color:white";>Revisada</td>
             <td></td>
             </tr>
            </tbody>
        </thead>
    </table>
</div>

</body>

</html>
