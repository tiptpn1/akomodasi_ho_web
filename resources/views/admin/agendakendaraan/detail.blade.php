<x-layouts.app>
    <x-slot name="slot">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h3 class="mt-4">Detail Agenda Kendaraan</h3><br>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="display responsive" style="width: 80%; float:center;"
                                    id="dataTables-example">
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
                                        @foreach ($view as $result)
                                            @php
                                                $gettgl = explode('-', $result->tanggal);
                                                $th = $gettgl[0];
                                                $d = $gettgl[2];
                                                $bln = $gettgl[1];
                                                $settgl = "$d-$bln-$th";
                                            @endphp
                                            <tr>
                                                <th>Kendaraan
                                                <td>
                                                    {{ $result->join_no_polisi }}
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
                                                <th>Pengemudi
                                                <td>
                                                    {{ $result->join_pengemudi }}
                                                </td>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>PIC
                                                <td>
                                                    {{ $result->join_bagian }}
                                                </td>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>Tujuan
                                                <td>
                                                    {{ $result->tujuan }}
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
                    $('#dataTables-example').dataTable();
                });
            </script>
        </x-slot>
    </x-slot>
</x-layouts.app>
