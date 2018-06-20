<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Correo</title>
</head>
<body>
<style>

body { 
  font: 14px/1.4 Georgia, Serif; 
}


  /* 
  Generic Styling, for Desktops/Laptops 
  */
  table { 
    width: 40%; 
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
Se llevó a cabo el siguiente Reproceso
                     
        <table border="1px" id="usuarios" class="table table-striped table-bordered table-condensed">
             <tr>
            <th>Fecha del reproceso</th>
              <td>{{$dt}}</td>
         </tr>
        <tr>
            <th>Usuario</th>
            <td>{{$No_Nomina}}{{$Nom_User}}</td> 
          
        </tr>
         <tr>
            <th>No.Orden</th>
             <td>{{$orden}}</td>
         </tr>
          <tr>
            <th>Cantidad</th>
             <td>{{$cant_r}}</td>
         </tr>
          <tr>
            <th>Estacion de Origen</th>  
               <td bgcolor="#58B435">{{$Est_act}}</td>
         </tr>
          <tr>
            <th>Estacion de Destino</th>
              <td bgcolor="#D13434">{{$Est_ant}}</td>
         </tr>
          <tr>
            <th>Motivo</th>
               <td>{{$reason}}</td>
         </tr>
          <tr>
            <th>Descripcion de la falla</th>
              <td>{{$nota}}</td>
         </tr>
          <tr>
            <th>Autorizado por:</th>
              <td>{{$autorizo}}</td>
            </tr>
          
                          
        </table>
 El usuario que hizo este movimiento debe entregar el producto a la estacion de Destino y verificar que se acepte
        </body>
</html>
    