<?php

namespace App\Exports;

use App\Models\MRegional;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterRegionalExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Mengambil data kendaraan untuk diekspor.
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * Menentukan header tabel di file Excel.
     */
    public function headings(): array
    {
        return ["ID", "Nama Regional"];
    }

    /**
     * Menyesuaikan isi dari setiap baris di file Excel.
     */
    public function map($row): array
    {
        return [
            $row->id_regional, // Sesuaikan dengan struktur tabel
            $row->nama_regional,
        ];
    }

    /**
     * Menentukan style untuk file Excel.
     */
    public function styles(Worksheet $sheet)
    {
        // Membuat header bold & teks rata tengah
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Menambahkan border pada semua sel yang ada
        $lastRow = $sheet->getHighestRow(); // Ambil jumlah baris terakhir
        $sheet->getStyle('A1:B' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
    }
}
