<x-layouts.app>

    <x-slot name="styles">
        <style type="text/css">
            .redboldfont {
                color: rgb(255, 0, 0);
                font-weight: bold;
            }

            .greenboldfont {
                color: rgb(0, 128, 0);
                font-weight: bold;
            }

            .select2-selection__choice {
                background-color: rgb(0, 195, 255) !important;
            }

            .select2-selection__choice__remove {
                color: white !important;
            }
        </style>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    </x-slot>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Jadwal Agenda</h3>
                @if (!in_array(Auth::user()->master_hak_akses_id, [5, 6]))
                    <button type="button" data-toggle="modal" data-target="#tambah" class="btn btn-success">Tambah</button>
                @endif
                <button type="submit" data-toggle="modal" data-target="#advance_filter" class="btn btn-warning">
                    Filter
                </button>
                <button type="button" class="btn btn-info"
                    onclick="window.location='{{ route('admin.vicon.resetfilter') }}'">
                    Reset Filter
                </button>

                <div class="card mb-4 mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display responsive" style="width: 100%; float:center;"
                                id="dataTables-agendavicon">
                                <thead>
                                    <tr>
                                        <th style="max-width: 5%">
                                            <center>No</center>
                                        </th>
                                        <th style="min-width: 25%">
                                            <center>Agenda</center>
                                        </th>
                                        <th style="min-width: 20%">
                                            <center>Hari, Tanggal</center>
                                        </th>
                                        <th style="min-width: 20%">
                                            <center>Waktu</center>
                                        </th>
                                        <th style="width: 20%">
                                            <center>Tempat</center>
                                        </th>
                                        <th style="width: 25%">
                                            <center>Divisi</center>
                                        </th>
                                        <th style="width: 10%">
                                            <center>Vicon</center>
                                        </th>
                                        <th style="max-width: 20%">
                                            <center>Keterangan</center>
                                        </th>
                                        <th style="max-width: 20%">
                                            <center>Status Approval</center>
                                        </th>
                                        <th style="min-width: 15%">
                                            <center>Aksi</center>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="advance_filter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Filter Data</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="surat" class="form-group container-fluid">
                        <form method="post" id='form_filtervicon'>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Tanggal Awal</b>
                                    <input type="text" name="tanggal_awal" id="tanggal_awal" class="form-control"
                                        readonly="true" value="">
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Tanggal Akhir</b>
                                    <input type="text" name="tanggal_akhir" id="tanggal_akhir" class="form-control"
                                        readonly="true" value="">
                                </div>
                            </div>
                            {{-- <div class="row"> --}}
                                <div class="form-group">
                                    <b>Agenda</b>
                                    <input type="text" class="form-control" name="acara" id="acara"
                                        placeholder="Pencarian Agenda">
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    <b>Agenda Direksi</b>
                                    <select name="agenda_direksi" id="agenda_direksi" class="form-control">
                                        <option selected disabled>Pilih Agenda Direksi</option>
                                        <option value="">Semua Agenda</option>
                                        <option value="Ya">Ya</option>
                                        <option value="Tidak">Tidak</option>
                                    </select>
                                </div> --}}
                            {{-- </div> --}}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Rapat Vicon</b>
                                    <select name="vicon" id="vicon" class="form-control">
                                        <option selected disabled>Pilih Rapat Vicon</option>
                                        <option value="">Semua Rapat</option>
                                        <option value="Ya">Ya</option>
                                        <option value="Tidak">Tidak</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Jenis Rapat</b>
                                    <select name="jenisrapat" id="jenisrapat" class="form-control">
                                        <option selected disabled>Pilih Jenis Rapat</option>
                                        <option value="">Semua Jenis Rapat</option>
                                        @foreach ($jenis_rapat as $rapat)
                                            <option value="{{ $rapat->id }}">{{ $rapat->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <b>PIC/Divisi</b><br />
                                <select id="bagian" name="bagian[]" class="custom-select" style="width: 100% !important;" multiple>
                                    <option value="">Semua PIC/Divisi</option>
                                    @foreach ($bagians as $bagian)
                                        <option value="{{ $bagian->id }}">{{ $bagian->bagian }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="vicon_filter_excel" class="btn btn-warning">
                                    Download Excel
                                </button>
                                <button type="button" id="vicon_filter_pdf" class="btn btn-info">
                                    Download PDF
                                </button>
                                <button type="submit" id="vicon_filter" class="btn btn-primary">Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="tambah" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Tambah Agenda</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="surat" class="form-group container-fluid">
                        <form id="form_tambah" class="form-horizontal calender" role="form"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Acara <span class="text-danger">*</span></b>
                                    <input type="text" class="form-control" name="acara"
                                        placeholder="Isikan Acara" required>
                                    <span class="text-danger">
                                        <strong id="acara_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Jenis Rapat <span class="text-danger">*</span></b>
                                    <select name="jenisrapat" class="form-control" required>
                                        <option value="">Pilih Jenis Rapat</option>
                                        @foreach ($jenis_rapat_with_status as $jenis)
                                            <option value='{{ $jenis->id }}'>{{ $jenis->nama }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        <strong id="jenisrapat_error"></strong>
                                    </span>
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    <b>Privat <span class="text-danger">*</span></b>
                                    <select name="privat" class="form-control" required>
                                        <option value=''>Pilihan</option>
                                        <option value='Ya'>Ya</option>
                                        <option value='Tidak'>Tidak</option>
                                    </select>
                                    <span class="text-danger">
                                        <strong id="privat_error"></strong>
                                    </span>
                                </div> --}}
                            </div>
                            {{-- <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Jenis Rapat <span class="text-danger">*</span></b>
                                    <select name="jenisrapat" class="form-control" required>
                                        <option value="">Pilih Jenis Rapat</option>
                                        @foreach ($jenis_rapat_with_status as $jenis)
                                            <option value='{{ $jenis->id }}'>{{ $jenis->nama }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        <strong id="jenisrapat_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Agenda Direksi <span class="text-danger">*</span></b>
                                    <select name="agenda_direksi" class="form-control" required>
                                        <option value=''>Pilih Agenda Direksi</option>
                                        <option value="Ya">Ya</option>
                                        <option value="Tidak">Tidak</option>
                                    </select>
                                    <span class="text-danger">
                                        <strong id="agenda_direksi_error"></strong>
                                    </span>
                                </div>
                            </div> --}}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Divisi <span class="text-danger">*</span></b>
                                    <select name="bagian" class="form-control" required>
                                        <option value=''>Pilih Divisi</option>
                                        @foreach ($bagians as $bagian)
                                            {{-- <option value='{{ $bagian->id }}'>{{ $bagian->bagian }}</option> --}}
                                            <option value='{{ $bagian->id }}'
                                                {{ $bagian->id == $bagian_id ? 'selected' : '' }}>{{ $bagian->bagian }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        <strong id="bagian_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Tanggal <span class="text-danger">*</span></b>
                                    <input type="text" class="form-control" id="daterange" name="tanggal"
                                        placeholder="Isikan Acara" readOnly={true} required>
                                    <span class="text-danger">
                                        <strong id="tanggal_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <div class="input-wrap">
                                        <b>Waktu Awal <span class="text-danger">*</span></b>
                                        <input type="time" id="waktu1" name="waktu"
                                            placeholder="Isikan Waktu Mulai" class="form-control" required>
                                    </div>
                                    <span class="text-danger">
                                        <strong id="waktu_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="input-wrap">
                                        <b>Waktu Akhir <span class="text-danger">*</span></b>
                                        <input type="time" id="waktu2" name="waktu2"
                                            placeholder="Isikan Waktu Akhir" class="form-control" required>
                                        <span class="text-danger">
                                            <strong id="waktu2_error"></strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Tempat <span class="text-danger">*</span></b>
                                    <select name="ruangan" id="ruangan2" onchange="showhidejawaban()"
                                        class="form-control" required>
                                        <option value=''>Pilih Tempat</option>
                                        @foreach ($ruangans as $ruangan)
                                            <option value='{{ $ruangan->id }}'>{{ $ruangan->nama }}</option>
                                        @endforeach
                                        <option value='lain'>Tempat Lain</option>
                                    </select>
                                    <span class="text-danger">
                                        <strong id="ruangan_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Rapat Vicon <span class="text-danger">*</span></b>
                                    <select name="vicon" class="form-control" id='vicon_add'
                                        onchange="showhidejenis_link_add()" required>
                                        <option value=''>Pilihan</option>
                                        <option value='Ya'>Ya</option>
                                        <option value='Tidak'>Tidak</option>
                                    </select>
                                    <span class="text-danger">
                                        <strong id="vicon_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12" id="banyak" style="display: none;">
                                    <b>Isikan lokasi rapat</b>
                                    <input type="text" class="form-control" name="ruangan2"
                                        placeholder="Isikan Nama Tempat">
                                    <span class="text-danger">
                                        <strong id="ruangan2_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" id='div_jenislink_add' style="display: none;">
                                    <div class="form-group">
                                        <label class="label" for="select_jenislink_add">
                                            Link
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" id="select_jenislink_add" name="jenis_link">
                                            <option value=''>Pilihan</option>
                                            <option value='Internal'>Internal</option>
                                            <option value='Eksternal'>Eksternal</option>
                                        </select>
                                        <span class="text-danger">
                                            <strong id="jenis_link_error"></strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Peserta</b>
                                    <input type="text" class="form-control" name="peserta"
                                        placeholder="Isikan Peserta">
                                    <span class="text-danger">
                                        <strong id="peserta_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Estimasi Jumlah Peserta</b>
                                    <input type="text" class="form-control" name="jumlahpeserta"
                                        placeholder="Isikan Estimasi Jumlah Peserta">
                                    <span class="text-danger">
                                        <strong id="jumlahpeserta_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Upload Surat/Memo Undangan</b>
                                    <input type="file" name="sk" class="form-control">
                                    <span class="text-danger">
                                        <strong id="sk_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Keterangan</b>
                                    <input type="textbox" name="keterangan" class="form-control"
                                        placeholder="Contoh : Permintaan Teh dan Makan Siang">
                                    <span class="text-danger">
                                        <strong id="keterangan_error"></strong>
                                    </span>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Link</b>
                                    <select name="link" class="form-control" id="link_add">
                                        <option value=''>Pilih Link</option>
                                        @foreach ($masterlink as $link)
                                            <option value='{{ $link->namalink }}'>{{ $link->namalink }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        <strong id="link_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Password</b>
                                    <input type="text" class="form-control" name="password"
                                        placeholder="Isikan Password">
                                    <span class="text-danger">
                                        <strong id="password_error"></strong>
                                    </span>
                                </div>
                            </div> --}}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Personel yang dapat dihubungi<br>(Nama dan Nomor WA)<span class="text-danger">*</span></b>
                                    <input type="text" class="form-control" name="nopersonel"
                                        placeholder="Isikan Contact Person" required>
                                    <span class="text-danger">
                                        <strong id="nopersonel_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Permintaan Konsumsi</b>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Makan:</b><br>
                                            <div class="form-check">
                                                <input type="hidden" name="makan[pagi]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="makan[pagi]" id="makan_pagi">
                                                <label class="form-check-label" for="makan_pagi">Pagi</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="hidden" name="makan[siang]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="makan[siang]" id="makan_siang">
                                                <label class="form-check-label" for="makan_siang">Siang</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="hidden" name="makan[malam]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="makan[malam]" id="makan_malam">
                                                <label class="form-check-label" for="makan_malam">Malam</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <b>Snack:</b><br>
                                            <div class="form-check">
                                                <input type="hidden" name="snack[pagi]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="snack[pagi]" id="snack_pagi">
                                                <label class="form-check-label" for="snack_pagi">Pagi</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="hidden" name="snack[siang]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="snack[siang]" id="snack_siang">
                                                <label class="form-check-label" for="snack_siang">Siang</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="hidden" name="snack[sore]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="snack[sore]" id="snack_sore">
                                                <label class="form-check-label" for="snack_sore">Sore</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-default antoclose"
                                    data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary antosubmit">Tambahkan</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Detail Jadwal Agenda</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="surat" class="form-group">
                        <div class="form-group">
                            <label class="col-sm-13 control-label"></label>
                            <div class="col-sm-12">
                                <table width="100%">
                                    <tr>
                                        <td style="width: 45%">Divisi</td>
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
                                        <td>Estimasi Jumlah Peserta</td>
                                        <td> : </td>
                                        <td id="det_jumlahpeserta"></td>
                                    </tr>
                                    <tr>
                                        <td>Tempat</td>
                                        <td> : </td>
                                        <td id="det_tempat"></td>
                                    </tr>
                                    {{-- <tr>
                                        <td>Bersifat Privat</td>
                                        <td> : </td>
                                        <td id="det_privat"></td>
                                    </tr> --}}
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
                                    {{-- <tr>
                                        <td>Agenda Direksi</td>
                                        <td> : </td>
                                        <td id="det_agendadireksi"></td>
                                    </tr> --}}
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
                                    {{-- <tr>
                                        <td>Link</td>
                                        <td> : </td>
                                        <td id="det_link"></td>
                                    </tr>
                                    <tr>
                                        <td>Password</td>
                                        <td> : </td>
                                        <td id="det_password"></td>
                                    </tr> --}}
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
    </div>

    <div id="invitation" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Undangan Jadwal Agenda</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="surat" class="form-group">
                        <form class="form-horizontal calender" role="form" action="login" method="post">
                            <div class="form-group">
                                <label class="col-sm-13 control-label"></label>
                                <div class="col-sm-12">
                                    <table width="100%">
                                        <tr>
                                            <td style="width: 40%">Divisi</td>
                                            <td style="width: 5%"> : </td>
                                            <td style="width: 50%" id="invit_bagian"></td>
                                        </tr>
                                        <tr>
                                            <td>Acara</td>
                                            <td> : </td>
                                            <td id="invit_acara"></td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td> : </td>
                                            <td id="invit_tgl"></td>
                                        </tr>
                                        <tr>
                                            <td>Waktu </td>
                                            <td> : </td>
                                            <td id="invit_waktu"></td>
                                        </tr>
                                        <tr>
                                            <td>Link</td>
                                            <td> : </td>
                                            <td id="invit_link"></td>
                                        </tr>
                                        <tr>
                                            <td>Password</td>
                                            <td> : </td>
                                            <td id="invit_pass"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-s"
                                    data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="update" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Update Jadwal Agenda</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="form-group container-fluid">
                        <div id="error_update" class="alert alert-danger" style="display: none;">
                            <ul id="error_list_update">
                                {{-- message error here --}}
                            </ul>
                        </div>
                        <form id="form_update" class="form-horizontal calender" role="form"
                            enctype="multipart/form-data">
                            <input type="hidden" name="id" id="update_id">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Acara <span class="text-danger">*</span></b>
                                    <input type="text" class="form-control" name="acara" id="update_acara"
                                        placeholder="Isikan Acara" required>
                                    <span class="text-danger">
                                        <strong id="ubah_acara_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Jenis Rapat <span class="text-danger">*</span></b>
                                    <select name="jenisrapat" id="update_jenisrapat" class="form-control" required>
                                        <option value="">Pilih Jenis Rapat</option>
                                        @foreach ($jenis_rapat_with_status as $jenis)
                                            <option value='{{ $jenis->id }}'>{{ $jenis->nama }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        <strong id="ubah_jenisrapat_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Divisi <span class="text-danger">*</span></b>
                                    <select name="bagian" id="update_bagian" class="form-control" required>
                                        <option value="">Pilih Divisi</option>
                                        @foreach ($bagians as $bagian)
                                            <option value='{{ $bagian->id }}'>{{ $bagian->bagian }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        <strong id="ubah_bagian_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Tanggal <span class="text-danger">*</span></b>
                                    <input type="text" class="form-control" id="update_tanggal" name="tanggal"
                                        placeholder="Isikan Tanggal" readOnly={true} required>
                                    <span class="text-danger">
                                        <strong id="ubah_tanggal_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <div class="input-wrap">
                                        <b>Waktu Awal <span class="text-danger">*</span></b>
                                        <input type="time" id="update_waktu" class="form-control"
                                            placeholder="Isikan Waktu" name="waktu">
                                        <span class="text-danger">
                                            <strong id="ubah_waktu_error"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="input-wrap">
                                        <b>Waktu Akhir <span class="text-danger">*</span></b>
                                        <input type="time" id="update_waktu2" class="form-control"
                                            placeholder="Isikan Waktu" name="waktu2">
                                        <span class="text-danger">
                                            <strong id="ubah_waktu2_error"></strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Tempat <span class="text-danger">*</span></b>
                                    <select name="ruangan" id="update_ruangan" onchange="showhidejawabanedit()"
                                        class="form-control" required>
                                        <option value=''>Pilih Tempat</option>
                                        @foreach ($ruangans as $ruangan)
                                            <option value='{{ $ruangan->id }}'>{{ $ruangan->nama }}</option>
                                        @endforeach
                                        <option value='lain'>Tempat Lain</option>
                                    </select>
                                    <span class="text-danger">
                                        <strong id="ubah_ruangan_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Rapat Vicon <span class="text-danger">*</span></b>
                                    <select name="vicon" class="form-control" id='update_vicon'
                                        onchange='showhidejenis_link_edit()' required>
                                        <option value=''>Pilihan</option>
                                        <option value='Ya'>Ya</option>
                                        <option value='Tidak'>Tidak</option>
                                    </select>
                                    <span class="text-danger">
                                        <strong id="ubah_vicon_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div id="banyakedit" style="display:none;" class="form-group col-md-12">
                                    <b>Isikan lokasi rapat</b>
                                    <input type="text" class="form-control" name="ruangan2" id="ruangan_lain"
                                        placeholder="Masukkan lokasi rapat">
                                    <span class="text-danger">
                                        <strong id="ubah_ruangan2_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div id='div_jenislink_edit' style="display:none;" class="form-group col-md-12">
                                    <label class="label" for="select_jenislink_edit">Link*</label>
                                    <select class="form-control" id="select_jenislink_edit" name="jenis_link">
                                        <option value=''>Pilihan</option>
                                        <option value='Internal'>Internal</option>
                                        <option value='Eksternal'>Eksternal</option>
                                    </select>
                                    <span class="text-danger">
                                        <strong id="ubah_jenis_link_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Peserta</b>
                                    <input type="text" class="form-control" name="peserta" id="update_peserta"
                                        placeholder="Isikan Peserta">
                                    <span class="text-danger">
                                        <strong id="ubah_peserta_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Estimasi Jumlah Peserta</b>
                                    <input type="text" class="form-control" name="jumlahpeserta"
                                        id="update_jumlahpeserta" placeholder="Isikan Estimasi Jumlah Peserta">
                                    <span class="text-danger">
                                        <strong id="ubah_jumlahpeserta_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Upload Surat/Memo Undangan</b>
                                    <input type="file" name="sk" class="form-control" id="update_sk">
                                    <label id="label_sk"></label>
                                    <span class="text-danger">
                                        <strong id="ubah_sk_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Keterangan</b>
                                    <input type="text" class="form-control" name="keterangan"
                                        id="update_keterangan" placeholder="Contoh : Permintaan Teh dan Makan Siang">
                                    <span class="text-danger">
                                        <strong id="ubah_keterangan_error"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <b>Personel yang dapat dihubungi<br>(Nama dan Nomor WA) <span class="text-danger">*</span></b>
                                    <input type="text" class="form-control" name="nopersonel"
                                        id="update_personil" placeholder="Isikan Contact Person" required>
                                    <span class="text-danger">
                                        <strong id="ubah_nopersonel_error"></strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <b>Permintaan Konsumsi</b>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Makan:</b><br>
                                            <div class="form-check">
                                                <input type="hidden" name="makan[pagi]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="makan[pagi]" id="update_makan_pagi">
                                                <label class="form-check-label" for="update_makan_pagi">Pagi</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="hidden" name="makan[siang]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="makan[siang]" id="update_makan_siang">
                                                <label class="form-check-label" for="update_makan_siang">Siang</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="hidden" name="makan[malam]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="makan[malam]" id="update_makan_malam">
                                                <label class="form-check-label" for="update_makan_malam">Malam</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <b>Snack:</b><br>
                                            <div class="form-check">
                                                <input type="hidden" name="snack[pagi]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="snack[pagi]" id="update_snack_pagi">
                                                <label class="form-check-label" for="update_snack_pagi">Pagi</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="hidden" name="snack[siang]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="snack[siang]" id="update_snack_siang">
                                                <label class="form-check-label" for="update_snack_siang">Siang</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="hidden" name="snack[sore]" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    name="snack[sore]" id="update_snack_sore">
                                                <label class="form-check-label" for="update_snack_sore">Sore</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default antoclose"
                                    data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary antosubmit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (in_array(auth()->user()->master_hak_akses_id, [2, 4]))
        <div id="approve" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Approve Jadwal Agenda</h4>
                        <button type="button" class="close" id="xButtonApprove" data-dismiss="modal"
                            aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div id="approveText"></div>
                        <div class="form-group container-fluid">
                            <div id="error_update" class="alert alert-danger" style="display: none;">
                                <ul id="error_list_update">
                                    {{-- message error here --}}
                                </ul>
                            </div>
                            <form id="form_approve" class="form-horizontal calender" role="form"
                                enctype="multipart/form-data">
                                <input type="hidden" name="id" id="approve_id">
                                @csrf
                                <div class="modal-footer">
                                    <button type="button" id="closeButtonApprove" class="btn btn-default antoclose"
                                        data-dismiss="modal">Close</button>
                                    <button type="submit" id="submitButtonApprove"
                                        class="btn btn-primary antosubmit">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form id="postForm" action="{{ route('admin.vicon.cancel') }}" method="POST">
        @csrf
        <input type="hidden" id="dataSession" name="dataSession" value="">
    </form>

    <form id="postCheckNama" action="{{ route('admin.vicon.checknama') }}" method="POST">
        @csrf
        <input type="hidden" id="dataSession" name="dataSession" value="">
    </form>

    @push('js')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

        <script>
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            today = dd + '-' + mm + '-' + yyyy;

            var todays = new Date();
            var dd = String(todays.getDate()).padStart(2, '0');
            var mm = String(todays.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = todays.getFullYear();
            todays = yyyy + '-' + mm + '-' + dd;

            $(document).ready(function() {
                $('#tanggal_awal').val(null);
                $('#tanggal_akhir').val(null);
                $('#tanggal_awal.form-control').datepicker({
                    dateFormat: "dd-mm-yy",
                    showButtonPanel: true,
                    closeText: 'Clear',
                    onClose: function(dateText, inst) {
                        if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
                            document.getElementById(this.id).value = '';
                        }
                    }
                });
                $('#tanggal_akhir.form-control').datepicker({
                    dateFormat: "dd-mm-yy",
                    showButtonPanel: true,
                    closeText: 'Clear',
                    onClose: function(dateText, inst) {
                        if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
                            document.getElementById(this.id).value = '';
                        }
                    }
                });
                $('#daterange').daterangepicker({
                    format: 'YYYY-MM-DD',
                    minDate: todays,
                });

                $('#update_tanggal').datepicker({
                    dateFormat: 'dd/mm/yy',
                    minDate: today
                });
            })

            function showhidejawaban() {
                var ruangan = document.getElementById("ruangan2").value;
                if (ruangan == "lain") {
                    banyak.style.display = "block";
                } else {
                    banyak.style.display = "none";
                }
            }

            function showhidejawabanedit() {
                var update_ruangan = document.getElementById("update_ruangan").value;
                if (update_ruangan == "lain") {
                    banyakedit.style.display = "block";
                } else {
                    banyakedit.style.display = "none";
                }
            }

            function showhidejenis_link_add() {
                var vicon_add = document.getElementById("vicon_add").value;
                if (vicon_add == "Ya") {
                    div_jenislink_add.style.display = "block";
                    $("#select_jenislink_add").prop('required', true);
                } else {
                    div_jenislink_add.style.display = "none";
                    $("#select_jenislink_add").prop('required', false);
                }
            }

            function showhidejenis_link_edit() {
                var update_vicon = document.getElementById("update_vicon").value;
                if (update_vicon == "Ya") {
                    div_jenislink_edit.style.display = "block";
                    $("#select_jenislink_edit").prop('required', true);
                } else {
                    div_jenislink_edit.style.display = "none";
                    $("#select_jenislink_edit").prop('required', false);
                }
            };

            function detail(id) {
                //Ajax Load data from ajax
                $.ajax({
                    url: "{{ route('admin.vicon.show', ':id') }}".replace(':id', id),
                    type: "GET",
                    dataType: "JSON",
                    success: function(response) {
                        var data = response.data

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
                            var url = "{{ asset('test') }}";
                            sk = "<a href='" + url.replace('test', data.sk) + "' target='_blank'>Open</a>"
                        }

                        var link = "";
                        if (data.link != null) {
                            var link = data.master_link.link;
                        }

                        $('#det_bagian').html(data.bagian != null ? data.bagian.bagian : '');
                        $('#det_acara').html(data.acara);
                        $('#det_tgl').html(tgl_tampil);
                        $('#det_waktu').html(waktu_tampil);
                        $('#det_peserta').html(data.peserta);
                        $('#det_jumlahpeserta').html(data.jumlahpeserta);
                        $('#det_tempat').html(tempat);
                        //$('#det_privat').html(data.privat);
                        $('#det_vicon').html(data.vicon);
                        $('#det_jenislink').html(data.jenis_link);
                        $('#det_jenisrapat').html(data.jenisrapat.nama);
                        //$('#det_agendadireksi').html(data.agenda_direksi);
                        $('#det_personil').html(data.personil);
                        $('#det_sk').html(sk);
                        $('#det_status').html(data.status);
                        // $('#det_link').html(link);
                        // $('#det_pass').html(data.password);
                        $('#det_keterangan').html(data.keterangan);
                        $('#det_user').html(data.user);
                        $('#detail').modal('show');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error get data from ajax');
                    }
                });
            }

            function absensi(id) {
                window.location = "{{ route('admin.absensi.rekap', ':id') }}".replace(':id', id);
            }

            function invitation(id) {
                //Ajax Load data from ajax
                $.ajax({
                    url: "{{ route('admin.vicon.show', ':id') }}".replace(':id', id),
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

                        var link = "";
                        if (data.link != null) {
                            link = data.master_link.link;
                        }

                        $('#invit_bagian').html(data.bagian != null ? data.bagian.bagian : '');
                        $('#invit_acara').html(data.acara);
                        $('#invit_tgl').html(tgl_tampil);
                        $('#invit_waktu').html(waktu_tampil);
                        $('#invit_link').html(link);
                        $('#invit_pass').html(data.password);
                        $('#invitation').modal('show');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error get data from ajax');
                    }
                });
            }

            function update(id) {
                //Ajax Load data from ajax
                $.ajax({
                    url: "{{ route('admin.vicon.show', ':id') }}".replace(':id', id),
                    type: "GET",
                    dataType: "JSON",
                    success: function(response) {
                        var data = response.data;
                        console.log(data);
                        var tanggal = data.tanggal;
                        var tgl_split = tanggal.split("-");
                        var tgl_tampil = tgl_split[2] + "/" + tgl_split[1] + "/" + tgl_split[0];

                        var waktu = data.waktu;
                        var waktu_split = waktu.split(":");
                        var waktu = waktu_split[0] + ":" + waktu_split[1];

                        var waktu2 = data.waktu2;
                        var waktu2_split = waktu2.split(":");
                        var waktu2 = waktu2_split[0] + ":" + waktu2_split[1];

                        var id_ruangan = data.id_ruangan;
                        if (id_ruangan == null) {
                            $('#update_ruangan').val('lain');
                            banyakedit.style.display = "block";
                            $('#ruangan_lain').val(data.ruangan_lain);
                        } else {
                            $('#update_ruangan').val(data.id_ruangan);
                            banyakedit.style.display = "none";
                        }

                        var vicon = data.vicon;
                        if (vicon == "Ya") {
                            div_jenislink_edit.style.display = "block";
                            $("#select_jenislink_edit").val(data.jenis_link);
                            $("#select_jenislink_edit").prop('required', true);
                        } else {
                            div_jenislink_edit.style.display = "none";
                            $("#select_jenislink_edit").prop('required', false);
                        }
                        var konsumsi = data.konsumsi; // Ambil data konsumsi dari response

                        if (konsumsi) {
                            // Jika konsumsi sudah ada, update checkbox status sesuai data
                            console.log("Konsumsi ditemukan:", konsumsi);

                            $('#update_makan_pagi').prop('checked', konsumsi.m_pagi == 1);
                            $('#update_makan_siang').prop('checked', konsumsi.m_siang == 1);
                            $('#update_makan_malam').prop('checked', konsumsi.m_malam == 1);

                            $('#update_snack_pagi').prop('checked', konsumsi.s_pagi == 1);
                            $('#update_snack_siang').prop('checked', konsumsi.s_siang == 1);
                            $('#update_snack_sore').prop('checked', konsumsi.s_sore == 1);
                        } else {
                            // Jika konsumsi tidak ada (first create), kosongkan semua checkbox
                            console.log("Konsumsi tidak ditemukan, ini adalah first create");

                            $('#update_makan_pagi').prop('checked', false);
                            $('#update_makan_siang').prop('checked', false);
                            $('#update_makan_malam').prop('checked', false);

                            $('#update_snack_pagi').prop('checked', false);
                            $('#update_snack_siang').prop('checked', false);
                            $('#update_snack_sore').prop('checked', false);
                        }

                        $('#update_id').val(data.id);
                        $('#update_bagian').val(data.bagian_id);
                        $('#update_acara').val(data.acara);
                        $('#update_tanggal').val(tgl_tampil);
                        $('#update_waktu').val(waktu);
                        $('#update_waktu2').val(waktu2);
                        $('#update_peserta').val(data.peserta);
                        $('#update_jumlahpeserta').val(data.jumlahpeserta);
                        //$('#update_privat').val(data.privat);
                        $('#update_vicon').val(data.vicon);
                        $('#update_jenisrapat').val(data.jenisrapat_id);
                        $('#update_agendadireksi').val(data.agenda_direksi);
                        $('#update_personil').val(data.personil);
                        $('#label_sk').html(data.sk != null ? data.sk.split('/').reverse()[0] : '');
                        $('#update_status').val(data.status);
                        $('#update_link').val(data.link);
                        $('#update_pass').val(data.password);
                        $('#update_keterangan').val(data.keterangan);
                        $('#update_user').val(data.user);
                        $('#update').modal('show');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error get data from ajax');
                    }
                });
            }

            function hapus(id) {
                event.preventDefault();
                if (confirm('Yakin untuk menghapus data?')) {
                    // ajax delete data to database
                    $.ajax({
                        url: "{{ route('admin.vicon.destroy', ':id') }}".replace(':id', id),
                        type: "POST",
                        data: {
                            '_token': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                fetchData();
                                swal({
                                    title: 'Success!',
                                    text: response.message,
                                    type: 'success',
                                    timer: 1500
                                })
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert('Error deleting data');
                        }
                    });
                }
            }

            function fetchData() {
                if ($.fn.DataTable.isDataTable('#dataTables-agendavicon')) {
                    $('#dataTables-agendavicon').DataTable().destroy();
                }
                $('#dataTables-agendavicon tbody').empty();

                $('#dataTables-agendavicon').DataTable({
                    "responsive": true,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('admin.vicon.data') }}",
                        "type": "POST",
                        "dataType": 'json',
                        "data": function(data) {
                            data._token = "{{ csrf_token() }}";
                            data.tanggal_awal = $('#tanggal_awal').val();
                            data.tanggal_akhir = $('#tanggal_akhir').val();
                            data.acara = $('#acara').val();
                            data.agenda_direksi = $('#agenda_direksi').val();
                            data.bagian = $('#bagian').val();
                            data.vicon = $('#vicon').val();
                            data.jenisrapat = $('#jenisrapat').val();
                        },
                        dataFilter: function(reps) {
                            return reps;
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    },
                    "language": {
                        "infoFiltered": ""
                    },
                    "lengthChange": false,
                    "columnDefs": [{
                        "targets": [0, 1, 2, 3, 4, 5, 6, 7, -1],
                        "orderable": false
                    }],

                    "rowCallback": function(row, data, index) {
                        var hari_tanggal = data[2].split(", ");
                        var tanggal = hari_tanggal[1];


                        if (tanggal == today) {
                            $(row).find('td').css({
                                'color': 'green',
                                'font-weight': 'bold'
                            });
                        }

                        var kolom_ruangan = data[4].split(",");
                        var kode_ruangan = kolom_ruangan[0];
                        var ruangan = kolom_ruangan[1];

                        $(row).find('td:eq(4)').html(ruangan);

                        var kolom_acara = data[1].split(",");
                        var kode_acara = kolom_acara[0];
                        var acara = kolom_acara[1];

                        $(row).find('td:eq(1)').html(acara);

                        if (kode_ruangan == "1") {
                            $(row).find('td:eq(4)').css({
                                'color': 'red',
                                'font-weight': 'bold'
                            });
                        }
                        if (kode_acara == "1") {
                            $(row).find('td:eq(1)').css({
                                'color': 'red',
                                'font-weight': 'bold'
                            });
                        }
                    },
                    // validasi warna untuk mode responsive
                    "responsive": {
                        "details": {
                            renderer: function(api, rowIdx, columns) {
                                var data = $.map(columns, function(col, i) {
                                    var print = '<td>' + col.data + '</td>';

                                    if (col.columnIndex == 2) {
                                        var hari_tanggal = col.data.split(", ");
                                        var tanggal = hari_tanggal[1];
                                        if (tanggal == today) {
                                            print = '<td class="greenboldfont">' + col.data + '</td>';
                                        }
                                    }

                                    if (col.columnIndex == 4) {
                                        var kolom_ruangan = col.data.split(",");
                                        var kode_ruangan = kolom_ruangan[0];
                                        var ruangan = kolom_ruangan[1];
                                        var ruangan_tampil = ruangan;

                                        if (kode_ruangan == '1') {
                                            print = '<td class="redboldfont">' + ruangan_tampil + '</td>';
                                        } else {
                                            print = '<td>' + ruangan_tampil + '</td>';
                                        }
                                    }

                                    if (col.columnIndex == 1) {
                                        var kolom_acara = col.data.split(",");
                                        var kode_acara = kolom_acara[0];
                                        var acara = kolom_acara[1];

                                        if (kode_acara == '1') {
                                            print = '<td class="redboldfont">' + acara + '</td>';
                                        } else {
                                            print = '<td>' + acara + '</td>';
                                        }
                                    }

                                    // console.log(col.columnIndex+" "+kolom_ruangan);

                                    return col.hidden ?
                                        '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col
                                        .columnIndex + '">' +
                                        '<td style="font-weight:bold;">' + col.title + '</td> ' +
                                        print +
                                        '</tr>' :
                                        '';
                                }).join('');

                                return data ?
                                    $('<table/>').append(data) :
                                    false;
                            }
                        }
                    },

                });
            }

            @if (in_array(auth()->user()->master_hak_akses_id, [2, 4]))
                function approve(id, acara) {
                    $('#approve_id').val(id);
                    $('#approveText').html(`<h6>Are you sure to approve ${acara}?</h6>`)
                    $('#approve').modal('show');
                }
            @endif

            $(document).ready(function() {
                fetchData();

                @if (in_array(auth()->user()->master_hak_akses_id, [2, 4]))
                    $('#form_approve').on('submit', function(e) {
                        e.preventDefault();

                        $('#xButtonApprove').prop('disabled', true);
                        $('#closeButtonApprove').css('display', 'none');
                        $('#closeButtonApprove').prop('disabled', true);
                        $('#submitButtonApprove').html('<i class="fas fa-spin fa-spinner"></i> Loading..');
                        $('#submitButtonApprove').prop('disabled', true);

                        formData = $(this).serialize();

                        $.ajax({
                            url: '{{ route('admin.vicon.approve') }}',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            data: formData,
                            success: function(response) {
                                fetchData();

                                swal({
                                    title: 'Success!',
                                    text: response.message,
                                    type: 'success',
                                    timer: 1500
                                });

                                $('#xButtonApprove').prop('disabled', false);
                                $('#closeButtonApprove').css('display', 'inline');
                                $('#closeButtonApprove').prop('disabled', false);
                                $('#submitButtonApprove').html('Submit');
                                $('#submitButtonApprove').prop('disabled', false);

                                $('#approve').modal('hide');
                            },
                            error: function(xhr, status, error) {
                                $('#xButtonApprove').prop('disabled', false);
                                $('#closeButtonApprove').css('display', 'inline');
                                $('#closeButtonApprove').prop('disabled', false);
                                $('#submitButtonApprove').html('Submit');
                                $('#submitButtonApprove').prop('disabled', false);

                                swal({
                                    title: 'Failed!',
                                    text: xhr.responseJSON.message,
                                    type: 'error',
                                    timer: 1500
                                })
                            }
                        })
                    });
                @endif

                $('#form_tambah').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "{{ route('admin.vicon.store') }}",
                        type: "POST",
                        data: formData,
                        processData: false, // Agar jQuery tidak mengubah data
                        contentType: false, // Agar jQuery tidak mengubah content type
                        success: function(response) {
                            if (response.success) {
                                $(e.target).trigger('reset');
                                $('.modal-backdrop').remove();
                                $('#tambah').modal('hide');
                                $('.text-danger strong').text('');
                                fetchData();
                                if (response.flashMessages) {
                                    if (response.flashMessages.success) {
                                        swal({
                                            icon: 'success',
                                            title: 'Success',
                                            text: response.flashMessages.success,
                                        });
                                    }
                                    if (response.flashMessages.ggl_ruangan) {
                                        swal({
                                            title: response.flashMessages.ggl_ruangan,
                                            type: "info",
                                            html: true,
                                            // showCancelButton: true,
                                            confirmButtonText: "Close",
                                            // cancelButtonText: "Ok",
                                            confirmButtonColor: "#ff0055",
                                            // cancelButtonColor: "#999999",
                                            // reverseButtons: true,
                                            focusConfirm: false,
                                            // focusCancel: true,
                                            closeOnConfirm: false,
                                            // closeOnCancel: false
                                        }, function(isConfirm) {
                                            if (isConfirm) {
                                                let sessionData = JSON.stringify(response
                                                    .data);
                                                $('#postForm').val(sessionData);
                                                $('#postForm').submit();
                                            } else {
                                                let sessionData = JSON.stringify(response
                                                    .data);
                                                $('#postCheckNama').val(sessionData);
                                                $('#postCheckNama').submit();
                                            }
                                        });
                                    }
                                    if (response.flashMessages.ggl_nama) {
                                        swal({
                                            title: response.flashMessages.ggl_nama,
                                            type: "info",
                                            // showCancelButton: true,
                                            confirmButtonText: "Close",
                                            // cancelButtonText: "ok",
                                            confirmButtonColor: "#ff0055",
                                            // cancelButtonColor: "#999999",
                                            // reverseButtons: true,
                                            focusConfirm: false,
                                            // focusCancel: true
                                        });
                                    }
                                }
                            } else {
                                Swal({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr) {
                            var errors = xhr.responseJSON;
                            if ($.isEmptyObject(errors) == false) {
                                $.each(errors.errors, function(key, value) {
                                    $('#' + key + '_error').html(value);
                                });
                            }
                        }
                    });
                })

                $('#form_update').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: "{{ route('admin.vicon.update') }}",
                        type: "POST",
                        data: formData,
                        processData: false, // Agar jQuery tidak mengubah data
                        contentType: false, // Agar jQuery tidak mengubah content type
                        success: function(response) {
                            if (response.success) {
                                $(e.target).trigger('reset');
                                $('.modal-backdrop').remove();
                                $('#update').modal('hide');
                                fetchData();
                                swal({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                });
                            } else {
                                Swal({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr) {
                            var errors = xhr.responseJSON;
                            if ($.isEmptyObject(errors) == false) {
                                $.each(errors.errors, function(key, value) {
                                    if (key == 'id') {
                                        $('#edit_id_error').html(value);
                                    } else {
                                        $('#ubah_' + key + '_error').html(value);
                                    }
                                });
                            }
                        }
                    });
                })

                $('#update').on('hidden.bs.modal', function(e) {
                    resetErrorMessages();
                })

                // Fungsi untuk menghapus pesan error dan reset form
                function resetErrorMessages() {
                    // Kosongkan semua pesan error
                    $('.text-danger strong').text('');

                    // Reset form input (jika dibutuhkan)
                    $('#form_update')[0].reset();

                    // Sembunyikan alert error jika ada
                    $('#error_update').hide();
                }

                $('#form_filtervicon').on('submit', function(e) {
                    e.preventDefault();
                    fetchData();
                    $('.modal-backdrop').remove();
                    $('#advance_filter').modal('hide');

                })

                $('#vicon_filter_excel').on('click', function(e) {
                    e.preventDefault();
                    var formData = $('#form_filtervicon').serialize();
                    window.location.href = "{{ route('admin.vicon.excel') }}" + '?' + formData;

                })
                $('#vicon_filter_pdf').on('click', function(e) {
                    e.preventDefault();
                    var formData = $('#form_filtervicon').serialize();
                    window.open("{{ route('admin.vicon.pdf') }}?" + formData, '_blank');
                })
            })
        </script>

        <script>
            $(document).ready(function () {
                $('#bagian').select2();
            });
        </script>
    @endpush
</x-layouts.app>
