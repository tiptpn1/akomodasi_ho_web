<x-layouts.app>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Data Presensi Rapat</h3>
                @if ($sendVicon->status_absensi == 'Closed')
                    <form action="{{ route('admin.absensi.export', ['id' => $sendVicon->id]) }}" method="get">
                        @csrf
                        {{-- <input type="hidden" name="id" value="{{ $sendVicon->id }}"> --}}
                        <button type="submit"class="btn btn-warning">
                            <i class="fas fa-arrow-circle-down"></i>
                            Excel
                        </button>
                    </form>
                @endif
                <div class="card mb-4 mt-4">
                    <div class="card-body">
                        <table class="display responsive mb-5" style="width: 100%; float:center;">
                            <tbody>
                                <tr>
                                    <th>Acara</th>
                                    <td>
                                        <b>:</b>
                                    </td>
                                    <td>
                                        <b>{{ $sendVicon->acara }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Hari, Tanggal</th>
                                    <td>
                                        <b>:</b>
                                    </td>
                                    <td>
                                        <b>{{ Carbon\Carbon::parse($sendVicon->tanggal)->translatedFormat('l') . ', ' . Carbon\Carbon::parse($sendVicon->tanggal)->translatedFormat('d F Y') }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td>
                                        <b>:</b>
                                    </td>
                                    <td>
                                        <b>{{ Carbon\Carbon::parse($sendVicon->waktu)->translatedFormat('H:i') . ' - ' . Carbon\Carbon::parse($sendVicon->waktu2)->translatedFormat('H:i') }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Presensi</th>
                                    <td>
                                        <b>:</b>
                                    </td>
                                    <td>
                                        <b>
                                            @if ($sendVicon->status_absensi == 'Closed')
                                                Closed
                                            @elseif ($sendVicon->status_absensi == 'Open')
                                                Open
                                            @else
                                                Not open yet
                                            @endif
                                        </b>
                                    </td>
                                    @php
                                        $datetime = $sendVicon->tanggal . ' ' . $sendVicon->waktu2;
                                        $is_closed = 0; //initial to false

                                        $datetime_now = date('Y-m-d H:i:s');
                                        $hoursToAdd = 8;

                                        $cek_close_vicon = new DateTime($datetime);
                                        $cek_close_vicon->add(new DateInterval("PT{$hoursToAdd}H"));
                                        $close_vicon = $cek_close_vicon->format('Y-m-d H:i:s');

                                        $now = new DateTime($datetime_now);
                                        $close = new DateTime($close_vicon);

                                        if ($now > $close) {
                                            $is_closed = 1;
                                        }
                                    @endphp
                                    @if ($is_closed != 1)
                                        <td>
                                            <center>
                                                <button style="margin-right: 6px; margin-bottom: 3px;" type="button"
                                                    data-toggle="modal" data-target="#editStatusModal"
                                                    class="btn btn-warning btn-sm">
                                                    <b>Edit Status</b>
                                                </button>
                                            </center>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <th>Presensi Online</th>
                                    <td>
                                        <b>:</b>
                                    </td>
                                    <td>
                                        <b>
                                            <a href="{{ route('absensi.create', ['token' => $sendVicon->token, 'id' => $sendVicon->id]) }}"
                                                target="_blank">
                                                    <i class="fas fa-external-link-alt"></i> Presensi Online</button>
                                            </a>
                                        </b>
                                    </td>

                                </tr>
                                
                                <tr>
                                    <th>Presensi Offline</th>
                                    <td>
                                        <b>:</b>
                                    </td>
                                    <td>
                                        <b>
                                        <a class="btn btn-sm btn-default mb-1" href="/file-sekper/Template-Daftar-Hadir.docx" download>
                                            <i class="fas fa-download"></i> Template Daftar Hadir
                                        </a><br>
                                        <a class="btn btn-sm btn-default" href="/file-sekper/Template-Risalah-Rapat.docx" download>
                                            <i class="fas fa-download"></i> Template Risalah Rapat
                                        </a>
                                        </b>
                                    </td>

                                </tr>
                            </tbody>
                        </table>

                        <div class="table-responsive">
                            <table class="display responsive" style="width: 100%; float:center;"
                                id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>
                                            <center>No</center>
                                        </th>
                                        <th>
                                            <center>Nama</center>
                                        </th>
                                        <th>
                                            <center>Jabatan</center>
                                        </th>
                                        <th>
                                            <center>Instansi</center>
                                        </th>
                                        <th>
                                            <center>Jam</center>
                                        </th>
                                        <th>
                                            <center>Aksi</center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sendVicon->absensis as $absensi)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $absensi->nama }}</td>
                                            <td>{{ $absensi->jabatan }}</td>
                                            <td>{{ $absensi->instansi }}</td>
                                            <td>
                                                <center>
                                                    {{ Carbon\Carbon::parse($absensi->created)->format('H:i:s') }}
                                                </center>
                                            </td>
                                            <td>
                                                <center>
                                                    <button style="margin-right: 6px; margin-bottom: 3px;"
                                                        type="button" class="btn btn-primary btn-sm"
                                                        onclick="detail({{ $absensi->id }})">
                                                        Detail
                                                    </button>
                                                </center>
                                            </td>
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

    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detailModalLabel">Detail Presensi</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="surat" class="form-group">
                        <form class="form-horizontal calender" role="form" action="login" method="post">
                            <div class="form-group">
                                <label class="col-sm-13 control-label"></label>
                                <div class="col-sm-12">
                                    <table width="100%">
                                        <tr>
                                            <th style="width: 40%">IP</td>
                                            <td style="width: 5%"> : </td>
                                            <th style="width: 50%" id="ip"></th>
                                        </tr>
                                        <tr>
                                            <td>City</td>
                                            <td> : </td>
                                            <td id="city"></td>
                                        </tr>
                                        <tr>
                                            <td>Region</td>
                                            <td> : </td>
                                            <td id="region"></td>
                                        </tr>
                                        <tr>
                                            <td>Country </td>
                                            <td> : </td>
                                            <td id="country"></td>
                                        </tr>
                                        <tr>
                                            <td>Location</td>
                                            <td> : </td>
                                            <td id="location"></td>
                                        </tr>
                                        <tr>
                                            <td>Timezone</td>
                                            <td> : </td>
                                            <td id="timezone"></td>
                                        </tr>
                                        <tr>
                                            <td>Browser</td>
                                            <td> : </td>
                                            <td id="browser"></td>
                                        </tr>
                                        <tr>
                                            <td>OS</td>
                                            <td> : </td>
                                            <td id="os"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-s" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="editStatusModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editStatusModalLabel">Update Status Presensi Rapat</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="surat" class="form-group">
                        <form class="form-horizontal calender" role="form"
                            action="{{ route('admin.absensi.edit.status', ['id' => $sendVicon->id]) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="col-sm-13 control-label"></label>
                                <div class="col-sm-12">
                                    <b>Status *</b>
                                    <select name="status_absensi" class="form-control"
                                        value="{{ $sendVicon->status_absensi }}" required>
                                        <option value=''>Pilihan</option>
                                        <option value='Open' @if ($sendVicon->status_absensi == 'Open') selected @endif>
                                            Open
                                        </option>
                                        <option value='Closed' @if ($sendVicon->status_absensi == 'Closed') selected @endif>
                                            Closed
                                        </option>
                                    </select>
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
</x-layouts.app>

<script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable();
    });

    function detail(id) {
        $.ajax({
            type: "GET",
            url: "{{ route('admin.absensi.show', ':id') }}".replace(':id', id),
            success: function(response) {
                if (response.success) {
                    var absensi = response.absensi;
                    $('#ip').text(absensi.ip);
                    $('#city').text(absensi.city);
                    $('#region').text(absensi.region);
                    $('#country').text(absensi.country);
                    $('#location').text(absensi.loc);
                    $('#timezone').text(absensi.timezone);
                    $('#browser').text(absensi.browser);
                    $('#os').text(absensi.os);
                    $('#detailModal').modal('show');
                }
            },
            error: function(xhr, status, error) {
                alert(xhr.responseJson.message);
            }
        })
    };
</script>
