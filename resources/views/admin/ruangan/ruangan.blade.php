<x-layouts.app>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Manajemen Ruangan</h3>
                @if (!in_array(Auth::user()->master_hak_akses_id, [5,6]))
                    <button type="button" data-toggle="modal" data-target="#CalenderModalNew1"
                        class="btn btn-success">Tambahkan</button><br>
                @endif
                <br>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table display responsive" style="width: 100%;" id="dataTables-ruangan">
                                <thead>
                                    <tr>
                                        <th>
                                            <center>No</center>
                                        </th>
                                        <th>
                                            <center>Regional</center>
                                        </th>
                                        <th>
                                            <center>Ruangan</center>
                                        </th>
                                        <th>
                                            <center>Lantai</center>
                                        </th>
                                        <th>
                                            <center>Kapasitas</center>
                                        </th>
                                        <th>
                                            <center>Status</center>
                                        </th>
                                        @if (!in_array(Auth::user()->master_hak_akses_id, [5,6]))
                                            <th>
                                                <center>Aksi</center>
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ruangan as $index => $result)
                                        <tr>
                                            <td style="text-align: center;">{{ $index + 1 }}</td>
                                            <td>{{ $result->regional->nama_regional }}</td>
                                            <td>{{ $result->nama }}</td>
                                            <td>Lantai {{ $result->lantai }}</td>
                                            <td>{{ $result->kapasitas }} Orang</td>
                                            <td style="text-align: center;">{{ $result->status }}</td>
                                            @if (!in_array(Auth::user()->master_hak_akses_id, [5,6]))
                                                <td style="text-align: center;">
                                                    <button style="margin-right: 6px; margin-bottom: 3px;"
                                                        type="button" data-toggle="modal"
                                                        data-target="#CalenderModalNew3{{ $result->id }}"
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
    <div id="CalenderModalNew1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_tambah"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_tambah">Tambah Ruangan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form id="antoform" class="form-horizontal calender" role="form"
                        action="{{ route('admin.ruangan.store') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="col-sm-13 control-label"></label>
                            <div class="col-sm-12">
    <b>Regional *</b>
    <select class="form-control" name="ruangan_regional_id" required>
        <option value="">-- Pilih Regional --</option> {{-- Opsi default --}}
        @foreach ($regionals as $regional)
            <option value="{{ $regional->id_regional }}"
                @if (isset($result) && $result->ruangan_regional_id == $regional->id_regional) selected @endif>
                {{ $regional->nama_regional }}
            </option>
        @endforeach
    </select>
    </div>
</div>
                        <div class="form-group">
                            <label class="col-sm-13 control-label"></label>
                            <div class="col-sm-12">
                                <b>Ruangan *</b>
                                <input type="text" class="form-control" name="nama" placeholder="Isikan Ruangan"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-13 control-label"></label>
                            <div class="col-sm-12">
                                <b>Lantai *</b>
                                <input type="number" class="form-control" name="lantai" placeholder="Isikan Lantai Ruangan, contoh: 12"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-13 control-label"></label>
                            <div class="col-sm-12">
                                <b>Kapasitas *</b>
                                <input type="number" class="form-control" name="kapasitas" placeholder="Isikan Kapasitas Ruangan, contoh: 8"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-13 control-label"></label>
                            <div class="col-sm-12">
                                <b>Status *</b>
                                <select name="status" class="form-control" required>
                                    <option value=''>Pilihan</option>
                                    <option value='Aktif'>Aktif</option>
                                    <option value='Non-Aktif'>Non-Aktif</option>
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
    @foreach ($ruangan as $result)
        <div id="CalenderModalNew3{{ $result->id }}" class="modal fade" tabindex="-1" role="dialog"
            aria-labelledby="modal_update" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal_update">Update Daftar Ruangan</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal calender" role="form"
                            action="{{ route('admin.ruangan.update', $result->id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <!-- Menambahkan metode PUT untuk update -->
                            <input type="hidden" name="id" value="{{ $result->id }}">
                             {{-- Tambahkan bagian ini untuk dropdown Regional --}}
                    <div class="form-group">
                        <label>Regional *</label>
                        <select class="form-control" name="ruangan_regional_id" required>
                            <option value="">-- Pilih Regional --</option>
                            @foreach ($regionals as $regional)
                                <option value="{{ $regional->id_regional }}"
                                    {{-- Ini adalah kunci untuk memilih opsi yang sudah ada --}}
                                    @if ($result->ruangan_regional_id == $regional->id_regional) selected @endif
                                >
                                    {{ $regional->nama_regional }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Akhir bagian dropdown Regional --}}
                            <div class="form-group">
                                <label class="col-sm-13 control-label"></label>
                                <div class="col-sm-12">
                                    <b>Ruangan *</b>
                                    <input type="text" class="form-control" name="nama"
                                        value="{{ $result->nama }}" placeholder="Isikan Ruangan" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-13 control-label"></label>
                                <div class="col-sm-12">
                                    <b>Lantai *</b>
                                    <input type="number" class="form-control" name="lantai"
                                        value="{{ $result->lantai }}" placeholder="Isikan Lantai Ruangan, contoh: 12" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-13 control-label"></label>
                                <div class="col-sm-12">
                                    <b>Kapasitas *</b>
                                    <input type="number" class="form-control" name="kapasitas"
                                        value="{{ $result->kapasitas }}" placeholder="Isikan Kapasitas Ruangan, contoh: 8" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-13 control-label"></label>
                                <div class="col-sm-12">
                                    <b>Status *</b>
                                    <select name="status" class="form-control" required>
                                        <option value=''>Pilihan</option>
                                        <option value='Aktif' {{ $result->status == 'Aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value='Non-Aktif'
                                            {{ $result->status == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
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
        $('#dataTables-ruangan').DataTable({
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
