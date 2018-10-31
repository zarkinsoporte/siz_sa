

@extends('home')
@section('homecontent')


    <div >

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div >
                    <div class="visible-xs"><br><br></div>
                    <h3 class="page-header">
                        Set Parámetro de Bono
                    </h3>

                       <div class= "col-lg-6.5 col-md-8 col-sm-7">
                        <div class="hidden-xs">
                        <div class="hidden-sm">
                        <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="{!! url('home') !!}">Inicio</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i> <a href="#">RH</a>
                       </li>
                       <li>
                            <i class="fa fa-money"></i>  <a href="#">Parámetros</a>
                        </li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->
            {!! Form::open(['url' => '', 'method' => 'POST']) !!}
  <div class="form-group">
    <label for="exampleFormControlInput1">Tipo de Bono</label>
    <input type="text" class="form-control" id="Autor" name="Autor" require>
  </div>
  <div class="form-group">
    <label for="exampleFormControlInput1">Rango Inicio (en MXN)</label>
    <input type="text" class="form-control" id="Destinatario" name="Destinatario" require >
  </div>
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Rango Fin (en MXN)</label>
    <input class="form-control" id="Nota"name="Nota" rows="3" require></input>
  </div>
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Bono (en MXN)</label>
    <input class="form-control" id="Nota"name="Nota" rows="3" require></input>
  </div>
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Tipo de Empleado</label>
    <input class="form-control" id="Nota"name="Nota" rows="3" require></input>
  </div>
  <button type="submit" class="btn btn-primary">Enviar</button>
  {!! Form::close() !!}
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->
    </div>
    </div>



    <!-- /#wrapper -->

@endsection
