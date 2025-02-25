<x-layouts.app>
    <x-slot name="styles">
        <style type="text/css">
            .select2-selection__choice__remove {
                color: white !important;
            }

            .hidden-section {
                display: none;
            }

            .btn-info {
                background-color: #2980b9 !important;
                border-color: #2471a3 !important;
                /* Warna border lebih gelap */
            }

            .btn-info:hover {
                background-color: #1f6690 !important;
                /* Warna lebih gelap saat hover */
                border-color: #1a5579 !important;
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
                <h3 class="mt-4">Pengajuan Kendaraan</h3>
                @if (!in_array(Auth::user()->master_hak_akses_id, [5, 6]))
                    <button id="btnTambah" type="button" data-toggle="modal" data-target="#tambah"
                        class="btn btn-primary btn-sm">Tambah Data</button>
                    <button id="btnExport" type="button" data-toggle="modal" data-target="#exportModal"
                        class="btn btn-warning btn-sm">Export Data</button>
                @endif
                @if (session('success'))
                    <div class="alert alert-success alert-sm alert-dismissible fade show" role="alert"
                        style="max-width: 400px; margin:">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-danger alert-sm alert-dismissible fade show" role="alert"
                        style="max-width: 400px;">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <!-- Modal Tambah -->
                <div id="tambah" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Tambah Pengajuan Kendaraan</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div id="kaskecil" class="form-group container-fluid">
                                    <form id="form_tambah" class="form-horizontal calender" role="form"
                                        enctype="multipart/form-data" method="POST" action="/pkendaraan/store">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Divisi <span class="text-danger">*</span></b>
                                                {{-- <input type="text" class="form-control" name="divisi" id="divisi"
                                                    value="{{ $divisi }}" required readonly> --}}

                                                <select class="form-control" name="divisi" id="divisi">
                                                    @if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga']))

                                                        <option value="" disabled selected>Pilih Divisi</option>
                                                        @foreach ($get_divisi as $data_divisi)
                                                            <option value='{{ $data_divisi->master_bagian_nama }}'>
                                                                {{ $data_divisi->master_bagian_nama }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value="{{ $divisi }}">{{ $divisi }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <b>Nama PIC <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nama_pic"
                                                    id='nama_pic' required>
                                            </div>

                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Tanggal Berangkat <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="tgl_berangkat"
                                                    id="tgl_berangkat" required>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <div class="input-wrap">
                                                    <b>Waktu <span class="text-danger">*</span></b>
                                                    <input type="time" id="jam_berangkat" name="jam_berangkat"
                                                        class="form-control" required>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Jenis Tujuan <span class="text-danger">*</span></b>
                                                <select name="jenis_tujuan" id="jenis_tujuan" class="form-control"
                                                    required>
                                                    <option value="Dalam Kota">Dalam Kota</option>
                                                    <option value="Luar Kota">Luar Kota</option>
                                                    <option value="Luar Negeri">Luar Negeri</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <b>Tujuan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="tujuan"
                                                    id='tujuan' required>
                                            </div>

                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Penjemputan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="pejemputan"
                                                    id='pejemputan' required>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <b>Upload Memo <span class="text-danger">*</span></b>
                                                <input type="file" class="form-control" name="file_memo"
                                                    id="file_memo" required accept=".pdf, .jpg, .jpeg, .png">
                                            </div>

                                        </div>

                                        <!-- Modal Footer (buttons) -->
                                        <div class="modal-footer d-flex justify-content-end">
                                            <button type="button" class="btn btn-default antoclose"
                                                data-dismiss="modal">Close</button>
                                            <button type="submit"
                                                class="btn btn-primary antosubmit">Tambahkan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div id="edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Edit Data</h4>
                                <button type="button" class="close" data-dismiss="modal"
                                    aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div id="kaskeciledit" class="form-group container-fluid">
                                    <form id="form_edit" class="form-horizontal calender" role="form"
                                        enctype="multipart/form-data" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <input type="hidden" name="id" id="id">
                                            <div class="form-group col-md-6">
                                                <b>Divisi <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="divisi1"
                                                    id="divisi1" required readonly>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <b>Nama PIC <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nama_pic1"
                                                    id='nama_pic1' required>
                                            </div>

                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Tanggal Berangkat <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="tgl_berangkat1"
                                                    id="tgl_berangkat1" required>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <div class="input-wrap">
                                                    <b>Waktu <span class="text-danger">*</span></b>
                                                    <input type="time" id="jam_berangkat1" name="jam_berangkat1"
                                                        class="form-control" required>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Jenis Tujuan <span class="text-danger">*</span></b>
                                                <select name="jenis_tujuan1" id="jenis_tujuan1" class="form-control"
                                                    required>
                                                    <option value="Dalam Kota">Dalam Kota</option>
                                                    <option value="Luar Kota">Luar Kota</option>
                                                    <option value="Luar Negeri">Luar Negeri</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <b>Tujuan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="tujuan1"
                                                    id='tujuan1' required>
                                            </div>

                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Penjemputan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="pejemputan1"
                                                    id='pejemputan1' required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Upload Memo <span class="text-danger">*</span></b>
                                                <input type="file" class="form-control" name="file_memo1"
                                                    id="file_memo1" accept=".pdf, .jpg, .jpeg, .png">
                                            </div>

                                        </div>

                                        @if (in_array(Auth::user()->master_user_nama, ['asisten_ga']))
                                            <!-- Bagian yang hanya muncul jika user adalah 'asisten_ga' -->
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <b>Driver <span class="text-danger">*</span></b>
                                                    <select name="driver1" id="driver1" class="form-control"
                                                        required>
                                                        <!-- Option akan diisi dari JavaScript -->
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <b>Kendaraan <span class="text-danger">*</span></b>
                                                    <select name="no_polisi1" id="no_polisi1" class="form-control"
                                                        required>
                                                        <!-- Option akan diisi dari JavaScript -->
                                                    </select>
                                                </div>
                                        @endif

                                </div>
                                <!-- Modal Footer (buttons) -->
                                <div class="modal-footer d-flex justify-content-end">
                                    <button type="button" class="btn btn-default antoclose"
                                        data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary antosubmit">Update
                                        Data</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Modal Export -->
            <div class="modal fade" id="exportModal" tabindex="-1" role="dialog"
                aria-labelledby="exportModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exportModalLabel">Export Permintaan Kendaraan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="exportForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_berangkat_awal">Tanggal Berangkat</label>
                                            <input type="date" class="form-control" id="tgl_awal"
                                                name="tgl_awal" value="{{ now()->format('Y-m-d') }}">

                                        </div>
                                        <div class="form-group">
                                            <label for="nama_group">Divisi</label>
                                            <select class="form-control" name="id_divisi">
                                                @if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga']))
                                                    <option value="" disabled selected>Pilih Divisi</option>
                                                    <option value='all'>Seluruh Divisi</option>
                                                    @foreach ($get_divisi as $data_divisi)
                                                        <option value='{{ $data_divisi->master_bagian_nama }}'>
                                                            {{ $data_divisi->master_bagian_nama }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="{{ $divisi }}">{{ $divisi }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Jenis Tujuan</label>
                                            <select class="form-control" name="jenis_tujuan">
                                                <option value="" disabled selected>Pilih Jenis Tujuan</option>
                                                <option value="Dalam Kota">Dalam Kota</option>
                                                <option value="Luar Kota">Luar Kota</option>
                                                <option value="Luar Negeri">Luar Negeri</option>

                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <!-- <input type="text" class="form-control" id="nomor_gl" name="nomor_gl"> -->
                                            <select class="form-control" name="status">
                                                <option value="" disabled selected>Pilih Status</option>
                                                <option value='2'>Approved</option>
                                                <option value='3'>Rejected</option>
                                                <option value='0'>Canceled</option>
                                                <option value='1'>Pengajuan Divisi</option>
                                                <option value='all'>Seluruh Status</option>

                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" id="exportBtn">Export to
                                    Excel</button>
                                <button type="reset" class="btn btn-secondary mr-2" id="resetBtn">Reset
                                    Filter</button>
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
                                    <th>Status</th>
                                    <th>Divisi</th>
                                    <th>Nama PIC</th>
                                    <th>Jenis Tujuan</th>
                                    <th>Tanggal Berangkat</th>
                                    <th>Jam Berangkat</th>
                                    <th>Tujuan</th>
                                    <th>Penjemputan</th>
                                    <th>File Memo</th>
                                    <th>Driver</th>
                                    <th>Plat Nomor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pkendaraan as $index => $item)
                                    <tr align="center">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @php
                                                    $today = \Carbon\Carbon::today();
                                                    $requestDate = \Carbon\Carbon::parse($item->tgl_permintaan);
                                                    $isDisabled =
                                                        $requestDate->lte($today) ||
                                                        $item->status == 0 ||
                                                        $item->status == 3 ||
                                                        $item->status == 2; // Disabled jika tgl_permintaan <= hari ini atau status = 0 (Canceled) atau status = 2 (Approved)
                                                    $isPending = $item->status == 1; // Hanya aktif jika status = 1
                                                @endphp
                                                <!-- Edit Button -->
                                                <button type="button" class="btn btn-sm btn-info"
                                                    data-toggle="modal" data-target="#edit"
                                                    data-id="{{ $item->id ?? '-' }}" id="btnEdit"
                                                    @if ($isDisabled && !in_array(Auth::user()->master_user_nama, ['asisten_ga'])) disabled @endif>
                                                    <i class="fa fa-pencil" style="color: white;"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <form action="{{ route('pkendaraan.destroy', $item->id ?? '-') }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah yakin cancel data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        @if ($isDisabled) disabled @endif>
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <!-- Approve and Reject Buttons -->
                                                @if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga']))
                                                    <!-- Approve Button -->
                                                    <form action="{{ route('pkendaraan.approve', $item->id ?? '-') }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah yakin menyetujui data ini?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            @if (!$isPending) disabled @endif>
                                                            <i class="fa fa-check"></i> <!-- Icon centang -->
                                                        </button>
                                                    </form>

                                                    <!-- Reject Button -->
                                                    <form action="{{ route('pkendaraan.reject', $item->id ?? '-') }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah yakin menolak data ini?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            @if (!$isPending) disabled @endif>
                                                            <i class="fa fa-times"></i> <!-- Icon silang -->
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </td>
                                        <td align="center">
                                            @if ($item->status == 0)
                                                <span class="text-warning">Canceled</span> <!-- Warna orange -->
                                            @elseif ($item->status == 1)
                                                <span class="text-primary">Pengajuan Divisi</span>
                                                <!-- Warna biru -->
                                            @elseif ($item->status == 2)
                                                <span class="text-success">Approved</span> <!-- Warna hijau -->
                                            @elseif ($item->status == 3)
                                                <span class="text-danger">Rejected</span> <!-- Warna merah -->
                                            @else
                                                <span class="text-muted">-</span>
                                                <!-- Warna abu-abu untuk status tidak terdefinisi -->
                                            @endif
                                        </td>
                                        <td align="center">{{ $item->divisi ?? '-' }}</td>
                                        <td align="center">{{ $item->nama_pic ?? '-' }}</td>
                                        <td align="center">{{ $item->jenis_tujuan ?? '-' }}</td>
                                        <td align="center">
                                            {{ $item->tgl_berangkat ? \Carbon\Carbon::parse($item->tgl_berangkat)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td align="center">
                                            {{ $item->jam_berangkat ? \Carbon\Carbon::parse($item->jam_berangkat)->format('H:i') : '-' }}
                                        </td>

                                        <td align="center">{{ $item->tujuan ?? '-' }}</td>
                                        <td align="center">{{ $item->pejemputan ?? '-' }}</td>
                                        <td align="center">
                                            @if ($item->file_memo)
                                                <a href="{{ asset('storage/' . str_replace('storage/', '', $item->file_memo)) }}"
                                                    target="_blank">Lihat File</a>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td align="center">
                                            {{ $item->driverDetail ? $item->driverDetail->nama_driver . ' - ' . $item->driverDetail->no_hp : '-' }}
                                        </td>
                                        <td align="center">
                                            {{ $item->kendaraanDetail ? $item->kendaraanDetail->nopol . ' - ' . $item->kendaraanDetail->tipe_kendaraan : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
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
        <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#dataTables-kaskecil').DataTable({
                    responsive: true
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                // Set tanggal hari ini pada input secara langsung
                $('#tgl_berangkat').val(moment().add(1, 'days').format('DD-MM-YYYY'));

                // Inisialisasi datepicker setelah nilai input di-set
                $('#tgl_berangkat').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate: moment().add(1, 'days'), // Tanggal default adalah hari ini
                    minDate: moment().add(1, 'days'), // Blokir tanggal sebelumnya
                    locale: {
                        format: 'DD-MM-YYYY' // Format tanggal
                    }
                });

                // Mengatur agar input hanya bisa dipilih (tidak bisa diketik)
                $('#tgl_berangkat').prop('readonly', true); // Menonaktifkan input manual
            });
        </script>
        <script>
            $(document).ready(function() {
                // Set tanggal besok pada input secara langsung
                $('#tgl_berangkat1').val(moment().add(1, 'days').format('DD-MM-YYYY'));

                // Inisialisasi datepicker setelah nilai input di-set
                $('#tgl_berangkat1').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate: moment().add(1, 'days'), // Tanggal default adalah besok
                    minDate: moment().add(1, 'days'), // Blokir tanggal hari ini dan sebelumnya
                    locale: {
                        format: 'DD-MM-YYYY' // Format tanggal
                    }
                });

                // Mengatur agar input hanya bisa dipilih (tidak bisa diketik)
                $('#tgl_berangkat1').prop('readonly', true); // Menonaktifkan input manual
            });
        </script>
        <script>
            var today = new Date();
            var tomorrow = new Date(today);
            tomorrow.setDate(today.getDate() + 1); // Tetap pada hari ini

            // Ambil tanggal, bulan, dan tahun dari objek Date
            var day = ("0" + tomorrow.getDate()).slice(-2); // Ambil 2 digit angka
            var month = ("0" + (tomorrow.getMonth() + 1)).slice(-2); // Bulan dimulai dari 0, jadi +1
            var year = tomorrow.getFullYear();

            var dateString = day + "-" + month + "-" + year; // Format DD-MM-YYYY

            document.getElementById("btnTambah").addEventListener("click", function() {
                resetForm();
                document.getElementById("tgl_berangkat").value = dateString; // Isi dengan tanggal format DD-MM-YYYY
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Ambil elemen input
                const jlhKaryawanInput = document.getElementById('jlh_karyawan');
                const kadivInputs = document.querySelectorAll('input[name="kadiv"]');
                const jlhpkendaraanInput = document.getElementById('jlh_pkendaraan');

                // Fungsi untuk menghitung jumlah makan siang
                function calculateJumlahpkendaraan() {
                    const jlhKaryawan = parseInt(jlhKaryawanInput.value) || 0; // Nilai jumlah karyawan
                    const kadivValue = parseInt(document.querySelector('input[name="kadiv"]:checked').value) ||
                        0; // Nilai kadiv (1 atau 0)
                    const totalpkendaraan = jlhKaryawan + kadivValue; // Perhitungan
                    jlhpkendaraanInput.value = totalpkendaraan; // Isi input jumlah makan siang
                }

                // Tambahkan event listener ke input jumlah karyawan
                jlhKaryawanInput.addEventListener('input', calculateJumlahpkendaraan);

                // Tambahkan event listener ke radio button kadiv
                kadivInputs.forEach(input => {
                    input.addEventListener('change', calculateJumlahpkendaraan);
                });

                // Hitung otomatis saat halaman pertama kali dimuat
                calculateJumlahpkendaraan();
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Ambil elemen input
                const jlhKaryawanInput1 = document.getElementById('jlh_karyawan1');
                const kadivInputs1 = document.querySelectorAll('input[name="kadiv1"]');
                const jlhpkendaraanInput1 = document.getElementById('jlh_pkendaraan1');

                // Fungsi untuk menghitung jumlah makan siang
                function calculateJumlahpkendaraan1() {
                    const jlhKaryawan1 = parseInt(jlhKaryawanInput1.value) || 0; // Nilai jumlah karyawan
                    const kadivValue1 = parseInt(document.querySelector('input[name="kadiv1"]:checked').value) ||
                        0; // Nilai kadiv (1 atau 0)
                    const totalpkendaraan1 = jlhKaryawan1 + kadivValue1; // Perhitungan
                    jlhpkendaraanInput1.value = totalpkendaraan1; // Isi input jumlah makan siang
                }

                // Tambahkan event listener ke input jumlah karyawan
                jlhKaryawanInput1.addEventListener('input', calculateJumlahpkendaraan1);

                // Tambahkan event listener ke radio button kadiv
                kadivInputs1.forEach(input => {
                    input.addEventListener('change', calculateJumlahpkendaraan1);
                });

                // Hitung otomatis saat halaman pertama kali dimuat
                calculateJumlahpkendaraan1();
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
        {{-- <script>
            $(document).on('click', '.btn-info', function() {
                resetFormEdit();
                const id = $(this).data('id'); // Ambil ID dari tombol
                $.ajax({
                    url: `/pkendaraan/edit/${id}`, // Endpoint untuk mendapatkan data
                    method: 'GET',
                    success: function(data) {
                        // Ubah format tanggal dari YYYY-MM-DD ke DD-MM-YYYY
                        let tglBerangkat = data.tgl_berangkat ? data.tgl_berangkat.split('-').reverse()
                            .join('-') : '';

                        // Isi form dengan data yang sudah diformat
                        $('input[name="id"]').val(data.id);
                        $('input[name="divisi1"]').val(data.divisi);
                        $('input[name="nama_pic1"]').val(data.nama_pic);
                        $('input[name="tgl_berangkat1"]').val(tglBerangkat);
                        $('input[name="jam_berangkat1"]').val(data.jam_berangkat);
                        $('#jenis_tujuan1').val(data.jenis_tujuan); // Untuk select
                        $('input[name="tujuan1"]').val(data.tujuan);
                        $('input[name="pejemputan1"]').val(data.pejemputan);
                        $('input[name="no_polisi1"]').val(data.no_polisi);
                        $('input[name="driver1"]').val(data.driver);

                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        </script> --}}

        <script>
            $(document).on('click', '.btn-info', function() {
                resetFormEdit();
                const id = $(this).data('id'); // Ambil ID dari tombol
                $.ajax({
                    url: `/pkendaraan/edit/${id}`, // Endpoint untuk mendapatkan data
                    method: 'GET',
                    success: function(response) {
                        let data = response.data;
                        let drivers = response.drivers;
                        let kendaraans = response.kendaraans;

                        // Format tanggal
                        let tglBerangkat = data.tgl_berangkat ? data.tgl_berangkat.split('-').reverse()
                            .join('-') : '';

                        // Isi form dengan data yang sudah diformat
                        $('input[name="id"]').val(data.id);
                        $('input[name="divisi1"]').val(data.divisi);
                        $('input[name="nama_pic1"]').val(data.nama_pic);
                        $('input[name="tgl_berangkat1"]').val(tglBerangkat);
                        $('input[name="jam_berangkat1"]').val(data.jam_berangkat);
                        $('#jenis_tujuan1').val(data.jenis_tujuan); // Untuk select
                        $('input[name="tujuan1"]').val(data.tujuan);
                        $('input[name="pejemputan1"]').val(data.pejemputan);
                        // $('input[name="no_polisi1"]').val(data.no_polisi);

                        // Kosongkan dropdown sebelum menambahkan opsi baru
                        $('#driver1').empty();
                        $('#no_polisi1').empty();

                        // // Tambahkan opsi dari database
                        // drivers.forEach(driver => {
                        //     let selected = (driver.id_driver == data.driver) ? 'selected' : '';
                        //     $('#driver1').append(
                        //         `<option value="" disabled selected>Pilih Driver</option>`,
                        //         `<option value="${driver.id_driver}" ${selected}>${driver.nama_driver}</option>`
                        //     );
                        // });

                        // Bersihkan select dan tambahkan opsi default terlebih dahulu
                        $('#driver1').html(`<option value="" disabled selected>Pilih Driver</option>`);

                        // Tambahkan opsi dari database
                        drivers.forEach(driver => {
                            let selected = (driver.id_driver == data.driver) ? 'selected' : '';
                            $('#driver1').append(
                                `<option value="${driver.id_driver}" ${selected}>${driver.nama_driver} - ${driver.no_hp}</option>`
                            );
                        });


                        // // Tambahkan opsi kendaraan dari database
                        // kendaraans.forEach(kendaraan => {
                        //     let selected = (kendaraan.id_kendaraan == data.no_polisi) ? 'selected' :
                        //         '';
                        //     $('#no_polisi1').append(
                        //         `<option value="${kendaraan.id_kendaraan}" ${selected}>${kendaraan.nopol} - ${kendaraan.tipe_kendaraan}</option>`
                        //     );
                        // });

                        // Bersihkan select dan tambahkan opsi default terlebih dahulu
                        $('#no_polisi1').html(
                            `<option value="" disabled selected>Pilih Kendaraan</option>`);

                        // Tambahkan opsi kendaraan dari database
                        kendaraans.forEach(kendaraan => {
                            let selected = (kendaraan.id_kendaraan == data.no_polisi) ? 'selected' :
                                '';
                            $('#no_polisi1').append(
                                `<option value="${kendaraan.id_kendaraan}" ${selected}>${kendaraan.nopol} - ${kendaraan.tipe_kendaraan}</option>`
                            );
                        });


                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        </script>

        <script>
            // $('#form_edit').on('submit', function(e) {
            //     e.preventDefault();

            //     const id = $('input[name="id"]').val(); // Ambil ID dari form
            //     const formData = $(this).serialize(); // Ambil semua data dari form

            //     $.ajax({
            //         url: `/pkendaraan/update/${id}`, // Endpoint untuk update
            //         method: 'PUT',
            //         data: formData,
            //         success: function(response) {
            //             if (response.redirect_url) {
            //                 // Redirect ke URL dari server
            //                 window.location.href = response.redirect_url;
            //             } else {
            //                 console.error('Redirect URL tidak ditemukan dalam respons.');
            //             }

            //         },
            //         error: function(xhr) {
            //             console.error('Error:', xhr.responseText);
            //             alert('Terjadi kesalahan saat mengirim data.');
            //         }
            //     });
            // });
            $('#form_edit').on('submit', function(e) {
                e.preventDefault();

                const id = $('input[name="id"]').val(); // Ambil ID dari form
                let formData = new FormData(this); // Gunakan FormData untuk mengirim file

                $.ajax({
                    url: `/pkendaraan/update/${id}`,
                    method: 'POST', // Gunakan POST agar bisa meng-handle file
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.redirect_url) {
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

                // Pastikan elemen hidden-section tersembunyi saat halaman dimuat
                $('.hidden-section').hide();

                // Pengecekan awal jika dalam mode edit
                const selectedGroupId = parseInt($('select[name="id_group"]').val());
                if (groupIdsToShow.includes(selectedGroupId)) {
                    $('.hidden-section').show(); // Tampilkan elemen
                    $('.hidden-section input, .hidden-section select').attr('required',
                        true); // Tambahkan validasi required
                }
                // Event listener untuk dropdown id_group
                $('select[name="id_group"]').on('change', function() {
                    const selectedGroupId = parseInt($(this).val());
                    if (groupIdsToShow.includes(selectedGroupId)) {
                        $('.hidden-section').show(); // Tampilkan elemen
                        //$('.hidden-section input, .hidden-section select').attr('required', true); // Tambahkan validasi required
                    } else {
                        $('.hidden-section').hide(); // Sembunyikan elemen
                        $('.hidden-section input, .hidden-section select').removeAttr(
                            'required'); // Hapus validasi required
                    }
                });
            });
        </script>
        <script>
            $('#exportBtn').on('click', function() {
                var formData = $('#exportForm').serialize(); // Get the form data

                // Trigger the Excel export request with the selected filters
                window.location.href = "{{ route('pkendaraan.export') }}?" + formData;

                // Use JavaScript to simulate a click event on the element with data-dismiss="modal"
                $('[data-dismiss="modal"]').click();
            });
            // });
        </script>
    </x-slot>
</x-layouts.app>
