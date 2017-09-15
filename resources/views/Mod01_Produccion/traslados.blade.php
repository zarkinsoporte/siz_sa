@extends('home')

@section('homecontent')


        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">
                        Traslados
                        <small>Producción</small>
                    </h3>
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard">  <a href="{!! url('home') !!}">Inicio</a></i>
                        </li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">

                <div class="col-md-12 ">
                    @include('partials.alertas')

                    @if(Session::has('usertraslados'))
                        <div id="login" data-field-id="{{Session::get('usertraslados') }}" >
                            {{Session::get('usertraslados') }}
                        </div>

                    @endif

                </div>
            </div>


            <!-- Modal -->

            <div class="modal fade" id="pass" role="dialog" >
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content" style=" background-color: rgb(189, 217, 254)">
                        <div class="modal-header" style="background-color: rgb(198,221,254)">

                            <h4 class="modal-title" id="pwModalLabel">Login</h4>
                        </div>
                        {!! Form::open(['url' => 'home/usuariotraslados', 'method' => 'POST']) !!}
                        <div class="modal-body image">

                            <input type="text" hidden name="send" value="send">

                            <br>
                            <div class="row">
                                <div class="col-md-2 col-md-offset-1">
                                    <img src= "{{ URL::asset('images/Mod01_Produccion/password.png')}}"
                                         alt="">
                                </div>
                                <div class="col-md-7 col-md-offset-1">
                                    <div id="hiddendiv" style="display: none">
                                        <label for="name" class="control-label">Usuario:</label>
                                        <input id="miusuario" type="text" class="form-control" name="usuario" minlength="3" maxlength="5">
                                        <br>
                                    </div>

                                    <label for="name" class="control-label">Contraseña:</label>
                                    <input id="pass" type="password" class="form-control" name="pass" required minlength="3" maxlength="10">
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">

                            <a  id="mostrar" onclick="mostrar()">Cambiar usuario</a>
                            <a  id="ocultar" onclick="ocultar()" style="display: none">Regresar</a>
                            &nbsp;&nbsp;&nbsp;
                            <button type="submit" class="btn btn-primary">Entrar</button>
                            <a type="button" class="btn btn-default"  href="{!!URL::previous()!!}">Cancelar</a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div><!-- /modal -->

        </div>
        <!-- /.container -->

@endsection

@section('homescript')


    var myuser = $('#login').data("field-id");

    if(myuser == false){
            $('#pass').modal(
            {
                    show: true,
                    backdrop: 'static',
                    keyboard: false
            }
            );
    }


@endsection

<script>

function ocultar(){
        $("#hiddendiv").hide();
        $("#ocultar").hide();
        $("#mostrar").show();
        $("#miusuario").removeAttr('required');
    };
function mostrar(){
        $("#hiddendiv").show();
        $("#mostrar").hide();
        $("#ocultar").show();
        $('#miusuario').attr('required', 'required');
    };

</script>