<aside class="main-sidebar sidebar-default-color elevation-1 sidebar-no-expand">
    <div class="sidenav-menu-section">
        <!-- LOGO -->
        <a @if($role == 1) href="{{ route('suadmin.index') }}" @elseif($role == 2) href="#"
           @elseif($role == 3) href="{{ route('cs.index') }}" @endif class="brand-link mt-2">
            <img src="{{ asset('/data/logo/logo.png') }}" alt="Fast Fitness" class="brand-image elevation-1">
            <h3 class="brand-text" style="color: #FFFFFF; padding-top: 0;">Fast Fitnes</h3>
        </a>

        <div class="sidebar vh-100">
            <!-- Sidebar Menu -->
            <nav class="mt-3 mb-4" style="overflow-x: hidden; overflow-y: auto;">
                <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu">
                    <li class="nav-item" style="border-bottom: 1px solid #505050;">
                        <a @if($role == 1) href="{{ route('suadmin.index') }}" @elseif($role == 2) href="#"
                           @elseif($role == 3) href="{{ route('cs.index') }}" @endif class="nav-link text-dark-default">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a @if($role == 1) href="{{ route('suadmin.member.checkin') }}" @elseif($role == 2) href="#"
                           @elseif($role == 3) href="{{ route('cs.member.checkin') }}" @endif class="nav-link text-dark-default">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Check-In</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a @if($role == 1) href="{{ route('suadmin.member.registration.index') }}" @elseif($role == 2) href="#"
                           @elseif($role == 3) href="{{ route('cs.member.index') }}" @endif class="nav-link text-dark-default">
                            <i class="nav-icon fas fa-plus"></i>
                            <p>Tambah Member</p>
                        </a>
                    </li>

                    <li class="text-dark-default nav-header">Management</li>
                    <li class="nav-item">
                        <a @if($role == 1) href="{{ route('suadmin.member.index') }}" @elseif($role == 2) href="#"
                           @elseif($role == 3) href="{{ route('cs.member.index') }}" @endif class="nav-link text-dark-default">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Data Member</p>
                        </a>
                    </li>

                    @if($role == 1 || $role == 2)
                        <li class="nav-item">
                            <a @if($role == 1) href="{{ route('suadmin.membership.index') }}" @endif
                                class="nav-link text-dark-default">
                                <i class="nav-icon fas fa-address-card"></i>
                                <p>Data Paket Member</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a @if($role == 1) href="{{ route('suadmin.pt.index') }}" @endif class="nav-link text-dark-default">
                                <i class="nav-icon fas fa-dumbbell"></i>
                                <p>Data Personal Trainer</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a @if($role == 1) href="{{ route('suadmin.marketing.index') }}" @endif class="nav-link text-dark-default">
                                <i class="nav-icon fas fa-coins"></i>
                                <p>Data Marketing</p>
                            </a>
                        </li>
                    @endif



                    <li class="text-dark-default nav-header">Member</li>
                    <li class="nav-item">
                        <button href="#" class="btn btn-link nav-link text-dark-default" disabled>
                            <i class="nav-icon fas fa-calendar-minus"></i>
                            <p>Data Cuti Member</p>
                        </button>
                    </li>

                    @if($role == 1 || $role == 2)
                        <li class="text-dark-default nav-header">Laporan</li>
                        <li class="nav-item">
                            <a href="{{route('report.index')}}" class="nav-link text-dark-default">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                <p>Laporan</p>
                            </a>
                        </li>
                    @endif

                    @if($role == 1)
                        <li class="text-dark-default nav-header">Aplikasi</li>
                        <li class="nav-item">
                            <button href="#" class="btn btn-link nav-link text-dark-default" disabled>
                                <i class="nav-icon fas fa-user-shield"></i>
                                <p>User Aplikasi</p>
                            </button>>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</aside>
