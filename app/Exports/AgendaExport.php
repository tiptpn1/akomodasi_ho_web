<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromView;

class AgendaExport implements FromView
{
    public $sendvicon;
    public $startPeriode;
    public $endPeriode;
    public function __construct($sendvicon, $startPeriode, $endPeriode)
    {
        $this->sendvicon = $sendvicon;
        $this->startPeriode = $startPeriode;
        $this->endPeriode = $endPeriode;
    }
    /**
     * @inheritDoc
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        $downloadTime = Carbon::now()->format('d F Y H:i:s');

        return view('exports.excel.agenda', [
            'sendvicon' => $this->sendvicon,
            'startPeriode' => $this->startPeriode,
            'endPeriode' => $this->endPeriode,
            'downloadTime' => $downloadTime
        ]);
    }
}
