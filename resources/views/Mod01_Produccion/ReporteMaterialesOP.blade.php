@extends('home')

            @section('homecontent')


                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                                <div class="visible-xs"><br><br></div>
                            <h3 class="page-header">
                                {{'Reporte de Materiales OP'}}
                                <small>Producción</small>
                            </h3>
                                <h3>OP: {{$op}}</h3>                                                      
                                <h4>Descripción: {{$info->ItemCode.' '.$info->ItemName}}</h4>
                                <h4>Cliente: {{$info->CardCode.' '.$info->CardName}}</h4>
                        <!-- <h5>Fecha & hora: {{date('d-m-Y h:i a', strtotime("now"))}}</h5> -->                          
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-md-12">   
                                    <p align="right">                                                                                                      
                            <a href="../ReporteMaterialesPDF/{{$op}}" target="_blank" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Reporte PDF</a>
                                                               
                        </p>
                        </div>                         
                    </div>   
                    <br>
                 <!-- /.row -->
                    <div class="row">
                        <div class="col-md-12">
                        <table  border="1px" class="table table-striped">
                    <thead class="table-bordered table-condensed">
                    <tr>
                        
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Código</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Descripción</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">UM</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Solicitada</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">Origen</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $bandera=false;
                    ?>
                    @foreach ($data as $rep)
                          <?php 
                          if($bandera==false){
                              $bandera=true;
                              $EstacionO=$rep->ESTACION;
                              ?>
                              <tr><td colspan="5"align="center" bgcolor="#ccc"> <?php echo $EstacionO ?> </td></tr>

                              <?php
                          }
                          
                           $temporal=$rep->ESTACION;
                          //dd($EstacionO);
                           if($EstacionO==$temporal){ 
                            ?>
                            <tr>
                           
                            
                            <td scope="row">
                                {{ $rep->CODIGO }}
                            </td>
                            <td scope="row">
                                {{$rep->MATERIAL}}
                            </td>
                            <td align="center"scope="row">
                                {{ $rep->UDM }}
                            </td>
                            <td align="center"scope="row">
                            <?php echo number_format($rep->CANTIDAD,2); ?>
                            </td>
                            <td align="center" scope="row">
                                {{ $rep->ORIGEN }}
                            </td>
                        </tr>
                           <?php
                           }else{
                            $EstacionO=$temporal;
                            ?>  
                           <tr><td colspan="5"align="center" bgcolor="#ccc"> <?php echo $EstacionO ?> </td></tr>
                           <tr>
                           
                            
                            <td scope="row">
                                {{ $rep->CODIGO }}
                            </td>
                            <td scope="row">
                                {{$rep->MATERIAL}}
                            </td>
                            <td align="center"scope="row">
                                {{ $rep->UDM }}
                            </td>
                            <td align="center"scope="row">
                                <?php echo number_format($rep->CANTIDAD,2); ?>

                            </td>
                            <td align="center" scope="row">
                                {{ $rep->ORIGEN }}
                            </td>
                        </tr>
                            <?php
                        }
                           ?>
                           
                    @endforeach 
                    </tbody>
                </table>
                        </div>
                    </div>

                    </div>
                    <!-- /.container -->

                    @endsection

                   

                    <script>

                        function mostrar(){
                            $("#hiddendiv").show();
                        };

                    </script>