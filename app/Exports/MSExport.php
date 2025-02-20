<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class MSExport implements FromCollection, WithHeadings, WithStyles, WithEvents
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
                $item->nama_pic ?? '-',
                // $item->tgl_permintaan ?? '-',
                $item->tgl_permintaan ? Carbon::parse($item->tgl_permintaan)->format('d-m-Y') : '-', // Format DD-MM-YYYY
                $item->divisi ?? '-',
                $item->kadiv ?? '-',
                $item->jlh_karyawan ?? '-',
                $item->jlh_makan ?? '-',
                $this->getStatusLabel($item->status)
                
            ];
        });
    
        return $collection;
    }
    

    public function headings(): array
    {
        return [
            'No', 'Nama Peminta', 'Tanggal Permintaan', 'Divisi', 'Kadiv', 'Jumlah Karyawan', 'Jumlah Makan Siang', 'Status'
        ];
    }

    public function getStatusLabel($status)
    {
        switch ($status) {
            case 0:
                return 'Canceled';
            case 1:
                return 'Pengajuan Divisi';
            case 2:
                return 'Approved';
            case 3:
                return 'Rejected';
            default:
                return '-';
        }
    }

    public function styles($sheet)
    {
        // Hitung jumlah baris berdasarkan jumlah data
        $rowCount = count($this->data) + 1; // +1 untuk header
        
        // Range dinamis untuk border berdasarkan jumlah data
        $range = "A1:H$rowCount";
    
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

