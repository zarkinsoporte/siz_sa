<!DOCTYPE html>
<html lang="en">

        <head>
            <meta charset="utf-8">
            <title>{{ 'Plantilla de Personal' }}</title>
                <style>
                /*
                Generic Styling, for Desktops/Laptops 
                */
                img {
                display: block;
                margin-left:50px;
                margin-right:50px;
                width: 700%;
                margin-top:4.5%;
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
                    font-family: 'Helvetica';
                    font-size:80%;
                }
                td{
                    font-family: 'Helvetica';
                    font-size:80%;
                }

                    img{
                    width:500;
                        height: 20;
                        position: absolute;right:-2%;
                        align-content:;
                    }
                    h3{
                        font-family: 'Helvetica';
                        margin-top: 4px;
                        margin-bottom: 3px;
                    }
                    b{
                        font-size:100%;
                    }
                #header  {position: fixed; margin-top:2px; }
                #content {position: relative; top:11%}

            </style>
        </head>
<body>

<div id="header" >

<img src="{{ url('/images/Mod01_Produccion/siz1.png') }}" >
<!--empieza encabezado, continua cuerpo-->
                
            <table border="1px" class="table table-striped">
                <thead class="thead-dark">  
                        <tr>
                         <td colspan="6" align="center" bgcolor="#fff">   
                         <b>MODULO 00 SISTEMAS </b>
                         <h3>PLANTILLA DE PERSONAL</h3>
                         <h3>{{'DEPARTAMENTO DE '.$clave}}</h3>
                         <b>{{'Impreso por: '. Auth::user()->firstName .' '. Auth::user()->lastName}}</b>
                        
                        </td>

                         </tr>
                         </thead>                      
</table>
</div> 
<div id="content">
<!--Cuerpo o datos de la tabla-->
    <table  border="1px" class="table table-striped">
        <thead class="thead-dark">
  <tr>
                <th>#</th>
                <th>Funciones</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>No. Nomina</th>
                <th>Estaciones</th>
                </tr>
        </thead><tbody>
            
        @foreach($users as $key => $P_user)
         
            <tr>              
                <td>{{$key + 1}}</td>
                <td>{{$P_user->jobTitle}}</td>                
                <td>{{$P_user->firstName}}</td>
                <td>{{$P_user->lastName}}</td>
                <td>{{$P_user->U_EmpGiro}}</td>
                <td>{{$P_user->U_CP_CT}}</td>
            </tr>
           
         @endforeach
         </tbody>
    </table>
  
</div>       



        <footer>
                <script type="text/php">
                $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}';
                $date = 'Fecha de impresion : <?php echo $hoy = date("d-m-Y H:i:s");?>';
                $tittle = 'Siz_Plantilla_Personal.Pdf';
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->page_text(35, 755, $text, $font, 9);
                $pdf->page_text(405, 23, $date, $font, 9);
                $pdf->page_text(420, 755, $tittle, $font, 9);
                $empresa =  'Sociedad: <?php echo env('EMPRESA_NAME'); ?>';
                $pdf->page_text(40, 23, $empresa, $font, 9); 
                </script> 
        </footer>   
     @yield('subcontent-01')

</body>

</html>