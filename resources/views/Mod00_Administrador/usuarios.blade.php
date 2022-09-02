
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
                                   <span style="float: right; padding-right: 8px;"> <button data-toggle="modal" data-target="#modal_add_user" data-puesto="{{$dept->jobTitle}}" data-depto="{{$clave}}" class="btn btn-sm btn-success"><i class="fa fa-user-plus"></i> </button></span>
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
                        {!! Form::open(['url' => 'home/add_user', 'method' => 'POST']) !!}
                        <div class="modal-content">
                
                            <div class="modal-header">
                
                                <h4 class="modal-title" id="pwModalLabel">ALTA DE USUARIO</h4>
                            </div>
                
                            <div class="modal-body">
                                <div class="">
                                    
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="input_user_nomina"># Nomina *</label>
                                                <input type="number" id="input_user_nomina" name="input_user_nomina" class='form-control'>
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
                                                <label for="input_user_depto">Departamento *</label>
                                                <input type="text" id="input_user_depto" name="input_user_depto" class='form-control' readonly>
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
                                                {!! Form::select("cbo_user_posicion", $posiciones, null, [
                                                "class" => "form-control selectpicker","id"=>"cbo_user_posicion", "data-style" => "btn-success btn-sm"])
                                                !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cbo_user_sucursal">Sucursal *</label>
                                                {!! Form::select("cbo_user_sucursal", $sucursales, null, [
                                                "class" => "form-control selectpicker","id"=>"cbo_user_sucursal", "data-style" => "btn-success btn-sm"])
                                                !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="input_user_fingreso">Fecha Ingreso *</label>
                                                <input type="text" id="input_user_fingreso" name="input_user_fingreso" class='form-control'>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cbo_user_estatus">Estatus *</label>
                                                {!! Form::select("cbo_user_estatus", $estatus, null, [
                                                "class" => "form-control selectpicker","id"=>"cbo_user_estatus", "data-style" => "btn-success btn-sm"])
                                                !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="input_user_correo">Correo *</label>
                                                <div class="input-group">
                                                    <input type="text" id="input_user_correo" name="input_user_correo" class='form-control'
                                                        onfocus="correo_sugerido()">
                                                    <span class="input-group-addon" id="basic-addon2">@zarkin.com</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cbo_user_grupo">Grupo Usuario SIZ *</label>
                                                {!! Form::select("cbo_user_grupo", $grupos, null, [
                                                "class" => "form-control selectpicker","id"=>"cbo_user_grupo", "data-style" => "btn-success btn-sm"])
                                                !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cbo_user_sexo">Sexo *</label>
                                                {!! Form::select("cbo_user_sexo", $sexos, null, [
                                                "class" => "form-control selectpicker","id"=>"cbo_user_sexo", "data-style" => "btn-success btn-sm"])
                                                !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cbo_user_tipo">Tipo usuario *</label>
                                                {!! Form::select("cbo_user_tipo", $tipos, null, [
                                                "class" => "form-control selectpicker","id"=>"cbo_user_tipo", "data-style" => "btn-success btn-sm"])
                                                !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cbo_user_operacion">Tipo Operación *</label>
                                                {!! Form::select("cbo_user_operacion", $operaciones, null, [
                                                "class" => "form-control selectpicker","id"=>"cbo_user_operacion", "data-style" => "btn-success btn-sm"])
                                                !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="input_user_estaciones">Estaciones de Trabajo Control Piso (separadas por
                                                    coma)</label>
                                                <textarea class="form-control" maxlength="50" rows="1" name="input_user_estaciones"
                                                    id="input_user_estaciones" style="text-transform:uppercase;" value=""
                                                    onkeyup="javascript:this.value=this.value.toUpperCase();"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                            <div class="modal-footer">
                
                               
                                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                                <input id="guardar_user" name="submit" value="Guardar" 
                                    class="btn btn-primary" />
                            </div>
                
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div><!-- /modal -->
               
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
            var puesto = button.data('puesto') // Extract info from data-* attributes
            var depto = button.data('depto') // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this)

                modal.find('#input_user_depto').val(depto)
                modal.find('#input_user_puesto').val(puesto)
        });
        $("#guardar_user").click(function(){ valida() });
    });
    function valida(){
        var test = /^[1-8]{3}(,[1-8]{3})*$/; //regex 
        var value = $("#input_user_estaciones").val(); 
        if(value.match(test) ) { 
            console.log("estaciones correcto")
        }else{ 
             console.log("estaciones no correcto") 
        }
    var test = /^\w+\.*\w+$/; //regex 
    var value = $("#input_user_correo").val(); 
    if (value.match(test) ) {
        console.log("correo correcto") }else{ console.log("correo no correcto") 
    } 
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
