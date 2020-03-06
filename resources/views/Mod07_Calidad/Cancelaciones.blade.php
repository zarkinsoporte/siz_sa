@extends('home')
@section('homecontent')

<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-12 ">
            <div class="hidden-lg"><br></div>
            <h3 class="page-header">
                Cancelación de Rechazos
                <small>Calidad</small>
            </h3>
          
        </div>
    </div>

    <style>
        th {
            font-size: 12px;
        }

        td {
            font-size: 11px;
        }

        th,
        td {
            white-space: nowrap;
        }
.table-condensed > thead > tr > td, .table-condensed > tbody > tr > td, .table-condensed > tfoot > tr > td {
padding: 1px 5px 1px 5px;
}


        .dataTables_wrapper .dataTables_filter {
            float: right;
            text-align: right;
            visibility: visible;
        }

        td {
            font-family: 'Helvetica';
            font-size: 100%;
        }

        th {
            font-family: 'Helvetica';
            font-size: 100%;
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
                <table id="tsolicitudes" class="stripe table-condensed" style="width:100%">
                    <thead>
                        <tr>
                            <th>F. Revisión</th>
                            <th># Factura</th>
                            <th>Proveedor</th>
                            <th>Descripción</th>
                            <th>UM</th>
                            <th>Cant. Aceptada</th>
                            <th>Cant. Rechazada</th>
                            <th>Cant. Revisada</th>
                            <th>Inspector</th>
                            <th>Descripción Rechazo</th>
                            <th>Quitar</th>
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
"order": [[ 0, "asc" ]],
orderCellsTop: true,
scrollX: true,
paging: true,
processing: true,
responsive: true,
deferRender: true,
ajax: {
url: '{!! route('datatables.cancelacionrechazos') !!}',
data: function () {

}
},
columns: [
    { data: 'fechaRevision',
    render: function(data){
        if (data === null){return data;}
        var d = new Date(data);
        return moment(d).format("DD-MM-YYYY");
    }
    },
    { data: 'DocumentoNumero'},
    { data: 'proveedorNombre'},
    { data: 'materialDescripcion'},
    { data: 'materialUM'},
    { data: 'cantidadAceptada'},
    { data: 'cantidadRechazada'},
    { data: 'cantidadRevisada'},
    { data: 'InspectorNombre'},
    { data: 'DescripcionRechazo'},
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