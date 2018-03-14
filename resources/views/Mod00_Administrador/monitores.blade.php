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
                            <i class="fa fa-dashboard"></i>  <a href="{!! url('MOD00-ADMINISTRADOR') !!}">MOD-ADMINISTRADOR</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="inventario">GESTIÒN DE INVENTARIO</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="monitores">MONITORES</a>
                        </li>

                    </ol>
                </div>
            </div>
            <!-- /.row -->
         <div class="well">
            <a href="{!! url('admin/altaMonitor') !!}" class="btn btn-primary">Alta Monitor</a>
            <a href="inventario" class="btn btn-primary">Inventario</a>
            <a href="inventarioObsoleto" class="btn btn-primary">Inventario Obsoleto</a>
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
