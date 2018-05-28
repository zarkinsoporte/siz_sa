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

        <div align="left">
            No. Orden <h2><?php echo $data[0]->DocNumOf ?></h2>
            Código <h2><?php echo $data[0]->ItemCode ?> - <?php echo $data[0]->ItemName ?></h2>
            Cliente <h2><?php echo $data[0]->CardCode ?> - <?php echo $data[0]->CardName ?></h2>
            Fecha Entrega <h2><?php echo $data[0]->FechaEntrega ?>
            Cantidad Planeada <h2><?php echo $data[0]->plannedqty ?>
            V.S <h2><?php echo $data[0]->VS ?>
            <?php echo $data[0]->ItemCode ?> - <?php echo $data[0]->ItemName ?>
        </div>
        <div align="right">
            Orden de fabricación: <?php echo $op ?>
            <br>
            V.S &nbsp; &nbsp; <?php echo number_format($data[0]->VS, 2, '.', ','); ?>
        </div> 
        <hr>    

     <div class="row">
        <div class="col-6">
             <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">No. Entrada</th>
                        <th scope="col">UM</th>
                        <th scope="col">Solicitada</th>
                        <th scope="col">Entregada</th>
                        <th scope="col">Devolución</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $rep)
                        <tr>
                            <td scope="row">
                               <?php echo date('d-m-Y', strtotime($rep->FechaEntrega));  ?>
                            </td>
                            <td scope="row">
                                {{ $rep->Codigo }}
                            </td>
                            <td scope="row">
                                {{$rep->Descripcion}}
                            </td>
                            <td scope="row">
 
                            </td>
                            <td scope="row">
                                {{ $rep->InvntryUom }}
                            </td>
                            <td scope="row">
                                {{ $rep->Cantidad }}
                            </td>
                            <td scope="row">
                                
                            </td>
                            <td scope="row">
                               
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