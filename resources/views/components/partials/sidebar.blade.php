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
                @elseif (Auth::user()->hakAkses->hak_akses_id == 5)
                    <a class="nav-link" href="{{ route('bookingkamar.list_booking') }}">
                        <div class="sb-nav-link-icon"></div>
                        Daftar Booking Kamar Petugas Mess
                    </a>
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
                        Konsumsi Rapat
                    </a>
                    <a class="nav-link" href="{{ route('makansiang.index') }}">
                        <div class="sb-nav-link-icon"></div>
                        Pengajuan Makan Siang
                    </a>
                    <a class="nav-link" href="{{ route('pkendaraan.index') }}">
                        <div class="sb-nav-link-icon"></div>
                        Permintaan Kendaraan
                    </a>
                    <a class="nav-link" href="{{ route('bookingkamar.index') }}">
                        <div class="sb-nav-link-icon"></div>
                        Booking Kamar Mess
                    </a>
                    <a class="nav-link" href="{{ route('bookingkamar.list_booking') }}">
                        <div class="sb-nav-link-icon"></div>
                        Daftar Booking Mess
                    </a>
                    @if (Auth::user()->hakAkses->hak_akses_id == 2)
                    <a class="nav-link" href="{{ route('kaskecil.index') }}">
                        <div class="sb-nav-link-icon"></div>
                        Kas Kecil
                    </a>
                    @endif
                    @if (Auth::user()->hakAkses->hak_akses_id != 4)
                        {{-- <a class="nav-link" href="{{ route('admin.dashboard.kendaraan.show') }}">
                            <div class="sb-nav-link-icon"></div>
                            Agenda Kendaraan
                        </a> --}}
                    @endif

                    @if (Auth::user()->hakAkses->hak_akses_id == 2)
                        <div class="sb-sidenav-menu-heading">Manajemen Master Data</div>
                        <a class="nav-link" href="{{ route('masterkendaraan.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Master Kendaraan<br>(Permintaan Kendaraan)
                        </a>
                        <a class="nav-link" href="{{ route('masterdriver.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Master Driver<br>(Permintaan Kendaraan)
                        </a>
                        <a class="nav-link" href="{{ route('mess.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Master Mess
                        </a>
                        <a class="nav-link" href="{{ route('kamar.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Master Kamar
                        </a>
                        <!-- <a class="nav-link" href="{{ route('admin.dashboard.master.kendaraan.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Kendaraan
                        </a> -->
                    @endif
                    @if (Auth::user()->hakAkses->hak_akses_id == 1)
                        <a class="nav-link" href="{{ route('admin.ruangan.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Master Ruangan
                        </a>
                        <!-- <a class="nav-link" href="{{ route('admin.masterlink.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Link
                        </a> -->
                        <a class="nav-link" href="{{ route('admin.bagian.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Master Divisi/Bagian
                        </a>
                        <a class="nav-link" href="{{ route('admin.dashboard.master.jenis.index') }}">
                            <div class="sb-nav-link-icon"></div>
                            Master Jenis Rapat
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
