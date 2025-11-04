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

table { 
  width: 50%; 
  border-collapse: collapse; 
}

tr:nth-of-type(odd) { 
  background: #eee; 
}

th { 
  background: #ccc; 
  color: black; 
  font-weight: bold; 
  font-family: monospace;
}

td, th { 
  padding: 6px; 
  border: 1px solid #000; 
  text-align: left; 
  font-family:monospace;
}

p{
  color:#5499C7;
  font-family: verdana;
  font-size: 20px;  
}

pP{
  color: red;
  font-family: Verdana;
  font-size: 15px;
}
</style>      
<p>• Se registró un Rechazo de Inspección en Proceso</p>
                     
<table border="1px" id="usuarios" class="table table-striped table-bordered table-condensed">
    <tr>
        <th>Fecha de Inspección</th>
        <td>{{$dt}}</td>
    </tr>
    <tr>
        <th>Inspector</th>
        <td>{{$No_Nomina}}&nbsp;&nbsp;{{$Nom_Inspector}}</td> 
    </tr>
    <tr>
        <th>Orden de Producción (OP)</th>
        <td>{{$op}}</td>
    </tr>
    <tr>
        <th>Artículo</th>
        <td>{{$cod_articulo}} - {{$nom_articulo}}</td>
    </tr>
    <tr>
        <th>Cantidad Inspeccionada</th>
        <td>{{$cant_inspeccionada}}</td>
    </tr>
    <tr>
        <th>Centro de Inspección</th>  
        <td bgcolor="#F5B7B1">{{$nombre_centro}}</td>
    </tr>
    <tr>
        <th>Observaciones Generales</th>
        <td>{{$observaciones}}</td>
    </tr>
    @if(isset($defectos) && count($defectos) > 0)
    <tr>
        <th colspan="2" style="background-color: #dc3545; color: white;">DEFECTOS DETECTADOS</th>
    </tr>
    @foreach($defectos as $defecto)
    <tr>
        <td><strong>{{$defecto['punto']}}</strong></td>
        <td>
            <span style="color: red;">No Cumple</span><br>
            @if($defecto['empleado'])
            <small>Empleado Responsable: {{$defecto['empleado']}}</small><br>
            @endif
            @if($defecto['observacion'])
            <small>Observación: {{$defecto['observacion']}}</small>
            @endif
        </td>
    </tr>
    @endforeach
    @endif
</table>
<br>
<pP>• Se requiere atención inmediata para corregir los defectos detectados en la inspección</pP>
</body>
</html>

