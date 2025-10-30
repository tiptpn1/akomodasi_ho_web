<x-layouts.app>
    {{-- Slot untuk custom page styles --}}
    <x-slot name="styles">
        <style>
            th, td { font-size: 12px; vertical-align: middle !important; text-align: center; }
            table { width: 100% !important; table-layout: fixed; }
            .table-overflow-x { width: 100% !important; overflow-x: auto !important; }
            .table-overflow-x::-webkit-scrollbar { height: 5px; background: #f1f1f1; }
            .table-overflow-x::-webkit-scrollbar-thumb { background: #888; border-radius: 5px; }
            .hover-pointer:hover { cursor: pointer; opacity: 0.8; }
            .trip-cell { background-color: #3498db; color: white; font-weight: bold; border: 1px solid #2980b9 !important; }
            .available-cell { background-color: #ecf0f1; }
            #detail .modal-body td { padding: 5px; vertical-align: top; font-size: 14px; }
        </style>
    </x-slot>

    {{-- Slot Konten Utama --}}
    <x-slot name="slot">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h3 class="mt-4">Jadwal Penggunaan Kendaraan <br /> <em>{{ Auth::user()->bagian->regional->nama_regional}}</em></h3>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 d-flex align-items-center">
                                    <b class="mr-2 flex-shrink-0">Tanggal:</b>
                                    <input type="text" id="tanggal" name="tanggal" class="form-control" placeholder="Pilih Tanggal" style="height: 100% !important;">
                                </div>
                                <div class="col-md-3">
                                     <button class="btn btn-success" id="exportPdf"> <i class="fas fa-file-pdf"></i> Export PDF</button>
                                </div>
                            </div>
                            
                            {{-- Kontainer untuk tabel jadwal yang dimuat dinamis --}}
                            <div id="scheduleContent"></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        {{-- Modal untuk Menampilkan Detail Perjalanan --}}
        <div id="detail" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Detail Perjalanan</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-borderless">
                            <tbody>
                            <tr><td style="width: 30%; text-align: left;">Driver</td><td style="width: 5%; text-align: left;">:</td><td id="det_driver" style="text-align: left;"></td></tr>
<tr><td style="width: 30%; text-align: left;">No. Polisi</td><td style="width: 5%; text-align: left;">:</td><td id="det_nopol" style="text-align: left;"></td></tr>
<tr><td style="width: 30%; text-align: left;">Foto Mobil</td><td style="width: 5%; text-align: left;">:</td><td id="det_foto" style="text-align: left;"></td></tr>
<tr><td style="width: 30%; text-align: left;">PIC</td><td style="width: 5%; text-align: left;">:</td><td id="det_pic" style="text-align: left;"></td></tr>
<tr><td style="width: 30%; text-align: left;">Divisi</td><td style="width: 5%; text-align: left;">:</td><td id="det_divisi" style="text-align: left;"></td></tr>
<tr><td style="width: 30%; text-align: left;">Tanggal</td><td style="width: 5%; text-align: left;">:</td><td id="det_tgl" style="text-align: left;"></td></tr>
<tr><td style="width: 30%; text-align: left;">Waktu</td><td style="width: 5%; text-align: left;">:</td><td id="det_waktu" style="text-align: left;"></td></tr>
<tr><td style="width: 30%; text-align: left;">Jenis & Tujuan</td><td style="width: 5%; text-align: left;">:</td><td id="det_tujuan" style="text-align: left;"></td></tr>
<tr><td style="width: 30%; text-align: left;">Titik Penjemputan</td><td style="width: 5%; text-align: left;">:</td><td id="det_penjemputan" style="text-align: left;"></td></tr>
<!-- <tr><td style="text-align: left;">Status</td><td style="text-align: left;">:</td><td id="det_status" style="text-align: left;"></td></tr> -->
<tr><td style="width: 30%; text-align: left;">Keterangan</td><td style="width: 5%; text-align: left;">:</td><td id="det_keterangan" style="text-align: left;"></td></tr>
<tr><td style="width: 30%; text-align: left;">Memo/Surat</td><td style="width: 5%; text-align: left;">:</td><td id="det_memo" style="text-align: left;"></td></tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Slot untuk skrip JavaScript halaman --}}
    <x-slot name="scripts">
        <script>
            let get_data = null;
            let current_date = null; // Variabel untuk menyimpan tanggal saat ini

            function fetchData() {
                // Ambil nilai tanggal terbaru langsung dari input setiap kali fungsi dipanggil
                const dateValue = $('#tanggal').val();

                if (!dateValue) {
                    console.error("fetchData dipanggil tetapi tanggal kosong.");
                    return;
                }
                
                // Hentikan permintaan jika tanggalnya sama dengan yang terakhir dimuat
                if (dateValue === current_date && get_data !== null) {
                    return;
                }
                current_date = dateValue; // Perbarui tanggal saat ini

                if (get_data) {
                    get_data.abort();
                }
                
                $('#scheduleContent').html(`<div class="text-center p-5"><em><i class="fas fa-spin fa-spinner"></i> Memuat Jadwal...</em></div>`);

                get_data = $.ajax({
                    url: "{{ route('admin.driver.content') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        date: dateValue
                    },
                    success: function(response) {
                        $('#scheduleContent').html(response);
                    },
                    error: function(xhr, status, error) {
                        if (status !== 'abort') {
                            let errorDetails = `<strong>Terjadi Kesalahan (Status: ${xhr.status})</strong><br>`;
                            if (xhr.status == 419) {
                                errorDetails += 'Sesi Anda telah berakhir. Halaman akan dimuat ulang.';
                                setTimeout(() => location.reload(), 2000);
                            } else {
                                errorDetails += 'Gagal memuat jadwal. Pastikan rute dan controller Anda sudah benar.';
                            }
                            $('#scheduleContent').html(`<div class="alert alert-danger text-center">${errorDetails}</div>`);
                        }
                    }
                });
            }
            
            // function detail(trip_id) {
            //     const url = "{{ route('admin.driver.trip.details', ['id' => ':id']) }}".replace(':id', trip_id);
            //     $.getJSON(url, response => {
            //          console.log(response);
            //         const data = response.data;
            //         if (!data) return;
                    
            //         const tgl_split = data.tgl_berangkat.split("-");
            //         $('#det_driver').html(data.driver_detail ? data.driver_detail.nama_driver : (data.rental_driver || 'N/A'));
            //         $('#det_nopol').html(data.kendaraan_detail.nopol);
                    
            //         $('#det_foto').html(data.kendaraan_detail.foto ? `<a href="{{ asset('uploads/foto_kendaraan') }}/${data.kendaraan_detail.foto}" target="_blank">Lihat Foto</a>` : 'Tidak ada Foto');
            //         $('#det_pic').html(data.nama_pic);
            //         $('#det_divisi').html(data.divisi);
            //         $('#det_tgl').html(`${tgl_split[2]}-${tgl_split[1]}-${tgl_split[0]}`);
            //         $('#det_waktu').html(`${data.jam_berangkat.substring(0,5)} - ${data.jam_kembali.substring(0,5)} WIB`);
            //         $('#det_tujuan').html(`${data.jenis_tujuan} - ${data.tujuan}`);
            //         $('#det_penjemputan').html(data.pejemputan);
            //         // $('#det_status').html(data.status);
            //         $('#det_keterangan').html(data.ket || '-');
            //         $('#det_user').html(data.username);
            //         $('#det_memo').html(data.file_memo ? `<a href="{{ asset('') }}${data.file_memo}" target="_blank">Lihat Memo</a>` : 'Tidak ada memo');
                    
            //         $('#detail').modal('show');
            //     }).fail(() => alert('Gagal mengambil data dari server.'));
            // }

//             function detail(trip_id) {
//     console.log("Mencoba mengambil detail untuk Trip ID:", trip_id); 
    
//     if (!trip_id || trip_id === 'undefined') {
//         alert('ID Perjalanan tidak valid atau hilang.');
//         return; 
//     }
    
//     const url = "{{ route('admin.driver.trip.details', ['id' => ':id']) }}".replace(':id', trip_id);
    
//     $.ajax({
//         url: url,
//         method: 'GET', 
//         dataType: 'json',
//         success: function(response) {
//             console.log(response);
//             const data = response.data;
//             if (!data) return;
            
//             const tgl_split = data.tgl_berangkat.split("-");
            
//             // Logic untuk menentukan Driver
//             const driverName = data.driver_detail 
//                 ? data.driver_detail.nama_driver 
//                 : (data.rental_driver || 'N/A');
                
//             // PERBAIKAN KRUSIAL DI SINI: Akses nopol dengan aman
//             // Menggunakan optional chaining (?.) untuk menghindari crash jika kendaraan_detail adalah null
//             const nopol = data.kendaraan_detail?.nopol || data.rental_kendaraan || 'N/A';

//             $('#det_driver').html(driverName);
//             // Hapus semua logika lama, gunakan variabel nopol yang sudah aman
//             $('#det_nopol').html(nopol); 
            
//             // PERBAIKAN KRUSIAL DI SINI: Akses foto dengan aman
//             const fotoLink = data.kendaraan_detail?.foto
//                 ? `<a href="{{ asset('uploads/foto_kendaraan') }}/${data.kendaraan_detail.foto}" target="_blank">Lihat Foto</a>` 
//                 : 'Tidak ada Foto';
                
//             const memoLink = data.file_memo
//                 ? `<a href="{{ asset('') }}${data.file_memo}" target="_blank">Lihat Memo</a>` 
//                 : 'Tidak ada memo';


//             $('#det_foto').html(fotoLink); 
//             $('#det_pic').html(data.nama_pic);
//             $('#det_divisi').html(data.divisi);
//             $('#det_tgl').html(`${tgl_split[2]}-${tgl_split[1]}-${tgl_split[0]}`);
//             $('#det_waktu').html(`${data.jam_berangkat.substring(0,5)} - ${data.jam_kembali.substring(0,5)} WIB`);
//             $('#det_tujuan').html(`${data.jenis_tujuan} - ${data.tujuan}`);
//             $('#det_penjemputan').html(data.pejemputan);
//             $('#det_keterangan').html(data.ket || '-');
//             $('#det_memo').html(memoLink);
            
//             $('#detail').modal('show');

//         },
//         error: function(jqxhr, textStatus, error) {
//             alert('Gagal mengambil data detail dari server (Status: ' + jqxhr.status + ')');
//             console.error("AJAX Failed:", jqxhr.responseText);
//         }
//     });
// }


function detail(trip_id) {
    console.log("Mencoba mengambil detail untuk Trip ID:", trip_id); 
    
    if (!trip_id || trip_id === 'undefined') {
        alert('ID Perjalanan tidak valid atau hilang.');
        return; 
    }
    
    // Sembunyikan semua baris terkait driver/kendaraan secara default sebelum memuat data
    $('#det_driver').closest('tr').show();
    $('#det_nopol').closest('tr').show();
    $('#det_foto').closest('tr').show();
    
    const url = "{{ route('admin.driver.trip.details', ['id' => ':id']) }}".replace(':id', trip_id);
    
    $.ajax({
        url: url,
        method: 'GET', 
        dataType: 'json',
        success: function(response) {
            console.log(response);
            const data = response.data;
            if (!data) return;
            
            const tgl_split = data.tgl_berangkat.split("-");
            
            // Tentukan jenis driver
            const isOnline = data.driver == 99;
            const isRental = !!data.rental_driver; // True jika rental_driver terisi

            
            // --- LOGIKA MENENTUKAN NILAI DAN KONDISI TAMPIL ---

            // 1. DRIVER NAME
            let driverName = 'N/A';
            if (data.driver_detail) {
                driverName = data.driver_detail.nama_driver;
            } else if (data.rental_driver) {
                driverName = data.rental_driver;
            } else if (isOnline) {
                driverName = 'Online'; // Tampilkan Online jika ID 99 dan tidak ada detail
            }

            // 2. NO. POLISI
            const nopol = data.kendaraan_detail?.nopol || data.rental_kendaraan || 'N/A';

            // 3. FOTO MOBIL
            const fotoLink = data.kendaraan_detail?.foto
                ? `<a href="{{ asset('uploads/foto_kendaraan') }}/${data.kendaraan_detail.foto}" target="_blank">Lihat Foto</a>` 
                : 'Tidak ada Foto';
            
            // --- MENGISI DATA KE MODAL ---

            $('#det_driver').html(driverName);
            $('#det_nopol').html(nopol);
            $('#det_foto').html(fotoLink);
            
            $('#det_pic').html(data.nama_pic);
            $('#det_divisi').html(data.divisi);
            $('#det_tgl').html(`${tgl_split[2]}-${tgl_split[1]}-${tgl_split[0]}`);
            $('#det_waktu').html(`${data.jam_berangkat.substring(0,5)} - ${data.jam_kembali.substring(0,5)} WIB`);
            $('#det_tujuan').html(`${data.jenis_tujuan} - ${data.tujuan}`);
            $('#det_penjemputan').html(data.pejemputan);
            $('#det_keterangan').html(data.ket || '-');
            
            const memoLink = data.file_memo
                ? `<a href="{{ asset('') }}${data.file_memo}" target="_blank">Lihat Memo</a>` 
                : 'Tidak ada memo';
            $('#det_memo').html(memoLink);
            
            
            // --- LOGIKA KONDISIONAL UNTUK MENYEMBUNYIKAN BARIS ---

            if (isOnline) {
                // Driver Online: Sembunyikan Driver Name, Nopol, dan Foto (karena ID 99)
                $('#det_driver').closest('tr').hide(); 
                $('#det_nopol').closest('tr').hide();
                $('#det_foto').closest('tr').hide();
            } else if (isRental) {
                // Rental Driver: Sembunyikan baris Foto Mobil
                // Tampilkan Driver (Nama Rental) dan No. Polisi (Nopol Rental)
                $('#det_foto').closest('tr').hide(); 
            }
            // Untuk Driver Reguler, semua baris tetap tampil (default .show() di awal)
            
            $('#detail').modal('show');

        },
        error: function(jqxhr, textStatus, error) {
            alert('Gagal mengambil data detail dari server (Status: ' + jqxhr.status + ')');
            console.error("AJAX Failed:", jqxhr.responseText);
        }
    });
}

            $(document).ready(function() {
                // Inisialisasi datepicker
                $('#tanggal').datepicker({
                    format: 'mm/dd/yyyy',
                    autoclose: true,
                    todayHighlight: true
                });

                // --- PERBAIKAN LOGIKA EVENT LISTENER ---
                // Pasang event listener yang akan berjalan setiap kali tanggal berubah
                $('#tanggal').on('change', function() {
                    fetchData();
                });
                
                // Fungsi untuk mengatur tanggal awal dan memicu pembaruan
                function setInitialDate() {
                    const today = new Date();
                    const month = today.getMonth() + 1;
                    const day = today.getDate();
                    const year = today.getFullYear();

                    // Atur nilai input dan panggil .trigger('change')
                    $('#tanggal').val(`${month}/${day}/${year}`).trigger('change');
                }

                // Panggil fungsi untuk mengatur tanggal dan memuat data pertama kali
                setInitialDate();
                
                // $('#exportPdf').on('click', function() {
                //     const exportDate = $('#tanggal').val();
                //     if (!exportDate) {
                //         alert('Silakan pilih tanggal terlebih dahulu.');
                //         return;
                //     }
                //     const button = $(this);
                //     button.html('<i class="fas fa-spin fa-spinner"></i> Memproses...').prop('disabled', true);

                //     fetch("{{ route('admin.driver.exportPdf') }}", {
                //         method: 'POST',
                //         headers: {
                //             'Content-Type': 'application/json',
                //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
                //         },
                //         body: JSON.stringify({ date: exportDate })
                //     })
                //     .then(res => {
                //         if (!res.ok) throw new Error('Gagal membuat PDF');
                //         return res.blob();
                //     })
                //     .then(blob => {
                //         const url = window.URL.createObjectURL(blob);
                //         const a = document.createElement('a');
                //         a.href = url;
                //         a.download = `Laporan Jadwal Driver ${exportDate.replace(/\//g, '-')}.pdf`;
                //         document.body.appendChild(a);
                //         a.click();
                //         a.remove();
                //         window.URL.revokeObjectURL(url);
                //     })
                //     .catch((error) => alert(error.message))
                //     .finally(() => button.html('<i class="fas fa-file-pdf"></i> Export PDF').prop('disabled', false));
                // });


                // Di dalam $(document).ready(function() { ...
// Pastikan pengaturan global AJAX (CSRF) ada di atas
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$('#exportPdf').on('click', function() {
    const exportDate = $('#tanggal').val();
    if (!exportDate) {
        alert('Silakan pilih tanggal terlebih dahulu.');
        return;
    }
    const button = $(this);
    button.html('<i class="fas fa-spin fa-spinner"></i> Memproses...').prop('disabled', true);

    // MENGGUNAKAN JQUERY AJAX UNTUK MENGHINDARI ISU FETCH/CSRF
    $.ajax({
        url: "{{ route('admin.driver.exportPdf') }}",
        method: 'POST',
        data: {
            date: exportDate,
            // _token: '{{ csrf_token() }}' // Mengirim token sebagai data form
        },
        xhrFields: {
            responseType: 'blob' // Minta respons dalam bentuk blob
        },
        success: function(blob, status, xhr) {
            // Memproses blob sebagai file download
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            
            // Ambil nama file dari header Content-Disposition jika tersedia, atau gunakan nama default
            const disposition = xhr.getResponseHeader('Content-Disposition');
            let filename = `Laporan Jadwal Driver ${exportDate.replace(/\//g, '-')}.pdf`;

            if (disposition && disposition.indexOf('attachment') !== -1) {
                const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                const matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }

            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        },
        error: function(xhr, status, error) {
            // Jika ada error (misalnya 400 Bad Request), coba baca pesan dari JSON response
            let errorMessage = 'Gagal membuat PDF. Coba lagi.';
            if (xhr.status === 400) {
                try {
                    const responseJson = JSON.parse(xhr.responseText);
                    errorMessage = responseJson.message || errorMessage;
                } catch (e) {
                    // Jika bukan JSON, tampilkan status umum
                    errorMessage = `Error: ${xhr.status} ${xhr.statusText}`;
                }
            } else if (xhr.status === 419) {
                errorMessage = 'Sesi berakhir. Harap login kembali.';
            }
            alert('Error Ekspor PDF: ' + errorMessage);
        },
        complete: function() {
            button.html('<i class="fas fa-file-pdf"></i> Export PDF').prop('disabled', false);
        }
    });
});


            });
        </script>
    </x-slot>
</x-layouts.app>
