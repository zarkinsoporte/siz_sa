<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'Fusion Confort' }}</title>

    <!-- Styles -->

    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
    <!-- Material Design fonts -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>

    <![endif]-->
{!! Html::style('assets/css/bootstrap.css') !!}
{!! Html::style('assets/css/bootstrap-switch.min.css') !!}
{!! Html::style('assets/css/bootstrap-switch.css') !!}
{!! Html::style('assets/css/font-awesome.css') !!}
{!! Html::style('assets/css/sb-admin.css') !!}
</head>

<body>
    <div id="app">
        <div id="wrapper">

<div class="container" >

     <div class="row">
        <div class="col-6">
             <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $inv)
                        <tr>
                            <td scope="row">
                                <img src="{{ asset('/images/zarkin.png') }}" width="120px" height="52px">       
                            </td>
                            <td scope="row">
                                    
                            </td>
                            <td scope="row">
                            INVENTARIO DE EQUIPO DE CÓMPUTO      
                            </td>
                        </tr>    
                        <tr>
                            <td scope="row"colspan="2">
                                Número de Equipo
                            </td>
                            <td scope="row">
                                {{ $inv->numero_equipo }}      
                            </td>
                        </tr> 
                        <tr>
                            <td scope="row"colspan="2">
                                Equipo
                            </td>
                            <td scope="row">
                                {{ $inv->nombre_equipo }}  
                            </td>
                        </tr>   
                        <tr>
                            <td scope="row"colspan="2">
                                Monitor
                            </td>
                            <td scope="row">
                                {{ $inv->nombre_monitor }}
                            </td>
                        </tr>
                        <tr>
                            <td scope="row"colspan="2">
                                Equipo Asignado a
                            </td>
                            <td scope="row">
                                {{ $inv->correo}}
                            </td>
                        </tr>    
                        <tr>
                            <td scope="row"colspan="2">
                                Tipo de Equipo
                            </td>
                            <td scope="row">
                                {{ $inv->tipo_equipo }}
                            </td>
                        </tr>       
                    @endforeach 
                    </tbody>
                </table>
        </div>
     <div class="col-6">
     <h4>Confidencialidad/Responsabilidades</h4>
        <h6>1. ES RESPONSABILIDAD DEL USUARIO ACATAR LA PRESENTE NORMATIVIDAD EN EL USO DE LOS BIENES INFORMÁTICOS</h6>
        <h6>2. SERÁ RESPONSABILIDAD DEL USUARIO EL USO Y CUIDADO DEL EQUIPO DE CÓMPUTO</h6>
        <h6>3. QUEDA ESTRICTAMENTE PROHIBIDO LA DESCARGA, INSTALACIÓN O DISTRIBUCIÓN DE SOFTWARE QUE NO SEA PROPORCIONADO POR EL ÁREA DE SISTEMAS</h6>
        <h6>4. EL ADMINISTRADOR DE SISTEMAS CUENTA CON LA FACULTAD DE AUDITAR LOS EQUIPOS EN EL MOMENTO QUE LO CONSIDERE OPORTUNO SIN QUE EXISTA LA OBLIGACIÓN DE INFORMAR AL USUARIO DE LA ACTIVIDAD A REALIZAR</h6>
        <h6>5. LA CONSERVACIÓN Y USO OPTIMO DEL BIEN INFORMÁTICO, ES RESPONSABILIDAD DIRECTA DEL ENCARGADO, EL CUAL DEBERÁ DAR AVISO A SISTEMAS EN CASO DE EXTRAVÍO O DE DAÑO AL EQUIPO</h6>
        <h6>6. AL TERMINO DE LAS LABORES EL RESPONSABLE VERIFICARÁ QUE EL EQUIPO SE ENCUENTREN DEBIDAMENTE APAGADO</h6>
        <h6>7. LOS USUARIOS DE LAS COMPUTADORAS NO PODRÁN CAMBIAR LA CONFIGURACIÓN DE LOS EQUIPOS</h6>
        <h6>8. LOS DAÑOS O SINIESTROS QUE OCURRAN AL EQUIPO DEBERAN NOTIFICARSE AL ÁREA DE SISTEMAS POR MEDIO DE UN ESCRITO PARA EVALUAR EL DAÑO</h6>
        <h6>9. TODOS LOS USUARIOS QUE REQUIERAN DE UN ADITAMENTO PARA SU EQUIPO, DEBERÁN SOLICITARLO AL DEPARTAMENTO DE SISTEMAS A TRAVÉS DE UN CORREO ELECTRÓNICO</h6>
        <h6>10. EL USUARIO DE UN BIEN INFORMÁTICO, CON FACULTADES DE ACUERDO A SUS FUNCIONES , MANIFIESTA QUE CONOCE, ACEPTA Y CUMPLIRÁ LAS OBLIGACIONES Y RESPONSABILIDADES QUE IMPLICA, RESUMIDAS EN LO YA DESCRITO, DE CONFORMIDAD PARA LA ADMINISTRACIÓN.</h6> 

        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <center>Nombre y Firma</center>
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