<?php

namespace App\Exports;

use App\Models\PKendaraan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PKendaraanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        return ["Status", "Divisi", "Nama PIC", "Jenis Tujuan", "Tgl Berangkat", "Jam Berangkat", "Tujuan", "Penjemputan", "Driver", "Mobil", "No Polisi"];
    }

    /**
     * Menyesuaikan isi dari setiap baris di file Excel.
     */
    public function map($row): array
    {
        // Mapping status
        $statusLabels = [
            0 => 'Cancel',
            1 => 'Pengajuan',
            2 => 'Approve',
            3 => 'Reject',
        ];
        return [
            $statusLabels[$row->status] ?? 'Unknown', // Ubah angka status menjadi teks
            $row->divisi,
            $row->nama_pic,
            $row->jenis_tujuan,
            $row->tgl_berangkat,
            $row->jam_berangkat,
            $row->tujuan,
            $row->pejemputan,
            optional($row->driverDetail)->nama_driver,
            optional($row->kendaraanDetail)->tipe_kendaraan,
            optional($row->kendaraanDetail)->nopol,

        ];
    }

    /**
     * Menentukan style untuk file Excel.
     */
    public function styles(Worksheet $sheet)
    {
        // Membuat header bold & teks rata tengah
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Menambahkan border pada semua sel yang ada
        $lastRow = $sheet->getHighestRow(); // Ambil jumlah baris terakhir
        $sheet->getStyle('A1:K' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
    }
}
