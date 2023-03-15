
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
</head>   
<body>
    <div class="container">
        <div class="row">
            <div class=" col-xs-5">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" style="background: #e5e5e5;font-size: 18px;font-weight: bold;">Orden de Compra</th>
                                <th class="text-center" style="background: #e5e5e5;font-size: 18px;font-weight: bold;">Fecha OC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" style="font-size: 21px;font-weight: bold;">{{$resumen->OC_NUM}}<br></td>
                                <td class="text-center" style="font-size: 21px;">{{$resumen->OC_DATE}}<br></td>
                            </tr>
                            <tr></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class=" col-xs-5">
                <table class = ""   cellpadding="0" cellspacing="0" border="0" width= "100%">
                    
                    <tr bgcolor="#e5e5e5">
                    <td width="" style="font-size: 15px; padding-left: 5px;"><b>DATOS DEL PROVEEDOR</b></td>
                    <td width="" style="font-size: 15px; padding: 15px;"><b></b></td>
                    </tr>
                    <tr>
                    <td width="" style="font-style: oblique; font-size: 15px; padding: 2px;">PROVEEDOR: <b>{{$resumen->PRO_NOMBRE}}</td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">RFC: <b>{{$resumen->PRO_RFC}}</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">DOMICILIO: <b>{{$resumen->PRO_DOMICILIO}}</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">CODIGO POSTAL: <b>{{$resumen->PRO_CP}}</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">TELEFONO: <b>{{$resumen->CON_TEL}}</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">CONTACTO: <b>{{$resumen->CONTACTO . '  '. $resumen->CON_EMAIL }}</b></td>
                    </tr>                    
                    <tr>
                    <td width="" style="padding: 2px;">METODO DE PAGO: <b>{{$resumen->OC_METODO_PAGO}}</b></td>
                    </tr>
                </table>
            </div>

            <div class="col-xs-7">
                <table class = ""   cellpadding="0" cellspacing="0" border="0" width= "100%">
                    
                    <tr bgcolor="#e5e5e5">
                    <td width="" style="font-size: 15px; padding-left: 5px;"><b>DATOS FISCALES</b></td>
                    <td width="" style="font-size: 15px; padding: 15px;"><b></b></td>
                    </tr>
                    <tr>
                    <td width="" style="font-style: oblique; font-size: 15px; padding: 2px;"> <b>{{$comp->RAZON_SOCIAL}}</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;"> <b>{{$comp->DOMICILIO_FICAL}}</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">CODIGO POSTAL: <b>{{$comp->CP_FISCAL}}</b></td>
                    </tr>
                    <td width="" style="padding: 2px;">RFC: <b>{{$comp->RFC}}</b></td>
                    </tr>
                
                    </table>
                    <table class = ""   cellpadding="0" cellspacing="0" border="0" width= "100%">
                    
                    <tr bgcolor="#e5e5e5">
                    <td width="" style="font-size: 15px; padding-left: 5px;"><b>DATOS DE ENTREGA</b></td>
                    <td width="" style="font-size: 15px; padding: 15px;"><b></b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;"> ALMACEN: <b>ALMACEN DE MATERIA PRIMA</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">DOMICILIO: <b>{{$comp->DOMICILIO_ENTREGA}}</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">CODIGO POSTAL: <b>{{$comp->CP_ENTREGA}}</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">CONTACTO COMPRAS: <b>{{$resumen->OC_COMPRADOR}}</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">EMAIL: <b>{{$resumen->OC_COMPRADOR_EMAIL}}</b></td>
                    </tr>
                    <!-- <tr>
                        <td width="" style="padding: 2px;">
                            ENTREGAR A: <b>LUIS ALBERTO</b></td>
                    </tr> -->
                
                </table>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
            
                    <tr>
                        <td width="10" align="left"><b>#</b></td>
                        <td width="30" align="left"><b>Codigo</b></td>
                        <td width="70" align="left"><b>Descripcion</b></td>
                        <td width="35" align="center"><b>Cantidad</b></td>
                        <td width="30" align="left"><b>UM</b></td>
                       
                        <td width="40" align="right"><b>Precio</b></td>
                        <td width="40" align="right"><b>Subtotal</b></td>
                        <td width="40" align="right"><b>Descuento</b></td>
                        <td width="40" align="right"><b>IVA</b></td>
                        <td width="40" align="right"><b>Total</b></td>
                    </tr>
                    <hr size="2" />
                @foreach ($detalle as $det)
                    <tr style="font-size: 13px">
                        <td width="15" align="left">{{$det->LIN_NUMERO + 1}}</td>
                        <td width="30" align="left">{{$det->LIN_CODIGO}}</td>
                        <td style ="white-space: nowrap;" align="left"> {{substr($det->LIN_DESCRIPCION, 0, 55)}}</td>
                        <td width="35" align="center">{{number_format($det->LIN_CANTIDAD,2)}}</td>
                        <td width="30" align="left">{{$det->LIN_UM}}</td>
                       
                        <td style ="white-space: nowrap;" width="40" align="right">$ {{number_format($det->LIN_PRECIO, 2, '.', ',')}}</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$ {{number_format($det->LIN_TOTAL, 2, '.', ',')}}</td> 
                        <td style ="white-space: nowrap;" width="40" align="right">$ {{number_format($det->LIN_DISC, 2, '.', ',')}}</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$ {{number_format($det->LIN_IVA, 2, '.', ',')}}</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$ {{number_format($det->LIN_GTOTAL, 2, '.', ',')}}</td>
                    </tr>
                @endforeach     
                    <tr bgcolor="#d4d4d4" >
                        <td style="font-size: 14px; ; padding-bottom: 10px"  width="15" align="left"></td>
                        <td style="font-size: 14px; padding-top: 10px; padding-bottom: 10px" width="60" align="left">MONEDA:</td>
                        <td style="font-size: 14px; padding-top: 10px; padding-bottom: 10px" width="60" align="left">{{$resumen->OC_MONEDA}}</td>
                        <td style="font-size: 14px; padding-top: 10px; padding-bottom: 10px" width="35" align="right">@if ($resumen->OC_MONEDA != 'MXP')
                            T.C.
                        @endif</td>
                        <td style="font-size: 14px; padding-top: 10px; padding-bottom: 10px" width="30" align="left">@if ($resumen->OC_MONEDA != 'MXP')
                            {{number_format($resumen->OC_RATE, 2, '.', ',')}}}}
                        @endif</td>
                        
                        <td style="font-size: 14px; padding-top: 10px; padding-bottom: 10px" width="40" align="right"><b>TOTAL</b></td>
                        <td style ="white-space: nowrap; font-size: 14px; padding-top: 10px; padding-bottom: 10px" width="40" align="right">$ {{number_format($resumen->OC_TOTAL_DOC - $resumen->OC_TOTAL_IVA + $resumen->OC_TOTAL_DISC, 2, '.', ',')}}</td>
                        <td style ="white-space: nowrap; font-size: 14px; padding-top: 10px; padding-bottom: 10px" width="40" align="right">$ {{number_format($resumen->OC_TOTAL_DISC, 2, '.', ',')}}</td>
                        <td style ="white-space: nowrap; font-size: 14px; padding-top: 10px; padding-bottom: 10px" width="40" align="right">$ {{number_format($resumen->OC_TOTAL_IVA, 2, '.', ',')}}</td>
                        <td style ="white-space: nowrap; font-size: 14px; padding-top: 10px; padding-bottom: 10px" width="40" align="right">$ {{number_format($resumen->OC_TOTAL_DOC, 2, '.', ',')}}</td>
                    </tr>
                </table>
                <hr size="2" />
                <table width="100%" cellpadding="1" cellspacing="0" border="0">
                    
                    <tr bgcolor="#d4d4d4">
                        <td style="font-size: 16px" colspan="2"> <b></b></td>
                    </tr>
                
                    <tr>
                        <td nowrap width="100%" style="font-size: 16px">COMENTARIOS OC: <b>{{$resumen->OC_COMENTARIO}}</b></td>
                    </tr>
                    
                    <tr style="">
                        <td width="100%" style="font-size: 16px; padding-top: 40px" colspan="2" align="right"><b>RECIBIO EMPLEADO:</b> </td>
                        <td width="250" style="font-size: 16px; padding-top: 40px">
                            <b>________________________________________________________________________________</b></td>
                    </tr>
                
                    <tr>
                        <td style="font-size: 16px" colspan="2"></td>
                        <td align="center" width="250" style="font-size: 16px"><b>Nombre y Firma, Fecha</b></td>
                    </tr>
                
                </table>
            </div>
        </div>
    </div>   
</body>
</html>