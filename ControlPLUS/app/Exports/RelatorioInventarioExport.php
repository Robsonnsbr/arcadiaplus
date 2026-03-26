<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioInventarioExport implements FromView
{
    protected $data;
    protected $deposito;
    protected $empresa;
    protected $livro;

    public function __construct($data, $deposito, $empresa, $livro)
    {
        $this->data = $data;
        $this->deposito = $deposito;
        $this->empresa = $empresa;
        $this->livro = $livro;
    }

    public function view(): View
    {
        return view('exports.relatorio_inventario', [
            'data' => $this->data,
            'deposito' => $this->deposito,
            'empresa' => $this->empresa,
            'livro' => $this->livro
        ]);
    }
}
