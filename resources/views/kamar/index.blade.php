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
            .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
                background-color: #426CEAFF; /* Bootstrap primary */
                border: none;
                color: rgb(0, 0, 0);
                font-size: 0.85rem;
                padding: 2px 8px;
                border-radius: 20px;
            }

        </style>
        <style>
            /* Fix Select2 di dalam modal Bootstrap 5 */
            .select2-container {
                z-index: 999999 !important;
            }
            .select2-container .select2-selection--multiple {
                min-height: 38px; /* agar sejajar dengan input lainnya */
                border: 1px solid #ced4da;
                border-radius: 0.375rem;
                padding: 0.375rem 0.75rem;
            }
            </style>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
        <!-- CSS Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- JavaScript Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Select2 Theme for Bootstrap 4 -->
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />



    </x-slot>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Master Kamar</h3>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kamarModal">
                    Tambah Kamar
                </button>

                <!-- Modal -->
                <div class="modal fade" id="kamarModal" tabindex="-1" aria-labelledby="kamarModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="kamarModalLabel">Tambah Mess Penginapan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('kamar.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Nama Mess:</label>
                                        <select class="form-control" name="mess_id" >
                                            <option value="" disabled selected>Pilih Mess</option>
                                            @foreach ($mess as $index =>$mess)
                                            <option value='{{ $mess->id }}'>{{ $mess->nama }}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nama Kamar :</label>
                                        <input type="text" name="nama_kamar" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Kapasitas:</label>
                                        <input type="text" name="kapasitas" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Peruntukan:</label>
                                        <!-- {{-- <select class="form-control" name="peruntukan" > --}} -->
                                            <select name="peruntukan[]" class="form-control select2 select2-tambah" multiple required>
                                            <!-- {{-- <option value="" disabled selected>Pilih Peruntukan</option> --}} -->
                                            @foreach ($jabatan as $index =>$jabatan)
                                            <option value='{{ $jabatan->id }}'>{{ $jabatan->jabatan }}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Fasilitas:</label>
                                        <textarea name="fasilitas" class="form-control"></textarea>
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
                
                <div class="modal fade" id="editKamarModal" tabindex="-1" aria-labelledby="editKamarModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editKamarModalLabel">Edit Kamar</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editKamarForm" action="" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="edit_kamar_id" name="id">
                
                                    <div class="mb-3">
                                        <label class="form-label">Nama Mess:</label>
                                        <select class="form-control" name="mess_id" id="edit_mess_id">
                                            <option value="" disabled>Pilih Mess</option>
                                            
                                        </select>
                                    </div>
                
                                    <div class="mb-3">
                                        <label class="form-label">Nama Kamar:</label>
                                        <input type="text" name="nama_kamar" id="edit_nama_kamar" class="form-control" required>
                                    </div>
                
                                    <div class="mb-3">
                                        <label class="form-label">Kapasitas:</label>
                                        <input type="text" name="kapasitas" id="edit_kapasitas" class="form-control" required>
                                    </div>
                
                                    <div class="mb-3">
                                        <label class="form-label">Peruntukan:</label>
                                        <!-- {{-- <select class="form-control" name="peruntukan" id="edit_peruntukan"> --}} -->
                                            <select class="form-control select2 select2-edit" name="peruntukan[]" id="edit_peruntukan" multiple="multiple" required>
                                            <!-- {{-- <option value="" disabled>Pilih Peruntukan</option> --}} -->
                                            
                                        </select>
                                    </div>
                
                                    <div class="mb-3">
                                        <label class="form-label">Fasilitas:</label>
                                        <textarea name="fasilitas" id="edit_fasilitas" class="form-control"></textarea>
                                    </div>
                
                                    <div class="mb-3">
                                        <label class="form-label">Foto Utama:</label>
                                        <input type="file" name="foto_utama" class="form-control" accept="image/*">
                                        <img id="preview_foto_utama" src="" alt="Foto Utama" class="img-fluid mt-2" style="max-width: 100px;">
                                    </div>
                
                                    <div class="mb-3">
                                        <label class="form-label">Foto Pendukung (Bisa lebih dari 1):</label>
                                        <input type="file" name="foto_pendukung[]" class="form-control" multiple accept="image/*">
                                        <div id="preview_foto_pendukung" class="mt-2"></div>
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
                                        <th>Nama Kamar</th>
                                        <th>Kapasitas</th>
                                        <th width="10%">Peruntukan</th>
                                        <th>Fasilitas</th>
                                        <th>Foto Mess</th>
                                        <th>Foto Kamar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rooms as $index =>$kamar)
                                        <tr align="center">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- Edit Button -->
                                                    {{-- <button class="btn btn-primary btn-sm btn-edit" data-id="{{ $kamar->id }}"> --}}
                                                        <button type="button" class="btn btn-sm btn-info"
                                                        data-toggle="modal" data-target="#editKamarModal"
                                                        data-id="{{ $kamar->id ?? '-' }}"
                                                        id="btnEdit" @if ($kamar->status==0)disabled @endif>
                                                        <i class="fa fa-pencil" style="color: white;"></i>
                                                    </button>
                                                        

                                                    <!-- Delete Button -->
                                                    <form action="{{ route('kamar.destroy', $kamar->id) }}" method="POST" class="delete-mess-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" @if ($kamar->status==0)disabled @endif>
                                                            <i class="fa fa-close"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('kamar.aktif', $kamar->id) }}" method="POST" class="active-mess-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-success" @if ($kamar->status==1)disabled @endif>
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>
                                            <td>{{ $kamar->mess->nama ?? '-' }}</td>
                                            <td>{{ $kamar->nama_kamar }} <br>
                                                @if ($kamar->reviews_avg_rating)
                                                    <span>
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= floor($kamar->reviews_avg_rating))
                                                                <i class="fas fa-star text-warning"></i> {{-- Bintang penuh --}}
                                                            @elseif ($i - 0.5 <= $kamar->reviews_avg_rating)
                                                                <i class="fas fa-star-half-alt text-warning"></i> {{-- Bintang setengah --}}
                                                            @else
                                                                <i class="far fa-star text-warning"></i> {{-- Bintang kosong --}}
                                                            @endif
                                                        @endfor
                                                    </span>
                                                    ({{ number_format($kamar->reviews_avg_rating, 1) }})
                                                @else
                                                    <span class="text-muted">Belum ada rating</span>
                                                @endif
                                            </td>
                                            <td>{{ $kamar->kapasitas }}</td>
                                            <td>{{ $kamar->peruntukan_teks }}</td>
                                            <td>{{ $kamar->fasilitas }}</td>
                                            <td>
                                                @php
                                                    $fotoUtama = $kamar->mess->photos->firstWhere('is_utama', 1);
                                                @endphp
                                                @if ($fotoUtama)
                                                    <img src="{{ asset($fotoUtama->foto) }}" width="100" height="70">
                                                @endif
                                            </td>
                                            <td>
                                                @foreach ($kamar->photos as $photo)
                                                    <img src="{{ asset($photo->foto) }}" width="100" height="70">
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
                $('.select2-tambah').select2({
                    // dropdownParent: $('#kamarModal'),
                    placeholder: "Pilih Peruntukan",
                    width: '100%',
                    theme: 'bootstrap-5'
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $('.select2-edit').select2({
                    dropdownParent: $('#editKamarModal'),
                    width: '100%',
                    placeholder: 'Pilih Peruntukan'
                });
            });
        </script>
        
        
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
    let url = "{{ url('kamar/edit') }}/" + id;

    $.get(url, function(data) {
        let kamar = data.kamar;
        let messList = data.mess_list;
        let jabatanList = data.jabatan_list;

        // Isi nilai input
        $('#edit_kamar_id').val(kamar.id);
        $('#edit_nama_kamar').val(kamar.nama_kamar);
        $('#edit_kapasitas').val(kamar.kapasitas);
        $('#edit_fasilitas').val(kamar.fasilitas);

        // Isi dropdown Mess
        $('#edit_mess_id').empty().append('<option value="" disabled>Pilih Mess</option>');
        $.each(messList, function(index, mess) {
            $('#edit_mess_id').append(`<option value="${mess.id}">${mess.nama}</option>`);
        });
        $('#edit_mess_id').val(kamar.mess_id).trigger('change');

        // Isi dropdown Jabatan
        $('#edit_peruntukan').empty().append('<option value="" disabled>Pilih Peruntukan</option>');
        $.each(jabatanList, function(index, jabatan) {
            $('#edit_peruntukan').append(`<option value="${jabatan.id}">${jabatan.jabatan}</option>`);
        });
        // $('#edit_peruntukan').val(kamar.peruntukan).trigger('change');
        let peruntukanArray = [];
        try {
            peruntukanArray = JSON.parse(kamar.peruntukan);
        } catch(e) {
            peruntukanArray = kamar.peruntukan.split(','); // fallback ke koma
        }

        $('#edit_peruntukan').val(peruntukanArray).trigger('change');

        // Cek apakah ada foto utama
        let fotoUtama = kamar.photos.find(photo => photo.is_utama == '1'); 
        if (fotoUtama) {
            $('#preview_foto_utama').attr('src', "/" + fotoUtama.foto);
        } else {
            $('#preview_foto_utama').attr('src', '');
        }

        // Kosongkan foto pendukung sebelumnya
        $('#preview_foto_pendukung').html('');

        // Tambahkan foto pendukung
        kamar.photos.forEach(photo => {
            if (photo.is_utama != '1') {
                const fotoHtml = `
                    <div class="d-inline-block position-relative me-2 mb-2">
                        <img src="/${photo.foto}" width="100" class="border">
                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-foto" data-id="${photo.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                `;
                $('#preview_foto_pendukung').append(fotoHtml);
            }
        });

        $('#editKamarForm').attr('action', "{{ url('kamar/update') }}/" + id);

        $('#preview_foto_pendukung').on('click', '.delete-foto', function () {
            const photoId = $(this).data('id');

            if (confirm('Yakin ingin menghapus foto ini?')) {
                $.ajax({
                    url: '/kamar/destroy-photo-kamar/' + photoId,
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
        // $('#editKamarModal').modal('show'); // Tampilkan modal edit
    });
});
        </script>
        
        <script>
            $(document).on('submit', '.delete-mess-form', function (e) {
                e.preventDefault();
                var form = this;

                Swal.fire({
                    title: "Yakin ingin menonaktikan?",
                    text: "Kamar ini akan tidak bisa digunakan!",
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
            $(document).on('submit', '.active-mess-form', function (e) {
                e.preventDefault();
                var form = this;

                Swal.fire({
                    title: "Yakin ingin mengaktifkan kamar?",
                    text: "Kamar akan bisa digunakan kembali",
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
        
        
    </x-slot>
</x-layouts.app>