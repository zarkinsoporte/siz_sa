@extends('app')

@section('content')
    <div class="container">
        <div class="row">

           <div class="col-md-10 col-md-offset-1">
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

               {!! Form::open(['route' => 'users', 'method' => 'GET', 'class' => 'navbar-form navbar-left pull-right', 'role' => 'search']) !!}

                   <div class="form-group">
                       {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nombre de Usuario']) !!}
                   </div>
                   <button type="submit" class="btn btn-primary">Buscar</button>
               {!! Form::close() !!}

           </div>

            <div class="col-md-10 col-md-offset-1">

                    <div class="panel-body">
                        <div class="panel panel-default">

                            <!-- Default panel contents -->
                            <div class="panel-heading">Usuarios Registrados

                            </div>

                            <!-- Table -->
                            <table id="usuarios" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th># Empleado</th>
                                    <th># Nómina</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Puesto</th>
                                    <th>Estaciones</th>
                                    <th>Password</th>
                                </tr>
                                </thead>
                                @foreach ($users as $o)
                                <tr>
                                    <td> {{$o->empID}} </td>
                                    <td> {{$o->U_EmpGiro}} </td>
                                    <td> {{$o->firstName}} </td>
                                    <td> {{$o->lastName}} </td>
                                    <td> {{$o->jobTitle}} </td>
                                    <td> {{$o->U_CP_CT}} </td>
                                    <td align="center">  <button type="button" class="btn btn-default" data-toggle="modal" data-target="#mymodal" data-whatever="{{$o->empID}}">
                                            <i class="fa fa-unlock" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach

                            </table>
                </div>

            </div>
                <div align="center"> {!! $users->render() !!} </div>


                <!-- Modal -->

                <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" >
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="pwModalLabel">Cambio de Password</h4>
                            </div>
                            {!! Form::open(['url' => 'cambio.password', 'method' => 'POST']) !!}
                            <div class="modal-body">

                                    <div class="form-group">
                                        <div >
                                            <label for="password" class="col-md-12 control-label">Id de Usuario:</label>
                                            <input type="text" name="userId" class="form-control" id="userId" value="" readonly/>
                                            <label for="password" class="col-md-12 control-label">Ingresa la nueva Contraseña:</label>
                                            <input id="password" type="password" class="form-control" name="password" required maxlength="6">
                                        </div>
                                    </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                <script data-require="jquery@*" data-semver="2.0.3" src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
                <script type="text/javascript" >
                    $(document).ready(function (event) {

                        $('#mymodal').on('show.bs.modal', function (event) {
                            var button = $(event.relatedTarget) // Button that triggered the modal
                            var recipient = button.data('whatever') // Extract info from data-* attributes
                            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                            var modal = $(this)

                            modal.find('.modal-body input').val(recipient)
                        });
                    });

                </script>

        </div>
    </div>


@endsection
