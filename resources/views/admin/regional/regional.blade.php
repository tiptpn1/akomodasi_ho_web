<x-layouts.app>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Manajemen Regional</h3>

                {{-- Tombol Tambah Regional (hanya selain Read Only) --}}
                @if (Auth::user()->role != 'Read Only')
                    <button type="button" data-toggle="modal" data-target="#modalTambahRegional"
                        class="btn btn-success">Tambahkan</button>
                    <br>
                @endif

                <br>

                {{-- Tabel Data Regional --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table display responsive" style="width: 100%;" id="dataTables-regional">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Nama Regional</th>
                                        @if (Auth::user()->role != 'Read Only')
                                            <th class="text-center">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($regional as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->nama_regional }}</td>
                                            @if (Auth::user()->role != 'Read Only')
                                                <td class="text-center">
                                                    <button style="margin-right: 6px; margin-bottom: 3px;"
                                                        type="button" data-toggle="modal"
                                                        data-target="#modalEditRegional{{ $item->id_regional }}"
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

    {{-- Modal Tambah Regional --}}
    <div id="modalTambahRegional" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_tambah"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_tambah">Tambah Regional</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form id="formTambahRegional" class="form-horizontal calender" role="form"
                        action="{{ route('admin.regional.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="col-sm-12">
                                <b>Regional *</b>
                                <input type="text" class="form-control" name="nama_regional"
                                    placeholder="Isikan Regional" required>
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

    {{-- Modal Edit Regional --}}
    @foreach ($regional as $item)
        <div id="modalEditRegional{{ $item->id_regional }}" class="modal fade" tabindex="-1" role="dialog"
            aria-labelledby="modal_update" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal_update">Update Daftar Regional</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal calender" role="form"
                            action="{{ route('admin.regional.update', $item->id_regional) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <!-- Metode PUT untuk update -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <b>Regional *</b>
                                    <input type="text" class="form-control" name="nama_regional"
                                        value="{{ $item->nama_regional }}" placeholder="Isikan Regional" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
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

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#dataTables-regional').DataTable({
            "lengthChange": true,
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "columnDefs": [{
                "targets": [2], // kolom aksi
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
