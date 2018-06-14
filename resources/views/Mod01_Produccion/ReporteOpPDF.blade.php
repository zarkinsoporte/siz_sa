<!DOCTYPE html>
<html lang="en">

<head>


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'Salotto' }}</title>

    <!-- Styles -->
<style>

body { 
	font: 14px/1.4 Georgia, Serif; 
}


	/* 
	Generic Styling, for Desktops/Laptops 
	*/
	table { 
		width: 100%; 
		border-collapse: collapse; 
	}
	/* Zebra striping */
	tr:nth-of-type(odd) { 
		background: #eee; 
	}
	th { 
		background: #333; 
		color: white; 
		font-weight: bold; 
	}
	td, th { 
		padding: 6px; 
		border: 1px solid #ccc; 
		text-align: left; 
	}
</style>

</head>

<body>
    <div id="app">
        <div id="wrapper">

<div class="container" >

        <div align="left">
        <FONT FACE="roman"><h4 align="right" > Historial por OP</h4></FONT>
        <h3><?php echo $data[0]->CompanyName ?></h3> <hr>
           
        <strong>  • Descripción: </strong> <?php echo $data[0]->ItemCode ?> - <?php echo $data[0]->ItemName ?>
        </div>
        <div>
        <strong>  • Orden de fabricación:</strong>  <?php echo $op ?>
        <br>
        <strong> • V.S : </strong>  <?php echo number_format($data[0]->VS, 2, '.', ','); ?>
        </div> 
        <hr>    
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
</div>

</body>
</html>