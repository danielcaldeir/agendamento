<!-- [ Header ] start -->
<header class="navbar pcoded-header navbar-expand-lg navbar-light">
    <div class="m-header">
        <a class="mobile-menu" id="mobile-collapse1" href="javascript:"><span></span></a>
        <a href="{{ route('dashboard') }}" class="b-brand">
            <div class="b-bg">
                <i class="feather icon-trending-up"></i>
            </div>
            <span class="b-title">MedMazza</span>
        </a>
    </div>
    <a class="mobile-menu" id="mobile-header" href="javascript:">
        <i class="feather icon-more-horizontal"></i>
    </a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li><a href="javascript:" class="full-screen" onclick="javascript:toggleFullScreen()"><i class="feather icon-maximize"></i></a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li>
                <div class="dropdown">
                    <a class="dropdown-toggle" href="javascript:" data-toggle="dropdown"><i class="icon feather icon-bell"></i></a>
                    <div class="dropdown-menu dropdown-menu-right notification">
                        <div class="noti-head">
                            <h6 class="d-inline-block m-b-0">Notificações</h6>
                            <div class="float-right">
                                <a href="javascript:">limpar</a>
                            </div>
                        </div>
                        <ul class="noti-body">
                        </ul>
                    </div>
                </div>
            </li>
            <li>
                <div class="dropdown drp-user">
                    <a href="javascript:" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon feather icon-settings"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-notification">
                        <div class="pro-head">
                            <img
                                class="rounded-circle"
                                src='{{ asset('img/pictures/' . $user->image ?? 'default.png') }}'
                                alt="User-Profile-Image"
                            >
                            <span>{{ $user->name }}</span>
                            <a href="{{ route('logout') }}" class="dud-logout" title="Logout">
                                <i class="feather icon-log-out"></i>
                            </a>
                        </div>
                        <ul class="pro-body">
                            <li>
                                <a href="{{ route('dashboard') }}" class="dropdown-item">
                                    <i class="feather icon-home"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('appointments.index') }}" class="dropdown-item">
                                    <i class="feather icon-calendar"></i> Agendamentos
                                </a>
                            </li>
                            @if ($user->type !== 'doctor')
                                <li>
                                    <a href="{{ route('doctors.index') }}" class="dropdown-item">
                                        <i class="feather icon-users"></i> Médicos
                                    </a>
                                </li>
                            @endif
                            @if ($user->type === 'admin')
                                <li>
                                    <a href="{{ route('patients.index') }}" class="dropdown-item">
                                        <i class="feather icon-user"></i>  Pacientes
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admins.index') }}" class="dropdown-item">
                                        <i class="feather icon-lock"></i>  Administradores
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('users.edit', $user->id) }}" class="dropdown-item">
                                    <i class="feather icon-settings"></i> Configuração
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}" class="dropdown-item">
                                    <i class="feather icon-log-out"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>
<!-- [ Header ] end -->
