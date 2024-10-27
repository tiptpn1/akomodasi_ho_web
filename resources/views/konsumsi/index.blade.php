<x-layouts.app>
    <x-slot name="styles">
        <style type="text/css">
            .highlight {
                background-color: #d4edda;
            }

            .highlight-red {
                background-color: #f8d7da;
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

            .modal-lg {
                max-width: 55%;
            }

            .confirmation-modal {
                z-index: 1051;
            }

            .text-red {
                color: red;
            }

            .select2-selection__choice {
                background-color: rgb(0, 195, 255) !important;
            }

            .select2-selection__choice__remove {
                color: white !important;
            }

            .btn-kirim.disabled {
                background-color: grey;
                cursor: not-allowed;
                pointer-events: none;
            }
        </style>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    </x-slot>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Permintaan Konsumsi</h3>
                @php
                    // dd(Auth::user()->master_user_nama);
                @endphp
                <div class="mb-4">
                    <button class="btn btn-warning" data-toggle="modal" data-target="#filterModal">Filter</button>
                    <button class="btn btn-info" onclick="window.location='{{ route('konsumsi.index') }}'">Reset
                        Filter</button>
                    @if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga']))
                        <button class="btn btn-success" onclick="kirimSelected()">Kirim</button>
                        <button id="select-all-button" class="btn btn-warning">Select All</button>
                    @endif
                </div>

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
                            <th>Biaya Per Agenda</th>
                            <th>Status Approval</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($konsumsi as $index => $item)
                            @php
                                // dump($item->status_konsumsi);
                                $start++;

                                $total_makan =
                                    ($item->m_pagi == 1 && in_array($item->status_batal_m_pagi, $request_status)
                                        ? $item->biaya_m_pagi
                                        : 0) +
                                    ($item->m_siang == 1 && in_array($item->status_batal_m_siang, $request_status)
                                        ? $item->biaya_m_siang
                                        : 0) +
                                    ($item->m_malam == 1 && in_array($item->status_batal_m_malam, $request_status)
                                        ? $item->biaya_m_malam
                                        : 0);

                                $total_snack =
                                    ($item->s_pagi == 1 && in_array($item->status_batal_s_pagi, $request_status)
                                        ? $item->biaya_s_pagi
                                        : 0) +
                                    ($item->s_siang == 1 && in_array($item->status_batal_s_siang, $request_status)
                                        ? $item->biaya_s_siang
                                        : 0) +
                                    ($item->s_sore == 1 && in_array($item->status_batal_s_sore, $request_status)
                                        ? $item->biaya_s_sore
                                        : 0);

                                $total_biaya_semua_agenda = $total_makan + $total_snack + ($item->biaya_lain ?? 0);
                            @endphp
                            <tr>
                                <td rowspan="3">{{ $start }}</td>
                                <td rowspan="3">{{ $item->acara ?? 'Tidak ada acara' }}</td>
                                <td rowspan="3">
                                    {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '' }}
                                </td>
                                <td rowspan="3">{{ $item->master_bagian_nama ?? '' }}</td>

                                @if ($item->status_batal_m_pagi == 1 && in_array($item->status_batal_m_pagi, $request_status))
                                    <td class="highlight-red">Pagi</td>
                                    <td class="highlight-red">
                                        {{ 'Rp' . number_format($item->biaya_m_pagi ?? 0, 0, ',', '.') }}</td>
                                @elseif ($item->m_pagi == 1 && in_array($item->status_batal_m_pagi, $request_status))
                                    <td class="highlight">Pagi</td>
                                    <td class="highlight">
                                        {{ 'Rp' . number_format($item->biaya_m_pagi ?? 0, 0, ',', '.') }}</td>
                                @else
                                    <td>
                                        @if (count($request_status) == 3)
                                            Pagi
                                        @endif
                                    </td>
                                    <td>
                                        @if (count($request_status) == 3)
                                            -
                                        @endif
                                    </td>
                                @endif

                                @if ($item->status_batal_s_pagi == 1 && in_array($item->status_batal_s_pagi, $request_status))
                                    <td class="highlight-red">Pagi</td>
                                    <td class="highlight-red">
                                        {{ 'Rp' . number_format($item->biaya_s_pagi ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif ($item->s_pagi == 1 && in_array($item->status_batal_s_pagi, $request_status))
                                    <td class="highlight">Pagi</td>
                                    <td class="highlight">
                                        {{ 'Rp' . number_format($item->biaya_s_pagi ?? 0, 0, ',', '.') }}
                                    </td>
                                @else
                                    <td>
                                        @if (count($request_status) == 3)
                                            Pagi
                                        @endif
                                    </td>
                                    <td>
                                        @if (count($request_status) == 3)
                                            -
                                        @endif
                                    </td>
                                @endif

                                <td rowspan="3">{{ $item->konsumsi_keterangan }}</td>
                                <td rowspan="3">{{ 'Rp' . number_format($item->biaya_lain, 0, ',', '.') }}</td>
                                <td rowspan="4">{{ 'Rp' . number_format($total_biaya_semua_agenda, 0, ',', '.') }}
                                </td>
                                <td rowspan="3">
                                    <!-- Status Approval Text -->
                                    @if ($item->status_konsumsi == 0)
                                        Waiting for Approve
                                    @elseif ($item->status_konsumsi == 1)
                                        @if ($item->konsumsi_kirim == 0)
                                            Approved
                                        @elseif ($item->konsumsi_kirim == 1)
                                            Waiting for Kasubdiv GA Approval
                                        @endif
                                    @elseif ($item->status_konsumsi == 2)
                                        @if ($item->konsumsi_kirim == 1)
                                            Approve by Kasubdiv GA
                                        @elseif ($item->konsumsi_kirim == 2)
                                            Waiting for Kadiv GA Approval
                                        @endif
                                    @elseif ($item->status_konsumsi == 3)
                                        Approve by Kadiv GA
                                    @elseif ($item->status_konsumsi == 4)
                                        Canceled
                                    @endif
                                </td>
                                @if (Auth::user()->hakAkses->hak_akses_id == 2)
                                    <td rowspan="3" style="text-align: center;">
                                        @if (Auth::user()->master_user_nama == 'asisten_ga' && in_array($item->status_konsumsi, [0, 2]))
                                            <a href="#" class="btn btn-warning btn-sm icon-action mb-1"
                                                data-toggle="modal" data-target="#editModal{{ $item->konsumsi_id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $item->konsumsi_id }}"
                                                action="{{ route('konsumsi.destroy', $item->konsumsi_id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action mb-1"
                                                    onclick="confirmDelete({{ $item->konsumsi_id }})" title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @elseif (Auth::user()->master_user_nama == 'asisten_ga' && $item->status_konsumsi == 1)
                                            @if ($item->konsumsi_kirim == 0)
                                                <div style=" justify-content: center; margin-bottom: 7px;">
                                                    <input type="checkbox" class="checkbox-item"
                                                        value="{{ $item->konsumsi_id }}"
                                                        style="transform: scale(2.35);">
                                                </div>
                                                <button type="button"
                                                    class="btn btn-kirim btn-success btn-sm icon-action mb-1"
                                                    title="Kirim" onclick="confirmKirim({{ $item->konsumsi_id }})">
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>
                                            @endif
                                            <a href="#" class="btn btn-warning btn-sm icon-action mb-1"
                                                data-toggle="modal" data-target="#editModal{{ $item->konsumsi_id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $item->konsumsi_id }}"
                                                action="{{ route('konsumsi.destroy', $item->konsumsi_id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action mb-1"
                                                    onclick="confirmDelete({{ $item->konsumsi_id }})" title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if (Auth::user()->master_user_nama == 'kasubdiv_ga' && $item->status_konsumsi == 2)
                                            @if ($item->konsumsi_kirim == 1)
                                                <div style=" justify-content: center; margin-bottom: 7px;">
                                                    <input type="checkbox" class="checkbox-item"
                                                        value="{{ $item->konsumsi_id }}"
                                                        style="transform: scale(2.35);">
                                                </div>
                                                <button type="button"
                                                    class="btn btn-kirim btn-success btn-sm icon-action mb-1"
                                                    title="Kirim" onclick="confirmKirim({{ $item->konsumsi_id }})">
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>
                                            @endif
                                            <a href="#" class="btn btn-warning btn-sm icon-action mb-1"
                                                data-toggle="modal" data-target="#editModal{{ $item->konsumsi_id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $item->konsumsi_id }}"
                                                action="{{ route('konsumsi.destroy', $item->konsumsi_id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action mb-1"
                                                    onclick="confirmDelete({{ $item->konsumsi_id }})"
                                                    title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @elseif (Auth::user()->master_user_nama == 'kasubdiv_ga' && $item->status_konsumsi == 1)
                                            <a href="#" class="btn btn-warning btn-sm icon-action mb-1"
                                                data-toggle="modal" data-target="#editModal{{ $item->konsumsi_id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if ($item->konsumsi_kirim == 1)
                                                <form id="approve-form-{{ $item->konsumsi_id }}"
                                                    action="{{ route('konsumsi.approve', $item->konsumsi_id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="button"
                                                        class="btn btn-success btn-sm icon-action mb-1"
                                                        title="Approve"
                                                        onclick="confirmApprove({{ $item->konsumsi_id }})">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form id="delete-form-{{ $item->konsumsi_id }}"
                                                action="{{ route('konsumsi.destroy', $item->konsumsi_id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action mb-1"
                                                    onclick="confirmDelete({{ $item->konsumsi_id }})"
                                                    title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if (Auth::user()->master_user_nama == 'kadiv_ga' && in_array($item->status_konsumsi, [0, 1]))
                                            <a href="#" class="btn btn-warning btn-sm icon-action mb-1"
                                                data-toggle="modal" data-target="#editModal{{ $item->konsumsi_id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $item->konsumsi_id }}"
                                                action="{{ route('konsumsi.destroy', $item->konsumsi_id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action mb-1"
                                                    onclick="confirmDelete({{ $item->konsumsi_id }})"
                                                    title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @elseif (Auth::user()->master_user_nama == 'kadiv_ga' && in_array($item->status_konsumsi, [2]))
                                            <a href="#" class="btn btn-warning btn-sm icon-action mb-1"
                                                data-toggle="modal" data-target="#editModal{{ $item->konsumsi_id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="approve-form-{{ $item->konsumsi_id }}"
                                                action="{{ route('konsumsi.approve', $item->konsumsi_id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                <button type="button" class="btn btn-success btn-sm icon-action mb-1"
                                                    title="Approve"
                                                    onclick="confirmApprove({{ $item->konsumsi_id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form id="delete-form-{{ $item->konsumsi_id }}"
                                                action="{{ route('konsumsi.destroy', $item->konsumsi_id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm icon-action mb-1"
                                                    onclick="confirmDelete({{ $item->konsumsi_id }})"
                                                    title="Batalkan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if (!in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga', 'kadiv_ga']))
                                            @if (in_array($item->status_konsumsi, [3, 4]))
                                                <a href="#" class="btn btn-warning btn-sm icon-action mb-1"
                                                    data-toggle="modal"
                                                    data-target="#editModal{{ $item->konsumsi_id }}" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form id="delete-form-{{ $item->konsumsi_id }}"
                                                    action="{{ route('konsumsi.destroy', $item->konsumsi_id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm icon-action mb-1"
                                                        onclick="confirmDelete({{ $item->konsumsi_id }})"
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
                                @if ($item->status_batal_m_siang == 1 && in_array($item->status_batal_m_siang, $request_status))
                                    <td class="highlight-red">Siang</td>
                                    <td class="highlight-red">
                                        {{ 'Rp' . number_format($item->biaya_m_siang ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif ($item->m_siang == 1 && in_array($item->status_batal_m_siang, $request_status))
                                    <td class="highlight">Siang</td>
                                    <td class="highlight">
                                        {{ 'Rp' . number_format($item->biaya_m_siang ?? 0, 0, ',', '.') }}
                                    </td>
                                @else
                                    <td>
                                        @if (count($request_status) == 3)
                                            Siang
                                        @endif
                                    </td>
                                    <td>
                                        @if (count($request_status) == 3)
                                            -
                                        @endif
                                    </td>
                                @endif

                                @if ($item->status_batal_s_siang == 1 && in_array($item->status_batal_s_siang, $request_status))
                                    <td class="highlight-red">Siang</td>
                                    <td class="highlight-red">
                                        {{ 'Rp' . number_format($item->biaya_s_siang ?? 0, 0, ',', '.') }}</td>
                                @elseif ($item->s_siang == 1 && in_array($item->status_batal_s_siang, $request_status))
                                    <td class="highlight">Siang</td>
                                    <td class="highlight">
                                        {{ 'Rp' . number_format($item->biaya_s_siang ?? 0, 0, ',', '.') }}</td>
                                @else
                                    <td>
                                        @if (count($request_status) == 3)
                                            Siang
                                        @endif
                                    </td>
                                    <td>
                                        @if (count($request_status) == 3)
                                            -
                                        @endif
                                    </td>
                                @endif
                            </tr>

                            <tr>
                                @if ($item->status_batal_m_malam == 1 && in_array($item->status_batal_m_malam, $request_status))
                                    <td class="highlight-red">Malam</td>
                                    <td class="highlight-red">
                                        {{ 'Rp' . number_format($item->biaya_m_malam ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif ($item->m_malam == 1 && in_array($item->status_batal_m_malam, $request_status))
                                    <td class="highlight">Malam</td>
                                    <td class="highlight">
                                        {{ 'Rp' . number_format($item->biaya_m_malam ?? 0, 0, ',', '.') }}
                                    </td>
                                @else
                                    <td>
                                        @if (count($request_status) == 3)
                                            Malam
                                        @endif
                                    </td>
                                    <td>
                                        @if (count($request_status) == 3)
                                            -
                                        @endif
                                    </td>
                                @endif

                                @if ($item->status_batal_s_sore == 1 && in_array($item->status_batal_s_sore, $request_status))
                                    <td class="highlight-red">Sore</td>
                                    <td class="highlight-red">
                                        {{ 'Rp' . number_format($item->biaya_s_sore ?? 0, 0, ',', '.') }}</td>
                                @elseif ($item->s_sore == 1 && in_array($item->status_batal_s_sore, $request_status))
                                    <td class="highlight">Sore</td>
                                    <td class="highlight">
                                        {{ 'Rp' . number_format($item->biaya_s_sore ?? 0, 0, ',', '.') }}</td>
                                @else
                                    <td>
                                        @if (count($request_status) == 3)
                                            Sore
                                        @endif
                                    </td>
                                    <td>
                                        @if (count($request_status) == 3)
                                            -
                                        @endif
                                    </td>
                                @endif
                            </tr>

                            <tr class="total-row">
                                <td colspan="4"></td>
                                <td><strong>Total Makanan</strong></td>
                                <td><strong>{{ 'Rp' . number_format($total_makan, 0, ',', '.') }}</strong>
                                </td>
                                <td><strong>Total Snack</strong></td>
                                <td><strong>{{ 'Rp' . number_format($total_snack, 0, ',', '.') }}</strong>
                                </td>
                                <td></td>
                                <td><strong>{{ 'Rp' . number_format($item->biaya_lain, 0, ',', '.') }}</strong></td>
                                <td></td>
                            </tr>


                            <!-- Modal untuk Edit Konsumsi -->
                            <div class="modal fade" id="editModal{{ $item->konsumsi_id }}" tabindex="-1"
                                role="dialog" aria-labelledby="editModalLabel{{ $item->konsumsi_id }}"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $item->konsumsi_id }}">Edit
                                                Permintaan Konsumsi</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('konsumsi.update', $item->konsumsi_id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <!-- Agenda (readonly) -->
                                                        <div class="form-group">
                                                            <label for="agenda">Agenda</label>
                                                            <input type="text" class="form-control" id="agenda"
                                                                name="agenda"
                                                                value="{{ $item->acara ?? 'Tidak ada acara' }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">

                                                        <div class="form-group">
                                                            <label for="tanggal">Tanggal</label>
                                                            <input type="text" class="form-control" id="tanggal"
                                                                name="tanggal"
                                                                value="{{ $item->tanggal ?? 'Tidak ada acara' }}"
                                                                readonly>
                                                        </div>

                                                    </div>
                                                </div>

                                                <b>Permintaan Konsumsi</b>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <b>Makan:</b><br>
                                                        <div
                                                            class="form-check d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <input type="hidden" name="makan[pagi]"
                                                                    value="0">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" name="makan[pagi]"
                                                                    id="makan_pagi{{ $item->konsumsi_id }}"
                                                                    {{ $item->m_pagi ? 'checked' : '' }}
                                                                    {{ $item->m_pagi ? '' : 'disabled' }}>
                                                                <label
                                                                    class="form-check-label {{ $item->status_batal_m_pagi != 0 ? 'text-red' : '' }}"
                                                                    for="makan_pagi{{ $item->konsumsi_id }}">
                                                                    Pagi
                                                                </label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="hidden" name="status_batal_m_pagi"
                                                                    id="status_batal_m_pagi{{ $item->konsumsi_id }}"
                                                                    value="{{ $item->status_batal_m_pagi }}">
                                                                @if ($item->m_pagi != 0)
                                                                    <button type="button"
                                                                        id="batal_m_pagi{{ $item->konsumsi_id }}"
                                                                        class="btn btn-danger btn-sm ms-2 mb-1"
                                                                        style="{{ $item->m_pagi ? '' : 'display: none;' }}"
                                                                        onclick="showModal('m_pagi', {{ $item->konsumsi_id }})">x</button>
                                                                    <span
                                                                        id="status_text_m_pagi{{ $item->konsumsi_id }}"
                                                                        style="display: none;"></span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="form-check d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <input type="hidden" name="makan[siang]"
                                                                    value="0">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" name="makan[siang]"
                                                                    id="makan_siang{{ $item->konsumsi_id }}"
                                                                    {{ $item->m_siang ? 'checked' : '' }}
                                                                    {{ $item->m_siang ? '' : 'disabled' }}>
                                                                <label
                                                                    class="form-check-label {{ $item->status_batal_m_siang != 0 ? 'text-red' : '' }}"
                                                                    for="makan_siang{{ $item->konsumsi_id }}">Siang</label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="hidden" name="status_batal_m_siang"
                                                                    id="status_batal_m_siang{{ $item->konsumsi_id }}"
                                                                    value="{{ $item->status_batal_m_siang }}">
                                                                @if ($item->m_siang != 0)
                                                                    <button type="button"
                                                                        id="batal_m_siang{{ $item->konsumsi_id }}"
                                                                        class="btn btn-danger btn-sm ms-2 mb-1"
                                                                        style="{{ $item->m_siang ? '' : 'display: none;' }}"
                                                                        onclick="showModal('m_siang', {{ $item->konsumsi_id }})">x</button>
                                                                    <span
                                                                        id="status_text_m_siang{{ $item->konsumsi_id }}"
                                                                        style="display: none;"></span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="form-check d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <input type="hidden" name="makan[malam]"
                                                                    value="0">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" name="makan[malam]"
                                                                    id="makan_malam{{ $item->konsumsi_id }}"
                                                                    {{ $item->m_malam ? 'checked' : '' }}
                                                                    {{ $item->m_malam ? '' : 'disabled' }}>
                                                                <label
                                                                    class="form-check-label {{ $item->status_batal_m_malam != 0 ? 'text-red' : '' }}"
                                                                    for="makan_malam{{ $item->konsumsi_id }}">Malam</label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="hidden" name="status_batal_m_malam"
                                                                    id="status_batal_m_malam{{ $item->konsumsi_id }}"
                                                                    value="{{ $item->status_batal_m_malam }}">
                                                                @if ($item->m_malam != 0)
                                                                    <button type="button"
                                                                        id="batal_m_malam{{ $item->konsumsi_id }}"
                                                                        class="btn btn-danger btn-sm ms-2 mb-1"
                                                                        style="{{ $item->m_malam ? '' : 'display: none;' }}"
                                                                        onclick="showModal('m_malam', {{ $item->konsumsi_id }})">x</button>
                                                                    <span
                                                                        id="status_text_m_malam{{ $item->konsumsi_id }}"
                                                                        style="display: none;"></span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <b>Snack:</b><br>
                                                        <div
                                                            class="form-check d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <input type="hidden" name="snack[pagi]"
                                                                    value="0">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" name="snack[pagi]"
                                                                    id="snack_pagi{{ $item->konsumsi_id }}"
                                                                    {{ $item->s_pagi ? 'checked' : '' }}
                                                                    {{ $item->s_pagi ? '' : 'disabled' }}>
                                                                <label
                                                                    class="form-check-label {{ $item->status_batal_s_pagi != 0 ? 'text-red' : '' }}"
                                                                    for="snack_pagi{{ $item->konsumsi_id }}">Pagi</label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="hidden" name="status_batal_s_pagi"
                                                                    id="status_batal_s_pagi{{ $item->konsumsi_id }}"
                                                                    value="{{ $item->status_batal_s_pagi }}">
                                                                @if ($item->s_pagi != 0)
                                                                    <button type="button"
                                                                        id="batal_s_pagi{{ $item->konsumsi_id }}"
                                                                        class="btn btn-danger btn-sm ms-2 mb-1"
                                                                        style="{{ $item->s_pagi ? '' : 'display: none;' }}"
                                                                        onclick="showModal('s_pagi', {{ $item->konsumsi_id }})">x</button>
                                                                    <span
                                                                        id="status_text_s_pagi{{ $item->konsumsi_id }}"
                                                                        style="display: none;"></span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="form-check d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <input type="hidden" name="snack[siang]"
                                                                    value="0">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" name="snack[siang]"
                                                                    id="snack_siang{{ $item->konsumsi_id }}"
                                                                    {{ $item->s_siang ? 'checked' : '' }}
                                                                    {{ $item->s_siang ? '' : 'disabled' }}>
                                                                <label
                                                                    class="form-check-label {{ $item->status_batal_s_siang != 0 ? 'text-red' : '' }}"
                                                                    for="snack_siang{{ $item->konsumsi_id }}">Siang</label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="hidden" name="status_batal_s_siang"
                                                                    id="status_batal_s_siang{{ $item->konsumsi_id }}"
                                                                    value="{{ $item->status_batal_s_siang }}">
                                                                @if ($item->s_siang != 0)
                                                                    <button type="button"
                                                                        id="batal_s_siang{{ $item->konsumsi_id }}"
                                                                        class="btn btn-danger btn-sm ms-2 mb-1"
                                                                        style="{{ $item->s_siang ? '' : 'display: none;' }}"
                                                                        onclick="showModal('s_siang', {{ $item->konsumsi_id }})">x</button>
                                                                    <span
                                                                        id="status_text_s_siang{{ $item->konsumsi_id }}"
                                                                        style="display: none;"></span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="form-check d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <input type="hidden" name="snack[sore]"
                                                                    value="0">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" name="snack[sore]"
                                                                    id="snack_sore{{ $item->konsumsi_id }}"
                                                                    {{ $item->s_sore ? 'checked' : '' }}
                                                                    {{ $item->s_sore ? '' : 'disabled' }}>
                                                                <label
                                                                    class="form-check-label {{ $item->status_batal_s_sore != 0 ? 'text-red' : '' }}"
                                                                    for="snack_sore{{ $item->konsumsi_id }}">Sore</label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="hidden" name="status_batal_s_sore"
                                                                    id="status_batal_s_sore{{ $item->konsumsi_id }}"
                                                                    value="{{ $item->status_batal_s_sore }}">
                                                                @if ($item->s_sore != 0)
                                                                    <button type="button"
                                                                        id="batal_s_sore{{ $item->konsumsi_id }}"
                                                                        class="btn btn-danger btn-sm ms-2 mb-1"
                                                                        style="{{ $item->s_sore ? '' : 'display: none;' }}"
                                                                        onclick="showModal('s_sore', {{ $item->konsumsi_id }})">x</button>
                                                                    <span
                                                                        id="status_text_s_sore{{ $item->konsumsi_id }}"
                                                                        style="display: none;"></span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Biaya Makanan dan Snack -->
                                                <div class="row biaya-section">
                                                    <div class="col-md-6">
                                                        <div class="form-group biaya-m-pagi" style="display: none;">
                                                            <label for="biaya_m_pagi">Biaya Makan
                                                                Pagi</label>
                                                            @if ($item->status_batal_m_pagi == 1)
                                                                <input type="number" class="form-control"
                                                                    id="biaya_m_pagi" name="biaya_m_pagi"
                                                                    value="{{ $item->biaya_m_pagi }}" required>
                                                            @else
                                                                <input type="number" class="form-control"
                                                                    id="biaya_m_pagi" name="biaya_m_pagi"
                                                                    value="{{ $item->biaya_m_pagi }}">
                                                            @endif
                                                            @error('biaya_m_pagi')
                                                                {{ $message }}
                                                            @enderror
                                                        </div>

                                                        <div class="form-group biaya-m-siang" style="display: none;">
                                                            <label for="biaya_m_siang">Biaya Makan
                                                                Siang</label>
                                                            @if ($item->status_batal_m_siang == 1)
                                                                <input type="number" class="form-control"
                                                                    id="biaya_m_siang" name="biaya_m_siang"
                                                                    value="{{ $item->biaya_m_siang }}" required>
                                                            @else
                                                                <input type="number" class="form-control"
                                                                    id="biaya_m_siang" name="biaya_m_siang"
                                                                    value="{{ $item->biaya_m_pagi }}">
                                                            @endif
                                                        </div>

                                                        <div class="form-group biaya-m-malam" style="display: none;">
                                                            <label for="biaya_m_malam">Biaya Makan
                                                                Malam</label>
                                                            @if ($item->status_batal_m_malam == 1)
                                                                <input type="number" class="form-control"
                                                                    id="biaya_m_malam" name="biaya_m_malam"
                                                                    value="{{ $item->biaya_m_malam }}" required>
                                                            @else
                                                                <input type="number" class="form-control"
                                                                    id="biaya_m_malam" name="biaya_m_malam"
                                                                    value="{{ $item->biaya_m_malam }}">
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group biaya-s-pagi" style="display: none;">
                                                            <label for="biaya_s_pagi">Biaya Snack
                                                                Pagi</label>
                                                            @if ($item->status_batal_s_pagi == 1)
                                                                <input type="number" class="form-control"
                                                                    id="biaya_s_pagi" name="biaya_s_pagi"
                                                                    value="{{ $item->biaya_s_pagi }}" required>
                                                            @else
                                                                <input type="number" class="form-control"
                                                                    id="biaya_s_pagi" name="biaya_s_pagi"
                                                                    value="{{ $item->biaya_s_pagi }}">
                                                            @endif
                                                        </div>

                                                        <div class="form-group biaya-s-siang" style="display: none;">
                                                            <label for="biaya_s_siang">Biaya Snack
                                                                Siang</label>
                                                            @if ($item->status_batal_s_siang == 1)
                                                                <input type="number" class="form-control"
                                                                    id="biaya_s_siang" name="biaya_s_siang"
                                                                    value="{{ $item->biaya_s_siang }}" required>
                                                            @else
                                                                <input type="number" class="form-control"
                                                                    id="biaya_s_siang" name="biaya_s_siang"
                                                                    value="{{ $item->biaya_s_siang }}">
                                                            @endif
                                                        </div>

                                                        <div class="form-group biaya-s-sore" style="display: none;">
                                                            <label for="biaya_s_sore">Biaya Snack
                                                                Sore</label>
                                                            @if ($item->status_batal_s_sore == 1)
                                                                <input type="number" class="form-control"
                                                                    id="biaya_s_sore" name="biaya_s_sore"
                                                                    value="{{ $item->biaya_s_sore }}" required>
                                                            @else
                                                                <input type="number" class="form-control"
                                                                    id="biaya_s_sore" name="biaya_s_sore"
                                                                    value="{{ $item->biaya_s_sore }}">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Biaya Lain --}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="biaya_lain">Biaya
                                                                Lain-lain</label>
                                                            <input type="number" class="form-control"
                                                                id="biaya_lain" name="biaya_lain"
                                                                value="{{ $item->biaya_lain }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <!-- Keterangan -->
                                                        <div class="form-group">
                                                            <label for="keterangan">Keterangan</label>
                                                            <textarea class="form-control" id="keterangan" name="keterangan">{{ $item->keterangan ?? '' }}</textarea>
                                                        </div>
                                                    </div>
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
                            <!-- Modal Konfirmasi -->
                            <div class="modal fade confirmation-modal" id="confirmationModal" tabindex="-1"
                                role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Pembatalan
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah Anda sudah membeli konsumsi? Pilih opsi yang sesuai untuk pembatalan:
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-warning"
                                                    onclick="updateStatus(1, currentType, currentId)">Sudah beli
                                                    konsumsi</button>
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="updateStatus(2, currentType, currentId)">Belum beli
                                                    konsumsi</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-2">
                    {{ $konsumsi->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </main>
    </div>

    <!-- Modal untuk Filter Konsumsi -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Permintaan Konsumsi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formFilterKonsumsi">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <b>Tanggal Awal</b>
                                <input type="text" name="tanggal_mulai" id="tanggal_mulai" class="form-control"
                                    readonly="true" value="">
                            </div>
                            <div class="form-group col-md-6">
                                <b>Tanggal Akhir</b>
                                <input type="text" name="tanggal_akhir" id="tanggal_akhir" class="form-control"
                                    readonly="true" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <b>Agenda</b>
                            <input type="text" class="form-control" name="acara" id="filterAcara"
                                placeholder="Pencarian Agenda">
                        </div>
                        <div class="form-group">
                            <b>Bagian</b><br />
                            <select name="bagian[]" id="selectFilterBagian" class="form-control"
                                style="width: 100% !important;" multiple>
                                @foreach ($bagians as $bagian)
                                    <option value='{{ $bagian->id }}'>{{ $bagian->bagian }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <b>Posisi</b>
                                <select id="filterPosisi" name="posisi" class="form-control">
                                    <option value=''>Semua Posisi</option>
                                    <option value='kadiv'>Kadiv</option>
                                    <option value='kasubdiv'>Kasubdiv</option>
                                    <option value='asisten'>Asisten</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <b>Status</b>
                                <select id="filterStatus" name="status" class="form-control">
                                    <option value=''>Semua Status</option>
                                    <option value='0'>Tidak Batal</option>
                                    <option value='1'>Batal, Sudah Beli</option>
                                    <option value='2'>Batal, Belum Beli</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="buttonExportExcel" onclick="execute('exportExcel')"
                                class="btn btn-warning">Export Excel</button>
                            <button type="button" id="buttonFilterData" onclick="execute('filterData')"
                                class="btn btn-primary">Cari</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Filter Modal -->
    <x-slot name="scripts">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

        <script>
            let currentType = '';
            let currentId = '';

            function showModal(type, id) {
                currentType = type;
                currentId = id; // Simpan konsumsi_id saat modal ditampilkan
                $('#confirmationModal').modal('show');
            }

            $('#confirmationModal').on('hidden.bs.modal', function() {
                $('#editModal' + currentId).css('overflow-y', 'auto');
            });

            function updateStatus(status, type, id) {
                let biayaInput;

                switch (type) {
                    case 'm_pagi':
                        document.getElementById('status_batal_m_pagi' + id).value = status;
                        biayaInput = document.getElementById('biaya_m_pagi' + id);
                        break;
                    case 'm_siang':
                        document.getElementById('status_batal_m_siang' + id).value = status;
                        biayaInput = document.getElementById('biaya_m_siang' + id);
                        break;
                    case 'm_malam':
                        document.getElementById('status_batal_m_malam' + id).value = status;
                        biayaInput = document.getElementById('biaya_m_malam' + id);
                        break;
                    case 's_pagi':
                        document.getElementById('status_batal_s_pagi' + id).value = status;
                        biayaInput = document.getElementById('biaya_s_pagi' + id);
                        break;
                    case 's_siang':
                        document.getElementById('status_batal_s_siang' + id).value = status;
                        biayaInput = document.getElementById('biaya_s_siang' + id);
                        break;
                    case 's_sore':
                        document.getElementById('status_batal_s_sore' + id).value = status;
                        biayaInput = document.getElementById('biaya_s_sore' + id);
                        break;
                }

                updateDisplay(type, status, id);
                $('#confirmationModal').modal('hide');
            }


            function submitForm(id) {
                let valid = true;

                // Ambil status batal untuk semua jenis
                let statusBatal = {
                    m_pagi: $('#status_batal_m_pagi' + id).val(),
                    m_siang: $('#status_batal_m_siang' + id).val(),
                    m_malam: $('#status_batal_m_malam' + id).val(),
                    s_pagi: $('#status_batal_s_pagi' + id).val(),
                    s_siang: $('#status_batal_s_siang' + id).val(),
                    s_sore: $('#status_batal_s_sore' + id).val(),
                };

                for (const [type, status] of Object.entries(statusBatal)) {
                    let biayaInput = document.getElementById('biaya_' + type + id);

                    if (!biayaInput) {
                        console.error(`Input biaya untuk ${type} tidak ditemukan.`);
                        continue;
                    }

                    if (status == 1) {
                        const biayaValue = parseFloat(biayaInput.value);
                        if (biayaInput.value.trim() === "" || biayaValue <= 0 || isNaN(biayaValue)) {
                            alert("Biaya untuk " + type + " wajib diisi dan harus lebih dari 0.");
                            valid = false;
                            break;
                        }
                    }
                }
                if (valid) {
                    $('#editModal' + id + ' form').submit();
                } else {
                    console.log("Form tidak valid, tidak dapat disubmit.");
                }
            }

            function updateDisplay(type, status, id) {
                const buttonId = 'batal_' + type + id;
                const statusTextId = 'status_text_' + type + id;

                document.getElementById(buttonId).style.display = 'none';
                const statusText = document.getElementById(statusTextId);

                if (status === 1) {
                    statusText.innerText = 'Dibatalkan, sudah beli konsumsi';
                } else {
                    statusText.innerText = 'Dibatalkan, belum beli konsumsi';
                }

                statusText.style.display = 'inline';
            }

            $('#editModal').on('hide.bs.modal', function(e) {
                if ($('#confirmationModal').is(':visible')) {
                    e.preventDefault();
                }
            });

            document.addEventListener("DOMContentLoaded", function() {
                let checkboxes = document.querySelectorAll('.form-check-input:checked');

                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('click', function(e) {
                        e.preventDefault();
                    });
                });

                function toggleKirimButton() {
                    let allCheckboxes = document.querySelectorAll('.checkbox-item');
                    let KirimButtons = document.querySelectorAll('.btn-kirim');

                    allCheckboxes.forEach(function(checkbox, index) {
                        let KirimButton = KirimButtons[index];

                        if (checkbox.checked) {
                            KirimButton.classList.add('disabled');
                        } else {
                            KirimButton.classList.remove('disabled');
                        }
                    });
                }

                function updateSelectAllButtonText() {
                    let allCheckboxes = document.querySelectorAll('.checkbox-item');
                    let checkedCheckboxes = document.querySelectorAll('.checkbox-item:checked').length;
                    let selectAllButton = document.getElementById('select-all-button');

                    if (selectAllButton) {
                        selectAllButton.textContent = 'Select All';
                        if (checkedCheckboxes === 0) {
                            selectAllButton.textContent = 'Select All';
                        } else if (checkedCheckboxes === allCheckboxes.length) {
                            selectAllButton.textContent = 'Deselect All';
                        } else {
                            selectAllButton.textContent = 'Select All';
                        }
                    }
                }
                document.getElementById('select-all-button').addEventListener('click', function() {
                    let allCheckboxes = document.querySelectorAll('.checkbox-item');
                    let isAllChecked = Array.from(allCheckboxes).some(checkbox => !checkbox.checked);


                    allCheckboxes.forEach(function(checkbox) {
                        checkbox.checked = isAllChecked;
                    });

                    updateSelectAllButtonText();
                    toggleKirimButton();
                });
                document.querySelectorAll('.checkbox-item').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        updateSelectAllButtonText();
                        toggleKirimButton();
                    });
                });
                updateSelectAllButtonText();
                toggleKirimButton();
            });

            function kirimSelected() {
                // console.log("KirimSelected");
                var selectedCheckboxes = document.querySelectorAll('.checkbox-item:checked');
                var selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

                if (selectedIds.length === 0) {
                    swal({
                        title: 'Peringatan!',
                        text: 'Silakan Pilih Setidaknya Satu Data Konsumsi.',
                        icon: 'warning',
                        confirmButtonColor: '#41B314',
                    });
                    return;
                }

                swal({
                    title: "Konfirmasi Pengiriman",
                    text: `Apakah Anda yakin ingin mengirim ${selectedIds.length} data konsumsi ini?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    confirmButtonText: "Kirim!",
                    cancelButtonText: "Batal",
                    closeOnConfirm: false
                }, function() {
                    var idsString = selectedIds.join(',');
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ url('konsumsi/kirim') }}/${idsString}`;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    document.body.appendChild(form);
                    form.submit();
                });
            }


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

                $('[id^=editModal]').on('show.bs.modal', function(event) {
                    let modal = $(this);
                    let itemId = modal.attr('id').replace('editModal', '');

                    modal.find('.biaya-section .form-group').hide();

                    const statusBatal = {
                        m_pagi: $('#status_batal_m_pagi' + itemId).val(),
                        m_siang: $('#status_batal_m_siang' + itemId).val(),
                        m_malam: $('#status_batal_m_malam' + itemId).val(),
                        s_pagi: $('#status_batal_s_pagi' + itemId).val(),
                        s_siang: $('#status_batal_s_siang' + itemId).val(),
                        s_sore: $('#status_batal_s_sore' + itemId).val(),
                    };

                    // Memeriksa status_batal untuk setiap jenis konsumsi
                    for (const [type, status] of Object.entries(statusBatal)) {
                        const button = modal.find('#batal_' + type + itemId);
                        const statusText = modal.find('#status_text_' + type + itemId);
                        if (status != 0) {
                            button.hide();
                            statusText.text(status == 1 ? 'Dibatalkan, sudah beli konsumsi' :
                                'Dibatalkan, belum beli konsumsi');
                            statusText.addClass('text-red');
                            statusText.show();
                        } else {
                            button.show();
                            statusText.hide();
                        }
                    }

                    if ($('#makan_pagi' + itemId).is(':checked')) {
                        modal.find('.biaya-m-pagi').show();
                    }

                    if ($('#makan_siang' + itemId).is(':checked')) {
                        modal.find('.biaya-m-siang').show();
                    }

                    if ($('#makan_malam' + itemId).is(':checked')) {
                        modal.find('.biaya-m-malam').show();
                    }

                    if ($('#snack_pagi' + itemId).is(':checked')) {
                        modal.find('.biaya-s-pagi').show();
                    }

                    if ($('#snack_siang' + itemId).is(':checked')) {
                        modal.find('.biaya-s-siang').show();
                    }

                    if ($('#snack_sore' + itemId).is(':checked')) {
                        modal.find('.biaya-s-sore').show();
                    }
                });

            });

            function confirmKirim(id) {
                swal({
                    title: "Konfirmasi Pengiriman",
                    text: "Apakah Anda yakin ingin mengirim data konsumsi ini?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    confirmButtonText: "Kirim!",
                    cancelButtonText: "Batal",
                    closeOnConfirm: false
                }, function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url('konsumsi/kirim') }}/' + id;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    document.body.appendChild(form);
                    form.submit();
                });
            }

            function confirmApprove(id) {
                swal({
                    title: "Konfirmasi Approval",
                    text: "Apakah Anda yakin ingin menyetujui data konsumsi ini?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Approve!",
                    cancelButtonText: "Cancel"
                }, function(isConfirm) {
                    if (isConfirm) {
                        console.log("Submitting form for ID:", id);
                        document.getElementById('approve-form-' + id).submit();
                    } else {
                        console.log("Approval canceled.");
                    }
                });
            }

            function confirmDelete(id) {
                if (confirm("Apakah Anda yakin ingin membatalkan permintaan konsumsi ini?")) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>

        <script>
            function execute(whatFor) {
                form = $('#formFilterKonsumsi');
                formData = new FormData(form[0]);

                if (whatFor === 'exportExcel') {
                    fetch("{{ route('konsumsi.exportExcel') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: formData,
                    }).then((response) => {
                        if (!response.ok) {
                            throw new Error('Gagal Export Excel');
                        }

                        return response.blob();
                    }).then((blob) => {
                        const url = URL.createObjectURL(blob); // Membuat URL objek dari Blob

                        const a = document.createElement('a'); // Membuat elemen anchor
                        a.href = url;
                        a.target = '_blank';
                        a.download =
                            `Laporan Permintaan Konsumsi_{{ time() }}.xlsx`; // Nama file yang akan diunduh
                        document.body.appendChild(a); // Menambahkan elemen anchor ke body
                        a.click(); // Mengklik anchor untuk memulai unduhan
                        a.remove(); // Menghapus elemen anchor setelah unduhan

                        URL.revokeObjectURL(url); // Menghapus URL objek untuk membebaskan memori
                    }).catch((error) => {
                        swal({
                            title: 'Error!',
                            text: 'Gagal Export Excel!',
                            type: 'error',
                        });
                    });
                } else if (whatFor === 'filterData') {
                    form.prop('action', "{{ route('konsumsi.index') }}");
                    form.prop('method', 'GET');
                    form.submit();
                }
            }

            $(document).ready(function() {
                date_mulai = new Date();
                now_mulai =
                    @if (Session::has('tanggal_mulai'))
                        '{{ Session::get('tanggal_mulai') }}'
                    @else
                        `${date_mulai.getDate()}-${date_mulai.getMonth() + 1}-${date_mulai.getFullYear()}`
                    @endif ;
                date_akhir = new Date();
                now_akhir =
                    @if (Session::has('tanggal_akhir'))
                        '{{ Session::get('tanggal_akhir') }}'
                    @else
                        `${date_akhir.getDate()}-${date_akhir.getMonth() + 1}-${date_akhir.getFullYear()}`
                    @endif ;
                $('#tanggal_mulai').val(now_mulai);
                $('#tanggal_akhir').val(now_akhir);

                $('#tanggal_mulai.form-control').datepicker({
                    dateFormat: "dd-mm-yy",
                    showButtonPanel: true,
                    closeText: 'Clear',
                    onClose: function(dateText, inst) {
                        if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
                            document.getElementById(this.id).value = '';
                        }
                    }
                });

                $('#tanggal_akhir.form-control').datepicker({
                    dateFormat: "dd-mm-yy",
                    showButtonPanel: true,
                    closeText: 'Clear',
                    onClose: function(dateText, inst) {
                        if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
                            document.getElementById(this.id).value = '';
                        }
                    }
                });

                $('#selectFilterBagian').select2();

                @if (Session::has('bagian'))
                    $('#selectFilterBagian').val('{{ implode(',', Session::get('bagian')) }}'.split(',')).trigger(
                        'change');
                @endif

                @if (Session::has('posisi'))
                    $('#filterPosisi').val('{{ Session::get('posisi') }}').trigger('change');
                @endif

                @if (Session::has('status'))
                    $('#filterStatus').val('{{ Session::get('status') }}').trigger('change');
                @endif

                @if (Session::has('acara'))
                    $('#filterAcara').val('{{ Session::get('acara') }}');
                @endif
            });
        </script>
    </x-slot>
</x-layouts.app>
