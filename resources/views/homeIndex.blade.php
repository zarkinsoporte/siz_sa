@extends('home')

@section('homecontent')

    <div class="container" >

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-6.5 col-md-8 col-sm-5">
            <div class="hidden-lg"><br><br></div>
                <h3 class="page-header">
                    {{env('EMPRESA_NAME')}}
                    <small>Sistema Informático Zarkin </small>
                </h3>
 
            </div>
        </div>
        <style>
.div{
    font-family:arial;
}
        </style>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-6.5 col-md-12 col-sm-12 ">

                @include('partials.alertas')
                <div class="alert alert-info">
                    <strong>Actividad reciente </strong>
                </div>
            </div>
        </div>
        <div class="row">
                <!-- small box -->
            @foreach ($links as $link)
            <div class="col-md-4 col-sm-12 col-xs-12">
                
                <div class="thumbnail">
                    <div class="caption">
                    <h4>{{ substr($link->tarea, 0, 31) }}</h4>
                    <p>{{$link->modulo}}</p>
                        <p align="right">                           
                        <a href="{!! url('home/'.$link->route) !!}" class="btn btn-default" role="button">
                                <i class="fa fa-send" aria-hidden="true"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
            <!-- ./col -->
      </div>

@endsection
 