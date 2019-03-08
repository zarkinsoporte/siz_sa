@extends('app') 
@section('content')
    @include('partials.menu-admin')

<div class="container">
    <!-- Page Heading -->
    <div class="row">
        <div class="visible-xs visible-sm"><br><br></div>
        <div class="col-lg-6.5 col-md-8 col-sm-7">
            <h3 class="page-header">
                Alta de Inventario
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6.5 col-md-8 col-sm-7">
            <div class="hidden-xs">
                <div class="hidden-sm">
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i> <a href="{!! url('home') !!}">Inicio</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i> <a href="{!! url('MOD00-ADMINISTRADOR') !!}">MOD-Asministrador</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i> <a href="inventario">Inventario cómputo</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i> <a href="altaInventario">Alta Inventario</a>
                        </li>

                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-11">
                @include('partials.alertas')
          </div>       
     </div>   
    {{--este form tiene que enviar la informacion para crear un modulo--}} {!! Form::open(['url' => 'admin/altaInventario', 'method'
    => 'POST']) !!}
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="numero_equipo">Número de Equipo</label>
                <input type="number" name="numero_equipo" class="form-control" placeholder="Ej 77" value="{{ old('numero_equipo') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="NombreEquipo">Nombre Equipo</label>
                <input type="text" name="nombre_equipo" class="form-control" placeholder="Ej. HP Probook 4520s" value="{{ old('nombre_equipo') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="nombre_usuario">Nombre Usuario</label>
                <input type="text" name="nombre_usuario" class="form-control" placeholder="Nombre y Apellido" value="{{ old('nombre_usuario') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">

            <div class="form-group">
                <label for="Correo">Correo</label>
                <input type="email" name="correo" class="form-control" placeholder="nombre.apellido@zarkin.com" value="{{ old('correo') }}" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="correo_password">Correo password</label>
                <input type="text" name="correo_password" class="form-control" value="{{ old('correo_password') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="monitor">Monitor</label>
                <select class="form-control" name="monitor" value="{{ old('monitor') }}" required>
                    <option value="1">N/A</option>
                    @foreach ($monitores as $monitor)
                        <option value="{{ $monitor->id_mon }}">{{ $monitor->nombre_monitor }}</option>
                    @endforeach 
                    </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="estatus">Estatus</label>
                <select class="form-control" name="estatus" value="{{ old('estatus') }}" required>
                        <option>ACTIVO</option>
                        <option>INACTIVO</option>                       
                        </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="ubicacion">Ubicación</label>
                <select class="form-control" name="ubicacion" value="{{ old('ubicacion') }}" required>
                        <option>LERMA OFICINAS</option>
                        <option>LERMA CARPINTERIA</option>                       
                        <option>GUADALAJARA</option>                       
                        </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="area">Área</label>
                <input type="text" name="area" class="form-control" value="{{ old('area') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="tipo_equipo">Tipo de Equipo</label>
                <select class="form-control" name="tipo_equipo" value="{{ old('tipo_equipo') }}" required>
                    <option>ESCRITORIO</option>
                    <option>LAPTOP</option>
                    <option>ALL IN ONE</option>
                    <option>SERVIDOR</option>
                    </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="serie">Serie</label>
                <input type="text" name="serie" class="form-control" value="{{ old('serie') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="marca">Marca</label>
                <input type="text" name="marca" class="form-control" value="{{ old('marca') }}" required>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="modelo">Módelo</label>
                <input type="text" name="modelo" class="form-control" value="{{ old('modelo') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="procesador">Procesador</label>
                <input type="text" name="procesador" class="form-control" value="{{ old('procesador') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="velocidad">Velocidad (GHZ)</label>
                <input type="number" name="velocidad" class="form-control" value="{{ old('velocidad') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="memoria">Memoria</label>
                <input type="text" name="memoria" class="form-control" value="{{ old('memoria') }}" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="disco_duro">Disco Duro (GB)</label>
                <input type="number" name="disco_duro" class="form-control" value="{{ old('disco_duro') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="so">SO</label>
                <input type="text" name="so" class="form-control" value="{{ old('so') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="arquitectura">Arquitectura</label>
                <input type="text" name="arquitectura" class="form-control" value="{{ old('arquitectura') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="ofimatica">Ofimática</label>
                <input type="text" name="ofimatica" class="form-control" value="{{ old('ofimatica') }}" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="antivirus">Antivirus</label>
                <input type="text" name="antivirus" class="form-control" value="{{ old('antivirus') }}" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="otro">Otro</label>
                <input type="text" name="otro" class="form-control" value="{{ old('otro') }}" >
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="mantenimiento_programado">Último mantenimiento Programado</label>
                <input type="Date" name="mantenimiento_programado" class="form-control" placeholder="" value="{{ old('mantenimiento_programado') }}" >
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="mantenimiento_realizado">Último mantenimiento Realizado</label>
                <input type="Date" name="mantenimiento_realizado" class="form-control" placeholder="" value="{{ old('mantenimiento_realizado') }}" >
            </div>
        </div>       
    </div>
    <div>
        <p align="right">
            <button type="submit" class="btn btn-primary">Guardar</button> {!! Form::close() !!}
        </p>
    </div>

</div>

@endsection
 
@section('script')
@endsection