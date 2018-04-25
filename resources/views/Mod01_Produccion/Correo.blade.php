@extends('app')

@section('content')

@include('partials.menu-admin')


    <div >

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div >
                    <div class="visible-xs"><br><br></div>
                    <h3 class="page-header">
                        Usuarios
                    </h3>
                    
                       <div class= "col-lg-6.5 col-md-8 col-sm-7">
                        <div class="hidden-xs">
                        <div class="hidden-sm">
                        <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="{!! url('home') !!}">Inicio</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i> <a href="users">Usuarios</a>
                       </li>
                       <li>
                            <i class="fa fa-bell"></i>  <a href="{!! url('/admin/Notificaciones') !!}">Notificaciones</a>
                        </li>
                    </ol>
                </div>
            </div>
            <body>
                <p><strong>Nombre:</strong>{!!$name !!}</p>
                <p><strong>Correo:</strong>{!!$email!!}</p>
                <p><strong>Mensaje</strong>{!!$mensaje!!}</p>
        </body>
            <!-- /.row -->
            {!! Form::open 'home/traslados/Correo', 'method'=> 'POST'!!}
                <div class="form-group">
   <div class="col-md-6 contact-left">
    {!! Form:: text('name',null,['placeholder'=> 'Nombre']) 
        !!}
        {!! Form:: text('Email',null,['placeholder'=> 'Email']) 
        !!}
  </div>
  <div class="col-md-6 contact-right">
  {!! Form:: textarea ('mensaje',null,['placeholder'=> 'Mensaje']) 
        !!}
  </div>
  {!! Form:: submit('Send') 
        !!}
        {!! Form::close() !!}
        </div>
        </div>

@endsection
