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
                    <h3 class="mt-4">PT Perkebunan Nusantara I <br /> <em>Head Office</em></h3>
                    <br>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row mb-2">
                                <b>Ruangan Rapat:</b>
                                <div class="ml-2">
                                    <select class="form-control" name="ruangan" id="ruanganRapat"
                                        style="height: 100% !important;">
                                        <option value="" disabled>--Pilih Lantai--</option>
                                        @foreach ($list_lantai as $lantai)
                                            <option value="{{ $lantai->id_driver }}">
                                                {{ $lantai->nama_driver }} </option>
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
        </script>
    </x-slot>
</x-layouts.app>
