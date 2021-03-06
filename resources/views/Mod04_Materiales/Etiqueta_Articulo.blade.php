@extends('home') 
@section('homecontent')

<style>
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

.boot-select{
    padding-bottom: 10px !important;
}
.bootstrap-select>.dropdown-toggle {
width: 100% !important;
}
.open > .dropdown-menu {

    display: block;
    max-height: 120.8px !important;

}
</style>
<div class="container">
 {!! Form::open(['url' => 'etiquetaQR', 'method' => 'POST', 'id' => 'mainform', 'target'=>"_blank"]) !!}
 {{ csrf_field() }}
    <!-- Page Heading -->
    <div class="row">
        
            <div class="visible-xs visible-sm"><br><br></div>
         
            <div class="col-md-12">
                <h3 class="page-header">
                    Generacion de Etiquetas 
                    <div class="visible-xs visible-sm"><br></div>
                    <span class="pull-right">
                        @if(!isset($oculto))  
                            <a class="btn btn-primary" href="{{url('home/GENERACION ETIQUETAS')}}">Ver Otro Artículo</a>                    
                        @else
                            <a class="btn btn-primary btn-sm" href="{{URL::previous()}}"><i class="fa fa-angle-left"></i> Atras</a>
                        @endif
                        <div class="visible-xs visible-sm"><br></div>
                    </span>         
                </h3>
                
            </div>
        
    </div>
   <div class="row">
    <div class="col-md-12">
        @include('partials.alertas')
    </div>
</div>
    <div class="row">
        <div class="col-md-3">
       <ul>
         <li class="list-group-item active">
            <div>
                <h5>Código <small><span class="pull-right" style="color:white">{{$data[0]->ItemCode}}</span></small></h5>
                <input type="hidden" name="pKey" value="{{$data[0]->ItemCode}}">
            </div>
            
        </li>
       </ul>
        </div>
        <div class="col-md-7">
            <ul>
                <li class="list-group-item">
                    <div>
                       <h5 class="my-0"> <small>{{$data[0]->ItemName}}</small></h5>
                        <input type="hidden" name="itemName" value="{{$data[0]->ItemName}}">
                    </div>
        
                </li>
            </ul>
        </div>
        
    </div>
    <!-- /.row -->
    <div class="row">
       
        <div class="col-md-7 col-sm-12">
            <ul>
                <li class="list-group-item ">
                    <div>
                        <h5>Proveedor</h5>                    
                        <div class="input-group">
                        
                            <select data-live-search="true" class="boot-select" id="proveedor" name="proveedor" {{$privilegioTarea}}>                               
                                <option value="" {{ old('proveedor', $data[0]->CardCode??'SIN DATOS') == 'SIN DATOS' ? 'selected' : '' }}>SIN DATOS</option>
                                @foreach ($proveedores as $proveedor)
                                <option value="{{old('proveedor',$proveedor->CardCode)}}" {{ ($proveedor->CardCode == $data[0]->CardCode) ? 'selected' : '' }}>                                   
                                    <span>{{$proveedor->CardCode}}  &nbsp;&nbsp;&nbsp; {{$proveedor->CardName}}</span></option>
                                @endforeach
                            </select>                         
                        </div><!-- /input-group -->
                    </div>        
                </li>
            </ul>
        </div>
        <div class="col-md-3 col-sm-12">
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#confirma" {{$privilegioTarea}}>
                                            <i class="fa fa-print" aria-hidden="true"></i> Etiqueta
                            </button>
        </div>
    </div>   
    
    <div class="row">
    <div class="col-md-3">
       <ul>
        <li class="list-group-item ">
                <div>
                    <h5 class="my-0">CANT X BULTO</h5>
                    <input type="number" step="0.01" min="0" class="form-control" name="cantx_bulto" id="cantx_bulto" value="{{old('factor', number_format($data[0]->Factor, 2, '.', ','))}}" {{$privilegioTarea}}>
               
                </div>
            
            </li>
           
        <li class="list-group-item">
                <div>
                    <h5 class="my-0">FACTOR CONV.
                    <small ><span
                                class="pull-right">{{ number_format($data[0]->Factor, 2, '.', ',') }}</span></small>
                    </h5>
               
                </div>
            
            </li>
       </ul>
    
    </div> <!-- /.md-3 -->  
    <div class="col-md-3">
        <ul>
            <li class="list-group-item">
               <div>
                <h5 class="my-0">FECHA RECIBO</h5>                
                <input type="text" class="form-control" name="date" required>
            </div>
            
            </li>
            <li class="list-group-item">
                 <div>
                    <h5 class="my-0">UM INVENTARIO<small ><span
                                class="pull-right">{{ $data[0]->UM}}</span></small> </h5>
                     <input type="hidden" name="um" value="{{$data[0]->UM}}">
                </div>
            </li>
        </ul>
        </div> <!-- /.md-3 -->
    </div> <!-- /.row -->
    
{!! Form::close() !!}
                    <div class="modal fade" id="confirma" tabindex="-1" role="dialog" >
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="pwModalLabel">Generar etiqueta</h4>
                                </div>
                             
                                <div class="modal-body">

                                    <div class="form-group">
                                        <div>
                                           <h4>¿Desea continuar?</h4>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                    <button type="button" id="submitBtn" class="btn btn-primary">Enviar</button>
                                </div>
                                
                            </div>
                        </div>
                    </div>


                    
</div>
    <!-- /.container -->
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
$("#submitBtn").click(function(){        

$("#mainform").submit(); // Submit the form
$('#confirma').modal('hide');

});
$("#showImg").click(function(){        
$('.imagepreview').attr('src', $("#showImg").attr('src'));
$('#imagemodal').modal('show');

});
var date_input=$('input[name="date"]'); //our date input has the name "date"

date_input.datepicker({
format: 'dd/mm/yyyy',
todayHighlight: true,
language: "es",
autoclose: true,
}).datepicker("setDate", new Date());
}  //js_iniciador
</script>
