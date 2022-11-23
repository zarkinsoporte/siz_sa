<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo csrf_token() ?>">
    
   
    <style>
    html {
margin: 0;
}
body {
margin: 1mm 1mm 1mm 1mm;
}
        img { 
            display: block;
  margin-left: auto;
  margin-right: auto;
  width: 50%;
        }
        h4{
            margin-top: 2px;
            margin-bottom: 2px;
        }
        span{
            font-size: 14px;
            font-weight:normal;
        }
    </style>
</head>

<body>
    <!--Cuerpo o datos de la tabla-->
    <div id="container">
        
                <h4 class="center" style="text-align: center">{{$codigo}}</h4>
                <img  src="data:image/png;base64, <?php echo base64_encode($CodigoQR) ?> ">
             
                <h4>{{$descripcion}}</h4>
                <h4>Orden de Producción: <span>{{$op}}</span></h4>
                <h4>Cliente: <span>{{$cliente}}</span></h4>
                <h4>Pedido: <span>{{$pedido}}</span></h4>
                <h4># Serie: <span>{{$serie}}</span></h4>
                <h4>Destino: <span>{{$destino}}</span></h4>
             
    

    </div>
</body>

</html>