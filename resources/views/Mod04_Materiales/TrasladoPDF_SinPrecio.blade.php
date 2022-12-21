<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transferencia SAP</title>
    <style>
        /*
                Generic Styling, for Desktops/Laptops
                */
        .firma {
            border: none;

        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-family: arial;
        }

        th {
            color: white;
            font-weight: bold;
            font-family: 'Verdana';
            font-size: 12px;
            background-color: #333333;
        }

        td {
            font-family: 'Verdana';
            font-size: 11px;
        }

        img {
            display: block;
            margin-top: 1.5%;
            width: 670;
            height: 45;
            position: absolute;
            right: 2%;
        }

        h5 {
            font-family: 'Helvetica';
            margin-bottom: 10;
        }

        h2 {
            font-family: 'Helvetica';
            margin-bottom: -15;
            margin-top: 0;
        }

        small {
            font-size: 16px;
        }

        .fz {
            font-size: 100%;
            margin-top: 7px;
        }

        #header {
            position: fixed;
            margin-top: 2px;

        }

        #content {
            position: relative;
            top: 11%
        }

        table,
        th,
        td {
            text-align: center;
            border: 1px solid black;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .zrk-silver {
            background-color: #AFB0AE;
            color: black;
        }

        .zrk-dimgray {
            background-color: #514d4a;
            color: white;
        }

        .zrk-gris-claro {
            background-color: #eeeeee;
            color: black;
        }

        .zrk-silver-w {
            background-color: #656565;
            color: white;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td {
            padding-bottom: 2px;
            padding-top: 2px;
            padding-left: 4px;
            padding-right: 0px;
        }

        .total {
            text-align: right;
            padding-right: 4px;
        }

        .text-left {
            text-align: left;
            padding-left: 4px;
        }

        .page_break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    
    <!--Cuerpo o datos de la tabla-->
    <div id="content">

        <div class="row">

            <div class="col-md-12">
                <div><span style="float:right">
                        <h2>
                            @if (isset($info1))
                            @if($info1[0]->Printed == 'N')
                            ORIGINAL
                            @else
                            COPIA
                            @endif
                            @endif
                        </h2>
                    </span>
                    <h2>Traslado SAP #{{$transfer}} <small>  # {{$tipoDoc}}:
                            @if (isset($info1))
                            {{$info1[0]->FolioNum}}
                            @else
                            @if (isset($id))
                            {{$id}}
                            @endif
                            @endif
                        </small></h2>
                </div>
                <div><span style="float:right">
                        <h3>Lista de Precios 10
                            <?php
                       $Soldate = date_create($fechaSol);
                    ?>

                            <br>Fecha: {{ date_format($Soldate, 'd-m-Y') }}

                        </h3>
                    </span>
                    <h3>De Almacén
                        @if (isset($info1))
                        {{$info1[0]->Filler}}
                        @else
                        @if (isset($almacenOrigen))
                        {{$almacenOrigen}}
                        @endif
                        @endif
                        <br>Fecha Traslado SAP:
                        @if (isset($info1))
                        <?php
                            $SAPdate = date_create($info1[0]->CreateDate);
                        ?>
                        {{ date_format($SAPdate, 'd-m-Y') }}
                        @else
                        Por Definir
                        @endif
                    </h3>
                </div>
                <table class="table table-striped mytable" style="table-layout:fixed;">
                    <thead>
                        <tr>
                            @if (isset($info1))

                            <th style="width:5%;">#</th>
                            @endif
                            <th style="width:7%;">Código</th>
                            <th style="width:35%;">Descripción</th>
                            <th style="width:7%;">UM</th>
                            <th style="width:12%;">Almacén</th>
                            <th style="width:7%;">Cantidad</th>


                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($transfer1 as $art)
                        <tr <?php ?>>
                            @if (isset($info1))
                            <td style="width:5%;">{{$art->lineNum}}</td>
                            @endif
                            <td style="width:7%;">{{$art->ItemCode}}</td>
                            <td style="width:35%;" class="text-left">{{$art->Dscription}}</td>
                            <td style="width:7%;">{{$art->unitMsr}}</td>
                            <td style="width:12%;">{{$art->WhsCode}}</td>
                            <td style="width:7%;">{{number_format($art->Quantity, 2)}}</td>


                        </tr>
                        @endforeach

                </table>
                <br>
                <div>
                    <table class="mytable">
                        <thead>
                            <th style="width:10%;">Comentario:</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width:90%;" class="text-left">
                                    @if (isset($info1))
                                    
                                    @if(strlen($comentario) == 0)
                                        {{$info1[0]->Comments}}
                                    @else
                                        {{str_replace($comentario, '',$info1[0]->Comments)}}
                                    @endif
                                    
                                    
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                @if(strlen($comentario) > 0)
                <div>
                    <table class="mytable">
                        <thead>
                            <th style="width:10%;">Observaciones de la Solicitud:</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width:90%;" class="text-left">{{$comentario}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif
               
            </div>
            <br><br><br><br>
            <table class="firma">

                <tbody>
                    <tr class="firma">
                        <td style="width:45%; " class="text-center firma">{{$entrega}}</td>
                        <td style="width:10%;" class="firma"></td>
                        <td style="width:45%; " class="text-center firma">{{$recibe}}</td>
                    </tr>
                    <tr class="firma">
                        <td style="width:45%; border-top: 2px solid black" class="text-center firma">Nombre y firma de
                            quien entrega</td>
                        <td style="width:10%;" class="firma"></td>
                        <td style="width:45%; border-top: 2px solid black" class="text-center firma">Nombre y firma de
                            quien recibe</td>
                    </tr>
                </tbody>
            </table>
            <br><br>
            <div>
                @if (isset($seguimiento))
                 <div class="col-md-6">
                    <table class="mytable" style="width: 60%">
                        <thead>
                            <th style="width:10%;">Seguimiento:</th>
                            <th style="width:20%;">Usuario</th>
                            <th style="width:20%;">Fecha</th>
                            <th style="width:10%;">Duración (d, h:m)</th>
                        </thead>
                        <tbody>
                            @foreach ($seguimiento as $item)
                            <tr>
                                <td style="widt:90%;" class="text-left">{{$item['seguimiento']}}</td>
                                <td style="widt:90%;" class="text-left">{{$item['usuario']}}</td>
                                <td style="widt:90%;" class="text-left">{{$item['fecha']}}</td>
                                <td style="widt:90%;" class="text-left">{{$item['duracion']}}</td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                 </div>
                @endif
            </div>
        </div>

    </div>


   
    @yield('subcontent-01')

</body>

</html>