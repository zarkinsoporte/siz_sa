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
                            <i class="fa fa-archive"></i>  <a href="inventario">GESTIÓN DE INVENTARIOS</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="altaInventario">ALTA INVENTARIOS</a>
                        </li>

                    </ol>
                </div>
            </div>
            <!-- /.row -->
         <div class="container">
         <div class="well">
            <a href="inventario" class="btn btn-primary">Gestion Inventario</a>
            <a href="inventarioObsoleto" class="btn btn-primary">Inventario Obsoleto</a>
         </div>
        <div class="row">
             {{--este form tiene que enviar la informacion para crear un modulo--}}
             <div class="col-md-6">
             {!! Form::open(['url' => 'admin/altaInventario', 'method' => 'POST']) !!}
                <div class="form-group">
                    <label for="NombreEquipo">Nombre Equipo</label>
                    <input type="text" name="nombre_equipo" class="form-control" placeholder="Ej. HP Probook 4520s" required>
                </div>
                <div class="form-group">
                    <label for="Correo">Correo</label>
                    <input type="email" name="correo" class="form-control" id="exampleFormControlInput1" placeholder="nombre.apellido@zarkin.com" required>
                </div>
                <div class="form-group">
                    <label for="NumeroEquipo">Número de Equipo</label>
                    <input type="number" name="numero_equipo" class="form-control" id="exampleFormControlInput1" placeholder="Ej 77" required>
                </div>
                <div class="form-group">
                    <label for="TipoEquipo">Tipo de Equipo</label>
                    <select class="form-control" name="tipo_equipo">
                    <option>ESCRITORIO</option>
                    <option>LAPTOP</option>
                    <option>ALL IN ONE</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="Monitor">Monitor</label>
                    <select class="form-control" name="monitor">
                    @foreach ($monitores as $monitor)
                        <option value="{{ $monitor->id_mon }}">{{ $monitor->nombre_monitor }}</option>
                    @endforeach 
                    </select>
                    <br>
                    <br>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
                {!! Form::close() !!} 
                </div>
                <div class="col-md-6">
                    TEXT
                </div>
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