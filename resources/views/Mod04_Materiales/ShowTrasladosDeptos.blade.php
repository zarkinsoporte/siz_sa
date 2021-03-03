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
                    Traslados Recepción de Mercancía <small></small>
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
                                    <th>#Folio</th>                  
                                    <th>Almacén Origen</th>
                                    <th>Usuario</th>
                                    <th>Area</th>
                                    <th>Fecha de Creación</th>
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
 
<script>
    function js_iniciador() {
        $('.toggle').bootstrapSwitch();
        $('[data-toggle="tooltip"]').tooltip();
        $('.boot-select').selectpicker();
        $('.dropdown-toggle').dropdown();
        setTimeout(function() {
        $('#infoMessage').fadeOut('fast');
        }, 5000); // <-- time in milliseconds
        $("#sidebarCollapse").on("click", function() {
            $("#sidebar").toggleClass("active"); 
            $("#page-wrapper").toggleClass("content"); 
            $(this).toggleClass("active"); 
        });
var table = $('#tsolicitudes').DataTable({
    dom: 'frtip',       
    "order": [[ 1, "desc" ]],
    orderCellsTop: true,   
    scrollX:        true,
    paging:         true,
    processing: true,
    responsive: true,
    deferRender:    true,
    ajax: {
        url: '{!! route('datatables.traslados') !!}',
        data: function () {
                         
                        }              
    },
    columns: [        
        { data: 'folio'},
        { data: 'AlmacenOrigen'},
        { data: 'user_name'},
        { data: 'area'},
        { data: 'FechaCreacion',
            render: function(data){
                if (data === null){return data;}
            var d = new Date(data);
            return moment(d).format("DD-MM-YYYY HH:mm:ss");
            }
        },       
    ],
    "language": {
      "url": "{{ asset('assets/lang/Spanish.json') }}",       
    },
    columnDefs: [    
    ],
    //revision
  
});

document.onkeyup = function(e) {
   if (e.shiftKey && e.which == 112) {
    window.open("ayudas_pdf/AyM00_00.pdf","_blank");
  }
  } 
        }
</script>

























