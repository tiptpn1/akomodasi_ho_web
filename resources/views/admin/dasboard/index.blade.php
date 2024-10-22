<x-layouts.app>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4 mb-3">Dashboard</h3>
                <div class="row">
                    <div class="mb-3" style="text-align: center; margin-left: 10px;">
                        <form action="{{ route('admin.dashboard.export.pdf.vicon') }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" name="tanggal_awal" value="{{ date('d-m-Y') }}">
                            <input type="hidden" name="tanggal_akhir" value="{{ date('d-m-Y') }}">
                            <button type="submit"class="btn btn-success">
                                <i class="fas fa-arrow-circle-down"></i>
                                PDF
                            </button>
                        </form>
                    </div>

                    <div style="text-align: center; margin-left: 10px;">
                        <form action="{{ route('admin.dashboard.export.excel.vicon') }}" method="post">
                            @csrf
                            <input type="hidden" name="tanggal_awal" value="{{ date('d-m-Y') }}">
                            <input type="hidden" name="tanggal_akhir" value="{{ date('d-m-Y') }}">
                            <button type="submit"class="btn btn-warning">
                                <i class="fas fa-arrow-circle-down"></i>
                                Excel
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display responsive" style="width: 100%; float:center;"
                                id="dataTables-dashboard">
                                <thead>
                                    <tr>
                                        <th style="max-width: 5%">
                                            <center>No</center>
                                        </th>
                                        <th style="min-width: 25%">
                                            <center>Agenda</center>
                                        </th>
                                        <th style="min-width: 20%">
                                            <center>Hari, Tanggal</center>
                                        </th>
                                        <th style="min-width: 20%">
                                            <center>Waktu</center>
                                        </th>
                                        <th style="width: 20%">
                                            <center>Tempat</center>
                                        </th>
                                        <th style="width: 25%">
                                            <center>PIC</center>
                                        </th>
                                        <th style="width: 10%">
                                            <center>Vicon</center>
                                        </th>
                                        <th style="max-width: 20%">
                                            <center>Keterangan</center>
                                        </th>
                                        <th style="max-width: 20%">
                                            <center>Created</center>
                                        </th>
                                        <th style="min-width: 15%">
                                            <center>Aksi</center>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        {{-- php numberOfColumn --}}
                        <div class="container-fluid mt-5">
                            <div class="row">
                                @foreach ($countVicon as $vicon)
                                    <div class="col-md-3">
                                        <div class="card card-widget widget-user">
                                            <div class="widget-user-header badge-info" style="height: 110px;">
                                                <h3 class="widget-user-username text-right">
                                                    Bulan
                                                    {{ Carbon\Carbon::parse($vicon->tanggal)->translatedFormat('F') }}
                                                </h3>
                                                <h4 class="widget-user-desc text-right">
                                                    {{ Carbon\Carbon::parse($vicon->tanggal)->translatedFormat('d F Y') }}
                                                </h4>
                                                <p class="widget-user-desc text-right">
                                                    {{ App\Models\SendVicon::countDate($vicon->tanggal) }}
                                                    Agenda
                                                </p>
                                            </div>
                                            <div class="card-footer" style="padding-top: 5px;">
                                                <div class="row">
                                                    <div class="col-sm-6 border-right"
                                                        style="text-align: center; padding-top: 10px;">
                                                        <form action="{{ route('admin.dashboard.export.excel.vicon') }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="tanggal_awal"
                                                                value="{{ date('Y-m-d', strtotime($vicon->tanggal)) }}">
                                                            <input type="hidden" name="tanggal_akhir"
                                                                value="{{ date('Y-m-d', strtotime($vicon->tanggal)) }}">
                                                            <button type="submit" class="btn btn-warning">
                                                                <i class="fas fa-arrow-circle-down"></i>
                                                                Excel
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div class="col-sm-6"
                                                        style="text-align: center; padding-top: 10px;">
                                                        <form action="{{ route('admin.dashboard.export.pdf.vicon') }}"
                                                            method="post" target="_blank">
                                                            @csrf
                                                            <input type="hidden" name="tanggal_awal"
                                                                value="{{ date('Y-m-d', strtotime($vicon->tanggal)) }}">
                                                            <input type="hidden" name="tanggal_akhir"
                                                                value="{{ date('Y-m-d', strtotime($vicon->tanggal)) }}">
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fas fa-arrow-circle-down"></i>
                                                                PDF
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{ $countVicon->links('pagination::bootstrap-4') }}
                        </div>

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
                                    <tr>
                                        <td>Bersifat Privat</td>
                                        <td> : </td>
                                        <td id="det_privat"></td>
                                    </tr>
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
                                    <tr>
                                        <td>Agenda Direksi</td>
                                        <td> : </td>
                                        <td id="det_agendadireksi"></td>
                                    </tr>
                                    <tr>
                                        <td>Personel yang dapat dihubungi</td>
                                        <td> : </td>
                                        <td id="det_personil"></td>
                                    </tr>
                                    <tr>
                                        <td>Memo/Surat Undangan</td>
                                        <td> : </td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td> : </td>
                                        <td id="det_status"></td>
                                    </tr>
                                    {{-- <tr>
                                        <td>Petugas Rapat/Protokoler</td>
                                        <td> : </td>
                                        <td id="det_petugasruangrapat"></td>
                                    </tr>
                                    <tr>
                                        <td>Petugas Vicon</td>
                                        <td> : </td>
                                        <td id="det_petugasti"></td>
                                    </tr> --}}
                                    <tr>
                                        <td>Link</td>
                                        <td> : </td>
                                        <td id="det_link"></td>
                                    </tr>
                                    <tr>
                                        <td>Password</td>
                                        <td> : </td>
                                        <td id="det_password"></td>
                                    </tr>
                                    <tr>
                                        <td>Keterangan</td>
                                        <td> : </td>
                                        <td id="det_keterangan"></td>
                                    </tr>
                                    <tr>
                                        <td>Created</td>
                                        <td> : </td>
                                        <td id="det_created"></td>
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

    @push('js')
    @endpush
</x-layouts.app>

<script>
    $(document).ready(function() {
        // mendapatkan tanggal hari ini
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        // susunan tanggal indonesia dd-mm-yyyy
        today = dd + '-' + mm + '-' + yyyy;

        $('#dataTables-dashboard').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.dashboard.ajax') }}",
                "type": "GET",
                "dataType": 'json',
                "data": function(data) {
                    data.tanggal_awal = $('#tanggal_awal').val();
                    data.tanggal_akhir = $('#tanggal_akhir').val();
                    data.acara = $('#acara').val();
                    data.agenda_direksi = $('#agenda_direksi').val();
                    data.bagian = $('#bagian').val();
                    data.vicon = $('#vicon').val();
                    data.jenisrapat = $('#jenisrapat').val();
                },
                dataFilter: function(reps) {
                    // console.log(reps);
                    return reps;
                },
                error: function(err) {
                    console.error(err);
                }
            },
            "language": {
                "infoFiltered": ""
            },
            "lengthChange": false,
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4, 5, 6, 7],
                "orderable": false
            }],
            "responsive": {
                "details": {
                    renderer: function(api, rowIdx, columns) {
                        var data = $.map(columns, function(col, i) {
                            var print = '<td>' + col.data + '</td>';

                            if (col.columnIndex == 2) {
                                var hari_tanggal = col.data.split(", ");
                                var tanggal = hari_tanggal[1];
                                if (tanggal == today) {
                                    print = '<td class="greenboldfont">' + col.data +
                                        '</td>';
                                }
                            }

                            if (col.columnIndex == 4) {
                                var kolom_ruangan = col.data.split(",");
                                var kode_ruangan = kolom_ruangan[0];
                                var ruangan = kolom_ruangan[1];
                                var ruangan_tampil = ruangan;

                                if (kode_ruangan == '1') {
                                    print = '<td class="redboldfont">' + ruangan_tampil +
                                        '</td>';
                                } else {
                                    print = '<td>' + ruangan_tampil + '</td>';
                                }
                            }

                            if (col.columnIndex == 1) {
                                var kolom_acara = col.data.split(",");
                                var kode_acara = kolom_acara[0];
                                var acara = kolom_acara[1];

                                if (kode_acara == '1') {
                                    print = '<td class="redboldfont">' + acara + '</td>';
                                } else {
                                    print = '<td>' + acara + '</td>';
                                }
                            }

                            // console.log(col.columnIndex+" "+kolom_ruangan);

                            return col.hidden ?
                                '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' +
                                col.columnIndex + '">' +
                                '<td style="font-weight:bold;">' + col.title + '</td> ' +
                                print +
                                '</tr>' :
                                '';
                        }).join('');

                        return data ?
                            $('<table/>').append(data) :
                            false;
                    }
                }
            },
            "rowCallback": function(row, data, index) {
                var hari_tanggal = data[2].split(", ");
                var tanggal = hari_tanggal[1];

                if (tanggal == today) {
                    $(row).find('td').css({
                        'color': 'green',
                        'font-weight': 'bold'
                    });
                }

                var kolom_ruangan = data[4].split(",");
                var kode_ruangan = kolom_ruangan[0];
                var ruangan = kolom_ruangan[1];

                $(row).find('td:eq(4)').html(ruangan);

                var kolom_acara = data[1].split(",");
                var kode_acara = kolom_acara[0];
                var acara = kolom_acara[1];

                $(row).find('td:eq(1)').html(acara);

                if (kode_ruangan == "1") {
                    $(row).find('td:eq(4)').css({
                        'color': 'red',
                        'font-weight': 'bold'
                    });
                }
                if (kode_acara == "1") {
                    $(row).find('td:eq(1)').css({
                        'color': 'red',
                        'font-weight': 'bold'
                    });
                }

            },
        })

    });

    function detail(id) {
        $.ajax({
            url: "{{ route('admin.dashboard.show', ':id') }}".replace(':id', id),
            method: "GET",
            dataType: "json",
            success: function(res) {
                console.log(res.success);

                if (res.success) {
                    var data = res.data;
                    console.log(data);

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
                        sk = "<a href='" + '#' + "' target='_blank'>Open</a>"
                    }

                    var link = "";
                    if (data.master_link !== null && data.master_link !== "" && data.master_link !==
                        undefined) {
                        link = "<a href='" + data.master_link.link + "' target='_blank'>" + data.master_link
                            .link + "</a>"
                    }

                    $('#det_bagian').html(data.bagian.bagian);
                    $('#det_acara').html(data.acara);
                    $('#det_tgl').html(tgl_tampil);
                    $('#det_waktu').html(waktu_tampil);
                    $('#det_peserta').html(data.peserta);
                    $('#det_jumlahpeserta').html(data.jumlahpeserta);
                    $('#det_tempat').html(tempat);
                    $('#det_privat').html(data.privat);
                    $('#det_vicon').html(data.vicon);
                    $('#det_jenislink').html(data.jenis_link);
                    $('#det_jenisrapat').html(data.jenisrapat.nama);
                    $('#det_agendadireksi').html(data.agenda_direksi);
                    $('#det_personil').html(data.personil);
                    $('#det_sk').html(sk);
                    $('#det_status').html(data.status);
                    $('#det_petugasruangrapat').html(data.petugasruangrapat);
                    $('#det_petugasti').html(data.petugasti);
                    $('#det_link').html(link);
                    $('#det_pass').html(data.password);
                    $('#det_keterangan').html(data.keterangan);
                    $('#det_created').html(data.created);
                    $('#det_user').html(data.user);
                    $('#detail').modal('show');
                }
            }
        })
    }

    function absensi(id) {
        window.location = "{{ route('admin.absensi.rekap', ':id') }}".replace(':id', id);
    }
</script>
