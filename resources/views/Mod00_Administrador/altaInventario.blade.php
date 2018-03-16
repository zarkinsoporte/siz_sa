@extends('app')

@section('content')
@include('partials.menu-admin')

    <div >

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div >
                    <h3 class="page-header">
                        Alta de Inventarios
                    
                        {{Route::current()->getName()}}
                    </h3>
                </div>
                    <ol class="breadcrumb">
                    <li>
                            <i class="fa fa-dashboard"></i> <a href="{!! url('home') !!}">Inicio</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="{!! url('MOD00-ADMINISTRADOR') !!}">MOD-Asministrador</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="inventario">Gestiòn de Inventarios</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="altaInventario">Alta Inventarios</a>
                        </li>

                    </ol>
                </div>
            </div>
            <!-- /.row -->
         <div class="container">
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
                    <option value="1">POR DEFINIR</option>
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
                    
                </div>
        </div>
             @yield('subcontent-01')
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