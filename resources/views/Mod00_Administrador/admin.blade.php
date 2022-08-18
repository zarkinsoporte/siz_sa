@extends('app')

@section('content')

@include('partials.menu-admin')


    <div >

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="visible-xs visible-sm"><br><br></div>
                    <h3 class="page-header">
                       MOD00-SISTEMAS
                    </h3>
                </div>
            </div>
            <!-- /.row -->
         <div class="container">
             <div class="row">

                 <div class="col-md-12">
                     @if (count($errors) > 0)
                         <div class="alert alert-danger text-center" role="alert">
                             @foreach($errors->getMessages() as $this_error)
                                 <strong>¡Lo sentimos!  &nbsp; {{$this_error[0]}}</strong><br>
                             @endforeach
                         </div>
                     @elseif(Session::has('mensaje'))
                         <div class="row">
                             <div class="alert alert-success text-center" role="alert">
                                 {{ Session::get('mensaje') }}
                             </div>
                         </div>
                     @endif
                 </div>
             </div>
             @yield('subcontent-01')
         </div>



        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->
    </div>
    </div>



    <!-- /#wrapper -->
@endsection

@section('script')





@endsection
