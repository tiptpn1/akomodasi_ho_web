<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class KaskecilExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // Remove the 'Aksi' column and add the 'No' column with auto numbering
        $collection = $this->data->map(function ($item, $index) {
            return [
                $index + 1, // Auto numbering
                $item->nama_pengaju ?? '-',
                $item->tgl_pengajuan ?? '-',
                $item->group->nama_group ?? '-',
                $item->keterangan ?? '-',
                ($item->gl->nomor_gl ?? '-') . ' - ' . ($item->gl->nama_gl ?? '-'),
                ($item->cc->nomor_cc ?? '-') . ' - ' . ($item->cc->nama_cc ?? '-'),            
                // 'Rp. ' . ($item->nominal !== null ? number_format($item->nominal, 2, ',', '.') : '-'),
                ($item->nominal !== null ? $item->nominal : '-'),
                isset($item->kendaraan) ? ($item->kendaraan->nopol ?? '-' . ' - ' . $item->kendaraan->tipe_kendaraan ?? '-') : '-',
                $item->km_awal ?? '-',
                $item->km_akhir ?? '-',
                ($item->km_akhir ?? 0) - ($item->km_awal ?? 0),
                $item->bbm->nama_bbm ?? '-',
                $item->liter_bensin ?? '-',
                // 'Rp. ' . ($item->harga_bensin !== null ? number_format($item->harga_bensin, 2, ',', '.') : '-'),
                // 'Rp. ' . ($item->tol !== null ? number_format($item->tol, 2, ',', '.') : '-'),
                // 'Rp. ' . ($item->parkir !== null ? number_format($item->parkir, 2, ',', '.') : '-'),
                // 'Rp. ' . ($item->ppn !== null ? number_format($item->ppn, 2, ',', '.') : '-'),
                // 'Rp. ' . ($item->pph !== null ? number_format($item->pph, 2, ',', '.') : '-'),
                // 'Rp. ' . ($item->biaya_aplikasi !== null ? number_format($item->biaya_aplikasi, 2, ',', '.') : '-'),
                // 'Rp. ' . ($item->lain_lain !== null ? number_format($item->lain_lain, 2, ',', '.') : '-'),
                ($item->harga_bensin !== null ? $item->harga_bensin : '-'),
                ($item->tol !== null ? $item->tol : '-'),
                ($item->parkir !== null ? $item->parkir : '-'),
                ($item->ppn !== null ? $item->ppn : '-'),
                ($item->pph !== null ? $item->pph : '-'),
                ($item->biaya_aplikasi !== null ? $item->biaya_aplikasi : '-'),
                ($item->lain_lain !== null ? $item->lain_lain : '-'),
                $item->dibayarkan_oleh ?? '-',
                $item->tgl_dibayarkan ?? '-',
                // $item->bukti_nota ? 'Lihat Bukti Nota' : '-',
                // $item->bukti_bayar ? 'Lihat Bukti Bayar' : '-',
                // $item->nominal !== null && $item->ppn !== null && $item->pph !== null && $item->tol !== null && $item->parkir !== null && $item->lain_lain !== null && $item->harga_bensin !== null
                // ? number_format($item->nominal + $item->ppn + $item->pph + $item->tol + $item->parkir + $item->lain_lain + $item->harga_bensin, 2, ',', '.')
                // 'Rp. ' . number_format(($item->nominal + $item->ppn + $item->pph + $item->tol + $item->parkir + $item->lain_lain + $item->harga_bensin + $item->biaya_aplikasi),2,',','.')
                ($item->nominal + $item->ppn + $item->pph + $item->tol + $item->parkir + $item->lain_lain + $item->harga_bensin + $item->biaya_aplikasi)
            ];
        });
    
        return $collection;
    }
    

    public function headings(): array
    {
        return [
            'No', 'Nama Pengaju', 'Tanggal Pengajuan', 'Group', 'Keterangan', 'GL', 'CC', 'Nominal',
            'Kendaraan', 'KM Awal', 'KM Akhir', 'Jumlah KM', 'BBM', 'Liter Bensin', 'Harga Bensin',
            'Biaya Tol', 'Parkir', 'PPN', 'PPH', 'Biaya Aplikasi', 'Biaya Lain-lain', 'Dibayarkan Oleh',
            'Tanggal Dibayarkan','Total Biaya'
        ];
    }

    public function styles($sheet)
    {
        // Hitung jumlah baris berdasarkan jumlah data
        $rowCount = count($this->data) + 1; // +1 untuk header
        
        // Range dinamis untuk border berdasarkan jumlah data
        $range = "A1:X$rowCount";
    
        return [
            // Styling untuk header (baris pertama)
            1    => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ADD8E6'], // Light blue
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            // Styling untuk border yang menyesuaikan jumlah baris data
            $range => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'], // Black borders
                    ],
                ],
            ],
        ];
    }    

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Auto width columns based on content
                foreach (range('A', 'X') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}

