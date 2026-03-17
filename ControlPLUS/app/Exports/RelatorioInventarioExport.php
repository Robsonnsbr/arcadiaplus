<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioInventarioExport implements FromView
{
    protected $data;
    protected $local;
    protected $empresa;
    protected $livro;

    public function __construct($data, $local, $empresa, $livro)
    {
        $this->data = $data;
        $this->local = $local;
        $this->empresa = $empresa;
        $this->livro = $livro;
    }

    public function view(): View
    {
        return view('exports.relatorio_inventario', [
            'data' => $this->data,
            'local' => $this->local,
            'empresa' => $this->empresa,
            'livro' => $this->livro
        ]);
    }
}
