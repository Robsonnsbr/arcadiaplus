<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioReservasExport implements FromView
{
    protected $data;
    protected $startDate;
    protected $endDate;
    protected $vagos;

    public function __construct($data, $startDate, $endDate, $vagos)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->vagos = $vagos;
    }

    public function view(): View
    {
        return view('exports.relatorio_reservas', [
            'data' => $this->data,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'vagos' => $this->vagos
        ]);
    }
}
