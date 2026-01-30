<!DOCTYPE html>
<head>
 <meta charset="utf-8">
    <title>{{$titulo}}</title>
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
            <td style="text-align:right">{{$fechaImpresion}}</td>
        </tr>
    </table>
    <br>
    <img src="{{ url('/images/Mod01_Produccion/siz1.png') }}">
    <table style="padding-bottom:10px">
        <tr style="background-color: white">
            <td colspan="2" align="center" bgcolor="#fff">
                <b>{{env('EMPRESA_NAME')}}</b><br>
                <h3>{{$titulo}}</h3>
                <h5 style="font-size: 14px;">
                    Del {{date('d/m/Y', strtotime($fechaIS))}} al {{date('d/m/Y', strtotime($fechaFS))}}
                </h5>
                <h5 style="font-size: 14px;">
                    Material: {{$codMaterial}} @if($materialNombre) - {{$materialNombre}} @endif
                </h5>
                <h5 style="font-size: 14px;">
                    UDM: {{$udm}}
                </h5>
            </td>
        </tr>
    </table>
    <br>
</div>
