<x-layouts.app>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Manajemen Hak Akses</h3>
                @if (Auth::user()->role != 'Read Only')
                    <button type="button" data-toggle="modal" data-target="#modalTambahHakAkses"
                        class="btn btn-success">Tambahkan</button><br>
                @endif
                <br>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table display responsive" style="width: 100%;" id="dataTables-hakAkses">
                                <thead>
                                    <tr>
                                        <th>
                                            <center>No</center>
                                        </th>
                                        <th>
                                            <center>Hak Akses</center>
                                        </th>
                                        <th>
                                            <center>Status</center>
                                        </th>
                                        @if (Auth::user()->role != 'Read Only')
                                            <th>
                                                <center>Aksi</center>
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hak_akses as $index => $hakAkses)
                                        <tr>
                                            <td style="text-align: center;">{{ $index + 1 }}</td>
                                            <td>{{ $hakAkses->hak_akses_nama }}</td>
                                            <td style="text-align: center;">
                                                @if ($hakAkses->status == 1)
                                                    Aktif
                                                @else
                                                    Non-Aktif
                                                @endif
                                            </td>
                                            @if (Auth::user()->role != 'Read Only')
                                                <td style="text-align: center;">
                                                    <button style="margin-right: 6px; margin-bottom: 3px;"
                                                        type="button" data-toggle="modal"
                                                        data-target="#modalEditHakAkses{{ $hakAkses->hak_akses_id }}"
                                                        class="btn btn-warning btn-sm">Edit</button>
                                                </td>
                                            @endif
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
    {{-- Modal Tambah --}}
    <div id="modalTambahHakAkses" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_tambah"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_tambah">Tambah Hak Akses</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form id="formTambahHakAkses" class="form-horizontal calender" role="form"
                        action="{{ route('admin.hak_akses.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="col-sm-12">
                                <b>Hak Akses *</b>
                                <input type="text" class="form-control" name="hak_akses_nama"
                                    placeholder="Isikan Hak Akses" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <b>Status *</b>
                                <select name="status" class="form-control" required>
                                    <option value="" {{ old('status') == '' ? 'selected' : '' }} disabled>Pilihan
                                    </option>
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>
                                        Aktif</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>
                                        Non-Aktif
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary antosubmit">Tambahkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Update -->
    @foreach ($hak_akses as $hakAkses)
        <div id="modalEditHakAkses{{ $hakAkses->hak_akses_id }}" class="modal fade" tabindex="-1" role="dialog"
            aria-labelledby="modal_update" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal_update">Update Daftar Hak Akses</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal calender" role="form"
                            action="{{ route('admin.hak_akses.update', $hakAkses->hak_akses_id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <!-- Menambahkan metode PUT untuk update -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <b>Hak Akses *</b>
                                    <input type="text" class="form-control" name="hak_akses_nama"
                                        value="{{ $hakAkses->hak_akses_nama }}" placeholder="Isikan Hak Akses" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <b>Status *</b>
                                    <select name="status" class="form-control" required>
                                        <option value="" disabled
                                            {{ old('status', $hakAkses->status) === null ? 'selected' : '' }}>
                                            Pilihan
                                        </option>
                                        <option value="1"
                                            {{ old('status', $hakAkses->status) == 1 ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="0"
                                            {{ old('status', $hakAkses->status) == 0 ? 'selected' : '' }}>
                                            Non-Aktif
                                        </option>
                                    </select>
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
    @endforeach

    @push('js')
    @endpush
</x-layouts.app>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#dataTables-hakAkses').DataTable({
            "lengthChange": true,
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "columnDefs": [{
                "targets": [3],
                "orderable": false
            }],
            "language": {
                "infoFiltered": ""
            }
        });
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                confirmButtonText: 'OK'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>
