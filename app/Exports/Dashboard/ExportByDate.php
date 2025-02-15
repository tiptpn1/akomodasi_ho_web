<?php

namespace App\Exports\Dashboard;

use Maatwebsite\Excel\Concerns\FromView;

class ExportByDate implements FromView
{
    public $date_start;
    public $date_end;
    public $download_date;
    public $sendvicon;

    public function __construct($date_start, $date_end, $download_date, $sendvicon)
    {
        $this->date_start = $date_start;
        $this->date_end = $date_end;
        $this->download_date = $download_date;
        $this->sendvicon = $sendvicon;
    }
    /**
     * @inheritDoc
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        return view('exports.excel.export_by_date', [
            'start_date' => $this->date_start,
            'end_date' => $this->date_end,
            'download_time' => $this->download_date,
            'sendvicon' => $this->sendvicon
        ]);
    }
}
