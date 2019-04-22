@extends('home') 
@section('homecontent')
<style>
    th { font-size: 12px; }
    td { font-size: 11px; }
    th, td { white-space: nowrap; }
    div.container {
        min-width: 980px;
        margin: 0 auto;
    }
    th:first-child {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        z-index: 5;
    }
    table.dataTable thead .sorting_asc{
        position: sticky;
    }
    .DTFC_LeftBodyWrapper{
        margin-top: 82px;
    }
    .DTFC_LeftHeadWrapper {
        display:none;
    }
</style>

<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header">
                Reporte BackOrder
                <small>Producción</small>
            </h3>

            <!-- <h5>Fecha & hora: {{date('d-m-Y h:i a', strtotime("now"))}}</h5> -->
        </div>
       
    </div>  
    <div id="infoMessage" class="alert alert-info" role="alert">
        ¡Importante!  Para un mejor rendimiento de las descargas, aplicar filtros al BackOrder.
     </div> 
  
    <!-- /.row -->
    <div class="row">
        <div class="container">
            <table  id="tbackorder" class="table-scroll display">
                <thead >
                    <tr>
                        <th class="estatico"><i>OP</i></label></th>
                        <th>Pedido</th>
                        <th>F.pedido</th>
                        <th>OC</th>
                        <th>D_proc</th>
                        <th>No_serie</th>
                        <th>Cliente</th>
                        <th>Modelo</th>
                        <th>Acabado</th>
                        <th>Descripción</th>
                        <th>Cant</th>
                        <th>%</th>
                        <th>Total %</th>
                        <th>Funda</th>
                        <th>Dias CT</th>
                        <th>Prg. Piel</th>
                        <th>HULE</th>
                        <th>CASCO</th>
                        <th>METAL</th>
                        <th>Sem-C</th>
                        <th>F.Compras</th>
                        <th>F.Ventas</th>
                        <th>Sem-P</th>
                        <th>F.Produc.</th>
                        <th>Prioridad</th>
                        <th>Desv</th>
                        <th>Notas</th>
                        <th>Especiales</th>
                        <th>Nom Modelo</th>
                    </tr>
                   
                </thead>
            </table>
        </div>
    </div>

</div>
<!-- /.container -->
@endsection
 
@section('homescript')
$('#tbackorder thead tr').clone(true).appendTo( '#tbackorder thead' );

$('#tbackorder thead tr:eq(1) th').each( function (i) {
    var title = $(this).text();
    $(this).html( '<input style="color: black;"  type="text" placeholder="Filtro '+title+'" />' );
   
    $( 'input', this ).on( 'keyup change', function () {       
            
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search(this.value, true, false)
                    
                    .draw();
            } 
                
    } );    
} );



var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
var diasSemana = new Array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
var f=new Date();
var hours = f.getHours();
var ampm = hours >= 12 ? 'pm' : 'am';
var fecha = 'ACTUALIZADO: '+ diasSemana[f.getDay()] + ', ' + f.getDate() + ' de ' + meses[f.getMonth()] + ' del ' + f.getFullYear()+', A LAS '+hours+":"+f.getMinutes()+ ' ' + ampm; 
var f = fecha.toUpperCase();

var table = $('#tbackorder').DataTable({
    dom: 'Bfrtip',
    buttons: [
        {
            text: '<i class="fa fa-columns" aria-hidden="true"></i> Columna',
            className: "btn btn-primary",
            extend: 'colvis',
            postfixButtons: [                                  
                {
                    text: 'Restaurar columnas',
                    extend: 'colvisRestore',     
                }             
                ]
        },
        {
            text: '<i class="fa fa-copy" aria-hidden="true"></i> Copy', 
            extend: 'copy',    
            exportOptions: {
                columns: ':visible',                
            }             
        },
        {
            text: '<i class="fa fa-file-excel-o"></i> Excel',
            className: "btn-success",
            action: function ( e, dt, node, config ) { 
                var data=table.rows( { filter : 'applied'} ).data().toArray(); 
                var json = JSON.stringify( data );
                $.ajax({ 
                    type:'POST', 
                    url:'reporte/backorderPDF', 
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: { "_token": "{{ csrf_token() }}", "arr": json }, 
                    success:
                        function(data){ 
                            window.location.href = 'reporte/backorderXLS';
                        } 
                }); 
            }          
        }, 
        {
            extend: 'collection',
            text: '<i class="fa fa-file-pdf-o"></i> Pdf',
            className: "btn-danger",
            buttons: [
                {
                    text: 'Ventas',                   
                    action: function ( e, dt, node, config ) {                                
                         var data=table.rows( { filter : 'applied'} ).data().toArray();               
                         var json = JSON.stringify( data );
                         $.ajax({
                            type:'POST',
                            url:'reporte/backorderPDF',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},                            
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "arr": json
                                },
                                success:function(data){
                                    window.open('reporte/backorderVentasPDF', '_blank')                                   
                            }
                         });
                     }
                       
                },
                {
                    text: 'Planeación',                    
                    action: function ( e, dt, node, config ) {
                        var data=table.rows( { filter : 'applied'} ).data().toArray();               
                        var json = JSON.stringify( data );
                        $.ajax({
                           type:'POST',
                           url:'reporte/backorderPDF',
                           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},                            
                           data: {
                               "_token": "{{ csrf_token() }}",
                               "arr": json
                               },
                               success:function(data){
                                  window.open('reporte/backorderPlaneaPDF', '_blank');          
                           }
                        });
                    },           
                },
            ]
        },
       
        {
            text: '<i class="fa fa-print"></i> Imprimir',
           
            extend: 'print',
            title: 'Reporte de Back Order',
            exportOptions: {
                columns: ':visible',                
            }
        },
       
    ],
   
    orderCellsTop: true,    
    scrollY:        "300px",
    "pageLength": 50,
    scrollX:        true,
    scrollCollapse: true,
    paging:         true,
   fixedColumns: true,
    processing: true,
    
    deferRender:    true,
    ajax: {
        url: '{!! route('datatables.showbackorder') !!}',
        data: function () {
                         
                        }              
    },
    columns: [        
        // { data: 'action', name: 'action', orderable: false, searchable: false}

        { data: 'OP', name:  'OP', orderable: true, searchable: true},
        { data: 'Pedido', name: 'Pedido'},
        { data: 'FechaPedido', name:  'FechaPedido'},
        { data: 'OC', name: 'OC'},
        { data: 'D_PROC', name: 'D_PROC'},
        { data: 'NO_SERIE', name: 'NO_SERIE'},
        { data: 'CLIENTE', name:  'CLIENTE'},
        { data: 'codigo1', name: 'codigo1'},
        { data: 'codigo3', name:  'codigo3'},
        { data: 'Descripcion', name:  'Descripcion'},
        { data: 'Cant', name:  'Cantidad'},
        { data: 'VSind', name:  'VSind' },
        { data: 'VS', name:  'VS'},
        { data: 'Funda', name:  'Funda'},
        { data: 'DEstacion', name:  'DEstacion' },
        { data: 'U_Grupo', name: 'U_Grupo' },
        { data: 'Secue', name:  'Secue'},
        { data: 'SecOT', name:  'SecOT' },
        { data: 'METAL', name:  'METAL' },
        { data: 'SEMANA2', name:  'SEMANA2'},
        { data: 'fentrega', name:  'fentrega'},//fCompras
        { data: 'fechaentregapedido', name:  'fechaentregapedido'},//fVentas
        { data: 'SEMANA3', name:  'SEMANA3'},
        { data: 'u_fproduccion', name:  'u_fproduccion'},
        { data: 'Prioridad', name:  'Prioridad'},
        { data: 'Desv', name:  'Desv'},
        { data: 'Comments', name:  'Comments'},
        { data: 'U_Especial', name:  'U_Especial'},
        { data: 'Modelo', name: 'Modelo'}
    ],
    "language": {
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json",
        buttons: {
            copyTitle: 'Copiar al portapapeles',
            copyKeys: 'Presiona <i>ctrl</i> + <i>C</i> para copiar o la tecla <i>Esc</i> para continuar.',
            copySuccess: {
                _: '%d filas copiadas',
                1: '1 fila copiada'
            }
        }
    },
    columnDefs: [
        { width: 80, targets: 2 },
        { width: 80, targets: 3 },
        { width: 100, targets: 6 },
        { width: 150, targets: 9 },
        { width: 80, targets: 13 },
        { width: 150, targets: 25 },
        { width: 150, targets: 27 },
    ],
    //revision
  
});

     
@endsection

<script>
document.onkeyup = function(e) {
  

   if (e.shiftKey && e.which == 112) {
    window.open("ayudas_pdf/AyM01_24.pdf","_blank");
  }
  } 
</script>