<x-layouts.app>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Manajemen Link</h3>
                @if (!in_array(Auth::user()->master_hak_akses_id, [5,6]))
                    <button type="button" data-toggle="modal" data-target="#CalenderModalNew1"
                        class="btn btn-success">Tambahkan</button><br>
                @endif
                <br><br>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive" style="width: 100%;">
                            <table class="table table-striped" id="dataTables-masterlink">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">
                                            <center>No</center>
                                        </th>
                                        <th style="width: 10%">
                                            <center>Nama</center>
                                        </th>
                                        <th style="width: 35%">
                                            <center>Link</center>
                                        </th>
                                        <th style="width: 15%">
                                            <center>Status</center>
                                        </th>
                                        @if (auth()->user()->role != 'Read Only')
                                            <th style="width: 25%">
                                                <center>Aksi</center>
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($masterlink as $index => $result)
                                        <tr>
                                            <td>
                                                <center>{{ $index + 1 }}</center>
                                            </td>
                                            <td>{{ $result->namalink }}</td>
                                            <td>{{ $result->link }}</td>
                                            <td style="text-align: center;">{{ $result->status }}</td>
                                            @if (auth()->user()->role != 'Read Only')
                                                <td>
                                                    <center>
                                                        <button style="margin-right: 6px; margin-bottom: 3px;"
                                                            type="button" data-toggle="modal"
                                                            data-target="#CalenderModalNew3{{ $result->id }}"
                                                            class="btn btn-warning btn-sm">Edit</button>
                                                    </center>
                                                </td>
                                            @endif
                                        </tr>

                                        <!-- Modal untuk Edit -->
                                        <div id="CalenderModalNew3{{ $result->id }}" class="modal fade" tabindex="-1"
                                            role="dialog" aria-labelledby="modal_update" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="modal_update">Update Daftar Link
                                                        </h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('admin.masterlink.update', $result->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="form-group">
                                                                <label>Nama Link *</label>
                                                                <input type="text" class="form-control"
                                                                    name="namalink" value="{{ $result->namalink }}"
                                                                    placeholder="Isikan Nama Link" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Link *</label>
                                                                <input type="text" class="form-control"
                                                                    name="link" value="{{ $result->link }}"
                                                                    placeholder="Isikan Link" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <b>Status *</b>
                                                                <select name="status" class="form-control" required>
                                                                    <option value=''>Pilihan</option>
                                                                    <option value='Aktif'
                                                                        {{ $result->status == 'Aktif' ? 'selected' : '' }}>
                                                                        Aktif</option>
                                                                    <option value='Non-Aktif'
                                                                        {{ $result->status == 'Non-Aktif' ? 'selected' : '' }}>
                                                                        Non-Aktif</option>
                                                                </select>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal untuk Tambah Link -->
    <div id="CalenderModalNew1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_tambah"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_tambah">Tambah Link</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.masterlink.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Nama Link *</label>
                            <input type="text" class="form-control" name="namalink" placeholder="Isikan Nama Link"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Link *</label>
                            <input type="text" class="form-control" name="link" placeholder="Isikan Link"
                                required>
                        </div>
                        <div class="form-group">
                            <b>Status *</b>
                            <select name="status" class="form-control" required>
                                <option value=''>Pilihan</option>
                                <option value='Aktif'>Aktif</option>
                                <option value='Non-Aktif'>Non-Aktif</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Tambahkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('js')
    @endpush
</x-layouts.app>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#dataTables-masterlink').DataTable({
            "lengthChange": true,
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "columnDefs": [{
                "targets": [2, 4],
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
