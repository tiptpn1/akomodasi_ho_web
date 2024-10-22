<x-layouts.app>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Detail Rapat Vicon</h3>
                <div class="card mb-4 mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display responsive" style="width: 80%; float:center;" id="vicon-table">
                                <tbody>
                                    @php
                                        $no = 0;
                                        $settgl = '';

                                        $daftar_hari = [
                                            'Sunday' => 'Minggu',
                                            'Monday' => 'Senin',
                                            'Tuesday' => 'Selasa',
                                            'Wednesday' => 'Rabu',
                                            'Thursday' => 'Kamis',
                                            'Friday' => 'Jumat',
                                            'Saturday' => 'Sabtu',
                                        ];
                                    @endphp
                                    @foreach ($list as $result)
                                        @php
                                            $gettgl = explode('-', $result->tanggal);
                                            $th = $gettgl[0];
                                            $d = $gettgl[2];
                                            $bln = $gettgl[1];
                                            $settgl = "$d-$bln-$th";

                                            $getwaktu = explode(':', $result->waktu);
                                            $jam = $getwaktu[0];
                                            $detik = $getwaktu[2];
                                            $menit = $getwaktu[1];
                                            $setwaktu = "$jam:$menit";

                                            $getwaktua = explode(':', $result->waktu2);
                                            $jam2 = $getwaktua[0];
                                            $detik = $getwaktua[2];
                                            $menit2 = $getwaktua[1];
                                            $setwaktu2 = "$jam2:$menit2";
                                        @endphp
                                        <tr>
                                            <th>Status
                                            <td>
                                                {{ $result->status }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Agenda
                                            <td>
                                                {{ $result->acara }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Hari, Tanggal
                                            <td>
                                                {{ $daftar_hari[date('l', strtotime($result->tanggal))] . ', ' . $settgl }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Waktu
                                            <td>
                                                {{ $setwaktu . ' - ' . $setwaktu2 }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Tempat
                                            <td>
                                                @if (is_null($result->id_ruangan))
                                                    {{ $result->ruangan_lain }}
                                                @else
                                                    {{ $result->ruangan }}
                                                @endif
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>PIC
                                            <td>
                                                {{ $result->bagian->bagian ?? '' }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Peserta
                                            <td>
                                                {{ $result->peserta }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Keterangan
                                            <td>
                                                {{ $result->keterangan }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Estimasi Jumlah Peserta di Kandir
                                            <td>
                                                {{ $result->jumlahpeserta }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Vicon
                                            <td>
                                                {{ $result->vicon }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Privat
                                            <td>
                                                {{ $result->privat }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Jenis Link
                                            <td>
                                                {{ $result->jenis_link }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Agenda Direksi
                                            <td>
                                                {{ $result->agenda_direksi }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Jenis Rapat
                                            <td>
                                                {{ $result->jenisrapat->nama ?? '' }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Personel yang dapat dihubungi
                                            <td>
                                                {{ $result->personil ?? '' }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Petugas Ruang Rapat
                                            <td>
                                                {{ $result->petugasruangrapat ?? '' }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Petugas Vicon
                                            <td>
                                                {{ $result->petugasti ?? '' }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Memo/Surat Undangan
                                            <td>
                                                <a href="{{ asset($result->sk) }}"
                                                    download="{{ $result->sk }}">Download</a>
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>link
                                            <td>
                                                {{ $result->link }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Password
                                            <td>
                                                {{ $result->password }}
                                            </td>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Keterangan
                                            <td>
                                                {{ $result->keterangan }}
                                            </td>
                                            </th>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('#vicon-table').dataTable();
            });
        </script>
    </x-slot>
</x-layouts.app>
