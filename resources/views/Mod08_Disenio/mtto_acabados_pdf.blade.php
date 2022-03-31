<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">#</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Código</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $bandera=false;
                    $num = 1;
                    ?>
                    @foreach ($acabados as $rep)
                          <?php 
                          if($bandera==false){
                              $bandera=true;
                              $codigoAcabado=$rep->CODIDATO;
                             
                              ?>
                            <tr>
                                <td bgcolor="#ccc"></td>
                                <td bgcolor="#ccc">{{$rep->CODIDATO}} </td>
                                <td bgcolor="#ccc">{{$rep->DESCDATO}} </td>
                            </tr>

                              <?php
                          }        
                           $temporal=$rep->CODIDATO;
                          //dd($codigoAcabado);
                           if($codigoAcabado==$temporal){ 
                            ?>
                            <tr>
                                <td>{{$num}}</td>
                                <td align="center" scope="row">
                                    {{ $rep->Surtir }}
                                </td>
                                <td style="white-space:nowrap;" scope="row">
                                    {{$rep->inval01_descripcion2}}
                                </td>
                            </tr>
                           <?php
                           }else{
                            $codigoAcabado=$temporal;
                            $num = 1;
                            ?>  
                            <tr>
                                <td bgcolor="#ccc"></td>
                                <td bgcolor="#ccc">{{$rep->CODIDATO}} </td>
                                <td bgcolor="#ccc">{{$rep->DESCDATO}} </td>
                            </tr>
                            <tr>
                                <td>{{$num}}</td>
                                <td align="center" scope="row">
                                    {{ $rep->Surtir }}
                                </td>
                                <td style="white-space:nowrap;" scope="row">
                                    {{$rep->inval01_descripcion2}}
                                </td>
                            </tr>
                            <?php
                        }  
                        $num++; 
                           ?>
                    @endforeach 
                </table>
                    
                </div>
</body>
</html>