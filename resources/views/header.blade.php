<!DOCTYPE html>
<html>

<head>
 <meta charset="utf-8">
    <title>{{ 'Orden de Producción' }}</title>
    <style>
    /*
	Generic Styling, for Desktops/Laptops 
	*/
    img {
        display: block;
    margin-left: 70px;
    width:90%;
    height: 50px;
        position: absolute;
}
	table { 
		width: 100%; 
		border-collapse: collapse; 
        font-family:arial;
	}
 
</style>

</head>

<body>
   <div id="header">
    <img src="{{ url('/images/Mod01_Produccion/siz1.png') }}" >
    <table>
        <tr style="background-color: white">
            <td colspan="2" align="center" bgcolor="#fff">
                <b><?php echo  $db?></b><br>
                <h3>Orden de Producción @if (isset($marcaagua))
                    ({{strtoupper ($marcaagua)}})
                @endif </h3>
            </td>
        </tr>
        <tr style="background-color: white">
            <td>
                <table>

                    <tbody>

                        <tr style="background-color: white">
                            <th align="center">Código: </th>
                            <td align='left' style="white-space:nowrap;"><?php echo $data[0]->ItemCode ?> -
                                <?php echo substr($data[0]->ItemName,0,60) ?></td>

                        </tr>
                        <tr style="background-color: white">
                            <th align="center">Cliente:</th>
                            <td align="left" style="white-space:nowrap;"><?php echo $data[0]->CardCode ?> -
                                <?php echo $data[0]->CardName ?></td>

                        </tr>
                        <tr style="background-color: white">
                            <th align="center" style="white-space:nowrap;">F. Entrega: </th>
                            <td align='left'><?php echo date_create($data[0]->FechaEntrega)->format('d-m-Y'); ?></td>

                        </tr>


                    </tbody>

                </table>
            </td>
            <td>
                <table>

                    <tbody>

                        <tr style="background-color: white">

                            <td colspan="1" align="center"><b>No. Orden:</b></td>
                            <td align="left" colspan="1"><b><?php echo $op ?></b></td>
                        </tr>
                        <tr style="background-color: white">

                            <th align="center">No. Serie:</th>
                            <td align="left"><?php echo ($data[0]->U_NoSerie); ?></td>
                        </tr>
                        <tr style="background-color: white">

                            <th align='center'>No. Pedido:</th>
                            <td align='left'><?php echo number_format($data[0]->NumPedido,0); ?></td>
                        </tr>
                        <tr style="background-color: white">

                            <th align='center' style="white-space:nowrap;">Cantidad Planeada:</th>
                            <td align='left'><?php echo number_format($data[0]->plannedqty,0); ?></td>
                        </tr>
                        <tr style="background-color: white">

                            <th align="center">V.S:</th>
                            <td align="left"><?php echo number_format($data[0]->VS, 2); ?></td>
                        </tr>
                        <tr style="background-color: white">

                            <th align='center'>Total V.S:</th>
                            <td align='left'><?php echo number_format($total_vs,2); ?></td>
                        </tr>
                    </tbody>

                </table>
            </td>
        </tr>

    </table>



</div>

</body>
</html>