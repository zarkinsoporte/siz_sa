<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Correo</title>
</head>
<body class="container-fluid" style=" background-image: url(http://localhost/sizb/sizb/public/images/Zrk.jpg);
        background-repeat:no-repeat;
        background-size:cover;
        background-position:center;"><br>
        
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

    
    <div class="row">
    <div style="overflow-x:auto" class="col-md-12">
   <table  id="usuarios" class="table table-striped table-bordered table-condensed">
        <tr>
                 
              <th id="Encabezado"> 
                  <div align="Right">
                      De:{{$autor}}</div>
                  <div align="Right">
                      {{$dt}}</div>
                      <img class="svg hidden-xs" id="u196" src={{ URL::asset('images/svg-pegado-150982x45.svg') }} alt="" data-mu-svgfallback="/siz/public/images/svg%20pegado%20150982x45_poster_.png?crc=4279418901" width="200" height="200"
           
<div class="alert alert-Primary" align="Center">Se esta llevando a cabo el siguiente Reproceso</div>
                     
            </th>
             
     </tr>
        </table>
        <table id="usuarios" class="table table-striped table-bordered table-condensed">
             <tr>
            <th>Fecha del reproceso</th>
              <td>{{$dt}}</td>
         </tr>
        <tr>
            <th>Usuario</th>
            <td>{{$Nomina}}{{$autor}}</td> 
          
        </tr>
         <tr>
            <th>No.Orden</th>
             <td>{{$orden}}</td>
         </tr>
          <tr>
            <th>Cantidad</th>
             <td>{{$cantidad}}</td>
         </tr>
          <tr>
            <th>Estacion de Origen</th>  
               <td bgcolor="#58B435">{{$est_Act}}</td>
         </tr>
          <tr>
            <th>Estacion de Destino</th>
              <td bgcolor="#D13434">{{$est_Ant}}</td>
         </tr>
          <tr>
            <th>Motivo</th>
               <td>{{$Descripcion}}</td>
         </tr>
          <tr>
            <th>Descripcion de la falla</th>
              <td>{{$Nota}}</td>
         </tr>
          <tr>
           <th>Recibido en Estación Destino</th>
              <td>{{$Leido}}</td>
         </tr>
                          
        </table>
    </div>
     </div>
        <div class="alert alert-success" role="alert">El usuario que hizo este movimiento debe entregar el producto a la estacion de Destino y verificar que se acepte</div>
        </body>
</html>
    