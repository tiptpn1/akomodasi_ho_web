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
                <h3 class="mt-4">Master Driver</h3>
                @if (!in_array(Auth::user()->master_hak_akses_id, [5, 6]))
                    <button id="btnTambah" type="button" data-toggle="modal" data-target="#tambah"
                        class="btn btn-primary btn-sm">Tambah Data</button>

                    <a href="{{ route('masterdriver.export') }}" class="btn btn-warning btn-sm">Export to Excel</a>
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
                                <h4 class="modal-title" id="myModalLabel">Tambah Driver</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div id="kaskecil" class="form-group container-fluid">
                                    <form id="form_tambah" class="form-horizontal calender" role="form"
                                        enctype="multipart/form-data" method="POST" action="/masterdriver/store">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Nama Driver <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nama_driver" required>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <b>Nomor HP <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="no_hp" required>
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
                                            <div class="form-group col-md-6">
                                                <input type="hidden" name="id">
                                                <b>Nama Driver <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nama_driver1"
                                                    id='nama_driver1' required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Nomor HP <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="no_hp1"
                                                    id="no_hp1" required>
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

                <div class="box box-primary mt-3">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTables-kaskecil">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No</th>
                                        <th class="text-center" style="width: 80px;">Aksi</th>
                                        <th>Nama Driver</th>
                                        <th>No HP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($drivers as $index => $driver)
                                        <tr align="center">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- Edit Button -->
                                                    <button type="button" class="btn btn-sm btn-info"
                                                        data-toggle="modal" data-target="#edit"
                                                        data-id="{{ $driver->id_driver ?? '-' }}" id="btnEdit">
                                                        <i class="fa fa-pencil" style="color: white;"></i>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form
                                                        action="{{ route('masterdriver.destroy', $driver->id_driver) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"><i
                                                                class="fa fa-trash"></i></button>
                                                    </form>

                                                </div>
                                            </td>
                                            <td>{{ $driver->nama_driver }}</td>
                                            <td>{{ $driver->no_hp }}</td>
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
                    url: `/masterdriver/edit/${id}`, // Endpoint untuk mendapatkan data
                    method: 'GET',
                    success: function(data) {

                        // Isi modal dengan data dari server
                        $('input[name="id"]').val(data.id_driver);
                        $('input[name="nama_driver1"]').val(data.nama_driver);
                        $('input[name="no_hp1"]').val(data.no_hp);

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
                    url: `/masterdriver/update/${id}`, // Endpoint untuk update
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
                var url = "/masterdriver/destroy/" + id; // Sesuaikan dengan route yang dibuat

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
