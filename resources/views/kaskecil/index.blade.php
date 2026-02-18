<x-layouts.app>
    <x-slot name="styles">
        <style type="text/css">
            .select2-selection__choice__remove {
                color: white !important;
            }

            .hidden-section {
                display: none;
            }
        </style>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
        <!-- CSS Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- JavaScript Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </x-slot>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Kas Kecil</h3>
                @if (!in_array(Auth::user()->master_hak_akses_id, [5, 6]))
                <button id="btnTambah" type="button" data-toggle="modal" data-target="#tambah" class="btn btn-primary">Tambah Data</button>
                <button id="btnExport" type="button" data-toggle="modal" data-target="#exportModal" class="btn btn-warning">Export Data</button>
                @endif
                @if(session('success'))
                <div class="alert alert-success alert-sm alert-dismissible fade show" role="alert" style="max-width: 400px; margin:">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @elseif(session('error'))
                <div class="alert alert-danger alert-sm alert-dismissible fade show" role="alert" style="max-width: 400px;">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <!-- Modal Tambah -->
                <div id="tambah" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Tambah Data Kas Kecil</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div id="kaskecil" class="form-group container-fluid">
                                    <form id="form_tambah" class="form-horizontal calender" role="form" enctype="multipart/form-data" method="POST" action="/kaskecil/store">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Nama Yang Mengajukan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nama_pengaju" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Tanggal Pengajuan <span class="text-danger">*</span></b>
                                                <input type="date" class="form-control" name="tgl_pengajuan" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Group <span class="text-danger">*</span></b>
                                                <select class="form-control" name="id_group" required>
                                                    <option value="" disabled selected>Pilih Group</option>
                                                    @foreach ($group as $groupdata)
                                                    <option value='{{ $groupdata->id_group }}'>{{ $groupdata->nama_group }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>GL <span class="text-danger">*</span></b>
                                                <select class="form-control" name="nomor_gl" required>
                                                    <option value="" disabled selected>Pilih GL</option>
                                                    @foreach ($gl as $gldata)
                                                    <option value='{{ $gldata->nomor_gl }}'>{{ $gldata->nomor_gl }} - {{ $gldata->nama_gl }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>CC <span class="text-danger">*</span></b>
                                                <select class="form-control" name="nomor_cc" required>
                                                    <option value="" disabled selected>Pilih CC</option>
                                                    @foreach ($cc as $ccdata)
                                                    <option value='{{ $ccdata->nomor_cc }}'>{{ $ccdata->nomor_cc }} - {{ $ccdata->nama_cc }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Nominal <span class="text-danger">*</span></b>
                                                <input type="number" class="form-control " name="nominal" required>
                                            </div>
                                        </div>
                                        <div class="row hidden-section">
                                            <div class="form-group col-md-6">
                                                <b>Kendaraan</b>
                                                <select class="form-control" name="id_kendaraan">
                                                    <option value="" disabled selected>Pilih Kendaraan</option>
                                                    @foreach ($kendaraan as $kdata)
                                                    <option value='{{ $kdata->id_kendaraan }}'>{{ $kdata->nopol }} - {{ $kdata->tipe_kendaraan }} - {{ $kdata->kepemilikan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>BBM</b>
                                                <select class="form-control" name="id_bbm">
                                                    <option value="" disabled selected>Pilih BBM</option>
                                                    @foreach ($bbm as $bbmdata)
                                                    <option value='{{ $bbmdata->id_bbm }}'>{{ $bbmdata->nama_bbm }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row hidden-section">
                                            <div class="form-group col-md-6">
                                                <b>KM Awal</b>
                                                <input type="number" class="form-control" name="km_awal">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>KM Akhir</b>
                                                <input type="number" class="form-control" name="km_akhir">
                                            </div>
                                        </div>
                                        <div class="row hidden-section">
                                            <div class="form-group col-md-6">
                                                <b>Jumlah KM </b><span class="text-success">*otomatis</span>
                                                <input type="text" class="form-control" id="jumlah_km_tambah" disabled readonly>
                                            </div>
                                        </div>
                                        <div class="row hidden-section">
                                            <div class="form-group col-md-6">
                                                <b>Liter Bensin</b>
                                                <input type="number" class="form-control" name="liter_bensin">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Harga Bensin</b>
                                                <input type="number" class="form-control" name="harga_bensin">
                                            </div>
                                        </div>
                                        <div class="row hidden-section">
                                            <div class="form-group col-md-6">
                                                <b>Biaya Tol</b>
                                                <input type="number" class="form-control " name="tol">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Parkir</b>
                                                <input type="number" class="form-control " name="parkir">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>PPN </b>
                                                <input type="number" class="form-control " name="ppn">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>PPH </b>
                                                <input type="number" class="form-control " name="pph">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Biaya Aplikasi </b>
                                                <input type="number" class="form-control " name="biaya_aplikasi">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Biaya Lain-Lain </b>
                                                <input type="number" class="form-control " name="lain_lain">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Dibayarkan Oleh <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="dibayarkan_oleh" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Tanggal Dibayarkan <span class="text-danger">*</span></b>
                                                <input type="date" class="form-control" name="tgl_dibayarkan" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Bukti Nota</b>
                                                <input type="file" class="form-control" name="bukti_nota">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Bukti Bayar</b>
                                                <input type="file" class="form-control" name="bukti_bayar">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Keterangan</b>
                                                <textarea class="form-control" name="keterangan" rows="4"></textarea>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Total Biaya </b><span class="text-success">*otomatis</span>
                                                <input type="text" class="form-control rupiah" id="total_biaya_tambah" readonly disabled>
                                            </div>
                                        </div>
                                        <!-- Modal Footer (buttons) -->
                                        <div class="modal-footer d-flex justify-content-end">
                                            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary antosubmit">Tambahkan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div id="edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Edit Data</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div id="kaskeciledit" class="form-group container-fluid">
                                    <form id="form_edit" class="form-horizontal calender" role="form" enctype="multipart/form-data" method="POST">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="id_kaskecil">
                                            <div class="form-group col-md-6">
                                                <b>Nama Yang Mengajukan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nama_pengaju" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Tanggal Pengajuan <span class="text-danger">*</span></b>
                                                <input type="date" class="form-control" name="tgl_pengajuan" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Group <span class="text-danger">*</span></b>
                                                <select class="form-control" name="id_group" required>
                                                    <option value="" disabled selected>Pilih Group</option>
                                                    @foreach ($group as $groupdata)
                                                    <option value='{{ $groupdata->id_group }}'>{{ $groupdata->nama_group }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>GL <span class="text-danger">*</span></b>
                                                <select class="form-control" name="nomor_gl" required>
                                                    <option value="" disabled selected>Pilih GL</option>
                                                    @foreach ($gl as $gldata)
                                                    <option value='{{ $gldata->nomor_gl }}'>{{ $gldata->nomor_gl }} - {{ $gldata->nama_gl }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>CC <span class="text-danger">*</span></b>
                                                <select class="form-control" name="nomor_cc" required>
                                                    <option value="" disabled selected>Pilih CC</option>
                                                    @foreach ($cc as $ccdata)
                                                    <option value='{{ $ccdata->nomor_cc }}'>{{ $ccdata->nomor_cc }} - {{ $ccdata->nama_cc }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Nominal <span class="text-danger">*</span></b>
                                                <input type="number" class="form-control " name="nominal" required>
                                            </div>
                                        </div>
                                        <div class="row hidden-section">
                                            <div class="form-group col-md-6">
                                                <b>Kendaraan</b>
                                                <select class="form-control" name="id_kendaraan">
                                                    <option value="" disabled selected>Pilih Kendaraan</option>
                                                    @foreach ($kendaraan as $kdata)
                                                    <option value='{{ $kdata->id_kendaraan }}'>{{ $kdata->nopol }} - {{ $kdata->tipe_kendaraan }} - {{ $kdata->kepemilikan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>BBM </b>
                                                <select class="form-control" name="id_bbm">
                                                    <option value="" disabled selected>Pilih BBM</option>
                                                    @foreach ($bbm as $bbmdata)
                                                    <option value='{{ $bbmdata->id_bbm }}'>{{ $bbmdata->nama_bbm }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row hidden-section">
                                            <div class="form-group col-md-6">
                                                <b>KM Awal</b>
                                                <input type="number" class="form-control" name="km_awal">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>KM Akhir</b>
                                                <input type="number" class="form-control" name="km_akhir">
                                            </div>
                                        </div>
                                        <div class="row hidden-section">
                                            <div class="form-group col-md-6">
                                                <b>Jumlah KM </b><span class="text-success">*otomatis</span>
                                                <input type="text" class="form-control" id="jumlah_km_edit" disabled readonly>
                                            </div>
                                        </div>
                                        <div class="row hidden-section">
                                            <div class="form-group col-md-6">
                                                <b>Liter Bensin </b>
                                                <input type="number" class="form-control" name="liter_bensin">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Harga Bensin </b>
                                                <input type="number" class="form-control" name="harga_bensin">
                                            </div>
                                        </div>
                                        <div class="row hidden-section">
                                            <div class="form-group col-md-6">
                                                <b>Biaya Tol </b>
                                                <input type="number" class="form-control " name="tol">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Parkir </b>
                                                <input type="number" class="form-control " name="parkir">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>PPN</b>
                                                <input type="number" class="form-control " name="ppn">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>PPH</b>
                                                <input type="number" class="form-control " name="pph">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Biaya Aplikasi </b>
                                                <input type="number" class="form-control " name="biaya_aplikasi">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Biaya Lain-Lain</b>
                                                <input type="number" class="form-control " name="lain_lain">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Dibayarkan Oleh <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="dibayarkan_oleh" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Tanggal Dibayarkan <span class="text-danger">*</span></b>
                                                <input type="date" class="form-control" name="tgl_dibayarkan" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Bukti Nota</b>
                                                <input type="file" class="form-control" name="bukti_nota">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Bukti Bayar</b>
                                                <input type="file" class="form-control" name="bukti_bayar">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Keterangan</b>
                                                <textarea class="form-control" name="keterangan" rows="4"></textarea>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Total Biaya </b><span class="text-success">*otomatis</span>
                                                <input type="text" class="form-control rupiah" id="total_biaya_edit" readonly disabled>
                                            </div>
                                        </div>
                                        <!-- Modal Footer (buttons) -->
                                        <div class="modal-footer d-flex justify-content-end">
                                            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary antosubmit">Update Data</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Export -->
                <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exportModalLabel">Export Kaskecil Data</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="exportForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tgl_pengajuan_awal">Tanggal Pengajuan Awal</label>
                                                <input type="date" class="form-control" id="tgl_pengajuan_awal" name="tgl_pengajuan_awal">
                                            </div>
                                            <div class="form-group">
                                                <label for="nama_group">Group</label>
                                                <!-- <input type="text" class="form-control" id="nama_group" name="nama_group"> -->
                                                <select class="form-control" name="id_group">
                                                    <option value="" disabled selected>Pilih Group</option>
                                                    @foreach ($group as $groupdata)
                                                    <option value='{{ $groupdata->id_group }}'>{{ $groupdata->nama_group }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="nomor_cc">Nomor CC - Nama CC</label>
                                                <!-- <input type="text" class="form-control" id="nomor_cc" name="nomor_cc"> -->
                                                <select class="form-control" name="nomor_cc">
                                                    <option value="" disabled selected>Pilih CC</option>
                                                    @foreach ($cc as $ccdata)
                                                    <option value='{{ $ccdata->nomor_cc }}'>{{ $ccdata->nomor_cc }} - {{ $ccdata->nama_cc }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tgl_pengajuan_akhir">Tanggal Pengajuan Akhir</label>
                                                <input type="date" class="form-control" id="tgl_pengajuan_akhir" name="tgl_pengajuan_akhir">
                                            </div>
                                            <div class="form-group">
                                                <label for="nomor_gl">Nomor GL - Nama GL</label>
                                                <!-- <input type="text" class="form-control" id="nomor_gl" name="nomor_gl"> -->
                                                <select class="form-control" name="nomor_gl2">
                                                    <option value="" disabled selected>Pilih GL</option>
                                                    @foreach ($gl as $gldata)
                                                    <option value='{{ $gldata->nomor_gl }}'>{{ $gldata->nomor_gl }} - {{ $gldata->nama_gl }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="tgl_dibayarkan">Tanggal Dibayarkan</label>
                                                <input type="date" class="form-control" id="tgl_dibayarkan" name="tgl_dibayarkan">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="exportBtn">Export to Excel</button>
                                    <button type="reset" class="btn btn-secondary mr-2" id="resetBtn">Reset Filter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-primary mt-3">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTables-kaskecil">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Aksi</th>
                                        <th>Nama Pengaju</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Group</th>
                                        <th>Keterangan</th>
                                        <th>GL</th>
                                        <th>CC</th>
                                        <th>Nominal</th>
                                        <th>Kendaraan</th>
                                        <th>KM Awal</th>
                                        <th>KM Akhir</th>
                                        <th>Jumlah KM</th>
                                        <th>BBM</th>
                                        <th>Liter Bensin</th>
                                        <th>Harga Bensin</th>
                                        <th>Biaya Tol</th>
                                        <th>Parkir</th>
                                        <th>PPN</th>
                                        <th>PPH</th>
                                        <th>Biaya Aplikasi</th>
                                        <th>Biaya Lain-lain</th>
                                        <th>Dibayarkan Oleh</th>
                                        <th>Tanggal Dibayarkan</th>
                                        <th>Bukti Nota</th>
                                        <th>Bukti Bayar</th>
                                        <th>Total Biaya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <x-slot name="scripts">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#dataTables-kaskecil').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('kaskecil.data') }}",
                    responsive: true,
                    order: [[0, 'desc']],
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
                        { data: 'nama_pengaju', name: 'nama_pengaju', defaultContent: '-' },
                        { data: 'tgl_pengajuan', name: 'tgl_pengajuan', defaultContent: '-' },
                        { data: 'nama_group', name: 'nama_group', orderable: false, searchable: false },
                        { data: 'keterangan', name: 'keterangan', defaultContent: '-' },
                        { data: 'gl_display', name: 'gl_display', orderable: false, searchable: false },
                        { data: 'cc_display', name: 'cc_display', orderable: false, searchable: false },
                        { data: 'nominal_formatted', name: 'nominal', orderable: false, searchable: false },
                        { data: 'kendaraan_display', name: 'kendaraan_display', orderable: false, searchable: false },
                        { data: 'km_awal', name: 'km_awal', defaultContent: '-' },
                        { data: 'km_akhir', name: 'km_akhir', defaultContent: '-' },
                        { data: 'jumlah_km', name: 'jumlah_km', orderable: false, searchable: false },
                        { data: 'nama_bbm', name: 'nama_bbm', orderable: false, searchable: false },
                        { data: 'liter_bensin', name: 'liter_bensin', defaultContent: '-' },
                        { data: 'harga_bensin_formatted', name: 'harga_bensin', orderable: false, searchable: false },
                        { data: 'tol_formatted', name: 'tol', orderable: false, searchable: false },
                        { data: 'parkir_formatted', name: 'parkir', orderable: false, searchable: false },
                        { data: 'ppn_formatted', name: 'ppn', orderable: false, searchable: false },
                        { data: 'pph_formatted', name: 'pph', orderable: false, searchable: false },
                        { data: 'biaya_aplikasi_formatted', name: 'biaya_aplikasi', orderable: false, searchable: false },
                        { data: 'lain_lain_formatted', name: 'lain_lain', orderable: false, searchable: false },
                        { data: 'dibayarkan_oleh', name: 'dibayarkan_oleh', defaultContent: '-' },
                        { data: 'tgl_dibayarkan', name: 'tgl_dibayarkan', defaultContent: '-' },
                        { data: 'bukti_nota_link', name: 'bukti_nota_link', orderable: false, searchable: false },
                        { data: 'bukti_bayar_link', name: 'bukti_bayar_link', orderable: false, searchable: false },
                        { data: 'total_biaya', name: 'total_biaya', orderable: false, searchable: false }
                    ]
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                // Mengubah data GL berdasarkan group yang dipilih
                $('select[name="id_group"]').on('change', function() {
                    var groupId = $(this).val(); // Ambil nilai id_group yang dipilih

                    // Filter data GL yang sesuai dengan id_group yang dipilih
                    var filteredGl = @json($glgroup).filter(function(glgroup) {
                        return glgroup.group_id == groupId;
                    });

                    // Clear opsi GL
                    var glSelect = $('select[name="nomor_gl"]');
                    glSelect.empty();

                    // Loop untuk menambahkan data GL yang sesuai
                    filteredGl.forEach(function(glgroup) {
                        // Cari data GL berdasarkan nomor_gl
                        var glData = @json($gl).find(function(gl) {
                            return gl.nomor_gl == glgroup.nomor_gl;
                        });

                        // Tambahkan opsi GL ke dalam select GL
                        if (glData) {
                            glSelect.append('<option value="' + glData.nomor_gl + '">' + glData.nomor_gl + ' - ' + glData.nama_gl + '</option>');
                        }
                    });
                });
            });
        </script>
        <script>
            document.getElementById("btnTambah").addEventListener("click", function() {
                resetForm();
                // Form siap digunakan untuk tambah data
            });
        </script>
        <script>
            function resetForm() {
                document.getElementById("form_tambah").reset(); // Reset semua field form
            }

            function resetFormEdit() {
                document.getElementById("form_edit").reset(); // Reset semua field form
            }
        </script>
        <script>
            $(document).on('click', '.btn-info', function() {
                resetFormEdit()
                const id = $(this).data('id'); // Ambil ID dari tombol
                $.ajax({
                    url: `/kaskecil/edit/${id}`, // Endpoint untuk mendapatkan data
                    method: 'GET',
                    success: function(data) {
                        // Isi modal dengan data dari server
                        $('input[name="id_kaskecil"]').val(data.id_kaskecil);
                        $('input[name="nama_pengaju"]').val(data.nama_pengaju);
                        $('input[name="tgl_pengajuan"]').val(data.tgl_pengajuan);
                        $('select[name="id_group"]').val(data.id_group);
                        $('select[name="nomor_gl"]').val(data.nomor_gl);
                        $('select[name="nomor_cc"]').val(data.nomor_cc);
                        $('select[name="id_kendaraan"]').val(data.id_kendaraan);
                        $('input[name="km_awal"]').val(data.km_awal);
                        $('input[name="km_akhir"]').val(data.km_akhir);
                        $('select[name="id_bbm"]').val(data.id_bbm);
                        $('input[name="liter_bensin"]').val(data.liter_bensin);
                        $('input[name="harga_bensin"]').val(data.harga_bensin);
                        $('input[name="nominal"]').val(data.nominal);
                        $('input[name="ppn"]').val(data.ppn);
                        $('input[name="pph"]').val(data.pph);
                        $('input[name="tol"]').val(data.tol);
                        $('input[name="parkir"]').val(data.parkir);
                        $('input[name="biaya_aplikasi"]').val(data.biaya_aplikasi);
                        $('input[name="lain_lain"]').val(data.lain_lain);
                        $('input[name="dibayarkan_oleh"]').val(data.dibayarkan_oleh);
                        $('input[name="tgl_dibayarkan"]').val(data.tgl_dibayarkan);
                        $('textarea[name="keterangan"]').val(data.keterangan);

                        // Manually trigger change event to show/hide sections and run calculations
                        $('#form_edit select[name="id_group"]').trigger('change');
                        $('#form_edit').trigger('input');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        </script>
        <script>
            // Function to calculate "Jumlah KM" and "Total Biaya"
            function calculateTotals(form) {
                let km_awal = parseFloat(form.querySelector('[name="km_awal"]').value) || 0;
                let km_akhir = parseFloat(form.querySelector('[name="km_akhir"]').value) || 0;
                let nominal = parseFloat(form.querySelector('[name="nominal"]').value.replace(/[^0-9.-]+/g, "")) || 0;
                let ppn = parseFloat(form.querySelector('[name="ppn"]').value.replace(/[^0-9.-]+/g, "")) || 0;
                let harga_bensin = parseFloat(form.querySelector('[name="harga_bensin"]').value.replace(/[^0-9.-]+/g, "")) || 0;
                let pph = parseFloat(form.querySelector('[name="pph"]').value.replace(/[^0-9.-]+/g, "")) || 0;
                let tol = parseFloat(form.querySelector('[name="tol"]').value.replace(/[^0-9.-]+/g, "")) || 0;
                let parkir = parseFloat(form.querySelector('[name="parkir"]').value.replace(/[^0-9.-]+/g, "")) || 0;
                let biaya_aplikasi = parseFloat(form.querySelector('[name="biaya_aplikasi"]').value.replace(/[^0-9.-]+/g, "")) || 0;
                let lain_lain = parseFloat(form.querySelector('[name="lain_lain"]').value.replace(/[^0-9.-]+/g, "")) || 0;

                // Calculate "Jumlah KM"
                let jumlah_km = km_akhir - km_awal;
                form.querySelector('input[id^="jumlah_km"]').value = jumlah_km > 0 ? jumlah_km : 0;

                // Calculate "Total Biaya"
                let total_biaya = nominal + ppn + pph + tol + parkir + biaya_aplikasi + lain_lain + harga_bensin;
                form.querySelector('input[id^="total_biaya"]').value = new Intl.NumberFormat('id-ID').format(Math.round(total_biaya));
            }

            // Add event listeners to both forms
            document.querySelectorAll('#form_tambah, #form_edit').forEach(form => {
                form.addEventListener('input', function() {
                    calculateTotals(this);
                });
            });
        </script>
        <script>
            $('#form_edit').on('submit', function(e) {
                e.preventDefault();

                const id = $('input[name="id_kaskecil"]').val(); // Ambil ID dari form
                const formData = $(this).serialize(); // Ambil semua data dari form

                $.ajax({
                    url: `/kaskecil/update/${id}`, // Endpoint untuk update
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.redirect_url) {
                            // Redirect ke URL dari server
                            window.location.href = response.redirect_url;
                        } else {
                            console.error('Redirect URL tidak ditemukan dalam respons.');
                        }

                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('Terjadi kesalahan saat mengirim data.');
                    }
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                const groupIdsToShow = [2, 3, 5, 13, 17, 18];
    
                function toggleVehicleFields(form) {
                    const selectedGroupId = parseInt($(form).find('select[name="id_group"]').val());
                    const hiddenSections = $(form).find('.hidden-section');
    
                    if (groupIdsToShow.includes(selectedGroupId)) {
                        hiddenSections.show();
                    } else {
                        hiddenSections.hide();
                        // Kosongkan nilai field yang disembunyikan agar tidak ikut terhitung
                        hiddenSections.find('input, select').val('');
                    }
                }
    
                // Event listener untuk kedua form
                $('#form_tambah, #form_edit').on('change', 'select[name="id_group"]', function() {
                    const form = $(this).closest('form');
                    toggleVehicleFields(form);
                });
            });
        </script>
        <script>
            $('#exportBtn').on('click', function() {
                var formData = $('#exportForm').serialize(); // Get the form data

                // Trigger the Excel export request with the selected filters
                window.location.href = "{{ route('kaskecil.export') }}?" + formData;

                $('#exportModal').modal('hide'); // Close the modal after export
            });
        </script>
    </x-slot>
</x-layouts.app>