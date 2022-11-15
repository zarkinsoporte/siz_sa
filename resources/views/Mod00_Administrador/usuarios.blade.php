
@extends('Mod00_Administrador.admin')

<style type="text/css">
    #myBtn {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 30px;
    z-index: 99;
    font-size: 18px;
    border: none;
    outline: none;
   
    color: white;
    cursor: pointer;
    padding: 15px;
    border-radius: 4px;
    }
    
</style>

@section('subcontent-01')

    <div class="row">
        <div class="col-md-6">
            <button onclick="topFunction()" class="btn btn-primary" id="myBtn" title="Go to top">Ir arriba</button>
            <h3>Usuarios Activos</h3>
        </div>
        <div class="col-md-6">
            <select style="" class="form-control btn-default" id="sel1" name="sel1">
        @foreach($finalarray as $key => $value)
        <option value="{{$key}}">{{$key}}</option>
        @endforeach
        </div>
        
    
    </select>
</div>
        <div class="row">
       @foreach($finalarray as $clave => $valor)

           <div class="col-md-12">

               <?php
               $total = 0
               ?>
               <div class="panel panel-default" id="{{$clave}}">
                   <div class="panel-heading">
                       <h3 class="panel-title"><i class="fa fa-users fa-fw"></i>&nbsp;{{$clave}}</h3>
                   </div>
                   <div class="panel-body">
                       <div class="list-group">
                           @foreach($valor as $dept)
                               <li href="#" class="list-group-item">
                                   
                                   <span class="badge" id="{{$clave.$dept->jobTitle}}">{{$dept->c}}</span>
                                   <span style="float: right; padding-right: 8px;"> 
                                   <button data-toggle="modal" data-target="#modal_add_user" 
                                   data-puesto="{{$dept->jobTitle}}" data-depto="{{$dept->dept}}" 
                                   data-grupo="{{$dept->ROL_GRUPO_ID}}" class="btn btn-sm btn-success"><i class="fa fa-user-plus"></i> </button></span>
                                   @if(empty($dept->jobTitle))
                                        NO CAPTURADO
                                   @else
                                      {{$dept->jobTitle}}
                                   @endif
                                   
                                   <?php
                                   $total = $total + $dept->c
                                   ?>
                               </a>
                           @endforeach
                           <li href="#" class="list-group-item">
                               <span class="badge">{{$total}}</span>
                               <i class="fa fa-fw fa-users"></i> TOTAL {{$clave}}
                           </li>

                       </div>
  <div class="text-left">                      
<a  class="btn btn-success btn-sm" href="plantilla/{{$clave}}"> <i class="fa fa-file-excel-o"></i>
</a>
<a class="btn btn-danger btn-sm" href="Plantilla_PDF/{{$clave}}" target="_blank"><i class="fa fa-file-pdf-o"></i></a>  
                      
                           &nbsp;<a href="detalle-depto/{{$clave}}">Ver detalles <i class="fa fa-arrow-circle-right"></i></a>
                       </div>
                   </div>
               </div>
           </div>

   @endforeach   </div> <!-- /.row -->
   
           <div class="row">

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
                
                                <h4 class="modal-title" id="pwModalLabel">ALTA DE USUARIO</h4>
                            </div>
                
                            <div class="modal-body">
                                <div class="">
                                    <input type="hidden" id="nuevo" name="nuevo" value="1" class='form-control' >
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="input_user_nomina"># Nomina *</label>
                                                <input min="1" type="number" id="input_user_nomina" name="input_user_nomina" class='form-control'>
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
                                                       placeholder="nombre.apellido" onfocus="correo_sugerido()">
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
               
<script type="text/javascript" >
function js_iniciador() {
}
    $(document).ready(function (event) {
        $('.boot-select').selectpicker();
        $('select[name=cbo_user_estatus]').val(1);
        $('select[name=cbo_user_estatus]').selectpicker('refresh')
        $('#mymodal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var recipient = button.data('whatever') // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this)

            modal.find('#userId').val(recipient)
        });

        
    });
    $('#modal_add_user').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var puesto = button.data('puesto') // Extract info from data-* attributes
            var depto = button.data('depto') // Extract info from data-* attributes
            var grupo = button.data('grupo') 
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this)
            $('select[name=cbo_user_grupo]').val(grupo);
            $('select[name=cbo_user_grupo]').selectpicker('refresh')
            $('select[name=cbo_user_depto]').val(depto);
            $('select[name=cbo_user_depto]').selectpicker('refresh')
            
            modal.find('#input_user_puesto').val(puesto)

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
        $('#sel1').on('change', function() {

            $([document.documentElement, document.body]).animate({
            scrollTop: $("#"+this.value).offset().top-100
            }, 2000);
        });
         //Get the button
        var mybutton = document.getElementById("myBtn");
        
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        
        function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        mybutton.style.display = "block";
        } else {
        mybutton.style.display = "none";
        }
        }
        
        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
        }           
                </script>
               

        </div>



@endsection
