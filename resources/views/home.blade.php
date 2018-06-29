@extends('app')

@section('content')

<?php
$bnd = null;
$bnd2 = null;
$index = 0;
        ?>
        
    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav ">
            @foreach($actividades as $n1)
                <?php
                 $index = $index + 1;
                ?>

                    @if ($bnd == null)
                        <!-- primer elemento, se crea el primer modulo, el primer menu y la primera tarea, NO se cierran las etiquetas (puede que haya una tarea más) -->
                            <?php
                            $bnd = $n1->id_modulo;
                            $bnd2 = $n1->id_menu;
                            ?>

                            <li><a href="javascript:;" data-toggle="collapse"  data-target="#mo{{$n1->id_modulo}}" ><i class="fa fa-fw fa-dashboard"></i> {{$n1->modulo}} <i class="fa fa-fw fa-caret-down"></i></a>
                                <ul id="mo{{$n1->id_modulo}}" class="collapse in">
                                    <li><a href="javascript:;" data-toggle="collapse" data-target="#me{{$n1->id_menu}}"><i class="fa fa-fw fa-tasks"></i> {{$n1->menu}} <i class="fa fa-fw fa-caret-down"></i></a>
                                        <ul id="me{{$n1->id_menu}}" class="collapse">
                                            <li>
                                                <a href="{!! url('home/'.$n1->tarea) !!}">{{$n1->tarea}}</a>
                                            </li>
                                        


                    @elseif($bnd == $n1->id_modulo)
                            <!-- si es el mismo modulo, pregunto si es el mismo menu -->
                            @if($bnd2 == $n1->id_menu)
                                <!-- si modulo y menu son iguales, solo agrego la tarea -->
                                    <li>
                                        <a href="{!! url('home/'.$n1->tarea) !!}">{{$n1->tarea}}</a>
                                    </li>
                                @if($ultimo == $index)
                                  <!--cerrar menu y modulo -->
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                                @endif
                            @else <!-- si es otro menu -->
                                <?php
                                $bnd2 = $n1->id_menu;
                                ?>
                                <!--  cierro ese menu-->
                                        </ul>
                                    </li>
                                    <!-- abro otro menu nuevo y agrego la tarea -->
                                    <li><a href="javascript:;" data-toggle="collapse" data-target="#me{{$n1->id_menu}}"><i class="fa fa-fw fa-tasks"></i> {{$n1->menu}} <i class="fa fa-fw fa-caret-down"></i></a>
                                        <ul id="me{{$n1->id_menu}}" class="collapse">
                                            <li>
                                                <a href="{!! url('home/'.$n1->tarea) !!}">{{$n1->tarea}}</a>
                                            </li>
                                    @if($ultimo == $index)
                                                    <!--cerrar menu y modulo -->
                                                    </ul>
                                                </li>
                                             </ul>
                                        </li>
                                    @endif
                            @endif
                    @else <!-- si no es el mismo modulo -->
                            <?php
                            $bnd = $n1->id_modulo;
                            $bnd2 = $n1->id_menu;
                            ?>
                             <!-- cierro el modulo anterior-->
                                          </ul>
                                      </li>
                                    </ul>
                                </li>

                        <li><a href="javascript:;" data-toggle="collapse" data-target="#mo{{$n1->id_modulo}}" ><i class="fa fa-fw fa-dashboard"></i> {{$n1->modulo}} <i class="fa fa-fw fa-caret-down"></i></a>
                            <ul id="mo{{$n1->id_modulo}}" class="collapse in">
                                <li><a href="javascript:;" data-toggle="collapse" data-target="#me{{$n1->id_menu}}"><i class="fa fa-fw fa-tasks"></i> {{$n1->menu}} <i class="fa fa-fw fa-caret-down"></i></a>
                                    <ul id="me{{$n1->id_menu}}" class="collapse">
                                        <li>
                                            <a href="{!! url('home/'.$n1->tarea) !!}">{{$n1->tarea}}</a>
                                        </li>

                             @if($ultimo == $index)
                                                <!--cerrar menu y modulo -->
                                    </ul>
                                </li>
                            </ul>
                        </li>
                            @endif

                    @endif

@endforeach
@if (Auth::user()->U_EmpGiro==246)
                    <li>
                        <a href="{!! url('/MOD00-ADMINISTRADOR') !!}">Administrador</a>
                    </li>
@endif

                @include('partials.section-navbar')
        </ul>
    </div>
    <!-- /.navbar-collapse -->
    </nav>

    <div id="page-wrapper2">
        @yield('homecontent')

        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->
    </div>
    </div>
    <!-- /#wrapper -->
@endsection

@section('script')
@yield('homescript')
@endsection
@section('script2')
@yield('homescript2')
@endsection
