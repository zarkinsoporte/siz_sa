@extends('app')

@section('content')

@include('partials.menu-admin')



    <div >

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div >
                   <div class="col-lg-6.5 col-md-9 col-sm-8">
                    <div class="visible-xs visible-sm"><br><br></div>
                    <h3 class="page-header">
                    Configuración de envío de Correo
                    </h3>
                </div>
                       <div class= "col-lg-6.5 col-md-12 col-sm-7">
                        <div class="hidden-xs">
                        <div class="hidden-sm">
                        
                </div>
            </div>
            @include('partials.alertas')

            <div class="row">
                    {!! Form::open(['url' => 'admin/save/email', 'method' => 'POST']) !!}
                
                    <div class="col-md-3">
                        <label>Usuario</label>
                        <select class="form-control" name="nomina" id="nomina" required>
                            <option value="">Seleccione</option>
                            @foreach ($activeUsers as $rep)
                            <option value="{{$rep->U_EmpGiro}}">{{$rep->firstName.' '.$rep->lastName}}</option>
                            @endforeach
                        </select>
                       
                    </div>
                    <div class="col-md-3">   
                        <label>Reprocesos</label>
                        <select class="form-control" name="reprocesos" id="reprocesos">
                            <option value="1">Activado</option>
                            <option value="0">Desactivado</option>
                        </select>
                        
                    </div>
                    <div class="col-md-3">                        
                        <label>SolicitudesMP</label>
                        <select class="form-control" name="solicitudmp" id="solicitudmp">
                            <option value="1">Activado</option>
                            <option value="2">Solicitudes</option>
                            <option value="3">Autorizaciones</option>
                            <option value="0">Desactivado</option>
                        </select>
                       
                    </div>
                    <div class="col-md-3">                        
                        <label>Err_Existencias</label>
                        <select class="form-control" name="errorexistencia_04" id="errorexistencia_04">
                            <option value="1">Activado</option>
                            <option value="0">Desactivado</option>
                        </select>
                       
                    </div>
                    <div class="col-md-3">
                       
                                <button class="btn btn-primary" style="margin-top:25px" type="submit">Guardar</button>
    
                    </div>

                            {!! Form::close() !!}
                </div><!-- /.row -->
<br>
            <table id="usuarios" class="table table-striped table-bordered table-condensed">
                                    <thead>
                                    <tr>
                                             <th>#</th>
                                            <th># Nómina</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Reprocesos</th>  
                                            <th>Solicitudes MP</th>
                                            <th>Err_Existencias</th>
                                            <th>Acciones</th>
                                          
                                    </tr>
                                    </thead>
                                    <tbody>
                        @foreach ($emails as $campo)
                        <tr>
                        <th>{{ $campo->id}}</th>
                        <td>{{ $campo->No_Nomina }}</td>
                        <td>{{ App\User::find($campo->No_Nomina)['firstName'].' '.App\User::find($campo->No_Nomina)['lastName'] }}</td>
                        <td>{{ App\User::find($campo->No_Nomina)['email'].'@zarkin.com' }}</td>
                        <td>{{ $campo->Reprocesos==1?'Activado':'-'}}</td>
                        <td>{{ $campo->SolicitudesMP==1?'Activado':'-' }}</td>
                        <td>{{ $campo->SolicitudesErrExistencias==1?'Activado':'-' }}</td>
                                                   
                        <td>
                        <a class="btn btn-warning" id="btneditar-{{ $campo->id}}" onclick="getItem({{ $campo->id}})" data-id="{{ $campo->id}}" data-nomina="{{ $campo->No_Nomina}}" data-reproceso="{{ $campo->Reprocesos}}" data-solicitudmp="{{$campo->SolicitudesMP}}" data-errorexistencia_04="{{$campo->SolicitudesErrExistencias}}"><i class="glyphicon glyphicon-edit"></i></a>
                       
                        <a href="email/del/{{$campo->id}}" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
                        </td>
                        </tr>
                    @endforeach 
                    </tbody>
                                
    </div>

@endsection
<script>
function getItem(btn) {

var nomina = $("#btneditar-"+btn).data("nomina");
var reprocesos = $("#btneditar-"+btn).data("reproceso");
var solicitudmp = $("#btneditar-"+btn).data("solicitudmp");
var errorexistencia_04 = $("#btneditar-"+btn).data("errorexistencia_04");

$('#nomina').val(nomina);
$('#reprocesos').val(reprocesos);
$('#solicitudmp').val(solicitudmp);
$('#errorexistencia_04').val(errorexistencia_04);
}
</script>
@section('script')
  
    @endsection