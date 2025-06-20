<x-layouts.app>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Manajemen Bagian</h3>
                @if (!in_array(Auth::user()->master_hak_akses_id, [5, 6]))
                    <button type="button" data-toggle="modal" data-target="#CalenderModalNew1"
                        class="btn btn-success">Tambahkan</button><br>
                @endif
                <br>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" style="width: 100%;" id="dataTables-bagian">
                                <thead>
                                    <tr>
                                        <th>
                                            <center>No</center>
                                        </th>
                                        <th>
                                            <center>Bagian</center>
                                        </th>
                                        <th>
                                            <center>Posisi</center>
                                        </th>
                                        <th>
                                            <center>Kode Bagian</center>
                                        </th>
                                        <th>
                                            <center>Regional</center>
                                        </th>
                                        <th>
                                            <center>Status</center>
                                        </th>
                                        @if (!in_array(Auth::user()->master_hak_akses_id, [5, 6]))
                                            <th>
                                                <center>Aksi</center>
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bagian as $index => $result)
                                        <tr>
                                            <td style="text-align: center;">{{ $index + 1 }}</td>
                                            <td>{{ $result->master_bagian_nama }}</td>
                                            <td style="text-align: center;">{{ $result->master_bagian_posisi }}</td>
                                            <td style="text-align: center;">{{ $result->master_bagian_kode }}</td>
                                            <td style="text-align: center;">{{ $result->regional->nama_regional }}</td>
                                            <td style="text-align: center;">
                                                @if ($result->is_active == 1)
                                                    Aktif
                                                @else
                                                    Non-Aktif
                                                @endif
                                            </td>
                                            @if (!in_array(Auth::user()->master_hak_akses_id, [5, 6]))
                                                <td>
                                                    <center>
                                                        <button type="button" data-toggle="modal"
                                                            data-target="#CalenderModalNew3{{ $result->master_bagian_id }}"
                                                            class="btn btn-warning btn-sm">Edit</button>
                                                    </center>
                                                </td>
                                            @endif
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div id="CalenderModalNew3{{ $result->master_bagian_id }}" class="modal fade"
                                            tabindex="-1" role="dialog" aria-labelledby="modal_update"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="modal_update">Update Daftar Bagian
                                                        </h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('admin.bagian.update', $result->master_bagian_id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="form-group">
                                                                <label>Nama Bagian *</label>
                                                                <input type="text" class="form-control"
                                                                    name="master_bagian_nama"
                                                                    value="{{ $result->master_bagian_nama }}"
                                                                    placeholder="Isikan Bagian" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Posisi Bagian *</label>
                                                                <input type="text" class="form-control"
                                                                    name="master_bagian_posisi"
                                                                    value="{{ $result->master_bagian_posisi }}"
                                                                    placeholder="Isikan Posisi Bagian" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Kode Bagian *</label>
                                                                <input type="text" class="form-control"
                                                                    name="master_bagian_kode"
                                                                    value="{{ $result->master_bagian_kode }}"
                                                                    placeholder="Isikan Kode Bagian" required>
                                                            </div>
                                                            {{-- Tambahkan bagian ini untuk dropdown Regional --}}
                    <div class="form-group">
                        <label>Regional *</label>
                        <select class="form-control" name="bagian_regional_id" required>
                            <option value="">-- Pilih Regional --</option>
                            @foreach ($regionals as $regional)
                                <option value="{{ $regional->id_regional }}"
                                    {{-- Ini adalah kunci untuk memilih opsi yang sudah ada --}}
                                    @if ($result->bagian_regional_id == $regional->id_regional) selected @endif
                                >
                                    {{ $regional->nama_regional }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Akhir bagian dropdown Regional --}}
                                                            <div class="form-group">
                                                                <label>Status *</label>
                                                                <select name="is_active" class="form-control" required>
                                                                    <option value="" disabled>Pilihan</option>
                                                                    <option value="1"
                                                                        {{ $result->is_active == 1 ? 'selected' : '' }}>
                                                                        Aktif</option>
                                                                    <option value="0"
                                                                        {{ $result->is_active == 0 ? 'selected' : '' }}>
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

    <!-- Modal Tambah Bagian -->
    <div id="CalenderModalNew1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_tambah"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_tambah">Tambah Bagian</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.bagian.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Nama Bagian *</label>
                            <input type="text" class="form-control" name="master_bagian_nama"
                                placeholder="Isikan Nama Bagian" required>
                        </div>
                        <div class="form-group">
                            <label>Posisi Bagian *</label>
                            <input type="text" class="form-control" name="master_bagian_posisi"
                                placeholder="Isikan Posisi Bagian" required>
                        </div>
                        <div class="form-group">
                            <label>Kode Bagian *</label>
                            <input type="text" class="form-control" name="master_bagian_kode"
                                placeholder="Isikan Kode Bagian" required>
                        </div>

                       <div class="form-group">
    <label>Regional *</label>
    <select class="form-control" name="bagian_regional_id" required>
        <option value="">-- Pilih Regional --</option> {{-- Opsi default --}}
        @foreach ($regionals as $regional)
            <option value="{{ $regional->id_regional }}"
                @if (isset($result) && $result->bagian_regional_id == $regional->id_regional) selected @endif>
                {{ $regional->nama_regional }}
            </option>
        @endforeach
    </select>
</div>

                        <div class="form-group">
                            <label>Status *</label>
                            <select name="is_active" class="form-control" required>
                                <option value="" {{ old('is_active') == '' ? 'selected' : '' }} disabled>Pilihan
                                </option>
                                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>
                                    Aktif</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>
                                    Non-Aktif
                                </option>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('#dataTables-bagian').DataTable({
                    "lengthChange": true,
                    "pageLength": 10,
                    "lengthMenu": [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ], // Opsi pilihan untuk show entries
                    "columnDefs": [{
                        "targets": [2, 3, 4, 5],
                        "orderable": false
                    }],
                    "language": {
                        "infoFiltered": ""
                    }
                });

                @if (Session::has('success'))
                    setTimeout(function() {
                        swal("{{ Session::get('success') }}");
                    }, 1000);
                @endif

                @if (Session::has('error'))
                    setTimeout(function() {
                        swal({
                            title: "{{ Session::get('error') }}",
                            type: "info",
                            confirmButtonText: "Ok",
                            confirmButtonColor: "#ff0055",
                            reverseButtons: true,
                            focusConfirm: true
                        });
                    }, 1000);
                @endif
            });
        </script>
    </x-slot>
</x-layouts.app>
