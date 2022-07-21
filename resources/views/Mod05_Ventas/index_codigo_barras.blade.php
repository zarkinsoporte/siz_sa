@extends('home')
@section('homecontent')
<script>
    let tokenapp = "{{ csrf_token() }}"
</script>

{!! Html::script('assets/js/Mod05_Ventas/index_codigo_barras.js') !!}
{!! Html::style('assets/css/customdt.css') !!}

<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header">
                Codigos de Barras
                <small></small>
            </h3>
            <!--
           
        </div>
    </div>
    <!-- /.row -->
    
    <div class="row">
        <div class="col-md-12" style="margin-top: 0px;">
            <table id="tabla_index" class="table table-striped table-bordered nowrap" width="100%">
                <thead class="table-condensed">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Precódigo</th>
                    </tr>
               
                </thead>
                <tbody>
                    
                </tbody>
              
            </table>
        </div> <!-- /.col-md-12 -->

    </div> <!-- /.row -->
    
</div>
<!-- /.container -->
@endsection


