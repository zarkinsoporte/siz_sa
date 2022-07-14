

@extends('home')

@section('homecontent')

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-8 col-md-11 col-xs-12">
                <div class="hidden-lg"><br><br></div>
                    <h3 class="page-header">
                       Notificaciones
                        <small></small>
                    </h3>
                  
                </div>
            </div>
            <!-- /.row -->
            @foreach ($noticias as $noticia)
           
            
<div class="col-md-11"   >
    <div class="alert alert-info alert-dismissible fade in">  
             <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
             <strong>{{$noticia->autor}}:</strong> 
             
             <br>
             <strong> {!!$noticia->Descripcion!!}</strong>.
             <br>            
<span style="display:flex; justify-content:flex-end; width:100%; padding:0;">
    <a href="../leido/{{$noticia->Id}}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Leido</a></div>
</span>
</div>
@endforeach
        <!-- /.container-fluid -->

@endsection
