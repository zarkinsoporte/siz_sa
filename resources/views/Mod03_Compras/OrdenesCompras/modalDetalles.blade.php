<div class="row">
    <div class="modal fade" id="modal-detalles" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true" style="z-index:1050" tabindex="-1" data-backdrop="static" data-keyboard="true">
        <div class="modal-dialog modal-lg">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-dismiss="modal" data-original-title="" title=""><i class="fa fa-times"></i></a>
                    </div>
                    <h4 class="panel-title">Detalles</h4>
                </div>
                <div class="panel-body">
                    <form data-parsley-validate="true" novalidate>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Cantidad Por Devolver</label>
                                        <input id="input-cantidad-devolver" style="text-align: right;" class="form-control input-sm" type="text" disabled="true">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <p style="margin-bottom: 23px"></p>
                                    <button type="button" class="btn btn-sm btn-primary m-r-5" id="boton-agregar"><i class="glyphicon glyphicon-plus-sign"></i> Agregar</button>
                                </div>
                                <div class="col-md-3" id="datosPedimento">
                                    <p style="margin-bottom: 23px"></p>
                                    <button type="button" class="form-control btn btn-success m-r-5 m-b-5" id="boton-datos-pedimento"> Datos Pedimento</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="tabla-detalles" class="table table-striped table-bordered nowrap" width="100%">
                                            <thead>
                                            <tr>
                                                <th>Cantidad *</th>
                                                <th>Fecha Requerida *</th>
                                                <th>Fecha Promesa *</th>
                                                <th>Eliminar</th>
                                                <th>OVR_OVD_DETALLE</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success m-r-5" aria-hidden="true" id="boton-aceptar">Aceptar</button>
                    <button type="button" class="btn btn-sm btn-inverse m-r-5" data-dismiss="modal" aria-hidden="true" id="boton-cerrar">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>