{{--
 Created by PhpStorm.
 User: WIL
 Date: 16/07/2019
 Time: 01:23 PM
 --}}
<div id="modal-articulo" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="titulo">Artículos</h4>
            </div>
            <div class="modal-body">
                <div class="well">
                    <input type="text" id="input-fila" style="display:none">
                    <div class="table-responsive" id="contenedor-articulo">
                        <table id="tabla-articulo" class="table table-bordered display" cellspacing="0" width="100%">
                            <thead>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>UMI</th>
                                <th>Factor Coversión</th>
                                <th>UMC</th>
                                <th>Precio Lista</th>
                                <th>Moneda Lista</th>
                                <th>Precio Tipo Cambio</th>
                                <th>Moneda Tipo Cambio</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true" id="boton-cerrar"><i class="fa fa-times"></i> Cerrar</button>
                <button type="button" class="btn btn-primary" aria-hidden="true" id="boton-aceptar"><i class="fa fa-check"></i> Aceptar</button>
            </div>
        </div>
    </div>
</div>