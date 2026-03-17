<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioMovimentacaoExport implements FromView
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate, $endDate)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('exports.relatorio_movimentacao', [
            'data' => $this->data,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate
        ]);
    }
}
