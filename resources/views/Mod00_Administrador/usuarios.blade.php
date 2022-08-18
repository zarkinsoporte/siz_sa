
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
                               <a href="#" class="list-group-item">
                                   <span class="badge">{{$dept->c}}</span>
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
                           <a href="#" class="list-group-item">
                               <span class="badge">{{$total}}</span>
                               <i class="fa fa-fw fa-users"></i> TOTAL {{$clave}}
                           </a>

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
                    });
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
