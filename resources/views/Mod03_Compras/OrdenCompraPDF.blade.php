
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
            <div class=" col-xs-6">
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
                                <td class="text-center" style="font-size: 21px;font-weight: bold;">OC07588<br></td>
                                <td class="text-center" style="font-size: 21px;">16/12/2022<br></td>
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
            <div class=" col-xs-6">
                <table class = ""   cellpadding="0" cellspacing="0" border="0" width= "100%">
                    
                    <tr bgcolor="#e5e5e5">
                    <td width="" style="font-size: 15px; padding-left: 5px;"><b>DATOS DEL PROVEEDOR</b></td>
                    <td width="" style="font-size: 15px; padding: 15px;"><b></b></td>
                    </tr>
                    <tr>
                    <td width="" style="font-style: oblique; font-size: 15px; padding: 2px;">PROVEEDOR: <b>(P0051) PROVEEDOR CARPINTERO S.A. DE C.V.</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">RFC: <b>PCA020712810</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">DOMICILIO: <b>PELICANO COL. MORELOS, GUADALAJARA, JALISCO, MEX.</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">CODIGO POSTAL: <b>44910</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">TELEFONO: <b>7351816777</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">CONTACTO: <b>LUIS ALBERTO JIMENEZ</b></td>
                    </tr>                    
                    <tr>
                    <td width="" style="padding: 2px;">METODO DE: <b>CONTADO</b></td>
                    </tr>
                </table>
            </div>

            <div class="col-xs-6">
                <table class = ""   cellpadding="0" cellspacing="0" border="0" width= "100%">
                    
                    <tr bgcolor="#e5e5e5">
                    <td width="" style="font-size: 15px; padding-left: 5px;"><b>DATOS FISCALES</b></td>
                    <td width="" style="font-size: 15px; padding: 15px;"><b></b></td>
                    </tr>
                    <tr>
                    <td width="" style="font-style: oblique; font-size: 15px; padding: 2px;"> <b>GAZTAMBIDE S.A. DE C.V.</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;"> <b>PELICANO COL. MORELOS, GUADALAJARA, JALISCO, MEX.</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">RFC: <b>GAZTA2023</b></td>
                    </tr>
                    
                    <tr>
                        <td width="" style="padding: 2px;">FACTURAS: <b>FACTURAS@ZARKIN.COM</b></td>
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
                    <td width="" style="padding: 2px;">DOMICILIO: <b>CALLE 4 NO. 2390</b></td>
                    </tr>
                    <tr>
                    <td width="" style="padding: 2px;">CODIGO POSTAL: <b>44940</b></td>
                    </tr>
                    <tr>
                        <td width="" style="padding: 2px;">
                            ENTREGAR A: <b>LUIS ALBERTO</b></td>
                    </tr>
                
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
                        <td width="40" align="left"><b>Fecha Req.</b></td>
                        <td width="40" align="right"><b>Precio</b></td>
                        <td width="40" align="right"><b>Subtotal</b></td>
                        <td width="40" align="right"><b>Descuento</b></td>
                        <td width="40" align="right"><b>IVA</b></td>
                        <td width="40" align="right"><b>Total</b></td>
                    </tr>
                    <hr size="2" />

                    <tr style="font-size: 13px">
                        <td width="15" align="left">1</td>
                        <td width="30" align="left">12345</td>
                        <td style ="white-space: nowrap;" align="left"> {{substr('ARTICULO PROYECTO ZARKIN ENERO 2023', 0, 55)}}</td>
                        <td width="35" align="center">2</td>
                        <td width="30" align="left">PZA</td>
                        <td width="40" align="left">01/02/2023</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$148.58</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$150.00</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$0.00</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$451.00</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$600.00</td>
                    </tr>
                    
                    <tr bgcolor="#d4d4d4" style="font-size: 16px">
                        <td  width="15" align="left"></td>
                        <td width="60" align="left">MONEDA:</td>
                        <td width="60" align="left">MXN</td>
                        <td width="35" align="right"></td>
                        <td width="30" align="left"></td>
                        <td width="40" align="left"></td>
                        <td width="40" align="right"><b>TOTAL</b></td>
                        <td style ="white-space: nowrap;" width="40" align="right">$400.00</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$500.00</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$10.00</td>
                        <td style ="white-space: nowrap;" width="40" align="right">$15498.00</td>
                    </tr>
                </table>
                <hr size="2" />
                <table width="100%" cellpadding="1" cellspacing="0" border="0">
                    
                    <tr bgcolor="#d4d4d4">
                        <td style="font-size: 16px" colspan="2"> <b></b></td>
                    </tr>
                
                    <tr>
                        <td nowrap width="100%" style="font-size: 16px">COMENTARIOS OC: <b>'OC_Comentarios.'</b></td>
                    </tr>
                
                    <tr>
                        <td nowrap width="100%" style="font-size: 16px">ELABORÓ: <b>LUIS ALBERTO JIMENEZ</b></td>
                    </tr>
                
                    <tr>
                        <td nowrap width="100%" style="font-size: 16px">SOLICITÓ: <b>'SOLICITO.'</b></td>
                    </tr>
                
                    <tr>
                        <td  nowrap width="100%" style="font-size: 16px">AUTORIZÓ: <b>'AUTORIZO.'</b></td>
                    </tr>
                
                    <tr>
                        <td nowrap width="100%" style="padding-bottom: 8em;  font-size: 16px" colspan="2">REQUISICIÓN: <b>'-REQ_CodigoRequisicion.'</b></td>
                    </tr>
                
                    <tr>
                        <td width="100%" style="font-size: 16px" colspan="2" align="right"><b>RECIBIÓ USUARIO:</b> </td>
                        <td width="250" style="font-size: 16px">
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