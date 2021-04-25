<style>
    .dropdown-menu{
        left: inherit !important;
    }
    .dropdown-menu-right{
        right: inherit !important;
    }
</style>

<nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container-fluid">
        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <!-- Toggler -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <span class="nav-link font-weight-bold">{{ $title }}</span>
                </li>
            </ul>
        </div>


        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
            <li class="user-card nav-item">
                <a class="card-section-dark" data-toggle="dropdown" href="#">
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
