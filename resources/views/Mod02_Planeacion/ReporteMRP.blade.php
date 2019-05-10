@extends('home') 
@section('homecontent')
<style>
    th { font-size: 12px; }
    td { font-size: 11px; }
   
    div.dataTables_wrapper {
    margin: 0 ;
    }
    div.container {
        min-width: 980px;
        margin: 0 auto;
    }
    td:first-child{
        width:2%;
    }
    th:first-child {
        position: -webkit-sticky;
        position: sticky;
        left: 0px;
        z-index: 4;
        width: 2%;
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

    th, td { white-space: nowrap; }
    .dataTables_wrapper .dataTables_length { /*mueve el selector de registros a visualizar*/
    float: right;
    }
</style>
<?php
                $fecha = \Carbon\Carbon::now();
            ?>
<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header">
                Resumen de MRP
                <small>Necesidades de Materia Prima  ({{$fechauser}}/{{$tipo}})</small>
            </h3>
            
            <h5>{{$text}}</h5>
            <!-- <h5>Fecha & hora: {{\AppHelper::instance()->getHumanDate(date('d-m-Y h:i a', strtotime("now")))}}</h5> -->
        </div>
    </div>
     <div  id="infoMessage" class="alert alert-info" role="alert">
        ¡Importante!  Para un mejor rendimiento de las descargas, aplicar filtros al MRP.
     </div> 
    <!-- /.row -->
    <div class="row">
        
        <div class="col-md-12">
            
            <table id="tmrp" class="stripe cell-border display" >
                        <thead class="table-condensed">
                            <tr>
                                <th style="width: 2%">Código</th>
                                <th>Descripción</th>
                                <th>Grupo</th>
                                <th style="width: 30px">UM</th>
                                <th>Exist. Gdl</th>

                                <th>Exist. Lerma</th>
                                <th style="width: 30px">WIP</th>
                                <th>Anterior</th>
                                <th>Sem-{{$fecha->weekOfYear}}</th>
                                <th>Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                
                                <th>Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                <th ref="s4">Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                <th>Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                <th ref="s6">Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                <th>Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                
                                <th ref="s8">Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                <th>Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                <th ref="s10">Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                <th>Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                <th ref="s12">Sem-{{$fecha->addWeek(1)->weekOfYear}}</th>
                                
                                <th style="width: 30px">Resto</th>
                                <th>Necesidad</th>
                                <th>Disp. S/WIP</th>
                                <th style="width: 30px">OC</th>
                                <th>P. Reorden</th>
                                
                                <th>S. Minimo</th>
                                <th>S. Maximo</th>
                                <th>T.E.</th>
                                <th>Costo C</th>
                                <th>Proveedor</th>
                                
                                <th>Comprador</th>
                            </tr> 
                         </thead>
                       
                    </table>
        </div> <!-- /.col-md-12 -->

   </div> <!-- /.row -->
<input hidden value="{{$fechauser}}" id="fechauser" name="fechauser" />
<input hidden value="{{$tipo}}" id="tipo" name="tipo" />

</div>
    <!-- /.container -->
@endsection
 
@section('homescript')
$('#tmrp thead tr').clone(true).appendTo( '#tmrp thead' );

$('#tmrp thead tr:eq(1) th').each( function (i) {
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

var table = $('#tmrp').DataTable({
    
    dom: 'Blrtfip',
    orderCellsTop: true,    
    scrollY:        "300px",
    "pageLength": 50,
    scrollX:        true,
    paging:         true,
    fixedColumns: true,
    processing: true,
    deferRender:    true,
    scrollCollapse: true, 
    ajax: {
        url: '{!! route('datatables.showmrp') !!}',
        data: function (d) {
             d.fechauser = $('input[name=fechauser]').val(); 
             d.tipo = $('input[name=tipo]').val();            
                         
        }      
    },
    columns: [        
        // { data: 'action', name: 'action', orderable: false, searchable: false}
        { data: 'Itemcode'},
        { data: 'ItemName'},
        { data: 'Descr', name: 'Descr'},
        { data: 'UM', name: 'UM'},
        { data: 'ExistGDL',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }},

        { data: 'ExistLERMA',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }},
        { data: 'WIP' ,
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }},
        { data: 'S0',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },
        { data: 'S1',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },
        { data: 'S2',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },

        { data: 'S3',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
    },
        { data: 'S4',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },
        { data: 'S5',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },
        { data: 'S6',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },
        { data: 'S7',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
    },

        { data: 'S8',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },
        { data: 'S9',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },
        { data: 'S10',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },
        { data: 'S11',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },
        { data: 'S12',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },

        { data: 'Resto',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }
        },
        { data: 'necesidadTotal',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        } },
        { data: 'Necesidad',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        } },
        { data: 'OC',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        } },
        { data: 'Reorden',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        } },

        { data: 'Minimo',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        } },
        { data: 'Maximo',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        } },
        { data: 'TE'},
        { data: 'Costo',
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return val;
        }},
        { data: 'Proveedor'},

        { data: 'Comprador'}
    ],
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
                            url:'ajaxtosession/mrp',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},                            
                            data: {
                                "_token": "{{ Session::token() }}",
                                "arr": json
                                },
                                success:function(data){
                                   window.location.href = 'mrpXLS';                                   
                            }
                         });
                     }         
        }, 
        /*{
            text: '<i class="fa fa-file-pdf-o"></i> Pdf',           
            className: "btn-danger",            
                    action: function ( e, dt, node, config ) {                                
                         var data=table.rows( { filter : 'applied'} ).data().toArray();               
                         var json = JSON.stringify( data );
                         $.ajax({
                            type:'POST',
                            url:'ajaxtosession/mrp',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},                            
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "arr": json
                                },
                                success:function(data){
                                    window.open('mrpPDF', '_blank')                                   
                            }
                         });
                     }         
        } */
       
        
       
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
        
        { width: 150, targets: 30 },
    ],   
    
});


@endsection
<script>
    document.onkeyup = function(e) {
   if (e.shiftKey && e.which == 112) {
    window.open("ayudas_pdf/AYM00_00.pdf","_blank");
  }
  }

</script>