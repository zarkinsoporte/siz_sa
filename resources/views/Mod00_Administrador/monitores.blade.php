@extends('app')

@section('content')
@include('partials.menu-admin')

    <div >

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div >
                    <h3 class="page-header">
                        Monitores
                    </h3>
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i> <a href="{!! url('home') !!}">Inicio</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="{!! url('MOD00-ADMINISTRADOR') !!}">MOD-Administrador</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="inventario">Gestiòn de Inventario</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="monitores">Monitores</a>
                        </li>

                    </ol>
                </div>
            </div>
            <!-- /.row -->
         <div class="well">
         <a href="altaMonitor" class="btn btn-success"><i class="glyphicon glyphicon-plus-sign"></i></a>
         </div>
             <div class="row">
             <div class="col-6">
             <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre Monitor</th>
                        <th scope="col">Modificar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($monitores as $monitor)
                        <tr>
                        <th scope="row">{{ $monitor->id }}</th>
                        <td>{{ $monitor->nombre_monitor }}</td>
                        <td>
                            <a href="mod_mon/{{$monitor->id}}/{{0}}" class="btn btn-warning"><i class="fa fa-pencil-square"></i</a>
                        </td>
                        </tr>
                    @endforeach 
                    </tbody>
                </table>

                 <div class="col-md-10">
                     @if (count($errors) > 0)
                         <div class="alert alert-danger text-center" role="alert">
                             @foreach($errors->getMessages() as $this_error)
                                 <strong>¡Lo sentimos!  &nbsp; {{$this_error[0]}}</strong><br>
                             @endforeach
                         </div>
                     @elseif(Session::has('mensaje'))
                         <div class="row">
                             <div class="alert alert-success text-center" role="alert">
                                 {{ Session::get('mensaje') }}
                             </div>
                         </div>
                     @endif

                 </div>
             </div>
             <div class="col-6">
             </div>
             </div>
             @yield('subcontent-01')
        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->
    </div>
    </div>



    <!-- /#wrapper -->
@endsection

@section('script')





@endsection
