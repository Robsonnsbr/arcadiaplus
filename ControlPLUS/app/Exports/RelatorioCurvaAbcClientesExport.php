<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioCurvaAbcClientesExport implements FromView
{
    protected $data;
    protected $soma;

    public function __construct($data, $soma)
    {
        $this->data = $data;
        $this->soma = $soma;
    }

    public function view(): View
    {
        return view('exports.relatorio_curva_abc_clientes', [
            'data' => $this->data,
            'soma' => $this->soma
        ]);
    }
}
