    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">

                        <li>
                            <a href="{!! url('admin/grupos/1') !!}"><i class="fa fa-fw fa-users"></i>   GESTIÓN GRUPOS</a>
                        </li>
                    <li>
                        <a href="{!! url('admin/users') !!}"><i class="fa fa-fw fa-user"></i> GESTIÓN USUARIOS</a>
                    </li>
                    <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#inventario">GESTIÓN INVENTARIO   <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="inventario" class="">
                            <li>
                                <a href="{!! url('admin/inventario') !!}"><i class="fa fa-archive"></i> INVENTARIO</a>
                            </li>
                            <li>
                                <a href="{!! url('admin/monitores') !!}"><i class="fa fa-desktop"></i> MONITORES</a>
                            </li>
                            <li>
                                <a href="{!! url('admin/inventarioObsoleto') !!}"><i class="fa fa-recycle"></i> OBSOLETO</a>
                            </li>
                        </ul>   
                    </li>
                    <li>
                    <a href="javascript:;" data-toggle="collapse" data-target="#inventario">GESTIÓN ALERTAS<i class="fa fa-fw fa-caret-down"></i></a>
                    <ul id="inventario" class="">
                            <li>
                                <a href="{!! url('admin/Nueva') !!}"><i class="glyphicon glyphicon-pencil"></i> NUEVA</a>
                            </li>
                            <li>
                                <a href="{!! url('admin/Notificaciones') !!}"><i class="glyphicon glyphicon-list-alt"></i> LOG ALERTAS</a>
                            </li>
                            <li>
                                <a href="{!! url('admin/emails') !!}"><i class="fa fa-envelope"></i> CONFIGURACION CORREO</a>
                            </li>
                         
                        </ul>  
                    </li>
            @include('partials.section-navbar')
        </ul>
    </div>
    <!-- /.navbar-collapse -->
    </nav>