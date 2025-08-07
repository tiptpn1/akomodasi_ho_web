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
            .prev-btn, .next-btn {
                width: 30px;
                height: 30px;
                font-size: 18px;
                text-align: center;
                padding: 0;
                line-height: 30px;
                background-color: rgba(255, 255, 255, 0.8);
                border: 1px solid #ccc;
                border-radius: 50%;
            }
        </style>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </x-slot>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h2 class="mb-4">Daftar Booking Kamar</h2>
                <button id="btnExport" type="button" data-toggle="modal" data-target="#exportModal" class="btn btn-warning">Export Data</button>
                <br><br>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-warning">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('bookingkamar.list_booking') }}" class="mb-4">
                    <div class="row">
                        @if(Auth::user()->master_nama_bagian_id != 53)
                        <div class="col-md-3">
                            <label>Mess</label>
                            <select name="mess" class="form-control">
                                <option value="all">-- Semua Mess --</option>
                                @foreach($mess as $m)
                                    <option value="{{ $m->id }}" {{ request('mess') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="col-md-3">
                            <label>Mess</label>
                            {{-- Gunakan relasi `mess` dan kolom `nama` dari model MessModel --}}
                            <input type="text" class="form-control" value="{{ Auth::user()->mess->nama ?? 'Mess tidak terdaftar' }}" disabled>
                            {{-- Gunakan langsung kolom `master_mess_id` dari user untuk input hidden --}}
                            <input type="hidden" name="mess" value="{{ Auth::user()->master_mess_id }}">
                        </div>
                        @endif

                        <div class="col-md-3">
                            <label>Tanggal Mulai (Dari)</label>
                            <input type="date" name="tgl_awal" class="form-control" value="{{ request('tgl_awal') }}">
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Selesai (Sampai)</label>
                            <input type="date" name="tgl_akhir" class="form-control" value="{{ request('tgl_akhir') }}">
                        </div>
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="all">-- Semua Status --</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('bookingkamar.list_booking') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTables-kaskecil">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pemesan</th>
                                <th>Kamar</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody >
                            @foreach($bookings as $index => $booking)
                            {{-- @if (!empty(Auth::user()->mess)) --}}{{-- Ini sebaiknya logic di controller atau diubah --}}
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $booking->nama_pemesan }}</td>
                                <td>{{ $booking->kamar->nama_kamar }} - {{ $booking->kamar->mess->nama ?? '-' }}</td>
                                <td>{{ $booking->tanggal_mulai }}</td>
                                <td>{{ $booking->tanggal_selesai }}</td>
                                <td>
                                    <span class="badge 
                                        @if($booking->status == 'pending') bg-warning 
                                        @elseif($booking->status == 'approved') bg-success 
                                        @elseif($booking->status == 'rejected') bg-danger 
                                        @elseif($booking->status == 'cancelled') bg-secondary 
                                        @elseif($booking->status == 'checked_out') bg-primary
                                        @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal"
                                        data-id="{{ $booking->id }}"
                                        data-nama="{{ $booking->nama_pemesan }}"
                                        data-kamar="{{ $booking->kamar->nama_kamar }} - {{ $booking->kamar->mess->nama ?? '-' }}"
                                        data-jabatan="{{ $booking->jabatan }}"
                                        data-regional="{{ $booking->regional }}"
                                        data-email="{{ $booking->email }}"
                                        data-no_hp="{{ $booking->no_hp }}"
                                        data-tanggal_mulai="{{ $booking->tanggal_mulai }}"
                                        data-tanggal_selesai="{{ $booking->tanggal_selesai }}"
                                        data-catatan="{{ $booking->catatan }}"
                                        data-status="{{ ucfirst($booking->status) }} ({{ $booking->keterangan ?? '-' }})"
                                        data-dokumen="{{ $booking->dokumen_pendukung }}">
                                        Detail
                                    </button>

                                    {{-- Tombol Edit --}}
                                    @if(in_array(Auth::user()->master_hak_akses_id, [1, 2]) && $booking->status != 'checked_out')
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-id="{{ $booking->id }}">
                                            Edit
                                        </button>
                                    @endif

                                    {{-- Tombol Checkout --}}
                                    @if(Auth::user()->master_nama_bagian_id == 53 && $booking->status == 'approved')
                                    <form action="{{ route('bookingkamar.checkout', $booking->id) }}" method="POST" class="d-inline show-loading-on-submit">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning btn-sm">Checkout</button>
                                    </form>
                                    @endif

                                    @if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga']))
                                        @if($booking->status == 'pending')
                                            <form action="{{ route('bookingkamar.approve', $booking->id) }}" method="POST" class="d-inline show-loading-on-submit">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <button class="btn btn-danger btn-sm show-loading-on-submit" data-bs-toggle="modal" data-bs-target="#rejectModal"
                                                data-id="{{ $booking->id }}">
                                                Reject
                                            </button>
                                        @endif
                                        @if($booking->status == 'approved')
                                        <button class="btn btn-primary btn-sm show-loading-on-submit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#perpanjangModal"
                                            data-id="{{ $booking->id }}"
                                            data-nama="{{ $booking->nama_pemesan }}"
                                            data-kamar="{{ $booking->kamar->nama_kamar }} - {{ $booking->kamar->mess->nama ?? '-' }}"
                                            data-tgl_awal="{{ $booking->tanggal_mulai }}"
                                            data-tanggal_selesai="{{ $booking->tanggal_selesai }}">
                                            Perpanjang
                                        </button>
                                        <form action="{{ route('bookingkamar.checkout', $booking->id) }}" method="POST" class="d-inline show-loading-on-submit">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-sm">Checkout</button>
                                        </form>
                                        @endif
                                    @elseif(auth()->user()->role == 'user' && $booking->status == 'pending')
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal"
                                            data-id="{{ $booking->id }}">
                                            Batalkan
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            {{-- @endif --}}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Export Data -->
            <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exportModalLabel">Export Data Booking</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="exportForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tgl_pengajuan_awal">Tanggal Awal</label>
                                                <input type="date" class="form-control" id="tgl_awal" name="tgl_awal">
                                            </div>
                                            <div class="form-group">
                                                <label for="nama_group">Mess</label>
                                                <select class="form-control" name="mess">
                                                @if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga']))
                                                    <option value="" disabled selected>Pilih Mess</option>
                                                    <option value='all'>Seluruh Mess</option>
                                                    @foreach ($mess as $data_mess)
                                                    <option value='{{ $data_mess->id }}'>{{ $data_mess->nama }}</option>
                                                    @endforeach
                                                    
                                                @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tgl_pengajuan_akhir">Tanggal Akhir</label>
                                                <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir">
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control" name="status">
                                                    <option value="" disabled selected>Pilih Status</option>
                                                    <option value='approved'>Approved</option>
                                                    <option value='rejected'>Rejected</option>
                                                    <option value='canceled'>Canceled</option>
                                                    <option value='pending'>Pending</option>
                                                    <option value='checked_out'>Check Out</option>
                                                    <option value='all'>Seluruh Status</option>
                                                    
                                                </select>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="exportBtn">Export to Excel</button>
                                    <button type="reset" class="btn btn-secondary mr-2" id="resetBtn">Reset Filter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- Modal Perpanjang --}}
            <div class="modal fade" id="perpanjangModal" tabindex="-1" aria-labelledby="perpanjangModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="perpanjangForm" method="POST">
                            @csrf
                            @method('PATCH')
                <input type="hidden" name="id" id="perpanjang_id">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Perpanjang Masa Menginap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                    <input type="hidden" name="booking_id" id="rejectBookingId">
                    <p><strong>Nama Pemesan:</strong> <span id="perpanjang_nama"></span></p>
                    <p><strong>Kamar:</strong> <span id="perpanjang_kamar"></span></p>
                    <p><strong>Tanggal Mulai:</strong> <span id="perpanjang_awal"></span></p>
                    <p><strong>Tanggal Selesai:</strong> <span id="perpanjang_selesai"></span></p>
                    <div class="mb-3">
                        <label for="tanggal_selesai_baru" class="form-label">Tanggal Selesai Baru</label>
                        <input type="date" class="form-control" name="tanggal_selesai_baru" id="tanggal_selesai_baru" required>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
                </form>
            </div>
            </div>

            {{-- Modal Edit --}}    
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editBookingForm" action="" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="editBookingId" name="booking_id"> 

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Nama Pemesan</label>
                                    <input type="text" id="editNamaPemesan"name="nama_pemesan" class="form-control" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Nama Kamar</label>
                                    <select class="form-select select2-edit-kamar" name="kamar_id" id="editKamarId" required>
                                        <option></option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Jabatan</label>
                                   <select class="form-select select2-edit-jabatan" name="jabatan" id="editJabatan" required>
                                        <option></option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Regional</label>
                                    <select class="form-select" name="regional" id="editRegional" required>
                                        <option value="" disabled selected>Pilih Regional</option>
                                        @php
                                            $regionals = ['Head Office','Regional 1', 'Regional 2', 'Regional 3', 'Regional 4',
                                                            'Regional 5', 'Regional 6', 'Regional 7', 'Regional 8'];
                                        @endphp
                                        @foreach($regionals as $reg)
                                            <option value="{{ $reg }}">{{ $reg }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" id="editEmail" name="email" class="form-control" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">No HP</label>
                                    <input type="number" id="editNoHp" name="no_hp" class="form-control" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">Tanggal Mulai</label>
                                    <input type="date" id="editTanggalMulai" name="tanggal_mulai" class="form-control bg-light">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">Tanggal Selesai</label>
                                    <input type="date" id="editTanggalSelesai" name="tanggal_selesai" class="form-control bg-light">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Catatan</label>
                                    <textarea id="editCatatan" name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Dokumen Pendukung (Opsional)</label>
                                    <input type="file" name="dokumen_pendukung" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                                    <div class="form-text text-muted mt-2" id="currentDokumenInfo">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer py-3">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-warning px-4 show-loading-on-submit" form="editBookingForm"> {{-- Tambahkan form="editBookingForm" --}}
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Detail  --}}
            <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailModalLabel">Detail Booking</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Nama Pemesan:</strong> <span id="detailNama"></span></p>
                            <p><strong>Kamar:</strong> <span id="detailKamar"></span></p>
                            <p><strong>Jabatan:</strong> <span id="detailJabatan"></span></p>
                            <p><strong>Regional:</strong> <span id="detailRegional"></span></p>
                            <p><strong>Email:</strong> <span id="detailEmail"></span></p>
                            <p><strong>No HP:</strong> <span id="detailNoHp"></span></p>
                            <p><strong>Tanggal Mulai:</strong> <span id="detailTanggalMulai"></span></p>
                            <p><strong>Tanggal Selesai:</strong> <span id="detailTanggalSelesai"></span></p>
                            <p><strong>Catatan:</strong> <span id="detailCatatan"></span></p>
                            <p><strong>Status:</strong> <span id="detailStatus"></span></p>
                            <p><strong>Dokumen:</strong> <a id="detailDokumen" href="#" target="_blank">Lihat Dokumen</a></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                <div class="modal-body">
                    <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5>Proses...</h5>
                </div>
                </div>
            </div>
            </div>

            <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel">Tolak Booking</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('bookingkamar.reject', ':id') }}" method="POST" id="rejectForm">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body">
                                <input type="hidden" name="booking_id" id="rejectBookingId">
                                <label for="alasan_reject">Alasan Penolakan:</label>
                                <textarea name="alasan_reject" class="form-control" required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-danger">Tolak</button>
                            </div>
                        </form>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                $('#dataTables-kaskecil').DataTable({
                    responsive: true
                });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let detailModal = document.getElementById('detailModal');
                detailModal.addEventListener('show.bs.modal', function (event) {
                    let button = event.relatedTarget;
                    document.getElementById("detailNama").textContent = button.getAttribute("data-nama");
                    document.getElementById("detailKamar").textContent = button.getAttribute("data-kamar");
                    document.getElementById("detailJabatan").textContent = button.getAttribute("data-jabatan");
                    document.getElementById("detailRegional").textContent = button.getAttribute("data-regional");
                    document.getElementById("detailEmail").textContent = button.getAttribute("data-email");
                    document.getElementById("detailNoHp").textContent = button.getAttribute("data-no_hp");
                    document.getElementById("detailTanggalMulai").textContent = button.getAttribute("data-tanggal_mulai");
                    document.getElementById("detailTanggalSelesai").textContent = button.getAttribute("data-tanggal_selesai");
                    document.getElementById("detailCatatan").textContent = button.getAttribute("data-catatan");
                    document.getElementById("detailStatus").textContent = button.getAttribute("data-status");
                    
                    // Handle dokumen pendukung
                    let dokumenPath = button.getAttribute("data-dokumen");
                    let detailDokumenLink = document.getElementById("detailDokumen");
                    if (dokumenPath && dokumenPath !== 'null' && dokumenPath !== '') { // Pastikan dokumenPath tidak null atau string kosong
                        detailDokumenLink.href = "/storage/" + dokumenPath;
                        detailDokumenLink.textContent = dokumenPath.split('/').pop(); // Tampilkan hanya nama file
                        detailDokumenLink.style.display = 'inline'; // Tampilkan link
                    } else {
                        detailDokumenLink.textContent = 'Tidak ada dokumen';
                        detailDokumenLink.removeAttribute('href'); // Hapus href jika tidak ada dokumen
                        detailDokumenLink.style.display = 'block'; // Sembunyikan link atau tampilkan teks "Tidak ada dokumen"
                    }
                });
            
                let rejectModal = document.getElementById('rejectModal');
                let rejectForm = document.getElementById('rejectForm');
                rejectModal.addEventListener('show.bs.modal', function (event) {
                    let button = event.relatedTarget;
                    let bookingId = button.getAttribute('data-id');
                    rejectForm.action = rejectForm.action.replace(':id', bookingId);
                    document.getElementById('rejectBookingId').value = bookingId;
                });
            });
        </script>
        <script>
            $('#perpanjangModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget)
                var id = button.data('id')
                var nama = button.data('nama')
                var kamar = button.data('kamar')
                var tanggalawal = button.data('tgl_awal')
                var tanggalSelesai = button.data('tanggal_selesai')

                var modal = $(this)
                modal.find('#perpanjang_id').val(id)
                modal.find('#perpanjang_nama').text(nama)
                modal.find('#perpanjang_kamar').text(kamar)
                modal.find('#perpanjang_selesai').text(tanggalSelesai)
                modal.find('#perpanjang_awal').text(tanggalawal)

                var minDate = new Date(tanggalSelesai);
                minDate.setDate(minDate.getDate() + 1); // Tambah 1 hari

                // Format kembali ke YYYY-MM-DD
                let day = ("0" + minDate.getDate()).slice(-2);
                let month = ("0" + (minDate.getMonth() + 1)).slice(-2);
                let formattedMinDate = minDate.getFullYear() + "-" + month + "-" + day;

                modal.find('#tanggal_selesai_baru').attr('min', formattedMinDate);
                modal.find('#tanggal_selesai_baru').val(formattedMinDate); // Opsional: langsung isi dengan default date
                modal.find('#perpanjangForm').attr('action', `/bookingkamar/booking/perpanjangan/${id}`);
            })
        </script>
        <script>
            $('#exportBtn').on('click', function() {
                var formData = $('#exportForm').serialize(); // Get the form data

                // Trigger the Excel export request with the selected filters
                window.location.href = "{{ route('bookingkamar.export') }}?" + formData;

                // Use JavaScript to simulate a click event on the element with data-dismiss="modal"
                $('[data-dismiss="modal"]').click();
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const forms = document.querySelectorAll('form.show-loading-on-submit');

                forms.forEach(function(form) {
                    form.addEventListener('submit', function () {
                        const modalElement = document.getElementById('loadingModal');
                        const loadingModal = new bootstrap.Modal(modalElement, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        loadingModal.show();
                    });
                });
            });
        </script>

        <script>
        $(document).ready(function () {
            // Inisialisasi Select2
            $('.select2-edit-kamar, .select2-edit-jabatan').select2({
                dropdownParent: $('#editModal'),
                width: '100%',
                placeholder: function(){
                    return $(this).data('placeholder');
                },
                theme: 'bootstrap-5'
            });

            // Reset form
            function resetFormEdit() {
                $('#editBookingForm')[0].reset();
                $('#editKamarId').val(null).trigger('change.select2');
                $('#editJabatan').val(null).trigger('change.select2');
                $('#editRegional').val(null).trigger('change.select2');
                $('#currentDokumenInfo').text('');
            }

            // Buka modal edit
            $(document).on('click', '.btn-warning[data-bs-target="#editModal"]', function () {
                resetFormEdit();
                const id = $(this).data('id');

                $.ajax({
                    url: `/bookingkamar/edit/${id}`,
                    method: 'GET',
                    success: function (data) {
                        const booking = data.booking;
                        const kamarList = data.kamar_list;
                        const jabatanList = data.jabatan_list;

                        $('#editBookingId').val(booking.id);
                        $('#editNamaPemesan').val(booking.nama_pemesan);
                        $('#editEmail').val(booking.email);
                        $('#editNoHp').val(booking.no_hp);
                        $('#editTanggalMulai').val(booking.tanggal_mulai);
                        $('#editTanggalSelesai').val(booking.tanggal_selesai);
                        $('#editCatatan').val(booking.catatan);
                        $('#editBookingForm').attr('action', `/bookingkamar/update/${booking.id}`);

                        // Dropdown Kamar
                        const kamarSelect = $('#editKamarId');
                        kamarSelect.empty().append('<option value="" disabled selected>Pilih Kamar</option>');
                        $.each(kamarList, function (i, kamar) {
                            const namaMess = kamar.mess ? kamar.mess.nama : '-';
                            kamarSelect.append(
                                `<option value="${kamar.id}">${namaMess} - ${kamar.nama_kamar}</option>`
                            );
                        });
                        kamarSelect.val(booking.kamar_id).trigger('change.select2');

                        // Dropdown Jabatan
                        const jabatanSelect = $('#editJabatan');
                        jabatanSelect.empty().append('<option value="" disabled selected>Pilih Jabatan</option>');
                        $.each(jabatanList, function (i, jabatan) {
                            jabatanSelect.append(
                                `<option value="${jabatan.jabatan}">${jabatan.jabatan}</option>`
                            );
                        });
                        jabatanSelect.val(booking.jabatan).trigger('change.select2');

                        // Dropdown Regional
                        $('#editRegional').val(booking.regional).trigger('change.select2');

                        // Dokumen pendukung
                        if (booking.dokumen_pendukung) {
                            $('#currentDokumenInfo').html(
                                `Dokumen saat ini: <a href="/storage/${booking.dokumen_pendukung}" target="_blank">${booking.dokumen_pendukung.split('/').pop()}</a>`
                            );
                        } else {
                            $('#currentDokumenInfo').text('Belum ada dokumen pendukung.');
                        }
                    },
                    error: function (xhr) {
                        console.error('Gagal ambil data booking:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Ambil Data',
                            text: 'Terjadi kesalahan saat mengambil data booking.'
                        });
                    }
                });
            });

            // Submit form update via AJAX
            $(document).on('submit', '#editBookingForm', function (e) {
                e.preventDefault();

                const form = $(this);
                const actionUrl = form.attr('action');
                const formData = new FormData(this);

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                location.reload();

                                // Reload datatable dengan aman
                                const dt = $('#table').DataTable();
                                if (dt) {
                                    dt.ajax.reload(null, false); 
                                } else {
                                    location.reload(); 
                                }
                            });
                        }
                    },
                    error: function (xhr) {
                        console.error('Error saat update:', xhr);
                        let message = 'Terjadi kesalahan saat menyimpan data.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            html: message
                        });
                    }
                });
            });

            // Reset form saat modal ditutup
            $('#editModal').on('hidden.bs.modal', function () {
                resetFormEdit();
            });
        });
        </script>


    </x-slot>
</x-layouts.app>