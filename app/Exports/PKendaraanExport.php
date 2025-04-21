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
        return ["Status", "Divisi", "Nama PIC", "Jenis Tujuan", "Tgl Berangkat", "Jam Berangkat", "Jam Kembali", "Tujuan", "Penjemputan", "Driver", "Mobil"];
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

        // Gabungkan No Polisi dan Tipe Kendaraan, hapus tanda "-" jika salah satu null
        $kendaraan = trim(optional($row->kendaraanDetail)->nopol . ' - ' . optional($row->kendaraanDetail)->tipe_kendaraan, ' -');

        // Jika driver null, gunakan rental_driver dengan label "(Rental) "
        $driver = optional($row->driverDetail)->nama_driver ?: ($row->rental_driver ? "(Rental) " . $row->rental_driver : null);

        // Jika kendaraan null, gunakan rental_kendaraan dengan label "(Rental) "
        $kendaraan = $kendaraan ?: ($row->rental_kendaraan ? "(Rental) " . $row->rental_kendaraan : null);

        return [
            $statusLabels[$row->status] ?? 'Unknown', // Ubah angka status menjadi teks
            $row->divisi,
            $row->nama_pic,
            $row->jenis_tujuan,
            $row->tgl_berangkat,
            $row->jam_berangkat,
            $row->jam_kembali,
            $row->tujuan,
            $row->pejemputan,
            $driver,     // Driver atau Rental Driver jika driver null
            $kendaraan,  // Kendaraan atau Rental Kendaraan jika kendaraan null
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
