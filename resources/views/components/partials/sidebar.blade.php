<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Manajemen Agenda</div>

                @if (Auth::user()->hakAkses->hak_akses_id == 6)
                    {{-- <a href="{{ route('admin.dashboard.kendaraan.show') }}" class="nav-link">
                    <div class="sb-nav-link-icon"></div>
                    Agenda Kendaraan
                </a> --}}
                @else
                    {{-- <a class="nav-link" href="{{ route('admin.dashboard.index') }}">
                        <div class="sb-nav-link-icon"></div>
                        Dashboard
                    </a> --}}
                    <a class="nav-link" href="{{ route('admin.agenda.index') }}">
                        <div class="sb-nav-link-icon"></div>
                        Dashboard Agenda
                    </a>
                    <a class="nav-link" href="{{ route('admin.vicon.index') }}">
                        <div class="sb-nav-link-icon"></div>
                        Jadwal Agenda
                    </a>
                    <a class="nav-link" href="{{ route('konsumsi.index') }}">
                        <div class="sb-nav-link-icon"></div>
                        Konsumsi
                    </a>
                    <a class="nav-link" href="{{ route('kaskecil.index') }}">
                        <div class="sb-nav-link-icon"></div>
                        Kas Kecil
                    </a>
                    @if (Auth::user()->hakAkses->hak_akses_id != 4)
                        {{-- <a class="nav-link" href="{{ route('admin.dashboard.kendaraan.show') }}">
                            <div class="sb-nav-link-icon"></div>
                            Agenda Kendaraan
                        </a> --}}
                    @endif

                    @if (Auth::user()->hakAkses->hak_akses_id == 1)
                        <div class="sb-sidenav-menu-heading">Manajemen Data</div>
                        <a class="nav-link" href="{{ route('admin.ruangan.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Ruangan
                        </a>
                        <a class="nav-link" href="{{ route('admin.masterlink.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Link
                        </a>
                        <a class="nav-link" href="{{ route('admin.bagian.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Bagian
                        </a>
                        <a class="nav-link" href="{{ route('admin.dashboard.master.jenis.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Jenis Rapat
                        </a>
                        <a class="nav-link" href="{{ route('admin.dashboard.master.kendaraan.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Kendaraan
                        </a>
                        <a class="nav-link" href="{{ route('admin.dashboard.master.pengguna.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Pengguna
                        </a>
                        <a class="nav-link" href="{{ route('admin.hak_akses.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Hak Akses
                        </a>
                    @endif
                @endif
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            {{ Auth::user()->master_user_nama }}
            <a href="{{ route('admin.logout') }}">
                Logout
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>
</div>
