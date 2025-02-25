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
                <h3 class="mt-4">Master Kendaraan</h3>
                @if (!in_array(Auth::user()->master_hak_akses_id, [5, 6]))
                    <button id="btnTambah" type="button" data-toggle="modal" data-target="#tambah"
                        class="btn btn-primary btn-sm">Tambah Data</button>

                    <a href="{{ route('masterkendaraan.export') }}" class="btn btn-warning btn-sm">Export to Excel</a>
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
                                <h4 class="modal-title" id="myModalLabel">Tambah Kendaraan</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div id="kaskecil" class="form-group container-fluid">
                                    <form id="form_tambah" class="form-horizontal calender" role="form"
                                        enctype="multipart/form-data" method="POST" action="/masterkendaraan/store">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>No Pol <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nopol" id='nopol'
                                                    required>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <b>Tipe Kendaraan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="tipe_kendaraan"
                                                    id='tipe_kendaraan' required>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <b>Kepemilikan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="kepemilikan" required>
                                            </div>
                                        </div>

                                        <!-- Modal Footer (buttons) -->
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

                <!-- Modal Edit -->
                <div id="edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Edit Data</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div id="kaskeciledit" class="form-group container-fluid">
                                    <form id="form_edit" class="form-horizontal calender" role="form"
                                        enctype="multipart/form-data" method="POST">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="id">
                                            <div class="form-group col-md-6">
                                                <b>No Pol <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nopol1"
                                                    id='nopol1' required>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <b>Tipe Kendaraan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="tipe_kendaraan1"
                                                    id='tipe_kendaraan1' required>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <b>Kepemilikan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="kepemilikan1"
                                                    id='kepemilikan1' required>
                                            </div>
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
                                                <input type="date" class="form-control" id="tgl_awal"
                                                    name="tgl_awal">
                                            </div>
                                            <div class="form-group">
                                                <label for="nama_group">Divisi</label>
                                                <!-- <input type="text" class="form-control" id="nama_group" name="nama_group"> -->
                                                <select class="form-control" name="id_divisi">

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tgl_pengajuan_akhir">Tanggal Akhir</label>
                                                <input type="date" class="form-control" id="tgl_akhir"
                                                    name="tgl_akhir">
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
                                        <th>No Pol</th>
                                        <th>Tipe Kendaraan</th>
                                        <th>Kepemilikan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kendaraans as $index => $kendaraan)
                                        <tr align="center">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- Edit Button -->
                                                    <button type="button" class="btn btn-sm btn-info"
                                                        data-toggle="modal" data-target="#edit"
                                                        data-id="{{ $kendaraan->id_kendaraan ?? '-' }}"
                                                        id="btnEdit">
                                                        <i class="fa fa-pencil" style="color: white;"></i>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form
                                                        action="{{ route('masterkendaraan.destroy', $kendaraan->id_kendaraan) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"><i
                                                                class="fa fa-trash"></i></button>
                                                    </form>

                                                </div>
                                            </td>
                                            <td>{{ $kendaraan->nopol }}</td>
                                            <td>{{ $kendaraan->tipe_kendaraan }}</td>
                                            <td>{{ $kendaraan->kepemilikan }}</td>
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
                    url: `/masterkendaraan/edit/${id}`, // Endpoint untuk mendapatkan data
                    method: 'GET',
                    success: function(data) {

                        // Isi modal dengan data dari server
                        $('input[name="id"]').val(data.id_kendaraan);
                        $('input[name="nopol1"]').val(data.nopol);
                        $('input[name="tipe_kendaraan1"]').val(data.tipe_kendaraan);
                        $('input[name="kepemilikan1"]').val(data.kepemilikan);

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
                    url: `/masterkendaraan/update/${id}`, // Endpoint untuk update
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
            $(document).on("click", ".btnDelete", function() {
                var id = $(this).data("id"); // Ambil ID kendaraan
                var url = "/masterkendaraan/destroy/" + id; // Sesuaikan dengan route yang dibuat

                if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}" // Laravel CSRF Protection
                        },
                        success: function(response) {
                            // alert(response.message);
                            location.reload(); // Refresh halaman setelah hapus
                        },
                        error: function(xhr) {
                            alert("Terjadi kesalahan: " + xhr.responseJSON.message);
                        }
                    });
                }
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
