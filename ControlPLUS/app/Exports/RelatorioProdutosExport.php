<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioProdutosExport implements FromView
{
    protected $data;
    protected $tipo;
    protected $marca;
    protected $categoria;

    public function __construct($data, $tipo, $marca, $categoria)
    {
        $this->data = $data;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->categoria = $categoria;
    }

    public function view(): View
    {
        return view('exports.relatorio_produtos', [
            'data' => $this->data,
            'tipo' => $this->tipo,
            'marca' => $this->marca,
            'categoria' => $this->categoria
        ]);
    }
}
