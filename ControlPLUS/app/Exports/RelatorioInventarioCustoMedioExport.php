<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioInventarioCustoMedioExport implements FromView
{
    protected $data;
    protected $local;

    public function __construct($data, $local)
    {
        $this->data = $data;
        $this->local = $local;
    }

    public function view(): View
    {
        return view('exports.relatorio_inventario_custo_medio', [
            'data' => $this->data,
            'local' => $this->local
        ]);
    }
}
