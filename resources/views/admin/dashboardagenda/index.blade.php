<x-layouts.app>
    <x-slot name="styles">
        <style>
            .color-legend {
                display: flex;
                flex-direction: row;
                justify-content: center;
                flex-wrap: wrap;
                gap: 20px;
            }

            @media (max-width: 494px) {
                .color-legend {
                    flex-direction: column !important;
                }
            }

            .color-legend>div {
                display: flex;
                flex-direction: row;
                gap: 5px;
            }

            .square {
                border: 1px solid black;
                width: 20px;
                height: 20px;
            }

            .green {
                background-color: rgb(8, 158, 8);
            }

            .yellow {
                background-color: rgb(241, 197, 37);
            }

            .blue {
                background-color: rgb(21, 223, 210);
            }

            .grey {
                background-color: rgb(177, 177, 177);
            }

            th,
            td {
                font-size: 12px;
            }

            table {
                width: 100% !important;
            }

            .table-overflow-x {
                width: 100% !important;
                overflow-x: scroll !important;
            }

            .table-overflow-x::-webkit-scrollbar {
                height: 0;
                background: transparent;
            }

            .hover-pointer:hover {
                cursor: pointer;
            }
        </style>
    </x-slot>

    <x-slot name="slot">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h3 class="mt-4">PT Perkebunan Nusantara I <br /> <em>{{ Auth::user()->bagian->regional->nama_regional}}</em></h3>
                    <br>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row mb-2">
                                <b>Ruangan Rapat:</b>
                                <div class="ml-2">
                                    <select class="form-control" name="ruangan" id="ruanganRapat" style="height: 100% !important;">
                                        <option value="" disabled>--Pilih Lantai--</option>
                                        @foreach ($list_lantai as $lantai)
                                            <option value="{{ $lantai->lantai }}" @if ($loop->iteration == 1) selected @endif>
                                                @if ($lantai->lantai == 99)
                                                    Ruang Eksternal
                                                @else
                                                    Lantai {{ $lantai->lantai }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <b>Tanggal:</b>
                                <div class="ml-2">
                                    <input type="text" id="tanggal" name="tanggal" class="form-control"
                                        placeholder="Pilih Tanggal" style="height: 100% !important;">
                                </div>
                            </div>
                            <div class="mb-4">
                                <button class="btn btn-success" id="exportPdf">Export PDF</button>
                            </div>
                            <div id="agendaContent"></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <div id="detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Detail Ruang Rapat Dan Video Converence</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div id="surat" class="form-group">
                            <div class="form-group">
                                <label class="col-sm-13 control-label"></label>
                                <div class="col-sm-12">
                                    <table width="100%">
                                        <tr>
                                            <td style="width: 45%">Bagian</td>
                                            <td style="width: 5%"> : </td>
                                            <td style="width: 50%" id="det_bagian"></td>
                                        </tr>
                                        <tr>
                                            <td>Acara</td>
                                            <td> : </td>
                                            <td id="det_acara"></td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td> : </td>
                                            <td id="det_tgl"></td>
                                        </tr>
                                        <tr>
                                            <td>Waktu</td>
                                            <td> : </td>
                                            <td id="det_waktu"></td>
                                        </tr>
                                        <tr>
                                            <td>Peserta</td>
                                            <td> : </td>
                                            <td id="det_peserta"></td>
                                        </tr>
                                        <tr>
                                            <td>Estimasi Jumlah Peserta di Kandir (jika ada)</td>
                                            <td> : </td>
                                            <td id="det_jumlahpeserta"></td>
                                        </tr>
                                        <tr>
                                            <td>Tempat (jika ada peserta dari Kandir)</td>
                                            <td> : </td>
                                            <td id="det_tempat"></td>
                                        </tr>
                                        {{-- <tr>
                                            <td>Bersifat Privat</td>
                                            <td> : </td>
                                            <td id="det_privat"></td>
                                        </tr> --}}
                                        <tr>
                                            <td>Bersifat Vicon</td>
                                            <td> : </td>
                                            <td id="det_vicon"></td>
                                        </tr>
                                        <tr>
                                            <td>Jenis Link</td>
                                            <td> : </td>
                                            <td id="det_jenislink"></td>
                                        </tr>
                                        <tr>
                                            <td>Jenis Rapat</td>
                                            <td> : </td>
                                            <td id="det_jenisrapat"></td>
                                        </tr>
                                        {{-- <tr>
                                            <td>Agenda Direksi</td>
                                            <td> : </td>
                                            <td id="det_agendadireksi"></td>
                                        </tr> --}}
                                        <tr>
                                            <td>Personel yang dapat dihubungi</td>
                                            <td> : </td>
                                            <td id="det_personil"></td>
                                        </tr>
                                        <tr>
                                            <td>Memo/Surat Undangan</td>
                                            <td> : </td>
                                            <td id="det_sk">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td> : </td>
                                            <td id="det_status"></td>
                                        </tr>
                                        {{-- <tr>
                                            <td>Link</td>
                                            <td> : </td>
                                            <td id="det_link"></td>
                                        </tr> --}}
                                        {{-- <tr>
                                            <td>Password</td>
                                            <td> : </td>
                                            <td id="det_password"></td>
                                        </tr> --}}
                                        <tr>
                                            <td>Keterangan</td>
                                            <td> : </td>
                                            <td id="det_keterangan"></td>
                                        </tr>
                                        <tr>
                                            <td>Penginput</td>
                                            <td> : </td>
                                            <td id="det_user"></td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default antoclose"
                                    data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="scripts">
        <script>
            var date;
            var lantai;
            var get_data = '';

            function setDateNow() {
                today = new Date();
                year = today.getFullYear();
                month = today.getMonth() + 1;
                date = today.getDate();

                $('#tanggal').val(`${month}/${date}/${year}`).trigger('change');
            }

            function fetchData() {
                if (get_data != '') {
                    get_data.abort();
                }

                $('#agendaContent').html(`
                        <div style="display: flex; flex-direction: row; justify-content: center;">
                            <em><i class="fas fa-spin fa-spinner"></i> Process</em>
                        </div>
                    `);

                get_data = $.ajax({
                    url: "{{ route('admin.agenda.content') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        lt: lantai,
                        date: date,
                    },
                    success: function(response) {
                        $('#agendaContent').html(response);
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 419) {
                            location.reload();
                        } else if (status != 'abort') {
                            $('#agendaContent').empty();
                            swal({
                                title: 'Error!',
                                text: 'Gagal Memuat!',
                                type: 'error',
                            }, function(confirm) {
                                fetchData();
                            })
                        }
                    }
                });
            }

            $(document).ready(function() {
                $('#tanggal').datepicker({});

                $('#ruanganRapat').on('change', function() {
                    lantai = $(this).val();

                    fetchData();
                });

                $('#tanggal').on('change', function() {
                    date = $(this).val();

                    fetchData();
                });

                setDateNow();

                $('#ruanganRapat').trigger('change');
            });

            function detail(id) {
                //Ajax Load data from ajax
                $.ajax({
                    url: "{{ route('admin.vicon.show', ':id') }}".replace(':id', id),
                    type: "GET",
                    dataType: "JSON",
                    success: function(response) {
                        var data = response.data

                        var tanggal = data.tanggal;
                        var tgl_split = tanggal.split("-");
                        var tgl_tampil = tgl_split[2] + "-" + tgl_split[1] + "-" + tgl_split[0];

                        var waktu = data.waktu;
                        var waktu_split = waktu.split(":");
                        var waktu = waktu_split[0] + ":" + waktu_split[1];

                        var waktu2 = data.waktu2;
                        var waktu2_split = waktu2.split(":");
                        var waktu2 = waktu2_split[0] + ":" + waktu2_split[1];

                        var waktu_tampil = waktu + " - " + waktu2 + " WIB";

                        var id_ruangan = data.id_ruangan;
                        var tempat = "";
                        if (id_ruangan == null) {
                            tempat = data.ruangan_lain;
                        } else {
                            tempat = data.ruangan.nama;
                        }

                        var sk = "";
                        if (data.sk != null) {
                            var url = "{{ asset('test') }}";
                            sk = "<a href='" + url.replace('test', data.sk) + "' target='_blank'>Open</a>"
                        }

                        var link = "";
                        if (data.link != null) {
                            var link = data.master_link.link;
                        }

                        $('#det_bagian').html(data.bagian != null ? data.bagian.bagian : '');
                        $('#det_acara').html(data.acara);
                        $('#det_tgl').html(tgl_tampil);
                        $('#det_waktu').html(waktu_tampil);
                        $('#det_peserta').html(data.peserta);
                        $('#det_jumlahpeserta').html(data.jumlahpeserta);
                        $('#det_tempat').html(tempat);
                        // $('#det_privat').html(data.privat);
                        $('#det_vicon').html(data.vicon);
                        $('#det_jenislink').html(data.jenis_link);
                        $('#det_jenisrapat').html(data.jenisrapat.nama);
                        // $('#det_agendadireksi').html(data.agenda_direksi);
                        $('#det_personil').html(data.personil);
                        $('#det_sk').html(sk);
                        $('#det_status').html(data.status);
                        // $('#det_link').html(link);
                        // $('#det_pass').html(data.password);
                        $('#det_keterangan').html(data.keterangan);
                        $('#det_user').html(data.user);
                        $('#detail').modal('show');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error get data from ajax');
                    }
                });
            }

            $('#exportPdf').on('click', function() {
                buttonExportPdf = $(this);
                buttonExportPdf.html('<i class="fas fa-spin fa-spinner"></i> Loading...');
                buttonExportPdf.prop('disabled', true);

                fetch('{{ route('admin.agenda.exportPdf') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            // lt: lantai,
                            date: date,
                        }),
                    }).then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal Export PDF');
                        }
                        return response.blob(); // Mengonversi respons menjadi Blob
                    })
                    .then(blob => {
                        const url = URL.createObjectURL(blob); // Membuat URL objek dari Blob

                        const a = document.createElement('a'); // Membuat elemen anchor
                        a.href = url;
                        a.target = '_blank';
                        a.download = `Laporan Agenda pada Tanggal ${date}_{{ time() }}.pdf`; // Nama file yang akan diunduh
                        document.body.appendChild(a); // Menambahkan elemen anchor ke body
                        a.click(); // Mengklik anchor untuk memulai unduhan
                        a.remove(); // Menghapus elemen anchor setelah unduhan

                        URL.revokeObjectURL(url); // Menghapus URL objek untuk membebaskan memori

                        buttonExportPdf.html(`Export PDF`);
                        buttonExportPdf.prop('disabled', false);
                    })
                    .catch(error => {
                        buttonExportPdf.html(`Export PDF`);
                        buttonExportPdf.prop('disabled', false);

                        swal({
                            title: 'Error!',
                            text: 'Gagal Export PDF!',
                            type: 'error',
                        });
                    });;
            });
        </script>
    </x-slot>
</x-layouts.app>
