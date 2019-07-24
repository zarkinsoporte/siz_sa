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
            <div class="col-lg-6.5 col-md-9 col-sm-8" style="margin-bottom: -20px;">
                    <div class="visible-xs visible-sm"><br><br></div>               
                <h3 class="page-header">
                    Picking de Artículos <small>Solicitudes de Material</small>
                </h3>
            </div>
        </div>
        <div class="col-lg-5.5 col-md-8 col-sm-7">
                @if (count($errors) > 0)
                <div class="alert alert-danger text-center" role="alert">
                    @foreach($errors->getMessages() as $this_error)
                    <strong>¡Lo sentimos!  &nbsp; {{$this_error[0]}}</strong><br> @endforeach
                </div>
                @elseif(Session::has('mensaje'))
                <div class="row">
                    <div class="alert alert-success text-center" role="alert">
                        {{ Session::get('mensaje') }}
                    </div>
                </div>
                @endif

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
        
        <div class="">
            <div class="">
                <div class="">
                    <table id="tsolicitudes" class="table table-striped table-bordered" style="width:100%" >
                        <thead>
                            <tr>          
                                    <th>#Folio</th>                  
                                    <th>Usuario</th>
                                    <th>Area</th>
                                    <th>Fecha</th>
                                    <th>Material</th>                                                                        
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
    dom: 'frtip',       
    "order": [[ 0, "desc" ]],
    orderCellsTop: true,   
    scrollX:        true,
    paging:         true,
    processing: true,
    responsive: true,
    deferRender:    true,
    ajax: {
        url: '{!! route('datatables.solicitudesMP') !!}',
        data: function () {
                         
                        }              
    },
    columns: [
   
        { data: 'Id_Solicitud'},
        { data: 'user_name'},
        { data: 'area'},
        { data: 'FechaCreacion',
            render: function(data){
                if (data === null){return data;}
            var d = new Date(data);
            return moment(d).format("DD-MM-YYYY HH:mm:ss");
            }
        },        
        { data: 'action'},
       
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

























