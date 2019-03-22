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
div.ColVis {
        float: left;
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
                Reporte BackOrder Casco
                <small>Producción</small>
            </h3>

            <!-- <h5>Fecha & hora: {{date('d-m-Y h:i a', strtotime("now"))}}</h5> -->
        </div>
       
    </div>  
    <div class="alert alert-info" role="alert">
        ¡Importante!  Para un mejor rendimiento de las descargas, aplicar filtros al BackOrder.
     </div> 
  
    <!-- /.row -->
    <div class="row">
        <div class="container">
            <table  id="tbackorder" class="display">
                <thead >
                    <tr>
                        <th>Orden Casco</th>
                        <th>Fecha del Programa</th>
                        <th>Dias Proc.</th>
                        <th>Orden de Trabajo</th>
                        <th>Código</th>

                        <th>Descripción</th>
                        <th>Total en Proceso</th>
                        <th>Planeación (400)</th>
                        <th>Habilitado (403)</th>
                        <th>Armado (406)</th>

                        <th>Tapado (409)</th>
                        <th>Pegado Hule (415)</th>
                        <th>Entrega Casco (418)</th>
                        <th>VS</th>
                        <th>Total VS</th>                       
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
            extend: 'excelHtml5',
            message: "SALOTTO S.A. DE C.V.\n",
            messagetwo: "BACK ORDER PROGRAMADO CASCO.\n",
            messagethree: f,
            exportOptions: {
                columns: ':visible',                
            }          
        }, 
        {
            text: '<i class="fa fa-file-pdf-o"></i> Pdf',           
            className: "btn-danger",            
                    action: function ( e, dt, node, config ) {                                
                         var data=table.rows( { filter : 'applied'} ).data().toArray();               
                         var json = JSON.stringify( data );
                         $.ajax({
                            type:'POST',
                            url:'reporte/inventario',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},                            
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "arr": json
                                },
                                success:function(data){
                                    window.open('reporte/inventarioComputoPDF', '_blank')                                   
                            }
                         });
                     }         
        },
       
        {
            text: '<i class="fa fa-print"></i> Imprimir',
           
            extend: 'print',
            title: 'Reporte de Back Order Casco',
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
    fixedColumns:   true,
    processing: true,
    
    deferRender:    true,
    ajax: {
        url: '{!! route('datatables.showbackordercasco') !!}',
        data: function () {
                         
                        }              
    },
    columns: [        
        // { data: 'action', name: 'action', orderable: false, searchable: false}

        { data: 'DocNum', name:  'DocNum', orderable: true, searchable: true},
        { data: 'DueDate', name: 'DueDate'},
        { data: 'diasproc', name:  'diasproc'},
        { data: 'U_OT', name: 'U_OT'},
        { data: 'ItemCode', name: 'ItemCode'},

        { data: 'ItemName', name: 'ItemName'},
        { data: 'totalproc', name:  'totalproc'},
        { data: 'PorIniciar', name: 'PorIniciar'},
        { data: 'Habilitado', name:  'Habilitado'},
        { data: 'Armado', name:  'Armado'},

        { data: 'Tapado', name:  'Tapado'},
        { data: 'Preparado', name:  'Preparado' },
        { data: 'Inspeccion', name:  'Inspeccion'},
        { data: 'U_VS', name:  'U_VS'},
        { data: 'totalvs', name:  'totalvs' },

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