<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioInventarioCustoMedioExport implements FromView
{
    protected $data;
    protected $deposito;

    public function __construct($data, $deposito)
    {
        $this->data = $data;
        $this->deposito = $deposito;
    }

    public function view(): View
    {
        return view('exports.relatorio_inventario_custo_medio', [
            'data' => $this->data,
            'deposito' => $this->deposito
        ]);
    }
}
