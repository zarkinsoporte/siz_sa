@extends('home') 
@section('homecontent')

<style>
.upperc{ 
    text-transform: uppercase;
}
    th, td{
        font-size: 12px;
    }
    
    .table{
        width: auto;
        margin-bottom:0px;
    }
    .detalle {
     margin-left: 3%;
    }
    .table > thead > tr > th, 
    .table > tbody > tr > th, 
    .table > tfoot > tr > th, 
    .table > thead > tr > td, 
    .table > tbody > tr > td,
    .table > tfoot > tr > td { 
        padding-bottom: 2px; padding-top: 2px; padding-left: 4px; padding-right: 0px;
    }
   
    .list-group-item {
        border: 1px solid #b3b0b0;
        padding: 3px 10px
    }
.list-group-item:last-child {
margin-bottom: 10px;

}
h5 small {
    font-size:100%;
}
.container {
padding-right: 15px;
padding-left: 15px;
margin-right: 15px;
margin-left: 10px;
}
.green-edit-field {
border: 1px solid #000000;  
}

</style>
<style>
    div.dataTables_wrapper div.dataTables_processing {
        width: 400px;
        height: 150px;
        margin-left: -25%;
        margin-top: -8%;
        background: linear-gradient(to right, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.95) 25%, rgba(255, 255, 255, 0.95) 75%, rgba(255, 255, 255, 0.2) 100%);
        z-index: 15;
    }

    input {
        color: black;
    }

    div.dataTables_wrapper {
        margin: 0;
    }

    div.container {
        min-width: 100%;
        margin: 0 auto;
    }

    table {
        //me ayudo a que no se desfazaran las columnas en Chrome
        table-layout: fixed;
    }

   .ignoreme{
       background-color: hsla(0, 100%, 46%, 0.10) !important;       
   }
</style>

<div class="container"  ng-controller="MainController">

    <!-- Page Heading -->
    <div class="row">
        
            <div class="visible-xs visible-sm"><br><br></div>
         
            <div class="col-md-12">
                <h3 class="page-header">
                    Traslado Entrega de Mercancía <small>Almacén Origen: {{$almacenOrigen}}</small>
                    <div class="visible-xs visible-sm"><br></div>                 
                </h3>
                
            </div>
        
    </div>
   
    <div class="row">      
        <div class="col-lg-6">
            <div class="input-group">
                
                <span class="">
                    <button class="btn btn-primary" ng-click="modals()" ng-if="!btnshow" type="button"><i class="fa fa-plus"></i> Agregar</button>
                    <a class="btn btn-primary"  ng-if="btnshow" type="button" href="{!! url('home/TRASLADO ENTREGA') !!}">Otro traslado</a>
                    <button ng-if="articulos.length > 0" class="btn btn-success" id="spin" ng-click="sendArt()">
                        <i class="fa fa-send"></i> Enviar</button>
                </span>
            </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
    </div><!-- /.row -->
    <br>
    <div class="row" ng-if="mensaje > 0 && mensaje2 == 0">
        <div class="col-md-12">
            <div class="alert alert-success" role="alert">
                Transferencia interna <%mensaje%> realizada. <a class="btn btn-danger btn-sm" href="{{'PDF/traslado/'}}<%mensaje%>" target="_blank"><i
                        class="fa fa-file-pdf-o"></i> PDF</a>                 
            </div>
        </div>
    </div>
    <div class="row" ng-if="mensaje > 0 && mensaje2 > 0">
        <div class="col-md-12">
            <div class="alert alert-success" role="alert">
                Transferencia interna <%mensaje%> realizada. <a class="btn btn-danger btn-sm" href="{{'PDF/traslado/'}}<%mensaje%>" target="_blank"><i
                        class="fa fa-file-pdf-o"></i> PDF</a>                 
            </div>
            <div class="alert alert-success" role="alert">
                La Entrega #<%mensaje2%> fue enviada. 
            </div>
        </div>
    </div>
    <div class="row" ng-if="successVar.includes('Error')">
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
                <%successVar%>
            </div>
        </div>
    </div>    
    <div class="row" ng-if="successVar.includes('Entrega')">
        <div class="col-md-12">
            <div class="alert alert-success" role="alert">
                <%successVar%>
            </div>
        </div>
    </div>    
    <div class="row" ng-if="nule === 1">
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
                Reinicia Sesión en SIZ. Sesión expirada.
            </div>
        </div>
    </div>    
    <div class="row">
       <div class="col-md-12" ng-if="articulos.length > 0">
           <table class="display condensed">
            <thead>
                <tr>                    
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Destino</th>
                    <th>Quitar</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="art in articulos">
                    <td><%art.pKey%></td>
                    <td><%art.descr%></td>
                    <td><%art.cant%></td>
                    <td><%art.labelDestino%></td>
                    <td><a role="button" ng-click="quitaArt(art)"  class="btn btn-default"><i class="fa fa-trash" style="color:red"></i></a></td>
                </tr>
            </tbody>
        </table>
        <br>
        <div class="form-group">
            <label for="comment">Observaciones:</label>
            <textarea ng-keyup="count = total - comment.length" ng-model="comment" 
            ng-init="total=100" class="form-control upperc" maxlength="100" rows="3" id="comment"></textarea>
            caracteres restantes: <%count%>
            
        </div> 
       </div>
    </div>   <!-- /.row -->
    
    <div class="row">
    
    </div> <!-- /.row -->
    

                   <div class="row">
                       <div class="col-md-12">
                           <div class="modal fade" id="confirma" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                    aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="pwModalLabel">Agregar Artículo <small>Almacén Origen: {{$almacenOrigen}}</small></h4>
                                        </div>
                                       
                                        <div class="modal-body">
                                            
                                            <div class="row">
                                                <div id="ajax_processing" class="dataTables_wrapper">
                                                    <div class="dataTables_processing" style="display: block;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"
                                                            style="font-size:20px; "></i><span style="font-size:20px; "><b>Procesando...</b></span></div>
                                                </div>
                                               <div class="col-md-12">
                                                   <table id="tabla" width="100%" class="table-condensed stripe cell-border display" style="width:100%">
                                                        <thead class="">
                                                            <tr>
                                                                <th><i>Código</i></th>
                                                                <th>Descripción</th>
                                                                <th>UM</th>
                                                                <th>Disponible</th>
                                                            </tr>
                                                        </thead>
                                                    
                                                    </table>
                                               </div>
                                            </div>
                                           
                                            <form name="modalForm" ng-submit="AddArt()" >
                                            <div class="row">
                                                <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pKey">Código:</label>
                                                    <input type="text"  name="pKey" class="form-control" required readonly>
                                                        <input type="text" name="descr" hidden>
                                             
                                                </div>                                               
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="cant">Cantidad:</label>
                                                        
                                                        <input type="number" step="0.01" min="0.1" class="form-control" name="cant" id="cant"
                                                     ng-model="insert.cant" required>
                                                    </div>                                               
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="destino">Almacén destino:</label>
                                                    <select class="form-control" name="destino" id="destino" ng-model="insert.destino" required>
                                                            <option value="" hidden disabled>Seleccione destino</option>
                                                            @foreach($almacenesDestino as $key)
                                                            <option value="{{$key->Code}}" {{ ($key->Code == $almacenesDestino[0]->Code) ? 'selected' : '' }}>{{$key->Label}}</option>
                                                            @endforeach                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" id="submitBtn" class="btn btn-primary" disabled>Añadir a Solicitud</button>
                                            <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="articulos.length > 0">
                                                Ver Entrega de Mercancía <span class="badge badge-light"><% articulos.length %></span>
                                            </button>                                            
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                       </div>
                   </div>
                   <input type="hidden" name="almacen" value="{{$almacenOrigen}}">
</div>
    <!-- /.container -->
@endsection

@section('homescript')

var data,
    tableName= '#tabla',
    table,
    str,
    jqxhr = $.ajax({
    dataType:'json',
    type: 'GET',
    data: {
        almacen :$('input[name=almacen]').val()
    },
    url: '{!! route('OITM.WH.traslados') !!}',
    success: function(data, textStatus, jqXHR) {
    data = JSON.parse(jqxhr.responseText);
  
    data.columns[3].render = function (data, type, row) {
    
    var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
    return val;
    }
    
     table = $(tableName).DataTable({
    dom: 'irtp',
    orderCellsTop: true,
    "order": [[ 1, "asc" ]],
    "autoWidth":true,
    scrollY: "200px",
    "pageLength": 50,
    scrollX: true,
    paging: true,
    processing: true,
    deferRender: true,
    scrollCollapse: true,
    data:data.data,
    columns: data.columns,
    "language": {
    "url": "{{ asset('assets/lang/Spanish.json') }}",
    },
    columnDefs: [
    { width: "7px", targets: 0},
   
    ],
   "rowCallback": function( row, data, index ) {
        //console.log(data['Existencia']);
    if ( data['Existencia'] == '.000000' )
    {
      //  $(row).addClass("ignoreme");
        $('td',row).addClass("ignoreme");
       
    }
    },
   "initComplete": function( settings, json ) {
        $('#ajax_processing').hide();
    }
    });
    
    $('#tabla thead tr:eq(1) th').each( function (i) {
    var title = $(this).text();
    $(this).html( '<input type="text" placeholder="Filtro '+title+'" />' );
    
    $( 'input', this ).on( 'keyup change', function () {
    
    if ( table.column(i).search() !== this.value ) {
    table
    .column(i)
    .search(this.value, true, false)
    .draw();
    
    }
    
    } );
    } );
    
    $('#tabla tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
            $('input[name=pKey]').val('');
            $('input[name=descr]').val('');
            $('input[name=cant]').val('');
            $('input[name=cant]').blur();
            $('#submitBtn').attr("disabled", true);
        }
        else {           
                table.$('tr.selected').removeClass('selected');
                table.$('tr.usel').removeClass('usel');
                $(this).addClass('usel');
                var idx = table.cell('.usel', 0).index();
                var fila = table.rows( idx.row ).data();
               // console.log(fila[0]['Existencia']);
                if(fila[0]['Existencia'] == '.000000'){
                    $('input[name=pKey]').val('');
                    $('input[name=descr]').val('');
                    $('input[name=cant]').val('');
                    $('input[name=cant]').blur();
                    $('#submitBtn').attr("disabled", true);
                }else{
                    var valor = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(fila[0]['Existencia']);
                    $(this).addClass('selected');
                   // console.log(fila[0]['ItemCode']);
                    $('input[name=pKey]').val(fila[0]['ItemCode']);
                    $('input[name=descr]').val(fila[0]['ItemName']);
                   // $('input[name=cant]').attr('title', valor+" en Existencia");
                   
                    $('input[name=cant]').focus();                    
                    $('input[name=cant]').attr('max', fila[0]['Existencia']);
                    $('input[name=cant]').attr('min',1);
                    $('#submitBtn').attr("disabled", false);                    
                }                           
        }
    } );
    
    },
    error: function(jqXHR, textStatus, errorThrown) {
    var msg = '';
    if (jqXHR.status === 0) {
    msg = 'Not connect.\n Verify Network.';
    } else if (jqXHR.status == 404) {
    msg = 'Requested page not found. [404]';
    } else if (jqXHR.status == 500) {
    msg = 'Internal Server Error [500].';
    } else if (exception === 'parsererror') {
    msg = 'Requested JSON parse failed.';
    } else if (exception === 'timeout') {
    msg = 'Time out error.';
    } else if (exception === 'abort') {
    msg = 'Ajax request aborted.';
    } else {
    msg = 'Uncaught Error.\n' + jqXHR.responseText;
    }
    console.log(msg);
    }
    });
    
    $('#tabla thead tr').clone(true).appendTo( '#tabla thead' );


$(window).on('load',function(){
$('#confirma').modal('show');
});


@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.2/angular.min.js"></script>
<script >
   
   var app = angular.module('app', [], function($interpolateProvider) {
$interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});
   app.controller("MainController",["$scope", "$http", function($scope, $http){
    $scope.articulos = [];
    $scope.successVar = '';
    $scope.nule = 0;
    $scope.mensaje = 0;
    $scope.mensaje2 = 0;
    $scope.btnshow = false;
    $scope.insert = {};
    $scope.modals = function(){
       
        $("#confirma").modal();
    }
    $scope.AddArt = function(){
        $scope.insert.pKey = $('input[name=pKey]').val();
        $scope.insert.descr = $('input[name=descr]').val();
        $scope.insert.labelDestino = $( "#destino option:selected" ).text();
        $scope.articulos.push($scope.insert);
        $scope.insert = null;
        $scope.successVar = null;
        $("#spin").attr("disabled", false);
       // console.log(this.articulos);
        
    };
    $scope.quitaArt = function(item){
        var pos = $scope.articulos.indexOf(item);
        //console.log(index-1);
        // removemos del array tareas el indice que guarda al elemento donde se hizo click
        $scope.articulos.splice(pos,1);
    };
    $scope.sendArt = function(){
        $( "#spin" ).html('<span><i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i> Enviando...</span>');
        $("#spin").attr("disabled", true);
       $http({
        method: 'POST',
        url: 'saveTraslado',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data:
        {
            "_token": "{{ Session::token() }}",
            "arts": $scope.articulos,
            "almacen" : $('input[name=almacen]').val(),
            "comentario": $('#comment').val()       
        },
       
        }).then(function (response) {
            $( "#spin" ).html('<span><i class="fa fa-send"></i> Enviar</span>');
        $scope.articulos = [];
        $scope.successVar = response.data;
        
        if($scope.successVar === null){
            $scope.nule = 1;
        } 
        if ($scope.successVar.includes('Mensaje')) {
            var aux = $scope.successVar.split(':')
            $scope.mensaje = aux[1] * 1;
            $scope.mensaje2 = 0;
            $scope.btnshow = true;
        } 
        if ($scope.successVar.includes(';')){
            var aux = $scope.successVar.split(';')
            $scope.mensaje = aux[1] * 1;
            $scope.mensaje2 = aux[0] * 1;
            $scope.btnshow = true;
        }

        return response.data;
        }, function (response) {
        
        }
        );
    }
    }]);
</script>