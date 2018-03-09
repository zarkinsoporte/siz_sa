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
                            <i class="fa fa-dashboard"></i>  <a href="MOD00-ADMINISTRADOR">INICIO</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="inventario">MONITORES</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="altaInventario">ALTA MONITORES</a>
                        </li>

                    </ol>
                </div>
            </div>
            <!-- /.row -->
         <div class="container">
         <div class="well">
            <a href="inventario" class="btn btn-primary">Gestion Inventario</a>
         </div>
        <div class="row">
        @if ($mensaje !='0')
        <div class="alert alert-success">
            <strong>Modificado</strong> Se ha modificado el monitor correctamente
        </div>
        @endif
             {{--este form tiene que enviar la informacion para crear un modulo--}}
             <div class="col-md-6">
             {!! Form::open(['url' => 'admin/mod_mon2', 'method' => 'POST']) !!}
                <div class="form-group">
                    <label for="exampleFormControlInput1">Nombre Monitor</label>
                    <input type="text" id="nombre_monitor" name="nombre_monitor" class="form-control" placeholder="Ej. HP LV1911" value="{{ $monitor->nombre_monitor }}" required>
                    <input type="hidden" id="id_monitor" name="id_monitor" class="form-control" placeholder="Ej. HP LV1911" value="{{ $monitor->id }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
             {!! Form::close() !!} 
             </div>
             @yield('subcontent-01')


             TEXT
         </div>



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