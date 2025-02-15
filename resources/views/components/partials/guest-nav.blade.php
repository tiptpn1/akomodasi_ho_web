<!-- <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">
        <a class="navbar-brand" href="/">ARHAN (APLIKASI RAPAT PERUSAHAAN) PTPN I</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="oi oi-menu"></span> Menu
        </button>

        <div class="collapse navbar-collapse" id="ftco-nav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a href="#" id="home_menu" class="nav-link">Home</a>
                </li>
                {{-- <li class="nav-item">
                    <a href="javascript:void(0)" id="form_pemesanan_menu" class="nav-link">
                        Form Pemesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0)" id="tabel_pemesanan_menu" class="nav-link">
                        Tabel Pemesanan
                    </a>
                </li> --}}
                <li class="nav-item">
                    @auth
                        <a class="nav-link" href="{{ route('admin.agenda.index') }}">
                            Dashboard
                        </a>
                    @else
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal" class="nav-link">
                            Login
                        </a>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav> -->
