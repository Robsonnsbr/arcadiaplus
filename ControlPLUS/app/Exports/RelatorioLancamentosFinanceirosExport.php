<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioLancamentosFinanceirosExport implements FromView
{
    protected $data;
    protected $total_receber;
    protected $total_pagar;
    protected $saldo;

    public function __construct($data, $total_receber, $total_pagar, $saldo)
    {
        $this->data = $data;
        $this->total_receber = $total_receber;
        $this->total_pagar = $total_pagar;
        $this->saldo = $saldo;
    }

    public function view(): View
    {
        return view('exports.relatorio_lancamentos_financeiros', [
            'data'           => $this->data,
            'total_receber'  => $this->total_receber,
            'total_pagar'    => $this->total_pagar,
            'saldo'          => $this->saldo,
        ]);
    }
}
