<style>
    .nav-menu .menu-item:hover{
        transition: 0.2s;
        background-color: #efefef;
    }
    .nav-menu .menu-item{
        padding: 0.25rem 0.5rem 0.25rem 0.5rem;
    }
    .menu-border{
        margin: 0.75rem 0;
        border-right: 1px solid #d7d7d7;
    }
    .navbar-menu{
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .dropdown-menu{
        left: inherit !important;
    }
    .dropdown-menu-right{
        right: inherit !important;
    }
</style>

<nav class="navbar sidebar-default-color text-white navbar-expand-md">
    <div class="container-fluid">
        <div class="order-3">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <img src="{{ asset('/data/logo/logo.png') }}" alt="Fast Fitness" width="43px">
                </li>
                <li class="nav-item ml-2">
                    <h5 class="nav-link font-weight-bold mb-0">Fast Fitnes</h5>
                </li>
            </ul>
        </div>

        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav">
            <li class="user-card nav-item">
                <a class="card-section-light bg-danger" data-toggle="dropdown" href="#">
                    <small><b>{{ $username }}</b><i class="fas fa-user ml-2"></i></small>
                </a>

                <div class="dropdown-menu dropdown-menu-sm">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user fa-sm mr-2"></i> Account
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt fa-sm mr-2"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>

<nav class="navbar navbar-white navbar-expand-md p-0 navbar-menu text-sm text-bold">
    <div class="container-fluid overflow-auto" style="white-space: nowrap;">
        <div class="order-1">
            <!-- Left navbar links -->
            <ul class="navbar-nav nav-menu">
                <li class="nav-item menu-item">
                    <a class="nav-link text-dark" data-toggle="dropdown" href="#">
                        <span class="fas fa-tachometer-alt fa-sm mr-1"></span> Dashboard
                    </a>

                    <div class="dropdown-menu dropdown-menu-lg">
                        <a href="{{ route('cs.member.checkin') }}" class="dropdown-item mt-2 mb-2 font-weight-bold">
                            <i class="fas fa-calendar-check fa-sm mr-2"></i> Check-In
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('cs.index') }}" class="dropdown-item mt-2 mb-2">
                            <i class="fas fa-tachometer-alt fa-sm mr-2"></i> Dashboard
                        </a>
                    </div>
                </li>
                <li class="nav-item menu-border"></li>
                <li class="nav-item menu-item">
                    <a class="nav-link text-dark" data-toggle="dropdown" href="#">
                        <span class="fas fa-users fa-sm mr-1"></span> Member
                    </a>

                    <div class="dropdown-menu dropdown-menu-lg">
                        <a href="{{ route('cs.member.registration.index') }}" class="dropdown-item mt-2 mb-2 font-weight-bold">
                            <i class="fas fa-user-plus fa-sm mr-2"></i> Tambah Member
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('cs.member.index') }}" class="dropdown-item mt-2 mb-2">
                            <i class="fas fa-users fa-sm mr-2"></i> Data Member
                        </a>
                    </div>
                </li>
                <li class="nav-item menu-border"></li>
                <li class="nav-item menu-item">
                    <a class="nav-link text-dark" data-toggle="dropdown">
                        <span class="fas fa-calendar-minus fa-sm mr-1"></span> Cuti Member
                    </a>

                    <div class="dropdown-menu dropdown-menu-lg">
                        <a href="{{ route('cs.cuti.index') }}" class="dropdown-item mt-2 mb-2 font-weight-bold">
                            <i class="fas fa-user-plus fa-sm mr-2"></i> Pengajuan Cuti Member
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('cs.cuti.index') }}" class="dropdown-item mt-2 mb-2">
                            <i class="fas fa-calendar-minus fa-sm mr-2"></i> Data Cuti Member
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
