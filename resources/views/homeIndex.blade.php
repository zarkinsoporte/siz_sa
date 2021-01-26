@extends('home')

@section('homecontent')

    <div class="container" >

        <!-- Page Heading -->
        
        <div class="row">
            <div class="col-lg-6.5 col-md-8 col-sm-5">                <h3 class="page-header">
                    {{env('EMPRESA_NAME')}}
                    <small>Sistema Informático Zarkin </small>
              
 
            </div>
        </div>
        <style>
.div{
    font-family:arial;
}
.badge-info {
  background-color: #3a87ad;
}
.badge-warning {
  background-color: #f89406;
}
        </style>
        <!-- /.row -->
        <div id="content" class="row">
            <div class="col-lg-6.5 col-md-12 col-sm-12 ">

                @include('partials.alertas')
                <div class="alert alert-info">
                    <strong>Actividad reciente </strong>
                </div>
            </div>
        </div>
        <div class="row">
                <!-- small box -->
                <?php
                $indice = 0
                ?>
            @foreach ($links as $link)
            <div class="col-md-4 col-sm-6 col-xs-12">
                
                <div class="thumbnail">
                    <div class="caption">
                    <h4>{{ substr($link->tarea, 0, 31) }}</h4>
                    <p>{{$link->modulo}} 
                    @if ($traslados > 0 && $link->route == 'TRASLADO RECEPCION') &nbsp;
                        <span class="badge badge-warning"> {{$traslados}}  @if ($traslados > 1) Pendientes
                        @else
                        Pendiente    
                        @endif</span>
                    @endif
                    </p>
                        <p align="right">                           
                        <a href="{!! url('home/'.$link->route) !!}" class="btn btn-default" role="button">
                                <i class="fa fa-send" aria-hidden="true"></i>
                            </a>
                        </p>
                    </div>
                </div>
                <?php 
               $indice++;                
                ?>
                @if ($indice % 3 == 0)
                    </div><div class="row">
                @endif
            </div>
            @endforeach
            <!-- ./col -->
      </div>

@endsection
 
<script>
    function js_iniciador() {
        $('.toggle').bootstrapSwitch();
        $('[data-toggle="tooltip"]').tooltip();
        $('.boot-select').selectpicker();
        $('.dropdown-toggle').dropdown();
        setTimeout(function() {
        $('#infoMessage').fadeOut('fast');
        }, 5000); // <-- time in milliseconds 

        $("#sidebarCollapse").on("click", function() {
        $("#sidebar").toggleClass("active"); 
        $("#page-wrapper").toggleClass("content"); 
        $(this).toggleClass("active");
        });
        for(i=0;i<4;i++) { $("#sidebarCollapse").fadeTo('slow', 0.5).fadeTo('slow', 1.0); }
    }  //js_iniciador
</script>
    
