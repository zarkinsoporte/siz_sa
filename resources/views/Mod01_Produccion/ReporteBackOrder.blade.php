@extends('home') 
@section('homecontent')


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
    <div class="row">
        <div class="col-md-4 col-md-offset-8">
            <a href="../ReporteMaterialesPDF/" target="_blank" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Reporte PDF</a>
            <a class="btn btn-success" href="materialesXLS"><i class="fa fa-file-excel-o"></i> Reporte XLS</a>
        </div>
    </div>
    <br>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <table  id="tbackorder">
                <thead class="thead-dark">
                    <tr>
                        <th>OP</th>
                        <th>Pedido</th>
                        <th>fechapedido</th>
                        <th>OC</th>
                        <th>d_proc</th>
                        <th>no_serie</th>
                        <th>cliente</th>
                        <th>codigo1</th>
                        <th>codigo3</th>
                        <th>Descripcion</th>
                        <th>Cantidad</th>
                        <th>VSind</th>
                        <th>VS</th>
                        <th>destacion</th>
                        <th>U_GRUPO</th>
                        <th>Secue</th>
                        <th>SecOT</th>
                        <th>SEMANA2</th>
                        <th>fentrega</th>
                        <th>fechaentregapedido</th>
                        <th>SEMANA3</th>
                        <th>u_fproduccion</th>
                        <th>prioridad</th>
                        <th>comments</th>
                        <th>u_especial</th>
                        <th>modelo</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>
<!-- /.container -->
@endsection
 
@section('homescript')
$('#tbackorder').DataTable({
    orderCellsTop: true,
    fixedHeader: true,
    processing: true,
    serverSide: true,
    deferRender:    true,
    ajax: {
        url: '{!! route('datatables.showbackorder') !!}',
        data: function () {
                         
                        }              
    },
    columns: [        
        // { data: 'action', name: 'action', orderable: false, searchable: false}

        { data: 'OP', name:  'OP'},
        { data: 'Pedido', name: 'Pedido'},
        { data: 'fechapedido', name:  'fechapedido'},
        { data: 'OC', name: 'OC'},
        { data: 'd_proc', name: 'd_proc'},
        { data: 'no_serie', name: 'no_serie'},
        { data: 'cliente', name:  'cliente'},
        { data: 'codigo1', name: 'codigo1'},
        { data: 'codigo3', name:  'codigo3'},
        { data: 'Descripcion', name:  'Descripcion'},
        { data:'Cantidad', name:  'Cantidad'},
        { data: 'VSind', name:  'VSind' },
        { data: 'VS', name:  'VS'},
        { data: 'destacion', name:  'destacion' },
        { data: 'U_GRUPO', name: 'U_GRUPO' },
        { data: 'Secue', name:  'Secue'},
        { data: 'SecOT', name:  'SecOT' },
        { data: 'SEMANA2', name:  'SEMANA2'},
        { data: 'fentrega', name:  'fentrega'},
        { data:'fechaentregapedido', name:  'fechaentregapedido'},
        { data: 'SEMANA3', name:  'SEMANA3'},
        { data: 'u_fproduccion', name:  'u_fproduccion'},
        { data: 'prioridad', name:  'prioridad'},
        { data: 'comments', name:  'comments'},
        { data: 'u_especial', name:  'u_especial'},
        { data: 'modelo', name: 'modelo'}
    ],
    "language": {
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
    },
 
});
@endsection





