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
<script>
    function setPageNumbers() {
        /*
          vars will have the following structure

          vars: {
            page,
            frompage,
            topage,
            webpage,
            section,
            subsection,
            date,
            isodate,
            time,
            title,
            doctitle,
            sitepage,
            sitepages
          }
        */
        var vars = {};
        var queryStringsFromUrl = document.location.search.substring(1).split('&');
        for (var queryString in queryStringsFromUrl) {
            if (queryStringsFromUrl.hasOwnProperty(queryString)) {
                var temp = queryStringsFromUrl[queryString].split('=', 2);
                vars[temp[0]] = decodeURI(temp[1]);
            }
        }

        var element = document.getElementById('pageNumber');

        if (document.cookie.split(';').length === 2) {
          var section = document.cookie.split(';')[0].split('=')[1];

          if (vars.section !== section) {
            document.cookie = 'currentSection=' + vars.section;
            document.cookie = 'startedAt=' + vars.page;
          }

          // startedAt is the page on which a section started
          var startedAt = parseInt(document.cookie.split(';')[1].split('=')[1]);
          element.textContent = vars.page - startedAt + 1;
        } else {
          document.cookie = 'currentSection=' + vars.section;
          document.cookie = 'startedAt=' + vars.page;
          element.textContent = 1;
        }
      }
</script>
</head>

<body onload="setPageNumbers()">
   


        
<div id="content">
             <table>
                    <thead class="thead-dark">
                    <tr>
                        
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Código</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Descripción</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">UM</th>
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
                              <tr><td colspan="4" align="center" bgcolor="#ccc"> <?php echo $EstacionO ?> </td></tr>

                              <?php
                          }
                          
                           $temporal=$rep->Estacion;
                          //dd($EstacionO);
                           if($EstacionO==$temporal){ 
                            ?>
                            <tr>
                          
                            
                            <td align="center" scope="row">
                                {{ $rep->Codigo }}
                            </td>
                            <td style="white-space:nowrap;" scope="row">
                                {{$rep->Descripcion}}
                            </td>
                            <td align="center" scope="row">
                                {{ $rep->InvntryUom }}
                            </td>
                            <td align="center" scope="row">
                            <?php echo number_format($rep->Cantidad,2); ?>
                            </td>
                        </tr>
                           <?php
                           }else{
                            $EstacionO=$temporal;
                            ?>  
                           <tr><td colspan="4"align="center" bgcolor="#ccc"> <?php echo $EstacionO ?> </td></tr>
                           <tr>
                           
                            <td align="center" scope="row">
                                {{ $rep->Codigo }}
                            </td>
                            <td style="white-space:nowrap;" scope="row">
                                {{$rep->Descripcion}}
                            </td>
                            <td align="center"scope="row">
                                {{ $rep->InvntryUom }}
                            </td>
                            <td align="center"scope="row">
                                <?php echo number_format($rep->Cantidad,2); ?>

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

       
        


<p style="font-size: 12px"> <span id="pageNumber"></span> </p>
</body>

</html>