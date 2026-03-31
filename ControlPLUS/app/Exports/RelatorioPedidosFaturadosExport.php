<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioPedidosFaturadosExport implements FromView
{
    protected $data;
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($data, $startDate, $endDate, $status)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function view(): View
    {
        return view('exports.relatorio_pedidos_faturados', [
            'data' => $this->data,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'status' => $this->status
        ]);
    }
}
