@extends('app')

@section('content')
    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">

            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo">MOD-Administrador<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo" class="">

                        <li>
                            <a href="{!! url('admin/grupos/1') !!}"><i class="fa fa-fw fa-users"></i>   Gestión de Grupos</a>
                        </li>
                    <li>
                        <a href="{!! url('admin/users') !!}"><i class="fa fa-fw fa-user"></i> Usuarios SIZ</a>
                    </li>
                    <li>
                        <a href="{!! url('admin/inventario') !!}"><i class="fa fa-archive"></i> Gestión de Inventario</a>
                    </li>
                </ul>
            </li>
            @include('partials.section-navbar')
        </ul>
    </div>
    <!-- /.navbar-collapse -->
    </nav>

    <div >

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div >
                    <h3 class="page-header">
                        {{Route::current()->getName()}}
                    </h3>
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="{!! url('MOD00-ADMINISTRADOR') !!}">MOD-ADIMINISTRADOR</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="inventario">GESTIÓN DE INVENTARIOS</a>
                        </li>

                    </ol>
                </div>
            </div>
            <!-- /.row -->
         <div class="well">
            <a href="altaInventario" class="btn btn-primary">Alta Inventario</a>
            <a href="monitores" class="btn btn-primary">Monitores</a>
            <a href="inventarioObsoleto" class="btn btn-primary">Inventario Obsoleto</a>
         </div>
             <div class="row">
             <div class="col-6">
             <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre Equipo</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Número de Equipo</th>
                        <th scope="col">Tipo de Equipo</th>
                        <th scope="col">Monitor</th>
                        <th scope="col">PDF (Incompleto)</th>
                        <th scope="col">Marcar Obsoleto</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($inventario as $inventario)
                        <tr>
                        <th scope="row">{{ $inventario->id_inv }}</th>
                        <td>{{ $inventario->nombre_equipo }}</td>
                        <td>{{ $inventario->correo}}</td>
                        <td>{{ $inventario->numero_equipo }}</td>
                        <td>{{ $inventario->tipo_equipo }}</td>
                        <td>{{ $inventario->nombre_monitor }}</td>
                        <td>
                            <a href="generarPdf/{{$inventario->id_inv}}" class="btn btn-danger"><i class="fa fa-recycle"></i</a>
                        </td>
                        <td>
                            <a href="mark_obs/{{$inventario->id_inv}}" class="btn btn-danger"><i class="fa fa-recycle"></i</a>
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
