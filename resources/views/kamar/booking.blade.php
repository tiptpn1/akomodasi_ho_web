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
                border-color: #2471a3 !important;
                /* Warna border lebih gelap */
            }

            .btn-info:hover {
                background-color: #1f6690 !important;
                /* Warna lebih gelap saat hover */
                border-color: #1a5579 !important;
            }

            .prev-btn,
            .next-btn {
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
        <!-- CSS Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- JavaScript Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </x-slot>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h2 class="mb-4">Booking Kamar</h2>
                @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- <form action="{{ route('bookingkamar.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggalMulai" class="form-control"
                            value="{{ request('tanggal_mulai', date('Y-m-d')) }}" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggalSelesai" class="form-control"
                            value="{{ request('tanggal_selesai', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Cek Ketersediaan</button>
                    </div>
                </div>
                </form> --}}

                <form action="{{ route('bookingkamar.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        <!-- Filter Tanggal -->
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggalMulai" class="form-control"
                                value="{{ request('tanggal_mulai', date('Y-m-d')) }}" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggalSelesai" class="form-control"
                                value="{{ request('tanggal_selesai', date('Y-m-d')) }}" required>
                        </div>

                        <!-- Filter Mess -->
                        <div class="col-md-3">
                            <label class="form-label">Mess</label>
                            <select name="mess_id" class="form-control">
                                <option value="all" {{ request('mess_id') == 'all' ? 'selected' : '' }}>-- Semua Mess --</option>
                                @foreach($messes as $mess)
                                <option value="{{ $mess->id }}" {{ request('mess_id') == $mess->id ? 'selected' : '' }}>
                                    {{ $mess->nama }} - {{ $mess->lokasi }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Jabatan -->
                        <div class="col-md-3">
                            <label class="form-label">Jabatan</label>
                            <select name="jabatan_id" class="form-control">
                                <option value="all" {{ request('jabatan_id') == 'all' ? 'selected' : '' }}>-- Semua Jabatan --</option>
                                @foreach($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}" {{ request('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                                    {{ $jabatan->jabatan }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 d-flex align-items-end">
                            <br><br>
                            <button type="submit" class="btn btn-success btn-block">Cek Ketersediaan</button>
                        </div>
                    </div>
                </form>




                @if(request('tanggal_mulai') && request('tanggal_selesai'))
                <div class="row">
                    @forelse($kamars as $kamar)
                    @if ($kamar->sisa_kapasitas > 0)
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <!-- Gambar Utama -->
                            <a href="{{ asset($kamar->photos->firstWhere('is_utama', true)?->foto ?? 'images/default.jpg') }}"
                                class="main-photo-link" target="_blank">
                                <img src="{{ asset($kamar->photos->firstWhere('is_utama', true)?->foto ?? 'images/default.jpg') }}"
                                    class="card-img-top"
                                    alt="Gambar Kamar"
                                    style="height: 250px; object-fit: cover; cursor: pointer;">
                            </a>
                            
                            <!-- Galeri Gambar Pendukung -->
                            <div class="row mt-2 px-2">
                                <div class="col-12 position-relative">
                                    <!-- Tombol Prev -->
                                    <button class="btn btn-sm btn-light prev-btn" data-kamar-id="{{ $kamar->id }}"
                                        style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); z-index: 10; width: 30px; height: 30px; display: none;">❮</button>

                                    <!-- Galeri Gambar -->
                                    <div class="gallery-container d-flex" data-kamar-id="{{ $kamar->id }}">
                                        @foreach($kamar->photos->where('is_utama', '!=', true) as $photo)
                                        <div class="gallery-item" style="padding: 2px;">
                                            <img src="{{ asset($photo->foto) }}"
                                                class="img-fluid rounded kamar-photo"
                                                data-kamar-id="{{ $kamar->id }}"
                                                style="height: 60px; object-fit: cover; cursor: pointer;">
                                        </div>
                                        @endforeach
                                    </div>



                                    <!-- Tombol Next -->
                                    <button class="btn btn-sm btn-light next-btn" data-kamar-id="{{ $kamar->id }}"
                                        style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); z-index: 10; width: 30px; height: 30px; display: none;">❯</button>
                                </div>
                            </div>


                            <div class="card-body">
                                <h4 class="card-title"><strong>{{ $kamar->nama_kamar }}</strong></h4>
                                <p class="card-text mb-1"><strong>Mess:</strong> {{ $kamar->mess->nama }}</p>
                                <p class="card-text mb-1"><strong>Alamat:</strong> {{ $kamar->mess->lokasi }}
                                    <button class="btn btn-sm btn-info" style="color: white;" onclick="window.open('https://www.google.com/maps?q={{ $kamar->mess->lat }},{{ $kamar->mess->lng }}', '_blank')"><i class="fa fa-map-marker" aria-hidden="true"></i></button>
                                </p>
                                <p class="card-text mb-1"><strong>Jarak Ke HO :</strong> ± {{ $kamar->mess->jarak ?? '-'}} km ({{ $kamar->mess->waktu ?? '-'}} menit)</p>
                                <!-- <p class="card-text"><strong>Contact Person:</strong> {{ $kamar->mess->cp ?? '' }} - {{ $kamar->mess->no_cp ?? ''}}</p> -->
                                @if($kamar->petugas->isNotEmpty())
                                <p class="card-text mb-1"><strong>Contact Person:</strong><br>
                                    @foreach($kamar->petugas as $petugas)
                                    | {{ $petugas->nama_petugas }} - {{ $petugas->no_petugas }}<br>
                                    @endforeach
                                </p>
                                @else
                                <p class="card-text mb-1"><strong>Contact Person:</strong> Tidak ada petugas terdaftar.</p>
                                @endif
                                <p class="card-text mb-1"><strong>Kapasitas Bed:</strong> {{ $kamar->kapasitas }}</p>
                                <p class="card-text mb-1"><strong>Ketersediaan Bed:</strong> {{ $kamar->sisa_kapasitas }}</p>
                                <p class="card-text mb-1"><strong>Peruntukan:</strong> {{ $kamar->peruntukan_teks  }}</p>
                                <p class="card-text mb-1"><strong>Fasilitas:</strong> {{ $kamar->fasilitas }}</p>
                                {{-- <p class="card-text"><strong>
                                Rating: </strong>
                                {{ $kamar->rating }} ⭐ ({{ $kamar->reviews->count() }} Review)
                                </p> --}}
                                <p class="card-text">
                                    <strong>Rating:</strong> {{ $kamar->rating }}
                                    <span onclick="showRatingHistory({{ $kamar->id }})" style="cursor: pointer;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <=floor($kamar->rating))
                                            <i class="fas fa-star text-warning"></i> {{-- Bintang penuh --}}
                                            @elseif ($i - 0.5 <= $kamar->rating)
                                                <i class="fas fa-star-half-alt text-warning"></i> {{-- Bintang setengah --}}
                                                @else
                                                <i class="far fa-star text-warning"></i> {{-- Bintang kosong --}}
                                                @endif
                                                @endfor
                                    </span>
                                    ({{ $kamar->reviews->count() }} Review)
                                </p>



                                <button class="btn btn-primary btn-book"
                                    data-bs-toggle="modal"
                                    data-bs-target="#bookingModal"
                                    data-id="{{ $kamar->id }}"
                                    data-nama="{{ $kamar->nama_kamar }}"
                                    data-tanggal-mulai="{{ request('tanggal_mulai') }}"
                                    data-tanggal-selesai="{{ request('tanggal_selesai') }}">
                                    Booking Sekarang
                                </button>
                                {{-- @if($booking->status == 'pending' || $booking->status == 'approved')
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal" 
                                    data-id="{{ $booking->id }}">
                                Batalkan
                                </button>
                                @endif --}}

                            </div>
                        </div>
                    </div>
                    @endif
                    @empty
                    <div class="col-12">
                        <p class="text-muted text-center">Tidak ada kamar tersedia untuk tanggal yang dipilih.</p>
                    </div>
                    @endforelse
                </div>
                @endif

                <!-- Modal History Rating -->
                <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ratingModalLabel">History Rating</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="ratingList">
                                    <p class="text-center">Memuat data...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Modal Booking -->
                <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="bookingModalLabel">Booking Kamar</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('bookingkamar.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" name="kamar_id" id="modalKamarId">

                                    <div class="mb-3">
                                        <label class="form-label">Nama Kamar</label>
                                        <input type="text" id="modalNamaKamar" class="form-control bg-light" disabled readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nama Pemesan</label>
                                        <input type="text" name="nama_pemesan" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jabatan</label>
                                        <select name="jabatan" class="form-control" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                            @foreach ($jabatans as $jabatan)
                                            <option value="{{ $jabatan->jabatan }}">{{ $jabatan->jabatan }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Regional</label>
                                        <select name="regional" class="form-control" required>
                                            <option value="">-- Pilih Regional --</option>
                                            @foreach ($regionals as $regional)
                                            <option value="{{ $regional }}">{{ $regional }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">No HP</label>
                                        <input type="number" name="no_hp" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label bg-light">Tanggal Mulai</label>
                                        <input type="date" name="tanggal_mulai" id="modalTanggalMulai" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label bg-light">Tanggal Selesai</label>
                                        <input type="date" name="tanggal_selesai" id="modalTanggalSelesai" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Catatan</label>
                                        <textarea name="catatan" class="form-control"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Dokumen Pendukung (Opsional)</label>
                                        <input type="file" name="dokumen_pendukung" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                                        <small class="text-muted">Format: PDF, DOC, JPG, PNG (max 2MB)</small>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Booking</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                <!-- Modal Cancel Booking -->
                <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cancelModalLabel">Batalkan Booking</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            {{-- <form action="{{ route('booking.cancel', ':id') }}" method="POST" id="cancelForm"> --}}
                            @csrf
                            @method('PATCH')
                            <div class="modal-body">
                                <input type="hidden" name="booking_id" id="cancelBookingId">
                                <label for="keterangan_cancel">Alasan Pembatalan:</label>
                                <textarea name="keterangan_cancel" class="form-control" required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-danger">Batalkan</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Preview Gambar -->
                <!-- Modal Preview Gambar -->
                <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Preview Gambar</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center position-relative">
                                <!-- Tombol Previous -->
                                <button id="prevImage" class="btn btn-dark position-absolute"
                                    style="top: 50%; left: 10px; transform: translateY(-50%); z-index: 10; display: none;">
                                    &#9664; <!-- Left Arrow -->
                                </button>

                                <img id="previewImage" class="img-fluid rounded" style="max-height: 500px;">

                                <!-- Tombol Next -->
                                <button id="nextImage" class="btn btn-dark position-absolute"
                                    style="top: 50%; right: 10px; transform: translateY(-50%); z-index: 10; display: none;">
                                    &#9654; <!-- Right Arrow -->
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Modal Booking -->
                <!-- Modal Preview Gambar -->
                <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Preview Gambar</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <button id="prevImage" class="btn btn-secondary" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);">
                                    &#9664; <!-- Arrow Left -->
                                </button>
                                <img id="previewImage" class="img-fluid rounded" style="max-height: 500px;">
                                <button id="nextImage" class="btn btn-secondary" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                    &#9654; <!-- Arrow Right -->
                                </button>
                            </div>
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
            document.addEventListener("DOMContentLoaded", function() {
                let maxVisible = 4; // Maksimal gambar yang terlihat
                let galleries = document.querySelectorAll(".gallery-container");

                galleries.forEach(gallery => {
                    let kamarId = gallery.dataset.kamarId;
                    let items = gallery.querySelectorAll(`.gallery-item[data-kamar-id="${kamarId}"]`);
                    let prevBtn = document.querySelector(`.prev-btn[data-kamar-id="${kamarId}"]`);
                    let nextBtn = document.querySelector(`.next-btn[data-kamar-id="${kamarId}"]`);

                    let currentIndex = 0;

                    function updateGallery() {
                        items.forEach((item, index) => {
                            item.style.display = (index >= currentIndex && index < currentIndex + maxVisible) ? "inline-block" : "none";
                        });

                        prevBtn.style.display = (currentIndex > 0) ? "block" : "none";
                        nextBtn.style.display = (currentIndex + maxVisible < items.length) ? "block" : "none";
                    }

                    if (items.length > maxVisible) {
                        nextBtn.style.display = "block";
                    }

                    prevBtn.addEventListener("click", function() {
                        if (currentIndex > 0) {
                            currentIndex--;
                            updateGallery();
                        }
                    });

                    nextBtn.addEventListener("click", function() {
                        if (currentIndex + maxVisible < items.length) {
                            currentIndex++;
                            updateGallery();
                        }
                    });

                    updateGallery();
                });
            });
        </script>

        <script>
            let imageList = []; // Menyimpan daftar gambar untuk kamar tertentu
            let currentIndex = 0; // Indeks gambar aktif

            function showPreview(imageUrl, index, imagesArray) {
                imageList = imagesArray; // Simpan daftar gambar
                currentIndex = index; // Simpan indeks saat ini

                document.getElementById("previewImage").src = imageUrl;

                // Tampilkan modal
                var myModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
                myModal.show();

                updateNavigation(); // Update tombol navigasi
            }

            function updateNavigation() {
                let prevBtn = document.getElementById("prevImage");
                let nextBtn = document.getElementById("nextImage");

                prevBtn.style.display = (currentIndex === 0) ? "none" : "block";
                nextBtn.style.display = (currentIndex === imageList.length - 1) ? "none" : "block";
            }

            document.getElementById("prevImage").addEventListener("click", function() {
                if (currentIndex > 0) {
                    currentIndex--;
                    document.getElementById("previewImage").src = imageList[currentIndex];
                    updateNavigation();
                }
            });

            document.getElementById("nextImage").addEventListener("click", function() {
                if (currentIndex < imageList.length - 1) {
                    currentIndex++;
                    document.getElementById("previewImage").src = imageList[currentIndex];
                    updateNavigation();
                }
            });

            document.querySelectorAll('.kamar-photo').forEach((img) => {
                img.addEventListener("click", function() {
                    let kamarId = this.dataset.kamarId;

                    // Ambil hanya gambar dari kamar yang sama
                    let allImages = Array.from(document.querySelectorAll(`.kamar-photo[data-kamar-id="${kamarId}"]`))
                        .map(img => img.src);

                    let index = allImages.indexOf(this.src);
                    showPreview(this.src, index, allImages);
                });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let bookingModal = document.getElementById('bookingModal');

                bookingModal.addEventListener('show.bs.modal', function(event) {
                    let button = event.relatedTarget;
                    let kamarId = button.getAttribute('data-id');
                    let namaKamar = button.getAttribute('data-nama');

                    document.getElementById('modalKamarId').value = kamarId;
                    document.getElementById('modalNamaKamar').value = namaKamar;
                });
            });

            document.addEventListener("DOMContentLoaded", function() {
                let bookingModal = document.getElementById("bookingModal");

                bookingModal.addEventListener("show.bs.modal", function(event) {
                    let button = event.relatedTarget; // Tombol yang membuka modal
                    let tanggalMulai = button.getAttribute("data-tanggal-mulai");
                    let tanggalSelesai = button.getAttribute("data-tanggal-selesai");

                    document.getElementById("modalTanggalMulai").value = tanggalMulai;
                    document.getElementById("modalTanggalSelesai").value = tanggalSelesai;
                });
            });

            document.addEventListener("DOMContentLoaded", function() {
                let tanggalMulai = document.getElementById("tanggalMulai");
                let tanggalSelesai = document.getElementById("tanggalSelesai");

                function updateMinTanggalSelesai() {
                    let minDate = tanggalMulai.value;
                    if (!minDate) return; // Jika tanggal mulai kosong, tidak perlu diubah

                    let nextDay = new Date(minDate);
                    nextDay.setDate(nextDay.getDate() + 1); // Tambah 1 hari

                    let formattedNextDay = nextDay.toISOString().split("T")[0];
                    tanggalSelesai.min = formattedNextDay;

                    if (tanggalSelesai.value < formattedNextDay) {
                        tanggalSelesai.value = formattedNextDay;
                    }
                }

                // Set nilai min saat halaman dimuat
                updateMinTanggalSelesai();

                // Update min tanggal selesai ketika tanggal mulai berubah
                tanggalMulai.addEventListener("change", updateMinTanggalSelesai);
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let cancelModal = document.getElementById('cancelModal');
                let cancelForm = document.getElementById('cancelForm');

                cancelModal.addEventListener('show.bs.modal', function(event) {
                    let button = event.relatedTarget;
                    let bookingId = button.getAttribute('data-id');

                    cancelForm.action = cancelForm.action.replace(':id', bookingId);
                    document.getElementById('cancelBookingId').value = bookingId;
                });
            });
        </script>

        {{-- <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll(".rating-click").forEach(item => {
                    item.addEventListener("click", function() {
                        let kamarId = this.getAttribute("data-id");
                        let modalBody = document.getElementById("ratingHistoryList");
                        
                        // Kosongkan list sebelum memuat data baru
                        modalBody.innerHTML = "<li class='list-group-item'>Loading...</li>";

                        // Fetch review dari backend
                        fetch(`{{ url('kamar') }}/${kamarId}/reviews`)
        .then(response => response.json())
        .then(data => {
        modalBody.innerHTML = ""; // Bersihkan sebelum menambahkan data
        if (data.length > 0) {
        data.forEach(review => {
        let listItem = `<li class="list-group-item">
            <strong>Rating: ${review.rating} ★</strong><br>
            ${review.review ? review.review : "Tidak ada komentar"}
        </li>`;
        modalBody.innerHTML += listItem;
        });
        } else {
        modalBody.innerHTML = "<li class='list-group-item'>Belum ada review</li>";
        }
        })
        .catch(error => {
        console.error("Error fetching reviews:", error);
        modalBody.innerHTML = "<li class='list-group-item text-danger'>Gagal memuat review</li>";
        });

        // Tampilkan modal
        let ratingModal = new bootstrap.Modal(document.getElementById("ratingModal"));
        ratingModal.show();
        });
        });
        });
        </script> --}}
        <script>
            function showRatingHistory(kamarId) {
                fetch(`{{ url('kamar') }}/${kamarId}/reviews`)
                    .then(response => response.json())
                    .then(data => {
                        let content = '';

                        if (data.length > 0) {
                            data.forEach(review => {
                                content += `
                                    <div class="border-bottom mb-3 pb-2">
                                        <strong>${review.booking.nama_pemesan}</strong>  
                                        <span class="text-muted">-${review.booking.regional} (${new Date(review.updated_at).toLocaleDateString()})</span>
                                        <br>
                                        <span>
                                            ${[...Array(5)].map((_, i) => 
                                                i < review.rating ? '<i class="fas fa-star text-warning"></i>' 
                                                : '<i class="far fa-star text-warning"></i>'
                                            ).join('')}
                                        </span>
                                        <p><i>${review.review ?? 'Tidak ada komentar'}</i></p>
                                    </div>`;
                            });
                        } else {
                            content = '<p class="text-center">Belum ada review.</p>';
                        }

                        document.getElementById('ratingList').innerHTML = content;
                        new bootstrap.Modal(document.getElementById('ratingModal')).show();
                    })
                    .catch(error => console.error('Error:', error));
            }
        </script>






    </x-slot>
</x-layouts.app>