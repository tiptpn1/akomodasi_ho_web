<x-layouts.app>
    <x-slot name="styles">
        <style type="text/css">
            .highlight {
                background-color: #d4edda;
            }

            th {
                background-color: #f8f9fa;
            }

            .total-row {
                font-weight: bold;
            }

            .icon-action {
                font-size: 18px;
                margin: 0 8px;
            }

            .icon-action:hover {
                color: #007bff;
                cursor: pointer;
            }


            .btn {
                padding: 0.25rem 0.5rem;
                font-size: 14px;
            }
        </style>
    </x-slot>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Permintaan Konsumsi</h3>
                <table class="table table-bordered display responsive" style="width: 100%; float:center;"
                    id="dataTables-konsumsi">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Agenda</th>
                            <th>Tanggal</th>
                            <th>Divisi</th>
                            <th>Makanan</th>
                            <th>Biaya Makanan</th>
                            <th>Snack</th>
                            <th>Biaya Snack</th>
                            <th>Keterangan</th>
                            <th>Biaya Lain-lain</th>
                            <th>Status Approval</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($konsumsi as $index => $item)
                            <tr>
                                <td rowspan="3">{{ $index + 1 }}</td>
                                <td rowspan="3">{{ $item->sendVicon->acara ?? 'Tidak ada acara' }}</td>
                                <td rowspan="3">
                                    {{ $item->sendVicon->tanggal ? \Carbon\Carbon::parse($item->sendVicon->tanggal)->format('d/m/Y') : '' }}
                                </td>
                                <td rowspan="3">{{ $item->sendVicon->bagian->master_bagian_nama ?? '' }}</td>

                                @if ($item->m_pagi == 1)
                                    <td class="highlight">Pagi</td>
                                    <td class="highlight">{{ $item->biaya_m_pagi ?? 0 }}</td>
                                @else
                                    <td>Pagi</td>
                                    <td>-</td>
                                @endif

                                @if ($item->s_pagi == 1)
                                    <td class="highlight">Pagi</td>
                                    <td class="highlight">{{ $item->biaya_s_pagi ?? 0 }}</td>
                                @else
                                    <td>Pagi</td>
                                    <td>-</td>
                                @endif

                                <td rowspan="3">{{ $item->keterangan ?? '' }}</td>
                                <td rowspan="3">{{ $item->biaya_lain ?? 0 }}</td>
                                <td rowspan="3">
                                    <!-- Status Approval Text -->
                                    @if ($item->status == 0)
                                        Waiting for Approve
                                    @elseif ($item->status == 1)
                                        Pengajuan oleh Asisten GA
                                    @elseif ($item->status == 2)
                                        Approve Kasubdiv GA
                                    @elseif ($item->status == 3)
                                        Approve Kadiv GA
                                    @elseif ($item->status == 4)
                                        Dibatalkan
                                    @endif
                                </td>
                                @if (Auth::user()->hakAkses->hak_akses_id == 2)
                                    <td rowspan="3">
                                        @if (Auth::user()->master_user_nama == 'asisten_ga' && $item->status == 0)
                                            <a href="#" class="btn btn-warning btn-sm icon-action"
                                                data-toggle="modal" data-target="#editModal{{ $item->id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('konsumsi.approve', $item->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm icon-action"
                                                    title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form id="delete-form-{{ $item->id }}"
                                                action="{{ route('konsumsi.destroy', $item) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action"
                                                    onclick="confirmDelete({{ $item->id }})" title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @elseif (Auth::user()->master_user_nama == 'asisten_ga' && in_array($item->status, [1, 2]))
                                            <a href="#" class="btn btn-warning btn-sm icon-action"
                                                data-toggle="modal" data-target="#editModal{{ $item->id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $item->id }}"
                                                action="{{ route('konsumsi.destroy', $item) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action"
                                                    onclick="confirmDelete({{ $item->id }})" title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if (Auth::user()->master_user_nama == 'kasubdiv_ga' && in_array($item->status, [0, 2]))
                                            <a href="#" class="btn btn-warning btn-sm icon-action"
                                                data-toggle="modal" data-target="#editModal{{ $item->id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $item->id }}"
                                                action="{{ route('konsumsi.destroy', $item) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action"
                                                    onclick="confirmDelete({{ $item->id }})" title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @elseif (Auth::user()->master_user_nama == 'kasubdiv_ga' && in_array($item->status, [1]))
                                            <a href="#" class="btn btn-warning btn-sm icon-action"
                                                data-toggle="modal" data-target="#editModal{{ $item->id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('konsumsi.approve', $item->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm icon-action"
                                                    title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form id="delete-form-{{ $item->id }}"
                                                action="{{ route('konsumsi.destroy', $item) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action"
                                                    onclick="confirmDelete({{ $item->id }})" title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if (Auth::user()->master_user_nama == 'kadiv_ga' && in_array($item->status, [0, 1]))
                                            <a href="#" class="btn btn-warning btn-sm icon-action"
                                                data-toggle="modal" data-target="#editModal{{ $item->id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $item->id }}"
                                                action="{{ route('konsumsi.destroy', $item) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action"
                                                    onclick="confirmDelete({{ $item->id }})" title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @elseif (Auth::user()->master_user_nama == 'kadiv_ga' && in_array($item->status, [2]))
                                            <a href="#" class="btn btn-warning btn-sm icon-action"
                                                data-toggle="modal" data-target="#editModal{{ $item->id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('konsumsi.approve', $item->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm icon-action"
                                                    title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form id="delete-form-{{ $item->id }}"
                                                action="{{ route('konsumsi.destroy', $item) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action"
                                                    onclick="confirmDelete({{ $item->id }})" title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if (!in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga', 'kadiv_ga']))
                                            @if (in_array($item->status, [3, 4]))
                                                <a href="#" class="btn btn-warning btn-sm icon-action"
                                                    data-toggle="modal" data-target="#editModal{{ $item->id }}"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form id="delete-form-{{ $item->id }}"
                                                    action="{{ route('konsumsi.destroy', $item) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm icon-action"
                                                        onclick="confirmDelete({{ $item->id }})"
                                                        title="Batalkan">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                @else
                                    <td rowspan="3"></td>
                                @endif
                            </tr>

                            <tr>
                                @if ($item->m_siang == 1)
                                    <td class="highlight">Siang</td>
                                    <td class="highlight">{{ $item->biaya_m_siang ?? 0 }}</td>
                                @else
                                    <td>Siang</td>
                                    <td>-</td>
                                @endif

                                @if ($item->s_siang == 1)
                                    <td class="highlight">Siang</td>
                                    <td class="highlight">{{ $item->biaya_s_siang ?? 0 }}</td>
                                @else
                                    <td>Siang</td>
                                    <td>-</td>
                                @endif
                            </tr>

                            <tr>
                                @if ($item->m_malam == 1)
                                    <td class="highlight">Malam</td>
                                    <td class="highlight">{{ $item->biaya_m_malam ?? 0 }}</td>
                                @else
                                    <td>Malam</td>
                                    <td>-</td>
                                @endif

                                @if ($item->s_sore == 1)
                                    <td class="highlight">Sore</td>
                                    <td class="highlight">{{ $item->biaya_s_sore ?? 0 }}</td>
                                @else
                                    <td>Sore</td>
                                    <td>-</td>
                                @endif
                            </tr>

                            <tr class="total-row">
                                <td colspan="4"></td>
                                <td><strong>Total Makanan</strong></td>
                                <td><strong>{{ ($item->m_pagi == 1 ? $item->biaya_m_pagi : 0) + ($item->m_siang == 1 ? $item->biaya_m_siang : 0) + ($item->m_malam == 1 ? $item->biaya_m_malam : 0) }}</strong>
                                </td>
                                <td><strong>Total Snack</strong></td>
                                <td><strong>{{ ($item->s_pagi == 1 ? $item->biaya_s_pagi : 0) + ($item->s_siang == 1 ? $item->biaya_s_siang : 0) + ($item->s_sore == 1 ? $item->biaya_s_sore : 0) }}</strong>
                                </td>
                                <td></td>
                                <td><strong>{{ $item->biaya_lain ?? 0 }}</strong></td>
                                <td></td>
                            </tr>

                            <!-- Modal untuk Edit Konsumsi -->
                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $item->id }}">Edit
                                                Permintaan Konsumsi</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('konsumsi.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <!-- Agenda (readonly) -->
                                                <div class="form-group">
                                                    <label for="agenda">Agenda</label>
                                                    <input type="text" class="form-control" id="agenda"
                                                        name="agenda"
                                                        value="{{ $item->sendVicon->acara ?? 'Tidak ada acara' }}"
                                                        readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="tanggal">Tanggal</label>
                                                    <input type="text" class="form-control" id="tanggal"
                                                        name="tanggal"
                                                        value="{{ $item->sendVicon->tanggal ?? 'Tidak ada acara' }}"
                                                        readonly>
                                                </div>

                                                <b>Permintaan Konsumsi</b>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <b>Makan:</b><br>
                                                        <div class="form-check">
                                                            <input type="hidden" name="makan[pagi]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="1" name="makan[pagi]"
                                                                id="makan_pagi{{ $item->id }}"
                                                                {{ $item->m_pagi ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="makan_pagi{{ $item->id }}">Pagi</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="hidden" name="makan[siang]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="1" name="makan[siang]"
                                                                id="makan_siang{{ $item->id }}"
                                                                {{ $item->m_siang ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="makan_siang{{ $item->id }}">Siang</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="hidden" name="makan[malam]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="1" name="makan[malam]"
                                                                id="makan_malam{{ $item->id }}"
                                                                {{ $item->m_malam ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="makan_malam{{ $item->id }}">Malam</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <b>Snack:</b><br>
                                                        <div class="form-check">
                                                            <input type="hidden" name="snack[pagi]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="1" name="snack[pagi]"
                                                                id="snack_pagi{{ $item->id }}"
                                                                {{ $item->s_pagi ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="snack_pagi{{ $item->id }}">Pagi</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="hidden" name="snack[siang]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="1" name="snack[siang]"
                                                                id="snack_siang{{ $item->id }}"
                                                                {{ $item->s_siang ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="snack_siang{{ $item->id }}">Siang</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="hidden" name="snack[sore]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="1" name="snack[sore]"
                                                                id="snack_sore{{ $item->id }}"
                                                                {{ $item->s_sore ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="snack_sore{{ $item->id }}">Sore</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Biaya Makanan -->
                                                <div class="form-group">
                                                    <label for="biaya_m_pagi">Biaya Makan Pagi</label>
                                                    <input type="number" class="form-control" id="biaya_m_pagi"
                                                        name="biaya_m_pagi" value="{{ $item->biaya_m_pagi ?? 0 }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="biaya_m_siang">Biaya Makan Siang</label>
                                                    <input type="number" class="form-control" id="biaya_m_siang"
                                                        name="biaya_m_siang" value="{{ $item->biaya_m_siang ?? 0 }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="biaya_m_malam">Biaya Makan Malam</label>
                                                    <input type="number" class="form-control" id="biaya_m_malam"
                                                        name="biaya_m_malam" value="{{ $item->biaya_m_malam ?? 0 }}">
                                                </div>

                                                <!-- Biaya Snack -->
                                                <div class="form-group">
                                                    <label for="biaya_s_pagi">Biaya Snack Pagi</label>
                                                    <input type="number" class="form-control" id="biaya_s_pagi"
                                                        name="biaya_s_pagi" value="{{ $item->biaya_s_pagi ?? 0 }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="biaya_s_siang">Biaya Snack Siang</label>
                                                    <input type="number" class="form-control" id="biaya_s_siang"
                                                        name="biaya_s_siang" value="{{ $item->biaya_s_siang ?? 0 }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="biaya_s_sore">Biaya Snack Sore</label>
                                                    <input type="number" class="form-control" id="biaya_s_sore"
                                                        name="biaya_s_sore" value="{{ $item->biaya_s_sore ?? 0 }}">
                                                </div>

                                                <!-- Keterangan -->
                                                <div class="form-group">
                                                    <label for="keterangan">Keterangan</label>
                                                    <textarea class="form-control" id="keterangan" name="keterangan">{{ $item->keterangan ?? '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="biaya_lain">Biaya Lain-lain</label>
                                                    <input type="number" class="form-control" id="biaya_lain"
                                                        name="biaya_lain" value="{{ $item->biaya_lain ?? 0 }}">
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Update
                                                        Konsumsi</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
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
        </main>
    </div>
    {{-- @push('js') --}}
    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                @if (session('success'))
                    swal({
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        icon: 'success',
                        button: 'OK',
                    });
                @endif

                @if (session('error'))
                    swal({
                        title: 'Gagal!',
                        text: '{{ session('error') }}',
                        icon: 'error',
                        button: 'OK',
                    });
                @endif
                $('#dataTables-konsumsi').DataTable({
                    "lengthChange": true,
                    "pageLength": 10,
                    "lengthMenu": [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    "responsive": true,
                    "processing": true,
                    "language": {
                        "infoFiltered": ""
                    },
                    "columnDefs": [{
                        "targets": [0, 1, 2, 3, 4, 5, 6, 7, -1],
                        "orderable": false
                    }],

                });
            });

            function confirmDelete(id) {
                if (confirm("Apakah Anda yakin ingin membatalkan permintaan konsumsi ini?")) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
        {{-- @endpush --}}
    </x-slot>
</x-layouts.app>
