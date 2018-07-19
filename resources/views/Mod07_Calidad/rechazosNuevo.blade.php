
@extends('home')
@section('homecontent')

        <div class="container" >

<!-- Page Heading -->
<div class="row">
<div class="col-lg-8 col-md-9 col-xs-10">
    <div class="hidden-lg"><br><br></div>
        <h3 class="page-header">
           Captura de Rechazos
            <small>Calidad</small>
        </h3>
        <div class="visible-lg">
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard">  <a href="{!! url('home') !!}">Inicio</a></i>
            </li>
            <li>
                <i class= "fa fa-archive"> <a href="traslados">Traslados</a></i>
            </li>
        </ol>
        </div>
    </div>

    
</div>
@include('partials.alertas')
<iframe class="col-md-9 " scrolling="no" height="200%" src="{!! url('getAutocomplete') !!}" frameborder="0"></iframe>
@endsection
@section ('homescript2')

@endsection

<script>

 var Proveedores = <?php echo json_encode($var);?>;
 var Materiales = <?php echo json_encode($Material);?>;
 var Codeprov= <?php echo json_encode($CodeP);?>;
 var Nameprov= <?php echo json_encode($NameP);?>;
 var CodeMaterial= <?php echo json_encode($CodeMat);?>;
 var NameMaterial= <?php echo json_encode($NameM);?>;

</script>
  