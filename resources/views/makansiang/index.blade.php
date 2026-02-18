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
                border-color: #2471a3 !important; /* Warna border lebih gelap */
            }

            .btn-info:hover {
                background-color: #1f6690 !important; /* Warna lebih gelap saat hover */
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
                <h3 class="mt-4">Pengajuan Makan Siang</h3>
                @if (!in_array(Auth::user()->master_hak_akses_id, [5, 6]))
                <button id="btnTambah" type="button" data-toggle="modal" data-target="#tambah" class="btn btn-primary btn-sm">Tambah Data</button>
                <button id="btnExport" type="button" data-toggle="modal" data-target="#exportModal" class="btn btn-warning btn-sm">Export Data</button>
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
                                <h4 class="modal-title" id="myModalLabel">Tambah Pengajuan Makan Siang</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div id="kaskecil" class="form-group container-fluid">
                                    <form id="form_tambah" class="form-horizontal calender" role="form" enctype="multipart/form-data" method="POST" action="/makansiang/store">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Tanggal Pengajuan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="tgl_pengajuan" id="tgl_pengajuan" required>
                                            </div>
                                            
                                            <div class="form-group col-md-6">
                                                <b>Nama Yang Mengajukan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nama_pengaju" id='ajukan' required>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Divisi <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="divisi" value="{{ $divisi }}" required readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Jumlah Karyawan <span class="text-danger">*</span></b>
                                                <input type="number" class="form-control" name="jlh_karyawan" id="jlh_karyawan" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Kadiv <span class="text-danger">*</span></b>
                                                <div>
                                                    <label>
                                                        <input type="radio" name="kadiv" value="1" required checked> Ya
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="kadiv" value="0" required> Tidak
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Jumlah Makan Siang </b><span class="text-success">(otomatis)</span>
                                                <input type="number" class="form-control" name="jlh_makansiang" id="jlh_makansiang" required readonly>
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
                                            <input type="hidden" name="id">
                                            <div class="form-group col-md-6">
                                                <b>Tanggal Pengajuan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="tgl_pengajuan1" id="tgl_pengajuan1" required>
                                            </div>
                                            
                                            <div class="form-group col-md-6">
                                                <b>Nama Yang Mengajukan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nama_pengaju1" id='ajukan1' required>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Divisi <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="divisi1" required readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Jumlah Karyawan <span class="text-danger">*</span></b>
                                                <input type="number" class="form-control" name="jlh_karyawan1" id="jlh_karyawan1" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Kadiv <span class="text-danger">*</span></b>
                                                <div>
                                                    <label>
                                                        <input type="radio" name="kadiv1" value="1" required > Ya
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="kadiv1" value="0" required> Tidak
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Jumlah Makan Siang </b><span class="text-success">(otomatis)</span>
                                                <input type="number" class="form-control" name="jlh_makansiang1" id="jlh_makansiang1" required readonly>
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
                                <h5 class="modal-title" id="exportModalLabel">Export Makan Siang</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="exportForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tgl_pengajuan_awal">Tanggal Awal</label>
                                                <input type="date" class="form-control" id="tgl_awal" name="tgl_awal">
                                            </div>
                                            <div class="form-group">
                                                <label for="nama_group">Divisi</label>
                                                <!-- <input type="text" class="form-control" id="nama_group" name="nama_group"> -->
                                                <select class="form-control" name="id_divisi">
                                                @if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga']))
                                                    <option value="" disabled selected>Pilih Divisi</option>
                                                    <option value='all'>Seluruh Divisi</option>
                                                    @foreach ($get_divisi as $data_divisi)
                                                    <option value='{{ $data_divisi->master_bagian_nama }}'>{{ $data_divisi->master_bagian_nama }}</option>
                                                    @endforeach
                                                    @else
                                                    <option value="{{ $divisi }}">{{ $divisi }}</option>
                                                @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tgl_pengajuan_akhir">Tanggal Akhir</label>
                                                <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir">
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
                                        <th>Nama Peminta</th>
                                        <th>Tanggal Permintaan</th>
                                        <th>Divisi</th>
                                        <th>Jumlah Karyawan</th>
                                        <th>Kadiv</th>
                                        <th>Jumlah Makan Siang</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($makansiang as $index => $item)
                                    <tr align="center">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @php
                                                    $today = \Carbon\Carbon::today();
                                                    $requestDate = \Carbon\Carbon::parse($item->tgl_permintaan);
                                                    $isDisabled = $requestDate->lte($today) || $item->status == 0 || $item->status == 2; // Disabled jika tgl_permintaan <= hari ini atau status = 0 (Canceled) atau status = 2 (Approved)
                                                    $isPending = $item->status == 1; // Hanya aktif jika status = 1
                                                @endphp
                                                <!-- Edit Button -->
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-info" 
                                                    data-toggle="modal" 
                                                    data-target="#edit" 
                                                    data-id="{{ $item->id ?? '-' }}" 
                                                    id="btnEdit" 
                                                    @if ($isDisabled && !in_array(Auth::user()->master_user_nama, ['asisten_ga'])) disabled @endif>
                                                    <i class="fa fa-pencil" style="color: white;"></i>
                                                </button>
                                                
                                                <!-- Delete Button -->
                                                <form action="{{ route('makansiang.destroy', $item->id ?? '-') }}" method="POST" onsubmit="return confirm('Apakah yakin cancel data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" @if ($isDisabled) disabled @endif>
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                
                                                <!-- Approve and Reject Buttons -->
                                                @if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga']))
                                                <!-- Approve Button -->
                                                <form action="{{ route('makansiang.approve', $item->id ?? '-') }}" method="POST" onsubmit="return confirm('Apakah yakin menyetujui data ini?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" @if (!$isPending) disabled @endif>
                                                        <i class="fa fa-check"></i> <!-- Icon centang -->
                                                    </button>
                                                </form>

                                                <!-- Reject Button -->
                                                <form action="{{ route('makansiang.reject', $item->id ?? '-') }}" method="POST" onsubmit="return confirm('Apakah yakin menolak data ini?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" @if (!$isPending) disabled @endif>
                                                        <i class="fa fa-times"></i> <!-- Icon silang -->
                                                    </button>
                                                </form>
                                                @endif

                                            </div>
                                        </td>
                                        <td>{{ $item->nama_pic ?? '-' }}</td>
                                        <!-- <td align="center">{{ $item->tgl_permintaan ?? '-' }}</td> -->
                                        <td align="center">
                                            {{ \Carbon\Carbon::parse($item->tgl_permintaan)->format('d-m-Y') ?? '-' }}
                                        </td>
                                        <td align="center">{{ $item->divisi ?? '-' }}</td>
                                        <td align="center">{{ $item->jlh_karyawan ?? '-' }}</td>
                                        <td align="center">
                                            @if ($item->kadiv == 1)
                                                <i class="fa fa-check text-success" title="Ya"></i> <!-- Icon centang -->
                                            @else
                                                <i class="fa fa-times text-danger" title="Tidak"></i> <!-- Icon silang -->
                                            @endif
                                        </td>
                                        <td align="center">{{ $item->jlh_makan ?? '-' }}</td>
                                        <td align="center">
                                            @if ($item->status == 0)
                                                <span class="text-warning">Canceled</span> <!-- Warna orange -->
                                            @elseif ($item->status == 1)
                                                <span class="text-primary">Pengajuan Divisi</span> <!-- Warna biru -->
                                            @elseif ($item->status == 2)
                                                <span class="text-success">Approved</span> <!-- Warna hijau -->
                                            @elseif ($item->status == 3)
                                                <span class="text-danger">Rejected</span> <!-- Warna merah -->
                                            @else
                                                <span class="text-muted">-</span> <!-- Warna abu-abu untuk status tidak terdefinisi -->
                                            @endif
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
            $(document).ready(function () {
                // Set tanggal hari ini pada input secara langsung
                $('#tgl_pengajuan').val(moment().add(1, 'days').format('DD-MM-YYYY'));
        
                // Inisialisasi datepicker setelah nilai input di-set
                $('#tgl_pengajuan').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate: moment().add(1, 'days'), // Tanggal default adalah hari ini
                    minDate: moment().add(1, 'days'), // Blokir tanggal sebelumnya
                    locale: {
                        format: 'DD-MM-YYYY' // Format tanggal
                    }
                });
        
                // Mengatur agar input hanya bisa dipilih (tidak bisa diketik)
                $('#tgl_pengajuan').prop('readonly', true);  // Menonaktifkan input manual
            });
        </script>
        <script>
            $(document).ready(function () {
                // Set tanggal besok pada input secara langsung
                $('#tgl_pengajuan1').val(moment().add(1, 'days').format('DD-MM-YYYY'));
        
                // Inisialisasi datepicker setelah nilai input di-set
                $('#tgl_pengajuan1').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate: moment().add(1, 'days'), // Tanggal default adalah besok
                    minDate: moment().add(1, 'days'), // Blokir tanggal hari ini dan sebelumnya
                    locale: {
                        format: 'DD-MM-YYYY' // Format tanggal
                    }
                });
        
                // Mengatur agar input hanya bisa dipilih (tidak bisa diketik)
                $('#tgl_pengajuan1').prop('readonly', true);  // Menonaktifkan input manual
            });
        </script>
        <script>
        var today = new Date();
        var tomorrow = new Date(today);
        tomorrow.setDate(today.getDate()+1); // Tetap pada hari ini

        // Ambil tanggal, bulan, dan tahun dari objek Date
        var day = ("0" + tomorrow.getDate()).slice(-2); // Ambil 2 digit angka
        var month = ("0" + (tomorrow.getMonth() + 1)).slice(-2); // Bulan dimulai dari 0, jadi +1
        var year = tomorrow.getFullYear();

        var dateString = day + "-" + month + "-" + year; // Format DD-MM-YYYY

        document.getElementById("btnTambah").addEventListener("click", function() {
            resetForm();
            document.getElementById("tgl_pengajuan").value = dateString; // Isi dengan tanggal format DD-MM-YYYY
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ambil elemen input
            const jlhKaryawanInput = document.getElementById('jlh_karyawan');
            const kadivInputs = document.querySelectorAll('input[name="kadiv"]');
            const jlhMakansiangInput = document.getElementById('jlh_makansiang');

            // Fungsi untuk menghitung jumlah makan siang
            function calculateJumlahMakanSiang() {
                const jlhKaryawan = parseInt(jlhKaryawanInput.value) || 0; // Nilai jumlah karyawan
                const kadivValue = parseInt(document.querySelector('input[name="kadiv"]:checked').value) || 0; // Nilai kadiv (1 atau 0)
                const totalMakanSiang = jlhKaryawan + kadivValue; // Perhitungan
                jlhMakansiangInput.value = totalMakanSiang; // Isi input jumlah makan siang
            }

            // Tambahkan event listener ke input jumlah karyawan
            jlhKaryawanInput.addEventListener('input', calculateJumlahMakanSiang);

            // Tambahkan event listener ke radio button kadiv
            kadivInputs.forEach(input => {
                input.addEventListener('change', calculateJumlahMakanSiang);
            });

            // Hitung otomatis saat halaman pertama kali dimuat
            calculateJumlahMakanSiang();
        });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ambil elemen input
        const jlhKaryawanInput1 = document.getElementById('jlh_karyawan1');
        const kadivInputs1 = document.querySelectorAll('input[name="kadiv1"]');
        const jlhMakansiangInput1 = document.getElementById('jlh_makansiang1');

        // Fungsi untuk menghitung jumlah makan siang
        function calculateJumlahMakanSiang1() {
            const jlhKaryawan1 = parseInt(jlhKaryawanInput1.value) || 0; // Nilai jumlah karyawan
            const kadivValue1 = parseInt(document.querySelector('input[name="kadiv1"]:checked').value) || 0; // Nilai kadiv (1 atau 0)
            const totalMakanSiang1 = jlhKaryawan1 + kadivValue1; // Perhitungan
            jlhMakansiangInput1.value = totalMakanSiang1; // Isi input jumlah makan siang
        }

        // Tambahkan event listener ke input jumlah karyawan
        jlhKaryawanInput1.addEventListener('input', calculateJumlahMakanSiang1);

        // Tambahkan event listener ke radio button kadiv
        kadivInputs1.forEach(input => {
            input.addEventListener('change', calculateJumlahMakanSiang1);
        });

        // Hitung otomatis saat halaman pertama kali dimuat
        calculateJumlahMakanSiang1();
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
                    url: `/makansiang/edit/${id}`, // Endpoint untuk mendapatkan data
                    method: 'GET',
                    success: function(data) {
                        // Ambil data.tgl_permintaan yang berformat YYYY-MM-DD
                        var tglPermintaan = data.tgl_permintaan;
                        // Pisahkan string berdasarkan "-"
                        var parts = tglPermintaan.split("-"); // ["YYYY", "MM", "DD"]
                        // Susun ulang ke format DD-MM-YYYY
                        var formattedDate = parts[2] + "-" + parts[1] + "-" + parts[0];

                        // Isi modal dengan data dari server
                        $('input[name="id"]').val(data.id);
                        $('input[name="tgl_pengajuan1"]').val(formattedDate);
                        $('input[name="nama_pengaju1"]').val(data.nama_pic);
                        $('input[name="divisi1"]').val(data.divisi);
                        $('input[name="jlh_karyawan1"]').val(data.jlh_karyawan);
                        $('input[name="jlh_makansiang1"]').val(data.jlh_makan);

                        // Atur radio button Kadiv sesuai data
                        if (data.kadiv == 1) {
                            $('input[name="kadiv1"][value="1"]').prop('checked', true);
                        } else if (data.kadiv == 0) {
                            $('input[name="kadiv1"][value="0"]').prop('checked', true);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        </script>
        <script>
            $('#form_edit').on('submit', function(e) {
                e.preventDefault();

                const id = $('input[name="id"]').val(); // Ambil ID dari form
                const formData = $(this).serialize(); // Ambil semua data dari form

                $.ajax({
                    url: `/makansiang/update/${id}`, // Endpoint untuk update
                    method: 'PUT',
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

                // Pastikan elemen hidden-section tersembunyi saat halaman dimuat
                $('.hidden-section').hide();

                // Pengecekan awal jika dalam mode edit
                const selectedGroupId = parseInt($('select[name="id_group"]').val());
                if (groupIdsToShow.includes(selectedGroupId)) {
                    $('.hidden-section').show(); // Tampilkan elemen
                    $('.hidden-section input, .hidden-section select').attr('required', true); // Tambahkan validasi required
                }
                // Event listener untuk dropdown id_group
                $('select[name="id_group"]').on('change', function() {
                    const selectedGroupId = parseInt($(this).val());
                    if (groupIdsToShow.includes(selectedGroupId)) {
                        $('.hidden-section').show(); // Tampilkan elemen
                        //$('.hidden-section input, .hidden-section select').attr('required', true); // Tambahkan validasi required
                    } else {
                        $('.hidden-section').hide(); // Sembunyikan elemen
                        $('.hidden-section input, .hidden-section select').removeAttr('required'); // Hapus validasi required
                    }
                });
            });
        </script>
        <script>
            $('#exportBtn').on('click', function() {
                var formData = $('#exportForm').serialize(); // Get the form data

                // Trigger the Excel export request with the selected filters
                window.location.href = "{{ route('makansiang.export') }}?" + formData;

                // Use JavaScript to simulate a click event on the element with data-dismiss="modal"
                $('[data-dismiss="modal"]').click();
            });
            // });
        </script>
    </x-slot>
</x-layouts.app>