<?php

namespace App\Exports;

use App\Models\SendVicon;
use Maatwebsite\Excel\Concerns\FromView;

class AbsensiExport implements FromView
{
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @inheritDoc
     */
    public function view(): \Illuminate\Contracts\View\View
    {

        $sendvicon = SendVicon::with(['absensis', 'ruangan'])
            ->where('id', '=', $this->id)
            ->select(['id', 'acara', 'tanggal', 'waktu', 'waktu2', 'ruangan as nama_ruangan', 'ruangan_lain', 'id_ruangan'])
            ->first();
        // dd($sendvicon);
        return view('exports.excel.absensi', compact('sendvicon'));
    }
}
