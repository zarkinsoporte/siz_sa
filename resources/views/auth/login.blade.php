<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
{!! Html::style('assets/css/bootstrap.css') !!}
{!! Html::style('assets/css/myMaterial-design.css') !!}
{!! Html::style('assets/css/site_global.css?crc=443350757.css') !!}
{!! Html::style('assets/css/index.css?crc=3185328.css') !!}



<!DOCTYPE html>
<html class="nojs html css_verticalspacer" lang="es-ES">
<head>

    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <meta name="generator" content="2017.0.0.363"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script type="text/javascript">
        // Update the 'nojs'/'js' class on the html node
        document.documentElement.className = document.documentElement.className.replace(/\bnojs\b/g, 'js');
        // Check that all required assets are uploaded and up-to-date
        if(typeof Muse == "undefined") window.Muse = {}; window.Muse.assets = {"required":["museutils.js", "museconfig.js", "webpro.js", "musewpslideshow.js", "jquery.museoverlay.js", "touchswipe.js", "jquery.watch.js", "require.js", "index.css"], "outOfDate":[]};
    </script>
    <title>Inicio</title>
    <link rel="shortcut icon" href="images/IconZrk.ico" type="image/x-icon" >
    <link rel="icon" href="imagen/IconoZain.png" sizes="32x32" ><link rel="icon"

</head>
<style> 
input[type=number]::-webkit-inner-spin-button{ 
  -webkit-appearance: none; 
  margin: 0; 
}

</style>

<body class="container-fluid" style="background-image: url({{ URL::asset('images/fondo_siz.jpg') }}); background-repeat:no-repeat; background-size:cover; background-position:center; min-height:100vh; display:flex; align-items:center; justify-content:center;">

<div class="container" style="display:flex; align-items:center; justify-content:center; min-height:100vh;">
    <div class="row" style="width:100%; display:flex; justify-content:center;">
        <div class="col-md-10 col-sm-10 col-xs-12" style="float:none; margin:0 auto;">
            <!-- Card Start -->
            <div class="panel panel-default" style="background: rgba(34,34,34,0.85); border-radius:18px; box-shadow:0 4px 24px rgba(0,0,0,0.25); margin-top:30px;">
                <div class="panel-body" style="padding:40px 40px 35px 40px;">
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <img class="svg" id="u196" src="{{ URL::asset('images/svg-pegado-150982x45.svg') }}" alt="Logo SIZ" width="180" height="180" style="display:block; margin:0 auto;" />
                        <img class="hidden-xs" alt="Bienvenido" src="{{ URL::asset('images/u343-4.png') }}" style="height: 7%; width: 90%; margin-top: 10px; display:block; margin-left:auto; margin-right:auto;"/>
                    </div>
                    <!-- Formulario -->
                    @if (count($errors) > 0)
                        <div class="alert alert-danger text-center" style="opacity: .6; border-radius: 15px; color: white" role="alert">
                            @foreach($errors->getMessages() as $this_error)
                                <strong>Error  &nbsp; {{$this_error[0]}}</strong><br>
                            @endforeach
                        </div>
                    @elseif(Session::has('mensaje'))
                        <div class="alert alert-success text-center"style="opacity: .9; border-radius: 15px; color: white" role="alert">
                            {{ Session::get('mensaje') }}
                        </div>
                    @endif
                    <form class="form-horizontal"  role="form" method="post" action="{{url('/auth/login')}}">
                        {{ csrf_field() }}
                        <div class="form-group label-floating">
                            <label class="control-label" for="id" style="color:#fff;">No. Nómina:</label>
                            <div class="input-group">
                                <input type="number" min="0" id="id" name="id" class="form-control" style="color: white" value="{{old('id')}}" required autofocus autocomplete="off">
                                <span class="input-group-btn">
                                </span>
                            </div>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="password" style="color:#fff;">Contraseña:</label>
                            <div class="input-group">
                                <input type="password" id="password" class="form-control"  style="color: white" name="password" required>
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-fab btn-fab-mini">
                                      <i class="material-icons">send</i>
                                    </button>
                                  </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Card End -->
        </div>
    </div>
</div>
                   
                    <!-- Eliminado formulario y alertas duplicados -->

</div>
<!-- Other scripts -->


</body>
</html>

<!-- Scripts -->


