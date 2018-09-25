<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ 'Reporte Producción' }}</title>
    <style>
    /*
	Generic Styling, for Desktops/Laptops 
	*/
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
    #produccion {    
    border-collapse: collapse;
    width: 100%;
}

#produccion td, #produccion th {
    border: 1px solid #ddd;
    padding: 4px;
}
#produccion tr:nth-child(even){background-color: #f2f2f2;}

#produccion th {
    padding-top: 6px;
    padding-bottom: 6px;
    text-align: left;
    background-color: #585858;
    color: white;
}

    img {
    display: block;
    margin-left:50px;
    margin-right:50px;
    width: 700%;
    margin-top:3.5%;
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
                <table border="1px">                   
                    <tbody>
                        <tr>
                            <td colspan="5" align="center"  bgcolor="#fff">                           
                                <b>Mod06-Producción</b>
                                <h3>Reporte de Producción por Cliente</h3></td>                              
                        </tr>                            
                    </tbody>
                </table>
        </div>
        <div id="content">
        <!-- /.row -->      
                    @foreach ($ofs as $clave => $valor)
                 
                            <div style="" > <h5>{{$clave}}</h5>
                                <table id="produccion" >
                                    <thead>                            
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Orden</th>
                                        <th>Pedido</th>
                                        <th>Código</th>
                                        <th>Modelo</th>
                                        <th>VS</th>
                                        <th>Cant.</th>
                                        <th>Total</th>
                                    </tr>
                                     </thead>  <tbody>                                  
                                            @foreach ($valor as $val)
                                            <tr>
                                                <?php   $tvs = $tvs + $val['TVS'];
                                                $cant = $cant + $val['Cantidad'];   
                                                ?>
                                                <td style="font-size: 10px; width: 9%"> {{substr($val['fecha'],0,10)}} </td>
                                                <td style="font-size: 10px; width: 6%"> {{$val['orden']}} </td>
                                                <td style="font-size: 10px; width: 5%"> {{$val['Pedido']}} </td>
                                                <td style="font-size: 10px; width: 12%"> {{$val['Codigo']}} </td>
                                                <td style="font-size: 10px; width: 48%">{{$val['modelo']}} </td>
                                                <td style="font-size: 10px; width: 8%"> {{$val['VS']}} </td>
                                                <td style="font-size: 10px; width: 4%"> {{$val['Cantidad']}} </td>
                                                <td style="font-size: 10px; width: 8%"> {{$val['TVS']}} </td>
                                            </tr>                                       
                                    </tbody>
                                     @endforeach
                                </table></div>
                                @endforeach
                            
<br>                                           
                                <table id="" >
                                    <thead >
                                    <h5>Totales</h5>
                                    <tr>
                                        <th>Total Cantidad</th>
                                        <th>Total VS</th>
                                    </tr>
                                    </thead>
                                    <tr>
                                        <td style="font-size: 10px"> {{$cant}} </td>
                                        <td style="font-size: 10px"> {{$tvs}} </td>
                                    </tr>
                                </table>
<div><!-- /.content -->
</body>

                
