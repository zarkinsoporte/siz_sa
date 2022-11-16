@extends('Mod00_Administrador.admin')

@section('subcontent-01')
<div class="row">
    <div class="col-md-12">
            <a onclick="cargando()" href="javascript:history.back()" class="btn btn-primary">Atras</a>                
             
    </div> 
</div>
       <h4>PLANTILLA DE {{$depto}}</h4>
   <div class="row">
    
<style>
    .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
        visibility: visible;
    }
</style>

               <div class="col-md-12">
                   
                   <table class="table table-bordered" id="users-table2">
                       <thead>
                       <input hidden value="{{$depto}}" id="getValue" name="getValue"/>
                       <tr>
                           <th># Nómina</th>
                           <th># CP</th>
                           <th>Nombre</th>  
                           <th>Apellido</th>
                           <th>Estaciones</th>
                           <th>Puesto</th>
                           <th>Acción</th>
                       </tr>
                       </thead>
                   </table>
               </div>



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
                <div class="modal fade" id="modal_add_user" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        {!! Form::open(['url' => 'admin/guardar_usuario', 'method' => 'POST', 'id' => 'form_guardarUsuario']) !!}
                        <div class="modal-content">
                
                            <div class="modal-header">
                
                                <h4 class="modal-title" id="pwModalLabel">EDITAR USUARIO</h4>
                            </div>
                
                            <div class="modal-body">
                                <div class="">
                                    <input type="hidden" id="nuevo" name="nuevo" value="0" class='form-control' >
                                    <input type="hidden" id="input_user_empID" name="input_user_empID" value="" class='form-control' >
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="input_user_nomina"># Nomina *</label>
                                                <input min="1" type="number" id="input_user_nomina" name="input_user_nomina" class='form-control' readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="input_user_nombre">Nombre *</label>
                                                <input type="text" id="input_user_nombre" name="input_user_nombre" class='form-control'>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="input_user_apellido">Apellido *</label>
                                                <input type="text" class="form-control" id="input_user_apellido" name="input_user_apellido">
                                            </div>
                                        </div>
                                    </div><!-- /.row -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cbo_user_sexo">Sexo *</label>
                                                <select 
                                                    id="cbo_user_sexo"
                                                    name="cbo_user_sexo" 
                                                    data-live-search="true" 
                                                    class="boot-select form-control" 
                                                    title="No has seleccionado nada" 
                                                    data-size="5"
                                                    data-dropup-auto="false" 
                                                    data-live-search-placeholder="Busqueda" 
                                                    autofocus required>
                                                    @foreach ($sexos as $v)
                                                        <option value="{{$v->llave}}" selected>{{$v->valor}}</option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cbo_user_estatus">Estatus *</label>
                                                <select 
                                                    id="cbo_user_estatus"
                                                    name="cbo_user_estatus" 
                                                    data-live-search="true" 
                                                    class="boot-select form-control" 
                                                    title="No has seleccionado nada" 
                                                    data-size="5"
                                                    data-dropup-auto="false" 
                                                    data-live-search-placeholder="Busqueda" 
                                                    autofocus required>
                                                    @foreach ($estatus as $v)
                                                        <option value="{{$v->llave}}" selected>{{$v->valor}}</option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cbo_user_sucursal">Sucursal *</label>
                                                <select 
                                                    id="cbo_user_sucursal"
                                                    name="cbo_user_sucursal" 
                                                    data-live-search="true" 
                                                    class="boot-select form-control" 
                                                    title="No has seleccionado nada" 
                                                    data-size="5"
                                                    data-dropup-auto="false" 
                                                    data-live-search-placeholder="Busqueda" 
                                                    autofocus required>
                                                    @foreach ($sucursales as $v)
                                                        <option value="{{$v->llave}}" selected>{{$v->valor}}</option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cbo_user_depto">Departamento *</label>
                                                <select 
                                                    id="cbo_user_depto"
                                                    name="cbo_user_depto" 
                                                    data-live-search="true" 
                                                    class="boot-select form-control" 
                                                    title="No has seleccionado nada" 
                                                    data-size="5"
                                                    data-dropup-auto="false" 
                                                    data-live-search-placeholder="Busqueda" 
                                                    autofocus required>
                                                    @foreach ($departamentos as $v)
                                                        <option value="{{$v->llave}}" selected>{{$v->valor}}</option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="input_user_puesto">Puesto *</label>
                                                <input type="text" id="input_user_puesto" name="input_user_puesto" class='form-control' readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cbo_user_posicion">Posición *</label>
                                                <select 
                                                    id="cbo_user_posicion"
                                                    name="cbo_user_posicion" 
                                                    data-live-search="true" 
                                                    class="boot-select form-control" 
                                                    title="No has seleccionado nada" 
                                                    data-size="5"
                                                    data-dropup-auto="false" 
                                                    data-live-search-placeholder="Busqueda" 
                                                    autofocus required>
                                                    @foreach ($posiciones as $v)
                                                        <option value="{{$v->llave}}" selected>{{$v->valor}}</option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="input_user_correo">Correo </label>
                                                <div class="input-group">
                                                    <input type="text" id="input_user_correo" name="input_user_correo" class='form-control'
                                                       placeholder="nombre.apellido" onfocus="">
                                                    <span class="input-group-addon primary-color" style="background-color: darkslategrey;
                                                    color: white;" id="basic-addon2">@zarkin.com</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cbo_user_grupo">Grupo Usuario SIZ *</label>
                                               <select 
                                                    id="cbo_user_grupo"
                                                    name="cbo_user_grupo" 
                                                    data-live-search="true" 
                                                    class="boot-select form-control" 
                                                    title="No has seleccionado nada" 
                                                    data-size="5"
                                                    data-dropup-auto="false" 
                                                    data-live-search-placeholder="Busqueda" 
                                                    autofocus required>
                                                    @foreach ($grupos as $v)
                                                        <option value="{{$v->llave}}" selected>{{$v->valor}}</option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="input_user_estaciones">Estaciones de Trabajo Control Piso (separadas por
                                                    coma)</label>
                                                <textarea class="form-control" maxlength="60" rows="1" name="input_user_estaciones"
                                                    id="input_user_estaciones" style="text-transform:uppercase;" value=""
                                                    onkeyup="javascript:this.value=this.value.toUpperCase();"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                            <div class="modal-footer">
                
                               
                                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                                <a id="guardar_user" name="submit" value="Guardar" 
                                    class="btn btn-primary"> Guardar</a>
                            </div>
                
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div><!-- /modal -->
             <!--Aqui termina HTML -->
             
                <script type="text/javascript" >
                    $(document).ready(function (event) {

                        $('#mymodal').on('show.bs.modal', function (event) {
                            var button = $(event.relatedTarget) // Button that triggered the modal
                            var recipient = button.data('whatever') // Extract info from data-* attributes
                            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                            var modal = $(this)
                            modal.find('#userId').val(recipient)
                        });
                        $('#modal_add_user').on('show.bs.modal', function (event) {
                            var button = $(event.relatedTarget) // Button that triggered the modal
                            var nomina = button.data('nomina') // Extract info from data-* attributes
                            var empID = button.data('empid') // Extract info from data-* attributes

                            $.ajax({
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    "nominaUsuario": nomina,                
                                },
                                url: "carga-edicion-usuario",
                                beforeSend: function () {
                                    $.blockUI({
                                        baseZ: 2000,
                                        message: '<h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                                        css: {
                                            border: 'none',
                                            padding: '16px',
                                            width: '50%',
                                            top: '40%',
                                            left: '30%',
                                            backgroundColor: '#fefefe',
                                            '-webkit-border-radius': '10px',
                                            '-moz-border-radius': '10px',
                                            opacity: .7,
                                            color: '#000000'
                                        }
                                    });
                                },
                                complete: function () {
                                    setTimeout($.unblockUI, 1500);
                                },
                                success: function (data) {
                                    console.log(data)
                                    
                                    $("#input_user_nomina").val(nomina);
                                    $("#input_user_empID").val(empID);
                                    $('#input_user_puesto').val(data.puesto);
                                    $("#input_user_nombre").val(data.nombre);

                                    $("#input_user_apellido").val(data.apellido);
                                    $("select[name=cbo_user_sexo]").val(data.sexo);
                                    $("select[name=cbo_user_sexo]").selectpicker('refresh');
                                    
                                    $("select[name=cbo_user_estatus]").val(data.estatus);
                                    $("select[name=cbo_user_estatus]").selectpicker('refresh');

                                    $("select[name=cbo_user_sucursal]").val(data.sucursal);
                                    $("select[name=cbo_user_sucursal]").selectpicker('refresh');
                                    
                                    $("select[name=cbo_user_posicion]").val(data.posicion);
                                    $("select[name=cbo_user_posicion]").selectpicker('refresh');
                                    
                                    $("#input_user_correo").val(data.correo);
                                    $("#input_user_estaciones").val(data.estaciones);
                                    
                                    $('select[name=cbo_user_grupo]').val(data.grupo);
                                    $('select[name=cbo_user_grupo]').selectpicker('refresh')
                                    $('select[name=cbo_user_depto]').val(data.departamento);
                                    $('select[name=cbo_user_depto]').selectpicker('refresh')
                                    
                                }
                            });

                        });
                        $("#guardar_user").click(function(){ 
                            if(valida()){
                                    $.blockUI({
                                        baseZ: 2000,
                                        message: '<h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                                        css: {
                                            border: 'none',
                                            padding: '16px',
                                            width: '50%',
                                            top: '40%',
                                            left: '30%',
                                            backgroundColor: '#fefefe',
                                            '-webkit-border-radius': '10px',
                                            '-moz-border-radius': '10px',
                                            opacity: .7,
                                            color: '#000000'
                                        }
                                    });
                               
                                $("#form_guardarUsuario").submit();
                            }
                        });
                        function valida(){
                            if($("#input_user_nomina").val() == '') { 
                                bootbox.dialog({
                                title: "Mensaje",
                                message: "<div class='alert alert-danger m-b-0'>Campo # Nòmina inválido.</div>",
                                buttons: {
                                success: {
                                label: "Ok",
                                className: "btn-primary m-r-5 m-b-5"
                                }
                                }
                                }).find('.modal-content').css({ 'font-size': '14px' });
                                return false;
                            }
                            if($("#input_user_nombre").val() == '') { 
                                bootbox.dialog({
                                title: "Mensaje",
                                message: "<div class='alert alert-danger m-b-0'>Campo Nombre inválido.</div>",
                                buttons: {
                                success: {
                                label: "Ok",
                                className: "btn-primary m-r-5 m-b-5"
                                }
                                }
                                }).find('.modal-content').css({ 'font-size': '14px' });
                                return false;
                            }
                            if($("#input_user_apellido").val() == '') { 
                                bootbox.dialog({
                                title: "Mensaje",
                                message: "<div class='alert alert-danger m-b-0'>Campo Apellido inválido.</div>",
                                buttons: {
                                success: {
                                label: "Ok",
                                className: "btn-primary m-r-5 m-b-5"
                                }
                                }
                                }).find('.modal-content').css({ 'font-size': '14px' });
                                return false;
                            }
                            var test = /^[0-9]{3}(,[0-9]{3})*$/; //regex 
                            var value = $("#input_user_estaciones").val(); 
                            //console.log(value)
                            if(value.match(test) || value == '') { 
                                console.log("estaciones correcto")
                            }else{ 
                                bootbox.dialog({
                                title: "Mensaje",
                                message: "<div class='alert alert-danger m-b-0'>Campo de Estaciones Control de Piso inválido.</div>",
                                buttons: {
                                success: {
                                label: "Ok",
                                className: "btn-primary m-r-5 m-b-5"
                                }
                                }
                                }).find('.modal-content').css({ 'font-size': '14px' });
                                return false;
                            }
                            test = /^\w+\.*\w+$/; //regex 
                            value = $("#input_user_correo").val(); 
                            if (value.match(test) || value == '') {
                                console.log("correo correcto") 
                                return true;
                            }else{ 
                                bootbox.dialog({
                                title: "Mensaje",
                                message: "<div class='alert alert-danger m-b-0'>Campo Correo incorrecto.</div>",
                                buttons: {
                                success: {
                                label: "Ok",
                                className: "btn-primary m-r-5 m-b-5"
                                }
                                }
                                }).find('.modal-content').css({ 'font-size': '14px' });
                                return false;
                            }
                            return false; 
                        }
                        function correo_sugerido(){ 
                            let nombre = $("#input_user_nombre").val() 
                            let nombres = []; 
                            let apellido = $("#input_user_apellido").val() 
                            let apellidos = []; 
                            if (nombre !== '' && apellido !== '') { 
                                nombres = nombre.split(" ");
                                apellidos = apellido.split(" "); 
                                $("#input_user_correo").val(nombres[0]+ '.' + apellidos[0]) 
                            }else if(nombre !== ''){
                                nombres = nombre.split(" "); 
                                $("#input_user_correo").val(nombres[0]) 
                            } 
                        }    
                        $('#users-table2').DataTable({
                            dom: 'lfrtip',
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: '{!! route('datatables.showusers') !!}',
                                data: function (d) {
                                    d.depto = $('input[name=getValue]').val();
                                }
                            },
                            columns: [
                                { data: 'U_EmpGiro', name: 'U_EmpGiro'},
                                { data: 'empID', name: 'empID'},
                                { data: 'firstName', name: 'firstName'},
                                { data: 'lastName', name: 'lastName'},
                                { data: 'U_CP_CT', name: 'U_CP_CT', orderable: false, searchable: false},
                                { data: 'jobTitle', name: 'jobTitle'},
                                { data: 'action', name: 'action', orderable: false, searchable: false}
                            ],
                            "language": {
                                "url": "{{ asset('assets/lang/Spanish.json') }}",
                            },
                            "columnDefs": [
                                { "width": "10%", "targets":0 },
                                { "width": "10%", "targets":0 },
                                { "width": "20%", "targets":0 },
                                { "width": "20%", "targets":0 },
                                { "width": "20%", "targets":0 },
                                { "width": "16%", "targets":0 },
                                { "width": "6%", "targets":0 }

                            ],
                        });

                    });

                </script>

        </div>



@endsection
