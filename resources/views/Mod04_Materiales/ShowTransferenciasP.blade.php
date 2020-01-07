@extends('home') 
@section('homecontent')
   
    <style>
            th { font-size: 12px; }
            td { font-size: 11px; }
            th, td { white-space: nowrap; }
           
      
           
         .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
        visibility: visible;
        }
                
            </style>
    <div class="container">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-md-12" style="margin-bottom: -20px;">
                    <div class="visible-xs visible-sm"><br><br></div>               
                <h3 class="page-header">
                    Transferencias Pendientes <small>SOLICITUDES Y TRASLADOS</small>
                </h3>
            </div>
        </div>
        
        <style>
            td {
                font-family: 'Helvetica';
                font-size: 80%;
            }

            th {
                font-family: 'Helvetica';
                font-size: 90%;
            }
        </style>
        <!-- /.row -->
        <div class="row">
      <div class="col-md-12 ">
        @include('partials.alertas')
      </div>
    </div>
        <div class="">
            <div class="">
                <div class="">
                    <table id="tsolicitudes" class="table table-striped table-bordered" style="width:100%" >
                        <thead>
                            <tr>                                                                                                                                             
                                    <th>Tipo</th>                  
                                    <th># Num</th>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Destino</th>

                                    <th>Estatus Solicitud</th>
                                    <th>Codigo</th>
                                    <th>Descripción</th>
                                    <th>UDM</th>
                                    <th>Pendiente</th>

                                    <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                         
                        </tbody>
                    </table>
                    
                </div>
            </div>
           
        </div>
         @yield('subcontent-01')
    </div>
@endsection
 
@section('script')



var table = $('#tsolicitudes').DataTable({
    dom: 'lfrtip',       
    "order": [[0, "asc"],[ 1, "desc" ]],
    orderCellsTop: true,   
    scrollX:        true,
    paging:         true,
     "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"] ],
    "pageLength": 10,
    processing: true,
    responsive: true,
    deferRender:    true,
    ajax: {
        url: '{!! route('datatables.transferencias_pendientes') !!}',
        data: function () {
                         
                        }              
    },
    columns: [        
        { data: 'TIPO_DOC'},
        { data: 'NUMERO'},
        { data: 'FECHA',
            render: function(data){
                if (data === null){return data;}
            var d = new Date(data);
            return moment(d).format("DD-MM-YYYY HH:mm");
            }
        },
        { data: 'USUARIO'},
        { data: 'DESTINO'},    

        { data: 'ST_SOL'},       
        { data: 'CODIGO'},       
        { data: 'DESCRIPCION'},       
        { data: 'UDM'},       
        { data: 'PENDIENTE'}, 

        { data: 'STATUS_LIN'},  


    ],
    "language": {
      "url": "{{ asset('assets/lang/Spanish.json') }}",       
    },
    columnDefs: [    
    ],
    //revision
  
});
@endsection

<script>
document.onkeyup = function(e) {
   if (e.shiftKey && e.which == 112) {
    window.open("ayudas_pdf/AyM00_00.pdf","_blank");
  }
  } 
</script>

























