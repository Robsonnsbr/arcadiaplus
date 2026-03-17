<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioVendasPorVendedorExport implements FromView
{
    protected $data;
    protected $funcionario;

    public function __construct($data, $funcionario)
    {
        $this->data = $data;
        $this->funcionario = $funcionario;
    }

    public function view(): View
    {
        return view('exports.relatorio_vendas_por_vendedor', [
            'data' => $this->data,
            'funcionario' => $this->funcionario
        ]);
    }
}
