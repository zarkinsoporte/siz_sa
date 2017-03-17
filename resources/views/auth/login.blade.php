
@extends('app')

@section('content')
    <div class="container full">
        <div class="row" >
            <div class="col-md-6 col-md-offset-3">
                <div >

                    <div >
                        <form class="form-horizontal" role="form" method="post" action="{{url('/auth/login')}}">
                            {{ csrf_field() }}
                            @if (count($errors) > 0)
                                <div class="alert alert-danger text-center" role="alert">
                                    @foreach($errors->getMessages() as $this_error)
                                        <strong>Error  &nbsp; {{$this_error[0]}}</strong><br>
                                    @endforeach
                                </div>
                            @endif
                            <div >
                                <label for="id" class="col-md-4 control-label label-floating">Número de usuario</label>

                                <div class="col-md-6">
                                    <input id="id" type="number" class="form-control" name="id" value="{{ old('id') }}" required autofocus>

                                    @if ($errors->has('id'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group label-floating is-empty">

                                <label class="control-label" for="id">No. Nómina:</label>
                                <div class="input-group">
                                    <input type="number" id="id" name="id" class="form-control" value="{{old('id')}}" required autofocus>
                                </div>


                                <label class="control-label" for="password">Contraseña:</label>
                                <div class="input-group">
                                    <input type="password" id="password" class="form-control" name="password" required>
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-fab btn-fab-mini">
                                          <i class="material-icons">functions</i>
                                        </button>
                                      </span>
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Contraseña</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                        </form>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection

