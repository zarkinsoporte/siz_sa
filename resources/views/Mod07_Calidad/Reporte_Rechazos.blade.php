@extends('home')

            @section('homecontent')


                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="page-header">
                                Salotto SA DE CV
                                <small>Reporte de Rechazos</small>
                            </h3>
                           
                                                      
                          
                        </div>
                    </div>
                    <!-- /.row -->
                    <div class="row">

                        <div class="col-md-12 ">
                            @include('partials.alertas')                        
                        </div>


                        <!-- Modal -->

  <div class="modal fade" id="pass" role="dialog" >
      <div class="modal-dialog modal-sm" role="document">
           <div class="modal-content" >
              <div class="modal-header">

                   <h4 class="modal-title" id="pwModalLabel">Reporte de Rechazos</h4>
              </div>
              {!! Form::open(['url' => 'pdfRechazo', 'method' => 'POST']) !!}

              <div class="modal-body">
                   <input type="text" hidden name="send" value="send">
                  <div class="form-group">
                       @include('partials.alertas')
                  </div>
                  <div class="form-group">
                      <label for="date_range" class="control-label">Rango de Fechas:</label><br>
                       Desde:<input type="date" id="FechIn" name="FechIn" class="form-control" >
                      Hasta:<input type="date" id="FechaFa" name="FechaFa" class="form-control" >

                        </div>
                                     


                         </div>


                         <div class="modal-footer">
                            <div id="hiddendiv" class="progress" style="display: none">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span>Espere un momento...<span class="dotdotdot"></span></span>
                            </div>
                            </div>
                            &nbsp;&nbsp;&nbsp;
                            <input id="submit" name="submit" type="submit" value="Generar" onclick="mostrar();"  class="btn btn-primary"/>

                             <a type="button" class="btn btn-default"  href="{!!url('home')!!}">Cancelar</a>
                    </div>
                    {!! Form::close() !!}
                    </div>
                            </div>
            </div><!-- /modal -->

                     
                        <!-- /cantidadModal-->

                    </div>
                    <!-- /.container -->

                    @endsection

                    @section('homescript')

                        var myuser = $('#login').data("field-id");
                       
                        $('#pass').modal(
                        {
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                        }
                        );

                    @endsection

                    <script>

                        function mostrar(){
                            $("#hiddendiv").show();
                        };

                    </script>