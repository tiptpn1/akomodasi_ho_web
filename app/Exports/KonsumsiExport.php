<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class KonsumsiExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    protected $konsumsi, $tanggal_mulai, $tanggal_akhir, $divisi, $posisi, $status, $request_status;

    public function __construct($konsumsi, $tanggal_mulai, $tanggal_akhir, $divisi, $posisi, $status, $request_status)
    {
        $this->konsumsi = $konsumsi;
        $this->tanggal_mulai = $tanggal_mulai;
        $this->tanggal_akhir = $tanggal_akhir;
        $this->divisi = $divisi;
        $this->posisi = $posisi;
        $this->status = $status;
        $this->request_status = $request_status;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('konsumsi.export_excel', [
            'konsumsi' => $this->konsumsi,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_akhir' => $this->tanggal_akhir,
            'divisi' => $this->divisi,
            'posisi' => $this->posisi,
            'status' => $this->status,
            'request_status' => $this->request_status,
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
