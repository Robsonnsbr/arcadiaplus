<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioFornecedoresExport implements FromView
{
    protected $data;
    protected $tipo;

    public function __construct($data, $tipo)
    {
        $this->data = $data;
        $this->tipo = $tipo;
    }

    public function view(): View
    {
        return view('exports.relatorio_fornecedores', [
            'data' => $this->data,
            'tipo' => $this->tipo
        ]);
    }
}
