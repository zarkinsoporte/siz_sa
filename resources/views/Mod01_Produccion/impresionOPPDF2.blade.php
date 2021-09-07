<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ 'Orden de Producción' }}</title>
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
        font-size:14px;
	}
    td{
        font-family: 'Helvetica';
        font-size:12px%;
        
    }
tr:nth-child(even) {
background-color: #f2f2f2;
}      
        h3{
            font-family: 'Helvetica';
        }
        b{
            font-size:100%;
        }
  
    #content {position: relative; top:20%}
</style>
</head>
<body>       
<div id="content">
             <table>
                    <thead class="thead-dark">
                    <tr>                        
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Código</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Descripción</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">UM</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Solicitada</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Origen</th>
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
                              $EstacionO=$rep->ESTACION;
                              ?>
                              <tr><td colspan="5" align="center" bgcolor="#ccc"> <?php echo $EstacionO ?> </td></tr>

                              <?php
                          }        
                           $temporal=$rep->ESTACION;
                          //dd($EstacionO);
                           if($EstacionO==$temporal){ 
                            ?>
                            <tr>
                            <td align="center" scope="row">
                                {{ $rep->CODIGO }}
                            </td>
                            <td style="white-space:nowrap;" scope="row">
                                {{$rep->MATERIAL}}
                            </td>
                            <td align="center" scope="row">
                                {{ $rep->UDM }}
                            </td>
                            <td align="center" scope="row">
                            <?php echo number_format($rep->CANTIDAD,2); ?>
                            </td>
                            <td align="center" scope="row">
                                {{ $rep->ORIGEN }}
                            </td>
                        </tr>
                           <?php
                           }else{
                            $EstacionO=$temporal;
                            ?>  
                           <tr><td colspan="5"align="center" bgcolor="#ccc"> <?php echo $EstacionO ?> </td></tr>
                           <tr>
                           
                            <td align="center" scope="row">
                                {{ $rep->CODIGO }}
                            </td>
                            <td style="white-space:nowrap;" scope="row">
                                {{$rep->MATERIAL}}
                            </td>
                            <td align="center"scope="row">
                                {{ $rep->UDM }}
                            </td>
                            <td align="center"scope="row">
                                <?php echo number_format($rep->CANTIDAD,2); ?>

                            </td>
                            <td align="center" scope="row">
                                {{ $rep->ORIGEN }}
                            </td>
                        </tr>
                            <?php
                        }
                           ?>
                    @endforeach 
                </table>
                    @if (count($ordenes_serie) > 1)
                    <table>
                        <tbody>
                    <tr>
                        <th colspan="5" align="center" bgcolor="#474747" style="color:white">Ordenes que componen la Serie</th>
                    </tr>
                    <tr>                        
                        <td align="center" bgcolor="#ccc">OP</td>
                        <td align="center" bgcolor="#ccc">Código</td>
                        <td align="center" bgcolor="#ccc">Descripción</td>
                        <td align="center" bgcolor="#ccc">V.S</td>
                        <td align="center" bgcolor="#ccc">Cantidad</td>
                    </tr>
                    @foreach ($ordenes_serie as $o_s)
                        <tr>
                            <td align="center" scope="row">
                                {{ $o_s->op }}
                            </td>
                            <td align="center" scope="row">
                                {{ $o_s->codigo }}
                            </td>
                            <td style="white-space:nowrap;" scope="row">
                                {{$o_s->descripcion}}
                            </td>
                            <td align="center" scope="row">
                                <?php echo number_format($o_s->VS,2); ?>
                        
                            </td>
                            <td align="center" scope="row">
                                <?php echo number_format($o_s->cantidad,2); ?>
                        
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    </table>
                    @endif
                    @if(!is_null($composicion))
                    <table>
                        <tbody>
                            <tr>
                                <th colspan="2" align="center" bgcolor="#474747" style="color:white" ;scope="col">Distribución de la Serie</th>
                            </tr>
                            <tr>
                                <td  align="center" bgcolor="#ccc">Código</td>
                                <td  align="center" bgcolor="#ccc">Descripción</td>
                            </tr>
                            <tr>
                                <td align="center" scope="row">
                                    {{ $composicion->codigo }}
                                </td>
                                <td align="center" scope="row">
                                    {{ $composicion->descripcion }}
                                </td>
                            </tr>
                        </tbody>
                    </table>        
                    @endif
                </div>
</body>
</html>