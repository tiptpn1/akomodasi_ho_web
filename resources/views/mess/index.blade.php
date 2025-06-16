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
                <h3 class="mt-4">Master Mess</h3>

                @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                <!-- Button Trigger Modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#messModal">
                    Tambah Mess
                </button>

                <!-- Modal -->
                <div class="modal fade" id="messModal" tabindex="-1" aria-labelledby="messModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="messModalLabel">Tambah Mess Penginapan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('mess.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Nama Mess:</label>
                                        <input type="text" name="nama" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Lokasi:</label>
                                        <textarea name="lokasi" class="form-control" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Latitude:</label>
                                        <input type="text" name="lat" step="0.0000001" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Longitude:</label>
                                        <input type="text" name="lng" step="0.0000001" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi:</label>
                                        <textarea name="deskripsi" class="form-control"></textarea>
                                    </div>
                                    <!-- <div class="mb-3">
                                        <label class="form-label">Petugas:</label>
                                        <input type="text" name="cp" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">No. Petugas:</label>
                                        <input type="text" name="no_cp" class="form-control" required>
                                    </div> -->
                                    <div class="mb-3">
                                        <label class="form-label">Petugas & No. Petugas:</label>
                                        <div id="petugas-wrapper">
                                            <div class="row mb-2 align-items-center">
                                                <div class="col-5">
                                                    <input type="text" name="cp[]" class="form-control" placeholder="Nama Petugas" required>
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" name="no_cp[]" class="form-control" placeholder="No. Petugas" required>
                                                </div>
                                                <div class="col-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-petugas">×</button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary" id="add-petugas">+ Tambah Petugas</button>
                                    </div>


                                    <!-- Input Foto Utama (Hanya 1 file) -->
                                    <div class="mb-3">
                                        <label class="form-label">Foto Utama:</label>
                                        <input type="file" name="foto_utama" class="form-control" accept="image/*" required>
                                    </div>

                                    <!-- Input Foto Pendukung (Bisa lebih dari 1) -->
                                    <div class="mb-3">
                                        <label class="form-label">Foto Pendukung (Bisa lebih dari 1):</label>
                                        <input type="file" name="foto_pendukung[]" class="form-control" multiple accept="image/*">
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal fade" id="editMessModal" tabindex="-1" aria-labelledby="editMessModalLabel" aria-hidden="true" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editMessModalLabel">Edit Mess Penginapan</h5>
                                
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editMessForm" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                
                                    <input type="hidden" name="id" id="editMessId">
                
                                    <div class="mb-3">
                                        <label class="form-label">Nama Mess:</label>
                                        <input type="text" name="nama" id="editNama" class="form-control" required>
                                    </div>
                
                                    <div class="mb-3">
                                        <label class="form-label">Lokasi:</label>
                                        <textarea name="lokasi" id="editLokasi" class="form-control" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Latitude:</label>
                                        <input type="text" name="lat" id="editlat" step="0.0000001" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Longitude:</label>
                                        <input type="text" name="lng" id="editlng" step="0.0000001" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi:</label>
                                        <textarea name="deskripsi" id="editDeskripsi" class="form-control"></textarea>
                                    </div>
                                    <!-- <div class="mb-3">
                                        <label class="form-label">Petugas:</label>
                                        <input type="text" name="cp" id="editcp" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">No. Petugas:</label>
                                        <input type="text" name="no_cp" id="editno_cp" class="form-control" required>
                                    </div> -->
                                    <div class="mb-3">
                                        <label class="form-label">Petugas & No. Petugas:</label>
                                        <div id="petugas-wrapper1">
                                            <div class="row mb-2 align-items-center">
                                                <div class="col-5">
                                                    <input type="text" name="cp[]" class="form-control" placeholder="Nama Petugas" required>
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" name="no_cp[]" class="form-control" placeholder="No. Petugas" required>
                                                </div>
                                                <div class="col-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-petugas1">×</button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary" id="add-petugas1">+ Tambah Petugas</button>
                                    </div>
                
                                    <div class="mb-3">
                                        <label class="form-label">Foto Utama (Ganti jika ingin diubah):</label>
                                        <input type="file" name="foto_utama" class="form-control" accept="image/*">
                                        <img id="currentFotoUtama" src="" width="150" class="mt-2">
                                    </div>
                
                                    <div class="mb-3">
                                        <label class="form-label">Foto Pendukung (Tambah lebih dari 1):</label>
                                        <input type="file" name="foto_pendukung[]" class="form-control" multiple accept="image/*">
                                        <div id="currentFotoPendukung" class="mt-2"></div>
                                    </div>
                
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                    </div>
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
                                        <th>Nama Mess</th>
                                        <th>Lokasi</th>
                                        <th>Deskripsi</th>
                                        <th>Foto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($messes as $index =>$mess)
                                        <tr align="center">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- Edit Button -->
                                                    <button type="button" class="btn btn-sm btn-info"
                                                        data-toggle="modal" data-target="#editMessModal"
                                                        data-id="{{ $mess->id ?? '-' }}"
                                                        id="btnEdit" @if ($mess->status==0)disabled @endif >
                                                        <i class="fa fa-pencil" style="color: white;"></i>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form action="{{ route('mess.destroy', $mess->id) }}" method="POST" class="delete-mess-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" @if ($mess->status==0)disabled @endif>
                                                            <i class="fa fa-close"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('mess.aktif', $mess->id) }}" method="POST" class="active-mess-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-success" @if ($mess->status==1)disabled @endif>
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>
                                            <td>{{ $mess->nama }}</td>
                                            <td>{{ $mess->lokasi }}</td>
                                            <td>{{ $mess->deskripsi }}</td>
                                            <td>@foreach ($mess->photos as $photo)
                                                {{-- <img src="{{ asset('storage/' . $photo->foto) }}" width="150"> --}}
                                                <img src="{{ asset($photo->foto) }}" width="150" height="100">

                                            @endforeach
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
            $(document).on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                let url = "{{ url('mess/edit') }}/" + id ;
                $.get(url, function(data) {
                    // console.log(data.petugas); 
                    // console.log('Data Petugas:', data.petugas);
                    // console.log('Isi Petugas:', Array.isArray(data.petugas));

                    $('#editMessId').val(data.id);
                    $('#editNama').val(data.nama);
                    $('#editLokasi').val(data.lokasi);
                    $('#editlat').val(data.lat);
                    $('#editlng').val(data.lng);
                    $('#editDeskripsi').val(data.deskripsi);
                    // console.log('Sebelum append, petugas-wrapper:', $('#petugas-wrapper').html());
                    $('#petugas-wrapper1').html('');
                    // console.log('Sesudah clear petugas-wrapper:', $('#petugas-wrapper').html());

                    data.petugas.forEach(function(p, index) {
                        let row = `
                            <div class="row mb-2 align-items-center">
                                <div class="col-5">
                                    <input type="text" name="cp[]" class="form-control" placeholder="Nama Petugas" value="${p.nama_petugas}" required>
                                </div>
                                <div class="col-5">
                                    <input type="text" name="no_cp[]" class="form-control" placeholder="No. Petugas" value="${p.no_petugas}" required>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-petugas1">×</button>
                                </div>
                            </div>`;
                        // console.log('Row to append:', row);
                        $('#petugas-wrapper1').append(row);
                    });
                    
                    // Cek apakah ada foto utama
                    let fotoUtama = data.photos.find(photo => photo.is_utama == '1');
                    if (fotoUtama) {
                        $('#currentFotoUtama').attr('src', "/" + fotoUtama.foto);
                    } else {
                        $('#currentFotoUtama').attr('src', '');
                    }

                    // Kosongkan foto pendukung sebelumnya
                    $('#currentFotoPendukung').html('');

                    // // Tambahkan foto pendukung
                    // data.photos.forEach(photo => {
                    //     if (photo.is_utama != '1') {
                    //         $('#currentFotoPendukung').append('<img src="/' + photo.foto + '" width="100" class="me-2">');
                    //     }
                    // });
                    data.photos.forEach(photo => {
                        if (photo.is_utama != '1') {
                            const fotoHtml = `
                                <div class="d-inline-block position-relative me-2 mb-2">
                                    <img src="/${photo.foto}" width="100" class="border">
                                    <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-foto" data-id="${photo.id}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            `;
                            $('#currentFotoPendukung').append(fotoHtml);
                        }
                    });

                    $('#editMessForm').attr('action', "{{ url('mess/update') }}/" + id);
                    // $('#editMessModal').modal('show'); // Tampilkan modal edit

                    $('#currentFotoPendukung').on('click', '.delete-foto', function () {
                        const photoId = $(this).data('id');

                        if (confirm('Yakin ingin menghapus foto ini?')) {
                            $.ajax({
                                url: '/mess/destroy-photo-mess/' + photoId,
                                type: 'DELETE',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                    $(`button[data-id="${photoId}"]`).closest('div').remove();
                                },
                                error: function () {
                                    alert('Gagal menghapus foto.');
                                }
                            });
                        }
                    });
                });
            });
        </script>
        
        <script>
            $(document).on('submit', '.delete-mess-form', function (e) {
                e.preventDefault();
                var form = this;

                Swal.fire({
                    title: "Yakin ingin menonaktifkan mess?",
                    text: "Seluruh kamar pada mess ini akan tidak bisa digunakan",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Nonaktifkan!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

        </script>
        <script>
            $(document).on('submit', '.active-mess-form', function (e) {
                e.preventDefault();
                var form = this;

                Swal.fire({
                    title: "Yakin ingin mengaktifkan mess?",
                    text: "Seluruh kamar akan bisa digunakan kembali",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Aktifkan!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.getElementById('add-petugas').addEventListener('click', function () {
                const wrapper = document.getElementById('petugas-wrapper');
                const row = document.createElement('div');
                row.classList.add('row', 'mb-2', 'align-items-center');
                row.innerHTML = `
                    <div class="col-5">
                        <input type="text" name="cp[]" class="form-control" placeholder="Nama Petugas" required>
                    </div>
                    <div class="col-5">
                        <input type="text" name="no_cp[]" class="form-control" placeholder="No. Petugas" required>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger btn-sm remove-petugas">×</button>
                    </div>
                `;
                wrapper.appendChild(row);
            });

            // Event delegation untuk tombol hapus
            document.getElementById('petugas-wrapper').addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-petugas')) {
                    e.target.closest('.row').remove();
                }
            });
            </script>
            <script>
            document.getElementById('add-petugas1').addEventListener('click', function () {
                const wrapper = document.getElementById('petugas-wrapper1');
                const row = document.createElement('div');
                row.classList.add('row', 'mb-2', 'align-items-center');
                row.innerHTML = `
                    <div class="col-5">
                        <input type="text" name="cp[]" class="form-control" placeholder="Nama Petugas" required>
                    </div>
                    <div class="col-5">
                        <input type="text" name="no_cp[]" class="form-control" placeholder="No. Petugas" required>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger btn-sm remove-petugas1">×</button>
                    </div>
                `;
                wrapper.appendChild(row);
            });

            // Event delegation untuk tombol hapus
            document.getElementById('petugas-wrapper1').addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-petugas1')) {
                    e.target.closest('.row').remove();
                }
            });
            </script>

    </x-slot>
</x-layouts.app>