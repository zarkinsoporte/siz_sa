    
<div class="container">                                                                                                                  
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
    
                <div class="panel-body">
                    <div>
                        @if (false)
                        <div class="alert alert-info" role="alert">
                            si hay alerta personalizada
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-scroll" id="registros-ordenes-venta">
                                <table id="tabla_pedidos" class="table table-striped table-bordered hover" width="100%">
                                    <thead>
                                        <tr>                                                                              
                                            <th>Grupal</th>
                                            <th>Inicio</th>
                                            <th>Prioridad</th>
                                            <th>Cliente</th>                                        
                                            <th>Pedido</th>
                                            <th>Entrega</th>
                                            <th>Código</th>                                                                
                                            <th>Descripcion</th>
                                            <th>Cant. Solicitada</th>
                                            <th>Procesado</th>
                                            <th>Pendiente</th>                                       
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="hiddendiv" class="progress" style="display: none">
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100"
                    aria-valuemin="0" aria-valuemax="100" style="width: 50%">
                    <span>Espere un momento...<span class="dotdotdot"></span></span>
                </div>
            </div>
        </div>
    </div>                                                                                                 
</div> 

<script>
   
</script>