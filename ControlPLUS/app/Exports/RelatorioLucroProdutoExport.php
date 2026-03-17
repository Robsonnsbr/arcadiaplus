<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioLucroProdutoExport implements FromView
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
        return view('exports.relatorio_lucro_produto', [
            'data' => $this->data,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate
        ]);
    }
}
