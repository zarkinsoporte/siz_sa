@extends('app') 
@section('content')
@include('partials.menu-admin')

    <div class="container">

        <!-- Page Heading -->
        <div class="row">
            <div class="visible-xs visible-sm"><br><br></div>
            <div class="col-lg-6.5 col-md-8 col-sm-7">
                <h3 class="page-header">
                    Modificación de Inventario {{$inventario->tipo_equipo}}
                </h3>
            </div>
        </div>
        <div class="row">
            <ol class="breadcrumb">
                <li>
                    <i class="fa fa-dashboard"></i> <a href="{!! url('home') !!}">Inicio</a>
                </li>
                <li>
                    <i class="fa fa-archive"></i> <a href="{!! url('MOD00-ADMINISTRADOR') !!}">MOD-Asministrador</a>
                </li>
                <li>
                    <i class="fa fa-archive"></i> <a href="{{Request::root().'/admin/inventario'}}">Inventario cómputo</a>
                </li>
                <li>
                    <i class="fa fa-archive"></i> <a href="#">Modificación de inventario</a>
                </li>

            </ol>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-md-11">
                 @include('partials.alertas')
            </div>
        </div>


        {{--este form tiene que enviar la informacion--}} {!! Form::open(['url' => 'admin/mod_inv2', 'method' => 'POST']) !!}
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="numero_equipo">Número de Equipo</label>
                    <input type="number" name="numero_equipo" class="form-control" placeholder="Ej 77" value="{{ $inventario->numero_equipo }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="NombreEquipo">Nombre Equipo</label>
                    <input type="text" name="nombre_equipo" class="form-control" placeholder="Ej. HP Probook 4520s" value="{{ $inventario->nombre_equipo }}"
                        required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="nombre_usuario">Nombre Usuario</label>
                    <input type="text" name="nombre_usuario" class="form-control" placeholder="Nombre y Apellido" value="{{ $inventario->nombre_usuario }}"
                        required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">

                <div class="form-group">
                    <label for="Correo">Correo</label>
                    <input type="email" name="correo" class="form-control" placeholder="nombre.apellido@zarkin.com" value="{{ $inventario->correo }}"
                        required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="correo_password">Correo password</label>
                    <input type="text" name="correo_password" class="form-control" value="{{ $inventario->correo_password }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="monitor">Monitor</label>
                    <select class="form-control" name="monitor" value="{{ $inventario->monitor }}" required>
                    <option value="1">N/A</option>
                    @foreach ($monitores as $monitor)                    
                        <option value="{{ $monitor->id_mon }}" {{$inventario->monitor==$monitor->id_mon ? 'selected' : null}}>{{ $monitor->nombre_monitor }}</option>
                    @endforeach 
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="estatus">Estatus</label>
                    <select class="form-control" name="estatus" value="{{ $inventario->estatus }}" required>
                        <option {{($inventario->estatus=='ACTIVO') ? 'selected' : ''}}>ACTIVO</option>
                        <option {{($inventario->estatus<>'ACTIVO') ? 'selected' : ''}}>INACTIVO</option>                       
                        </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="ubicacion">Ubicación</label>
                    <select class="form-control" name="ubicacion" value="{{ $inventario->ubicacion }}" required>
                        <option {{($inventario->ubicacion=='LERMA OFICINAS') ? 'selected' : ''}}>LERMA OFICINAS</option>
                        <option {{($inventario->ubicacion=='LERMA CARPINTERIA') ? 'selected' : ''}}>LERMA CARPINTERIA</option>                       
                        <option {{($inventario->ubicacion=='GUADALAJARA') ? 'selected' : ''}}>GUADALAJARA</option>                       
                        </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="area">Área</label>
                    <input type="text" name="area" class="form-control" value="{{ $inventario->area }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="tipo_equipo">Tipo de Equipo</label>
                    <select class="form-control" name="tipo_equipo" value="{{ $inventario->tipo_equipo }}" required>
                    <option {{($inventario->tipo_equipo=='ESCRITORIO') ? 'selected' : ''}}>ESCRITORIO</option>
                    <option {{($inventario->tipo_equipo=='LAPTOP')? 'selected' : ''}}>LAPTOP</option>
                    <option {{($inventario->tipo_equipo=='ALL IN ONE') ? 'selected' : ''}}>ALL IN ONE</option>
                    <option {{($inventario->tipo_equipo=='SERVIDOR') ? 'selected' : ''}}>SERVIDOR</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="serie">Serie</label>
                    <input type="text" name="serie" class="form-control" value="{{ $inventario->noserie }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" name="marca" class="form-control" value="{{ $inventario->marca }}" required>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="modelo">Módelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ $inventario->modelo }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="procesador">Procesador</label>
                    <input type="text" name="procesador" class="form-control" value="{{ $inventario->procesador }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="velocidad">Velocidad (GHZ)</label>
                    <input type="number" name="velocidad" class="form-control" value="{{ $inventario->velocidad }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="memoria">Memoria</label>
                    <input type="text" name="memoria" class="form-control" value="{{ $inventario->memoria }}" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="disco_duro">Disco Duro (GB)</label>
                    <input type="number" name="disco_duro" class="form-control" value="{{ $inventario->espacio_disco }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="so">SO</label>
                    <input type="text" name="so" class="form-control" value="{{ $inventario->so }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="arquitectura">Arquitectura</label>
                    <input type="text" name="arquitectura" class="form-control" value="{{ $inventario->arquitectura }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="ofimatica">Ofimática</label>
                    <input type="text" name="ofimatica" class="form-control" value="{{ $inventario->ofimatica }}" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="antivirus">Antivirus</label>
                    <input type="text" name="antivirus" class="form-control" value="{{ $inventario->antivirus }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="otro">Otro</label>
                    <input type="text" name="otro" class="form-control" value="{{ $inventario->otros }}">
                </div>
            </div>
            <?php   
                $date_programado = date_create($inventario->Fecha_mttoProgramado);
                $date_mtto = date_create($inventario->Fecha_mantenimiento);                               
            ?>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="mantenimiento_programado">Último mantenimiento Programado</label>
                    <input type="Date" name="mantenimiento_programado" class="form-control" placeholder="" value="{{ date_format($date_programado, 'Y-m-d') }}">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="mantenimiento_realizado">Último mantenimiento Realizado</label>
                    <input type="Date" name="mantenimiento_realizado" class="form-control" placeholder="" value="{{ date_format($date_mtto, 'Y-m-d') }}">
                </div>

            </div>
            <input type="hidden" name="id_inv" class="form-control" value="{{$inventario->id_inv}}">
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