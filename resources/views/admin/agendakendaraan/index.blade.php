<x-layouts.app>
    <x-slot name="slot">

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h3 class="mt-4">Manajemen Penggunaan Kendaraan</h3>
                    @if (!in_array(auth()->user()->master_hak_akses_id, [5,6]))
                        <button type="button" data-toggle="modal" data-target="#add"
                            class="btn btn-success">Tambahkan</button>
                    @endif

                    <button type="submit" data-toggle="modal" data-target="#filter_agendakendaraan"
                        class="btn btn-warning">Filter</button>
                    <button type="button" class="btn btn-info"
                        onclick="window.location='{{ route('admin.dashboard.kendaraan.resetFilter') }}'">Reset
                        Filter</button>
                    <br>
                    <br>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="display responsive" style="width: 100%; float:center;"
                                    id="dataTables-agendakendaraan">
                                    <thead>
                                        <tr>
                                            <th>
                                                <center>No
                                            </th>
                                            <th>
                                                <center>No. Polisi
                                            </th>
                                            <th>
                                                <center>Pengemudi
                                            </th>
                                            <th>
                                                <center>Tanggal
                                            </th>
                                            <th>
                                                <center>Bagian
                                            </th>
                                            <th>
                                                <center>Tujuan
                                            </th>
                                            <th>
                                                <center>Keterangan
                                            </th>
                                            @if (auth()->user()->role != 'Read Only')
                                                <th>
                                                    <center>Aksi
                                                </th>
                                            @endif
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <div id="filter_agendakendaraan" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Filter Data</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div id="surat" class="form-group container-fluid">
                            <form action="" method="post" id='form_filteragendakendaraan'>
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <b>Tanggal Awal</b>
                                        <input type="text" name="tanggal_awal" id="tanggal_awal"
                                            class="form-control datepicker" readonly="true">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <b>Tanggal Akhir</b>
                                        <input type="text" name="tanggal_akhir" id="tanggal_akhir"
                                            class="form-control datepicker" readonly="true">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <b>No. Polisi Kendaraan</b>
                                        <select name="kendaraan" id="kendaraan" class="form-control">
                                            <option disabled>Pilih No. Polisi Kendaraan</option>
                                            <option value="" selected>Semua Kendaraan</option>
                                            @foreach ($model->tampilall('kendaraan') as $ken)
                                                <option value='{{ $ken->id }}'>{{ $ken->no_polisi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <b>Pengemudi</b>
                                        <select name="pengemudi" id="pengemudi" class="form-control">
                                            <option disabled>Pilih Pengemudi</option>
                                            <option value="" selected>Semua Pengemudi</option>
                                            @foreach ($model->tampil('master_user', "master_hak_akses_id = 6") as $peng)
                                                <option value='{{ $peng->master_user_id }}'>{{ $peng->master_user_nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <b>PIC/Bagian (bisa lebih dari satu dengan +Ctrl)</b>
                                        <select id="bagian" name="bagian[]" class="custom-select" multiple>
                                            <option disabled>Pilih PIC/Bagian</option>
                                            <option value="" selected>Semua PIC/Bagian</option>
                                            @foreach ($model->tampilall('master_bagian') as $datakebun)
                                                <option value='{{ $datakebun->master_bagian_id }}'>{{ $datakebun->master_bagian_nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <b>Tujuan</b>
                                        <input type="text" class="form-control" name="tujuan" id="tujuan"
                                            placeholder="Pencarian Agenda">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="agendakendaraan_filter_excel"
                                        class="btn btn-warning">Download Excel</button>
                                    <button type="button" id="agendakendaraan_filter_pdf" class="btn btn-info">Download
                                        PDF</button>
                                    <button type="button" id="agendakendaraan_filter"
                                        class="btn btn-primary">Cari</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!in_array(auth()->user()->master_hak_akses_id, [5,6]))
            <div id="update" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Update Penggunaan Kendaraan</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <div id="surat" class="form-group">
                                <form class="form-horizontal calender" role="form" method="post"
                                    action="{{ route('admin.dashboard.kendaraan.update') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" id="idUpdateAgendakendaraan">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>No. Polisi *</b>
                                                <select name="no_polisi" id="updateNopol" class="form-control">
                                                    <option value="">Pilih No. Polisi Kendaraan</option>
                                                    @foreach ($model->tampil('kendaraan', 'status = "Aktif"') as $ken)
                                                        <option value='{{ $ken->id }}'>
                                                            {{ $ken->no_polisi }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Pengemudi *</b>
                                                <select name="pengemudi" id="updatePengemudi" class="form-control"
                                                    required>
                                                    <option value="">Pilih Pengemudi</option>
                                                    @foreach ($model->tampil('master_user', 'master_hak_akses_id == 6') as $peng)
                                                        <option value='{{ $peng->master_user_id }}'>
                                                            {{ $peng->master_user_nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Tanggal *</b>
                                                <input type="text" id="updateTanggal"
                                                    class="form-control datepicker" id="dtpnoex" name="tanggal"
                                                    placeholder="Isikan Tanggal" readOnly={true} required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Bagian *</b>
                                                <select name="bagian" id="updateBagian" class="form-control">
                                                    <option value="">Pilih Bagian</option>
                                                    @foreach ($bagian->all()->orderBy('master_bagian_id') as $datakebun)
                                                        <option value='{{ $datakebun->id }}'>
                                                            {{ $datakebun->bagian }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Tujuan *</b>
                                                <input type="text" id="updateTujuan" class="form-control"
                                                    name="tujuan" placeholder="Isikan Daerah dan Tujuan Agenda"
                                                    required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Keterangan</b>
                                                <input type="text" id="updateKeterangan" class="form-control"
                                                    name="keterangan"
                                                    placeholder="Contoh : Bersama tamu dari Kementerian BUMN">
                                            </div>
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
            </div>

            <div id="add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Tambah Penggunaan Kendaraan</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <div id="surat" class="form-group">
                                <form id="antoform" class="form-horizontal calender" role="form"
                                    action="{{ route('admin.dashboard.kendaraan.insert') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>No. Polisi *</b>
                                                <select name="no_polisi" class="form-control" required>
                                                    <option value=''>Pilih No. Polisi Kendaraan</option>
                                                    @foreach ($model->tampil('kendaraan', 'status = "Aktif"') as $ken)
                                                        <option value='{{ $ken->id }}'>{{ $ken->no_polisi }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Pengemudi *</b>
                                                <select name="pengemudi" class="form-control" required>
                                                    <option value=''>Pilih Pengemudi</option>
                                                    @foreach ($model->tampil('master_user', 'master_hak_akses_id == 6') as $peng)
                                                        <option value='{{ $peng->master_user_id }}'>{{ $peng->master_user_nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Tanggal *</b>
                                                <input type="text" id="tanggal" name="tanggal"
                                                    class="form-control daterange" placeholder="Isikan Tanggal"
                                                    readOnly={true} required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Bagian *</b>
                                                <select name="bagian" class="form-control" required>
                                                    <option value=''>Pilih Bagian</option>
                                                    @foreach ($bagian->all()->orderBy('master_bagian_id') as $bag)
                                                        <option value='{{ $bag->id }}'>{{ $bag->bagian }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Tujuan *</b>
                                                <input type="text" class="form-control" name="tujuan"
                                                    placeholder="Isikan Daerah dan Tujuan Agenda" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Keterangan</b>
                                                <input type="text" class="form-control" name="keterangan"
                                                    placeholder="Contoh : Bersama tamu dari Kementerian BUMN">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default antoclose"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary antosubmit">Tambahkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <x-slot name="scripts">
            <script type="text/javascript">
                $(document).ready(function() {
                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = today.getFullYear();
                    today = mm + '/' + dd + '/' + yyyy;
                    $('.daterange').daterangepicker({
                        format: 'YYYY-MM-DD',
                        minDate: today
                    });

                    $('.datepicker').datepicker({
                        dateFormat: "dd-mm-yy",
                        showButtonPanel: true,
                        closeText: 'Clear',
                        onClose: function(dateText, inst) {
                            if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
                                document.getElementById(this.id).value = '';
                            }
                        }
                    });

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content"),
                        }
                    });

                    table_agendakendaraan = $('#dataTables-agendakendaraan').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": "{{ route('admin.dashboard.kendaraan.ajax') }}",
                            "type": "POST",
                            "dataType": 'json',
                            "data": function(data) {
                                data.bagian = $('#bagian').val().length > 0 ? $('#bagian').val() : '';
                                data.tanggal_awal = $('#tanggal_awal').val();
                                data.tanggal_akhir = $('#tanggal_akhir').val();
                                data.kendaraan = $('#kendaraan').val();
                                data.pengemudi = $('#pengemudi').val();
                                data.tujuan = $('#tujuan').val();
                            },
                            dataFilter: function(reps) {
                                // console.log(reps);
                                return reps;
                            },
                            error: function(err) {
                                // console.log(err);
                            }
                        },
                        "language": {
                            "infoFiltered": ""
                        },
                        "lengthChange": false,
                        "columnDefs": [{
                            "targets": [0, 1, 2, 3, 4, 5, 6, -1],
                            "orderable": false
                        }],
                        // pengaturan warna berdasarkan ketentuan
                        "rowCallback": function(row, data, index) {
                            // tanggal agenda vicon = hari ini -> beri warna hijau tebal
                            if (data[3] == today) {
                                $(row).find('td').css({
                                    'color': 'green',
                                    'font-weight': 'bold'
                                });
                            }

                            // variable cek split data kendaraan apakah tersedia
                            var kolom_kendaraan = data[1].split(",");
                            var kode_kendaraan = kolom_kendaraan[0];
                            var kendaraan = kolom_kendaraan[1];

                            // variable cek split data pengemudi apakah tersedia
                            var kolom_pengemudi = data[2].split(",");
                            var kode_pengemudi = kolom_pengemudi[0];
                            var pengemudi = kolom_pengemudi[1];

                            // tuliskan yang diperlukan -> setelah displit
                            data[1] = kendaraan;
                            data[2] = pengemudi;
                            $(row).find('td:eq(1)').html(kendaraan);
                            $(row).find('td:eq(2)').html(pengemudi);

                            // apabila cek data nilai = 1 -> cetak merah tebal
                            if (kode_kendaraan == "1") {
                                $(row).find('td:eq(1)').css({
                                    'color': 'red',
                                    'font-weight': 'bold'
                                });
                            }
                            if (kode_pengemudi == "1") {
                                $(row).find('td:eq(2)').css({
                                    'color': 'red',
                                    'font-weight': 'bold'
                                });
                            }
                        },
                        "responsive": {
                            "details": {
                                renderer: function(api, rowIdx, columns) {
                                    var data = $.map(columns, function(col, i) {
                                        var print = '<td>' + col.data + '</td>';

                                        if (col.columnIndex == 3 && col.data == today) {
                                            print = '<td class="greenboldfont">' + col.data + '</td>';
                                        }

                                        return col.hidden ?
                                            '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' +
                                            col.columnIndex + '">' +
                                            '<td style="font-weight:bold;"' + col.title + '</td> ' +
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
                    })

                    @if (auth()->user()->role != 'Read Only')
                        table_agendakendaraan.on('click', '.btn-edit', function() {
                            id = $(this).data('id');
                            nopol = $(this).data('nopol');
                            pengemudi = $(this).data('driver');
                            tanggal = $(this).data('tanggal');
                            bagian = $(this).data('bagian');
                            tujuan = $(this).data('tujuan');
                            keterangan = $(this).data('keterangan');

                            $('#idUpdateAgendakendaraan').val(id);
                            $('#updateNopol').val(nopol);
                            $('#updatePengemudi').val(pengemudi);
                            $('#updateTanggal').val(tanggal);
                            $('#updateBagian').val(bagian);
                            $('#updateTujuan').val(tujuan);
                            $('#updateKeterangan').val(keterangan);

                            $('#update').modal('show');
                        });
                    @endif
                });

                @if (auth()->user()->role != 'Read Only')
                    function hapus(id) {
                        event.preventDefault();
                        if (confirm('Yakin untuk menghapus data?')) {
                            url = "{{ route('admin.dashboard.kendaraan.delete', ['id' => ':id']) }}";

                            location.replace(url.replace(':id', id));
                        }
                    }

                    @if (Session::has('success'))
                        setTimeout(function() {
                            swal("{{ Session::get('success') }}");
                        }, 1000);
                    @endif

                    @if (Session::has('gglkendaraan'))
                        setTimeout(function() {
                            swal({
                                    title: "{{ Session::get('gglkendaraan') }}",
                                    type: "info",
                                    showCancelButton: true,
                                    confirmButtonText: "Cancel",
                                    cancelButtonText: "Ok",
                                    confirmButtonColor: "#ff0055",
                                    cancelButtonColor: "#999999",
                                    reverseButtons: true,
                                    focusConfirm: false,
                                    focusCancel: true
                                },
                                function() {
                                    console.log('tes');
                                    location.replace("{{ route('admin.dashboard.kendaraan.cancel') }}");
                                });
                        }, 1000);
                    @endif
                @endif

                // send vicon form filter to open excel report
                $('#agendakendaraan_filter_excel').click(function() {
                    var x = $("#form_filteragendakendaraan");
                    x[0].action = "{{ route('admin.dashboard.kendaraan.exportExcel') }}";
                    x[0].submit();
                });

                // send vicon form filter to open PDF report
                $('#agendakendaraan_filter_pdf').click(function() {
                    var x = $("#form_filteragendakendaraan");
                    x.attr("target", "_blank");
                    x[0].action = "{{ route('admin.dashboard.kendaraan.exportPdf') }}";
                    x[0].submit();
                    x.removeAttr("target");
                });

                // filter datatables sgenda kendaraan server side
                $('#agendakendaraan_filter').click(function() {
                    $('#filter_agendakendaraan').modal('hide');
                    $('.modal-backdrop').remove();
                    table_agendakendaraan.ajax.reload();
                });
            </script>
        </x-slot>

    </x-slot>
</x-layouts.app>
