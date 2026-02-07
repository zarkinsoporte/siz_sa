<!DOCTYPE html>
<head>
 <meta charset="utf-8">
    <title>REP-09 RESUMEN DE INCENTIVOS</title>
    <style>
    img {
        display: block;
        margin-left: 70px;
        width:90%;
        height: 50px;
        position: absolute;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: arial;
    }
    h3 { font-family: 'Helvetica'; margin-bottom: 2px; margin-top: 3px; }
    h5 { margin-top: 2px; }
    </style>
</head>

<div id="header">
    <table>
        <tr>
            <td></td>
            <td style="text-align:right">Fecha de Impresión: <?php echo date('d/m/Y H:i:s'); ?></td>
        </tr>
    </table>
    <br>
    <img src="{{ url('/images/Mod01_Produccion/siz1.png') }}">
    <table style="padding-bottom:10px">
        <tr style="background-color: white">
            <td colspan="2" align="center" bgcolor="#fff">
                <b>{{env('EMPRESA_NAME')}}</b><br>
                <h3>R-147 RESUMEN DE INCENTIVOS</h3>
                <h5 style="font-size: 14px;">
                    Mes: {{$mesNombre}} {{$ano}}
                </h5>
                <h5 style="font-size: 12px;">
                    Período: Del {{$fechaIS}} al {{$fechaFS}}
                </h5>
            </td>
        </tr>
    </table>
    <br>
</div>
