


<!-- html5 no soporta <td align="right">$80</td> -->
<td class='alnright'>text to be aligned to right</td>
<style>
    .alnright { text-align: right; }
</style>

en cuidado con DOUBLE, porque no se maneja igual que DECIMAL. Double es un punto flotante, es decir un numero decimal por
aproximación, mientras que DECIMAL es de precisión. Esto puede hacer que se generen redondeos con el DOUBLE que terminen
ocasionado desfasajes en los cálculos, lo que no ocurrirá si usas DECIMAL. El manual recomienda DECIMAL para los valores
monetarios

{{date_format(date_create($rep->DocDate), 'd-m-Y')}}

$ {{number_format($totalEntrada,'2', '.',',')}} MXP



.table > tbody > tr > td
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {

    padding: 0px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;

}

table {

    width: 100%;
width: auto;
}