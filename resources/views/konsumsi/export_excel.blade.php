<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Konsumsi</title>
</head>

<body>
    <h3>Laporan Permintaan Konsumsi</h3>
    <h5>Tanggal: {{ $tanggal_mulai != $tanggal_akhir? $tanggal_mulai . ' - ' . $tanggal_akhir : $tanggal_mulai }} </h5>
    <h5>Divisi: {{ $divisi }} </h5>
    <h5>Posisi: {{ $posisi }}</h5>
    <h5>Status: {{ $status }}</h5>
    <h6><small><em>Diunduh pada tanggal {{ date('d-m-Y H:i:s') }}</em></small></h6>
    <table class="table table-bordered display responsive" style="width: 100%; float:center;" id="dataTables-konsumsi">
        <thead>
            <tr>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;" width="100%">No</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Agenda</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Tanggal</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Divisi</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Makanan</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Biaya Makanan</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Snack</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Biaya Snack</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Biaya Lain-lain</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Biaya per Agenda</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Keterangan</th>
                <th style="font-weight: bold; background-color: #f8f9fa; border: 1px solid black; text-align: center;">Status Approval</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_biaya_semua_agenda = 0;
            @endphp
            @foreach ($konsumsi as $index => $item)
                <tr>
                    <td style="border: 1px solid black;" rowspan="3">{{ $index + 1 }}</td>
                    <td style="border: 1px solid black;" rowspan="3">{{ $item->acara ?? 'Tidak ada acara' }}</td>
                    <td style="border: 1px solid black;" rowspan="3">
                        {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '' }}
                    </td>
                    <td style="border: 1px solid black;" rowspan="3">{{ $item->master_bagian_nama ?? '' }}</td>

                    @if ($item->status_batal_m_pagi != 0 && in_array($item->status_batal_m_pagi, $request_status))
                        <td style="background-color: #f8d7da; border: 1px solid black;">Pagi</td>
                        <td style="background-color: #f8d7da; border: 1px solid black;">{{ $item->biaya_m_pagi ?? 0 }}</td>
                    @elseif ($item->m_pagi == 1 && in_array($item->status_batal_m_pagi, $request_status))
                        <td style="background-color: #d4edda; border: 1px solid black;">Pagi</td>
                        <td style="background-color: #d4edda; border: 1px solid black;">{{ $item->biaya_m_pagi ?? 0 }}</td>
                    @else
                        <td style="border: 1px solid black;"></td>
                        <td style="border: 1px solid black;"></td>
                    @endif

                    @if ($item->status_batal_s_pagi != 0 && in_array($item->status_batal_s_pagi, $request_status))
                        <td style="background-color: #f8d7da; border: 1px solid black;">Pagi</td>
                        <td style="background-color: #f8d7da; border: 1px solid black;">{{ $item->biaya_s_pagi ?? 0 }}</td>
                    @elseif ($item->s_pagi == 1 && in_array($item->status_batal_s_pagi, $request_status))
                        <td style="background-color: #d4edda; border: 1px solid black;">Pagi</td>
                        <td style="background-color: #d4edda; border: 1px solid black;">{{ $item->biaya_s_pagi ?? 0 }}</td>
                    @else
                        <td style="border: 1px solid black;"></td>
                        <td style="border: 1px solid black;"></td>
                    @endif

                    <td style="border: 1px solid black;" rowspan="3">{{ $item->biaya_lain ?? 0 }}</td>
                    @php
                        $total_per_agenda = ($item->biaya_m_pagi && in_array($item->status_batal_m_pagi, $request_status) ? $item->biaya_m_pagi : 0) + ($item->biaya_m_siang && in_array($item->status_batal_m_siang, $request_status) ? $item->biaya_m_siang : 0) + ($item->biaya_m_malam && in_array($item->status_batal_m_malam, $request_status) ? $item->biaya_m_malam : 0) + ($item->biaya_s_pagi && in_array($item->status_batal_s_pagi, $request_status) ? $item->biaya_s_pagi : 0) + ($item->biaya_s_siang && in_array($item->status_batal_s_siang, $request_status) ? $item->biaya_s_siang : 0) + ($item->biaya_s_sore && in_array($item->status_batal_s_sore, $request_status) ? $item->biaya_s_sore : 0) + ($item->biaya_lain ?? 0);
                        $total_biaya_semua_agenda += $total_per_agenda;
                    @endphp
                    <td style="border: 1px solid black;" rowspan="4">{{ $total_per_agenda }}</td>
                    <td style="border: 1px solid black;" rowspan="3">{{ $item->konsumsi_keterangan }}</td>
                    <td style="border: 1px solid black;" rowspan="3">
                        <!-- Status Approval Text -->
                        @if ($item->konsumsi_status == 0)
                            Waiting for Approve
                        @elseif ($item->konsumsi_status == 1)
                            Approved
                        @elseif ($item->konsumsi_status == 2)
                            Approve by Kasubdiv GA
                        @elseif ($item->konsumsi_status == 3)
                            Approve by Kadiv GA
                        @elseif ($item->konsumsi_status == 4)
                            Canceled
                        @endif
                    </td>
                </tr>

                <tr>
                    @if ($item->status_batal_m_siang != 0  && in_array($item->status_batal_m_siang, $request_status))
                        <td style="background-color: #f8d7da; border: 1px solid black;">Siang</td>
                        <td style="background-color: #f8d7da; border: 1px solid black;">{{ $item->biaya_m_siang ?? 0 }}</td>
                    @elseif ($item->m_siang == 1 && in_array($item->status_batal_m_siang, $request_status))
                        <td style="background-color: #d4edda; border: 1px solid black;">Siang</td>
                        <td style="background-color: #d4edda; border: 1px solid black;">{{ $item->biaya_m_siang ?? 0 }}</td>
                    @else
                        <td style="border: 1px solid black;"></td>
                        <td style="border: 1px solid black;"></td>
                    @endif

                    @if ($item->status_batal_s_siang != 0 && in_array($item->status_batal_s_siang, $request_status))
                        <td style="background-color: #f8d7da; border: 1px solid black;">Siang</td>
                        <td style="background-color: #f8d7da; border: 1px solid black;">{{ $item->biaya_s_siang ?? 0 }}</td>
                    @elseif ($item->s_siang == 1 && in_array($item->status_batal_s_siang, $request_status))
                        <td style="background-color: #d4edda; border: 1px solid black;">Siang</td>
                        <td style="background-color: #d4edda; border: 1px solid black;">{{ $item->biaya_s_siang ?? 0 }}</td>
                    @else
                        <td style="border: 1px solid black;"></td>
                        <td style="border: 1px solid black;"></td>
                    @endif
                </tr>

                <tr>
                    @if ($item->status_batal_m_malam != 0 && in_array($item->status_batal_m_malam, $request_status))
                        <td style="background-color: #f8d7da; border: 1px solid black;">Malam</td>
                        <td style="background-color: #f8d7da; border: 1px solid black;">{{ $item->biaya_m_malam ?? 0 }}</td>
                    @elseif ($item->m_malam == 1 && in_array($item->status_batal_m_malam, $request_status))
                        <td style="background-color: #d4edda; border: 1px solid black;">Malam</td>
                        <td style="background-color: #d4edda; border: 1px solid black;">{{ $item->biaya_m_malam ?? 0 }}</td>
                    @else
                        <td style="border: 1px solid black;"></td>
                        <td style="border: 1px solid black;"></td>
                    @endif

                    @if ($item->status_batal_s_sore != 0 && in_array($item->status_batal_s_sore, $request_status))
                        <td style="background-color: #f8d7da; border: 1px solid black;">Sore</td>
                        <td style="background-color: #f8d7da; border: 1px solid black;">{{ $item->biaya_s_sore ?? 0 }}</td>
                    @elseif ($item->s_sore == 1 && in_array($item->status_batal_s_sore, $request_status))
                        <td style="background-color: #d4edda; border: 1px solid black;">Sore</td>
                        <td style="background-color: #d4edda; border: 1px solid black;">{{ $item->biaya_s_sore ?? 0 }}</td>
                    @else
                        <td style="border: 1px solid black;"></td>
                        <td style="border: 1px solid black;"></td>
                    @endif
                </tr>

                <tr class="total-row">
                    <td style="border: 1px solid black;" colspan="4"></td>
                    <td style="border: 1px solid black;"><strong>Total Makanan</strong></td>
                    <td style="border: 1px solid black;"><strong>{{ ($item->m_pagi == 1 && in_array($item->status_batal_m_pagi, $request_status)? $item->biaya_m_pagi : 0) + ($item->m_siang == 1 && in_array($item->status_batal_m_siang, $request_status)? $item->biaya_m_siang : 0) + ($item->m_malam == 1 && in_array($item->status_batal_m_malam, $request_status)? $item->biaya_m_malam : 0) }}</strong>
                    </td>
                    <td style="border: 1px solid black;"><strong>Total Snack</strong></td>
                    <td style="border: 1px solid black;"><strong>{{ ($item->s_pagi == 1 && in_array($item->status_batal_s_pagi, $request_status)? $item->biaya_s_pagi : 0) + ($item->s_siang == 1 && in_array($item->status_batal_s_siang, $request_status)? $item->biaya_s_siang : 0) + ($item->s_sore == 1 && in_array($item->status_batal_s_sore, $request_status)? $item->biaya_s_sore : 0) }}</strong>
                    </td>
                    <td style="border: 1px solid black;"><strong>{{ $item->biaya_lain ?? 0 }}</strong></td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"></td>
                </tr>

            @endforeach
            <tr class="total-row">
                <td style="border: 1px solid black;" colspan="8"></td>
                <td style="border: 1px solid black;"><strong>Total Biaya</strong></td>
                <td style="border: 1px solid black;"><strong>{{ $total_biaya_semua_agenda }}</strong></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
