<x-layouts.guest>
    <div class="hero-wrap" id="home" style="background-image: url({{ asset('assets/images/cocok2.jpeg') }});"
        data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-center">
                <div class="col-md-6 ftco-animate d-flex align-items-end">
                    <div class="text w-100">
                        <h1 class="mb-4" style="color: white">Portal Akomodasi PTPN 1 Head Office</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <section class="ftco-section bg-light" id="form_pemesanan">
        <div class="container">
            <div class="row justify-content-center pb-5">
                <div class="col-md-8 heading-section text-center ftco-animate">
                    <span class="subheading">Layanan Pemesanan</span>
                    <h2>Ruang Rapat Dan Video Conference</h2>
                </div>
            </div>
            <div class="row no-gutters ftco-animate">
                <div class="col-md-6" style="background: white">
                    <div class="contact-wrap w-100 p-md-5 p-4">
                        <div class="text w-150">
                            <h1 style="color:black;">Layanan Pemesanan Ruangan Rapat dan Vicon</h1>
                            <p style="color:black;">Sebelum mengisi pastikan membaca informasi berikut:</p>
                            <ol>
                                <li style="color:black;">
                                    Layanan ini digunakan untuk pemesanan layanan ruang rapat dan video conference (baik
                                    berupa vicon yang menggunakan link eksternal maupun internal)
                                </li>
                                <li style="color:black;">
                                    Sebelum mengisi pastikan melihat terlebih dahulu jadwal yang
                                    ada di tabel agar tidak bentrok. Apabila terdapat jadwal yang bentrok dapat
                                    menghubungan Bagian Pengadaan & Umum
                                </li>
                                <li style="color:black;">
                                    Untuk vicon yang menggunakan link Zoom internal PTPN XII
                                    memungkinkan melakukan 4 vicon secara bersamaan.
                                </li>
                                <li style="color:black;">
                                    Dikarenakan keterbatasan jumlah laptop untuk vicon, apabila ketersediaan laptop
                                    sudah habis maka vicon dilakukan menggunakan laptop Bagian penyelenggaran rapat.
                                </li>
                            </ol>
                            <p style="color:black;">Disclaimer:</p>
                            <li style="color:black; font-size: 14px;">
                                Apabila terdapat jadwal yang berbenturan dengan agenda rapat Direktur atau SEVP, maka
                                akan diprioritaskan agenda Direktur atau SEVP. Permintaan ruang rapat dan vicon yang
                                sudah diajukan oleh Bagian akan dijadwalkan ulang dengan pemberitahuan terlebih dahulu.
                            </li>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 order-md-first d-flex align-items-stretch">
                    <div class="contact-wrap w-100 p-md-5 p-4">
                        <h3 class="mb-4">Form Layanan Pemesanan</h3>
                        <p class="label" style="text-align: center; font-size: 16px;">
                            Sebelum mengisi pastikan melihat
                            terlebih dahulu jadwal yang ada di tabel agar tidak bentrok. Apabila terdapat jadwal yang
                            bentrok dapat menghubungi Bagian Pengadaan & Umum
                        </p>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $key => $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form autocomplete="off" class="contactForm appointment" id="sendvicon_form" method="post"
                            action="{{ route('sendvicon.store') }}" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="privat" value="Tidak">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="bagian">
                                            Bagian
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="form-field">
                                            <div class="select-wrap">
                                                <div class="icon" style="right: 0px">
                                                    <span class="fa fa-chevron-down"></span>
                                                </div>
                                                <select class="form-control" id="kotakijobagian" name="bagian"
                                                    value="{{ old('bagian') ?? '' }}" required>
                                                    <option>Pilih Bagian</option>
                                                    @foreach ($bagians as $bagian)
                                                        <option value="{{ $bagian->id }}"
                                                            {{ old('bagian') == $bagian->id ? 'selected' : '' }}>
                                                            {{ $bagian->bagian }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('bagian')
                                                    <span class="text-danger">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="acara">
                                            Acara
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="acara" name="acara"
                                            placeholder="Isikan Acara" value="{{ old('acara') ?? '' }}" required>
                                        @error('acara')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="email">
                                            Dokumentasi Humas
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" id="dokumentasi" name="dokumentasi" required>
                                            <option>Pilihan</option>
                                            <option value='Ya' {{ old('dokumentasi') == 'Ya' ? 'selected' : '' }}>
                                                Ya
                                            </option>
                                            <option value='Tidak'
                                                {{ old('dokumentasi') == 'Tidak' ? 'selected' : '' }}>
                                                Tidak
                                            </option>
                                        </select>
                                        @error('dokumentasi')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="peserta">Peserta</label>
                                        <input type="text" class="form-control" placeholder="Isikan Peserta"
                                            id="peserta" name="peserta" value="{{ old('peserta') ?? '' }}">
                                        @error('peserta')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="tanggal">
                                            Tanggal
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="tanggal" name="tanggal"
                                            class="form-control daterange" placeholder="Isikan Tanggal"
                                            readOnly={true} value="{{ old('tanggal') ?? '' }}" required>
                                        @error('tanggal')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="waktu">
                                            Waktu dimulai
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="time" id="waktu1" name="waktu1"
                                            placeholder="Isikan Waktu Mulai" class="form-control"
                                            value="{{ old('waktu1') ?? '' }}" required>
                                        @error('waktu1')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="waktu2">
                                            Waktu akhir
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="time" id="waktu2" name="waktu2"
                                            placeholder="Isikan Waktu Akhir" class="form-control"
                                            value="{{ old('waktu2') ?? '' }}" required>
                                        @error('waktu2')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="ruangan">
                                            Tempat (jika ada peserta dari Kandir)
                                        </label>
                                        <select class="form-control" name="ruangan" id="ruangan"
                                            onchange="showhidejawaban()">
                                            <option value=''>Pilih Tempat</option>
                                            @foreach ($ruangans as $ruangan)
                                                <option value="{{ $ruangan->id }}"
                                                    {{ old('ruangan') == $ruangan->id ? 'selected' : '' }}>
                                                    {{ $ruangan->nama }}
                                                </option>
                                            @endforeach
                                            <option value='lain'>Tempat Lain</option>
                                        </select>
                                        @error('ruangan')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="vicon">
                                            Rapat Vicon
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" id="vicon" onchange="showhidejenis_link()"
                                            name="vicon" required>
                                            <option value=''>Pilihan</option>
                                            <option value='Ya' {{ old('vicon') == 'Ya' ? 'selected' : '' }}>Ya
                                            </option>
                                            <option value='Tidak' {{ old('vicon') == 'Tidak' ? 'selected' : '' }}>
                                                Tidak</option>
                                        </select>
                                        @error('vicon')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="banyak" style="display: none;">
                                <div class="form-group">
                                    <label class="label" for="ruangan2">Isikan lokasi rapat</label>
                                    <input type="text" class="form-control" id="ruangan2" name="ruangan2"
                                        placeholder="Masukkan lokasi rapat" value="{{ old('ruangan2') ?? '' }}">
                                    @error('ruangan2')
                                        <span class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12" id='link' style="display: none;">
                                <div class="form-group">
                                    <label class="label" for="jenis_link">
                                        Link
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="jenis_link" name="jenis_link">
                                        <option value=''>Pilihan</option>
                                        <option value='Internal'
                                            {{ old('jenis_link') == 'Internal' ? 'selected' : '' }}>Internal (Dari TI)
                                        </option>
                                        <option value='Eksternal'
                                            {{ old('jenis_link') == 'Eksternal' ? 'selected' : '' }}>Eksternal (Dari
                                            Luar)</option>
                                    </select>
                                    @error('jenis_link')
                                        <span class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="jumlahpeserta">
                                            Estimasi Jumlah Peserta di Kandir (jika ada)
                                        </label>
                                        <input type="text" class="form-control" placeholder="Isikan Estimasi"
                                            id="jumlahpeserta" name="jumlahpeserta"
                                            value="{{ old('jumlahpeserta') ?? '' }}">
                                        @error('jumlahpeserta')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label" for="sk">
                                            Upload Surat/Memo Undangan
                                        </label>
                                        <input type="file" class="form-control" id="sk" name="sk"
                                            accept=".pdf, .jpg">
                                        @error('sk')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="label" for="nopersonel">
                                            Personel yang dapat dihubungi(nama dan no handphone)
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="nopersonel" name="nopersonel"
                                            placeholder="Isikan Contact Person" value="{{ old('nopersonel') ?? '' }}"
                                            required>
                                        @error('nopersonel')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="label" for="keterangan">Keterangan</label>
                                        <input type="texrarea" class="form-control" id="keterangan"
                                            name="keterangan" placeholder="Contoh : Permintaan Teh dan Makan Siang"
                                            value="{{ old('keterangan') ?? '' }}">
                                        @error('keterangan')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="label" for="captcha">
                                            Captcha
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div style="display: flex;">
                                            <div id="captchaValue">
                                                {!! captcha_img('math') !!}
                                            </div>
                                            <div style="height: 100%;">
                                                <button type="button" class="btn-primary ml-2" id="refreshCaptcha">
                                                    <i class="fa fa-refresh"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" id="captcha" name="captcha"
                                            required>
                                        @error('captcha')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="label" for="passwordVerif">
                                            Generate Kode
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="texrarea" class="form-control" id="passwordVerif"
                                            name="passwordVerif" placeholder="Isikan Kode" required>
                                        @error('passwordVerif')
                                            <span class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <p>
                                <div class="form-group">
                                    <center>
                                        <!-- <div class="g-recaptcha" data-sitekey="6LcynMUZAAAAADq43L0KdGdPUpS-kThX6KtZBScK"></div> -->
                                    </center>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button id="button_sendvicon" type="submit"
                                        class="btn btn-primary">Submit</button>
                                    <div class="submitting"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    {{-- <section class="ftco-section" id="tabel_pemesanan">
        <div class="container">
            <div class="row justify-content-center pb-5">
                <div class="col-md-7 heading-section text-center ftco-animate">
                    <span class="subheading">Tabel Pemesanan</span>
                    <h3>Tabel Pemesanan Layanan Ruang Rapat Dan Video Conference</h3>
                </div>
            </div>
            <div class="row ftco-animate">
                <div class="col-md-12">
                    <table class="display responsive" style="width: 100%; float:center;" id="pemesanan-table">
                        <thead>
                            <tr>
                                <th style="width: 5%">
                                    <center>No</center>
                                </th>
                                <th style="width: 10%">
                                    <center>Bagian</center>
                                </th>
                                <th style="width: 25%">
                                    <center>Acara</center>
                                </th>
                                <th style="width: 13%">
                                    <center>Tanggal</center>
                                </th>
                                <th style="width: 15%">
                                    <center>Waktu</center>
                                </th>
                                <th style="width: 15%">
                                    <center>Tempat</center>
                                </th>
                                <th style="width: 7%">
                                    <center>Vicon</center>
                                </th>
                                <th style="width: 7%">
                                    <center>Status</center>
                                </th>
                                <th style="width: 35%">
                                    <center>Keterangan</center>
                                </th>
                                <th style="width: 10%">
                                    <center>Detail</center>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section> --}}

    {{-- <div id="detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Detail Ruang Rapat Dan Video Converence</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="surat" class="form-group">
                        <div class="form-group">
                            <label class="col-sm-13 control-label"></label>
                            <div class="col-sm-12">
                                <table width="100%">
                                    <tr>
                                        <td style="width: 45%">Bagian</td>
                                        <td style="width: 5%"> : </td>
                                        <td style="width: 50%" id="det_bagian"></td>
                                    </tr>
                                    <tr>
                                        <td>Acara</td>
                                        <td> : </td>
                                        <td id="det_acara"></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal</td>
                                        <td> : </td>
                                        <td id="det_tgl"></td>
                                    </tr>
                                    <tr>
                                        <td>Waktu</td>
                                        <td> : </td>
                                        <td id="det_waktu"></td>
                                    </tr>
                                    <tr>
                                        <td>Peserta</td>
                                        <td> : </td>
                                        <td id="det_peserta"></td>
                                    </tr>
                                    <tr>
                                        <td>Estimasi Jumlah Peserta di Kandir (jika ada)</td>
                                        <td> : </td>
                                        <td id="det_jumlahpeserta"></td>
                                    </tr>
                                    <tr>
                                        <td>Tempat (jika ada peserta dari Kandir)</td>
                                        <td> : </td>
                                        <td id="det_tempat"></td>
                                    </tr>
                                    <tr>
                                        <td>Bersifat Privat</td>
                                        <td> : </td>
                                        <td id="det_privat"></td>
                                    </tr>
                                    <tr>
                                        <td>Bersifat Vicon</td>
                                        <td> : </td>
                                        <td id="det_vicon"></td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Link</td>
                                        <td> : </td>
                                        <td id="det_jenislink"></td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Rapat</td>
                                        <td> : </td>
                                        <td id="det_jenisrapat"></td>
                                    </tr>
                                    <tr>
                                        <td>Agenda Direksi</td>
                                        <td> : </td>
                                        <td id="det_agendadireksi"></td>
                                    </tr>
                                    <tr>
                                        <td>Personel yang dapat dihubungi</td>
                                        <td> : </td>
                                        <td id="det_personil"></td>
                                    </tr>
                                    <tr>
                                        <td>Memo/Surat Undangan</td>
                                        <td> : </td>
                                        <td id="det_sk">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td> : </td>
                                        <td id="det_status"></td>
                                    </tr>
                                    <tr>
                                        <td>Petugas Rapat/Protokoler</td>
                                        <td> : </td>
                                        <td id="det_petugasruangrapat"></td>
                                    </tr>
                                    <tr>
                                        <td>Petugas Vicon</td>
                                        <td> : </td>
                                        <td id="det_petugasti"></td>
                                    </tr>
                                    <tr>
                                        <td>Link</td>
                                        <td> : </td>
                                        <td id="det_link"></td>
                                    </tr>
                                    <tr>
                                        <td>Password</td>
                                        <td> : </td>
                                        <td id="det_password"></td>
                                    </tr>
                                    <tr>
                                        <td>Keterangan</td>
                                        <td> : </td>
                                        <td id="det_keterangan"></td>
                                    </tr>
                                    <tr>
                                        <td>Penginput</td>
                                        <td> : </td>
                                        <td id="det_user"></td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default antoclose"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div id="loginModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="loginModal"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="loginModalLabel">Login Video Conference</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="surat" class="form-group">
                        <form class="form-horizontal calender" id="login_form">
                            @csrf
                            <div id="error-message" class="alert alert-danger" style="display: none;"></div>
                            <div class="form-group">
                                <label class="col-sm-13 control-label">Username : </label>
                                <input type="text" class="form-control" name="username">
                                <p id="error-message-username" class="text-danger" style="display: none;"></p>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-13 control-label">Password : </label>
                                <input type="password" class="form-control" name="password">
                                <p id="error-message-password" class="text-danger" style="display: none;"></p>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default antoclose"
                                    data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-submit">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="postForm" action="{{ route('sendvicon.cancel') }}" method="POST">
        @csrf
    </form>

    @push('js')
        <script type="text/javascript">
            function detail(id) {
                //Ajax Load data from ajax
                $.ajax({
                    url: "{{ route('vicon.detail', ':id') }}".replace(':id', id),
                    type: "GET",
                    dataType: "JSON",
                    success: function(response) {
                        var data = response.data;
                        var tanggal = data.tanggal;
                        var tgl_split = tanggal.split("-");
                        var tgl_tampil = tgl_split[2] + "-" + tgl_split[1] + "-" + tgl_split[0];

                        var waktu = data.waktu;
                        var waktu_split = waktu.split(":");
                        var waktu = waktu_split[0] + ":" + waktu_split[1];

                        var waktu2 = data.waktu2;
                        var waktu2_split = waktu2.split(":");
                        var waktu2 = waktu2_split[0] + ":" + waktu2_split[1];

                        var waktu_tampil = waktu + " - " + waktu2 + " WIB";

                        var id_ruangan = data.id_ruangan;
                        var tempat = "";
                        if (id_ruangan == null) {
                            tempat = data.ruangan_lain;
                        } else {
                            tempat = data.ruangan.nama;
                        }

                        var sk = "";
                        if (data.sk != null) {
                            var url = "{{ asset(':sk') }}".replace(':sk', data.sk);
                            sk = "<a href='" + url + "' target='_blank'>Open</a>"
                        }

                        var link = "";
                        if (data.link != null) {
                            link = data.link;
                        }

                        $('#det_bagian').html(data.bagian.bagian);
                        $('#det_acara').html(data.acara);
                        $('#det_tgl').html(tgl_tampil);
                        $('#det_waktu').html(waktu_tampil);
                        $('#det_jumlahpeserta').html(data.jumlahpeserta);
                        $('#det_tempat').html(tempat);
                        $('#det_peserta').html(data.peserta);
                        $('#det_privat').html(data.privat);
                        $('#det_vicon').html(data.vicon);
                        $('#det_jenislink').html(data.jenis_link);
                        $('#det_jenisrapat').html(data.jenisrapat);
                        $('#det_agendadireksi').html(data.agenda_direksi);
                        $('#det_personil').html(data.personil);
                        $('#det_sk').html(sk);
                        $('#det_status').html(data.status);
                        $('#det_petugasruangrapat').html(data.petugasruangrapat);
                        $('#det_petugasti').html(data.petugasti);
                        $('#det_link').html(link);
                        $('#det_pass').html(data.password);
                        $('#det_keterangan').html(data.keterangan);
                        $('#det_user').html(data.user);
                        $('#detail').modal('show');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error get data from ajax');
                    }
                });
            }

            $(document).ready(function() {
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = today.getFullYear();
                today = mm + '/' + dd + '/' + yyyy;

                var todays = new Date();
                var dd = String(todays.getDate()).padStart(2, '0');
                var mm = String(todays.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = todays.getFullYear();
                todays = yyyy + '-' + mm + '-' + dd;
                $('.daterange').daterangepicker({
                    format: 'YYYY-MM-DD',
                    minDate: today
                });

                $('#pemesanan-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('vicon.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                if (row.tanggal == todays) {
                                    return '<span style="color: green; font-weight: bold">' + row
                                        .DT_RowIndex +
                                        '</span>';
                                } else {
                                    return row.DT_RowIndex
                                }
                            }
                        },
                        {
                            data: 'bagian',
                            name: 'bagian',
                            render: function(data, type, row) {
                                if (row.tanggal == todays) {
                                    return '<span style="color: green; font-weight: bold">' + row
                                        .bagian +
                                        '</span>';
                                } else {
                                    return row.bagian
                                }
                            },
                        },
                        {
                            data: 'acara',
                            name: 'acara',
                            render: function(data, type, row) {
                                var kolom_acara = data.split(",");
                                var kode = kolom_acara[0];
                                var acara = kolom_acara[1];
                                if (row.tanggal == todays) {
                                    if (kode == "1") {
                                        return '<span style="color: red; font-weight: bold">' + acara +
                                            '</span>';
                                    } else {
                                        return '<span style="color: green; font-weight: bold">' +
                                            acara +
                                            '</span>';
                                    }
                                } else {
                                    if (kode == "1") {
                                        return '<span style="color: red; font-weight: bold">' + acara +
                                            '</span>';
                                    } else {
                                        return acara;
                                    }
                                }
                            },
                        },
                        {
                            data: 'tanggal',
                            name: 'tanggal',
                            render: function(data, type, row) {
                                if (row.tanggal == todays) {
                                    return '<span style="color: green; font-weight: bold">' + row
                                        .tanggal +
                                        '</span>';
                                } else {
                                    return row.tanggal
                                }
                            }
                        },
                        {
                            data: 'waktu',
                            name: 'waktu',
                            render: function(data, type, row) {
                                if (row.tanggal == todays) {
                                    return '<span style="color: green; font-weight: bold">' + row
                                        .waktu +
                                        '</span>';
                                } else {
                                    return row.waktu
                                }
                            },
                        },
                        {
                            data: 'ruangan',
                            name: 'ruangan',
                            render: function(data, type, row) {
                                var kolom_ruangan = data.split(",");
                                var kode = kolom_ruangan[0];
                                var ruang = kolom_ruangan[1];
                                if (row.tanggal == todays) {
                                    if (kode == "1") {
                                        return '<span style="color: red; font-weight: bold">' +
                                            ruang +
                                            '</span>';
                                    } else {
                                        return '<span style="color: green; font-weight: bold">' +
                                            ruang +
                                            '</span>';
                                    }
                                } else {
                                    if (kode == "1") {
                                        return '<span style="color: red; font-weight: bold">' +
                                            ruang +
                                            '</span>';
                                    } else {
                                        return ruang;
                                    }
                                }
                            }
                        },
                        {
                            data: 'vicon',
                            name: 'vicon',
                            render: function(data, type, row) {
                                if (row.tanggal == todays) {
                                    return '<span style="color: green; font-weight: bold">' + row
                                        .vicon +
                                        '</span>';
                                } else {
                                    return row.vicon
                                }
                            },
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data, type, row) {
                                var status = row.status ?? '';
                                if (row.tanggal == todays) {
                                    return '<span style="color: green; font-weight: bold">' +
                                        status +
                                        '</span>';
                                } else {
                                    return status;
                                }
                            },
                        },
                        {
                            data: 'keterangan',
                            name: 'keterangan',
                            render: function(data, type, row) {
                                if (row.tanggal == todays) {
                                    return '<span style="color: green; font-weight: bold">' + row
                                        .keterangan +
                                        '</span>';
                                } else {
                                    return row.keterangan
                                }
                            },
                        },
                        {
                            name: 'action',
                            data: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                });
            });

            function showhidejawaban() {
                var ruangan = document.getElementById("ruangan").value;
                if (ruangan == "lain") {
                    banyak.style.display = "block";
                } else {
                    banyak.style.display = "none";
                }
            };

            function showhidejenis_link() {
                var vicon = document.getElementById("vicon").value;
                if (vicon == "Ya") {
                    link.style.display = "block";
                    $("#jenis_link").prop('required', true);
                } else {
                    link.style.display = "none";
                    $("#jenis_link").prop('required', false);
                }
            };

            $("#home_menu").click(function() {
                $('html, body').animate({
                    scrollTop: $("#home").offset().top
                }, 500);
            });

            $("#form_pemesanan_menu").click(function() {
                $('html, body').animate({
                    scrollTop: $("#form_pemesanan").offset().top
                }, 500);
            });

            $("#tabel_pemesanan_menu").click(function() {
                $('html, body').animate({
                    scrollTop: $("#tabel_pemesanan").offset().top
                }, 500);
            });

            $(document).ready(function() {
                $("#refreshCaptcha").click(function() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('refresh.captcha') }}",
                        success: function(data) {
                            $("#captchaValue").html(data);
                        }
                    });
                });

                $('#login_form').on('submit', function(e) {
                    event.preventDefault(); // Mencegah pengiriman form secara default

                    // Hapus pesan error sebelumnya
                    $('#error-message').hide().html('');
                    $('#error-message-username').hide().html('');
                    $('#error-message-password').hide().html('');


                    $.ajax({
                        url: "{{ route('admin.login') }}",
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(data, status, xhr) {
                            if (data.success) {
                                window.location.href = "{{ route('admin.dashboard.index') }}";
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);

                            // Jika ada kesalahan dari server (401)
                            if (xhr.status === 401) {
                                var errors = xhr.responseJSON.errors.message;

                                // Tampilkan pesan error secara umum
                                $('#error-message').html(errors.join('<br>')).show();
                            }

                            // Jika ada kesalahan validasi, tampilkan di input masing-masing
                            if (xhr.status === 422) {
                                var validationErrors = xhr.responseJSON.errors;

                                if (validationErrors.username) {
                                    $('#error-message-username').html(validationErrors.username[0])
                                        .show();
                                }

                                if (validationErrors.password) {
                                    $('#error-message-password').html(validationErrors.password[0])
                                        .show();
                                }
                            }
                        }
                    })
                });

                @if (session()->has('success'))
                    swal({
                        title: "Success",
                        text: "{{ session('success') }}",
                        icon: "success",
                    })
                @endif

                @if (session()->has('gglindex_ruangan'))
                    swal({
                        title: "{!! session('gglindex_ruangan') !!}",
                        type: "info",
                        html: true,
                        showCancelButton: true,
                        confirmButtonText: "Cancel",
                        cancelButtonText: "Ok",
                        confirmButtonColor: "#ff0055",
                        cancelButtonColor: "#999999",
                        reverseButtons: true,
                        focusConfirm: false,
                        focusCancel: true,
                    }, function(isConfirm) {
                        if (isConfirm) {
                            $('#postForm').submit();
                        } else {
                            location.replace('{{ route('sendvicon.ceknama') }}');
                        }
                    })
                @endif

                @if (session()->has('gglindex_nama'))
                    swal({
                        title: "{{ session('gglindex_nama') }}",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonText: "Cancel",
                        cancelButtonText: "ok",
                        confirmButtonColor: "#ff0055",
                        cancelButtonColor: "#999999",
                        reverseButtons: true,
                        focusConfirm: false,
                        focusCancel: true
                    }, function(isConfirm) {
                        if (isConfirm) {
                            $('#postForm').submit();
                        }
                    })
                @endif
            })
        </script>
    @endpush
</x-layouts.guest>
