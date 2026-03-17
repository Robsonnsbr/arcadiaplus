<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioRegistroInventarioExport implements FromView
{
    protected $data;
    protected $livro;
    protected $empresa;
    protected $date;

    public function __construct($data, $livro, $empresa, $date)
    {
        $this->data = $data;
        $this->livro = $livro;
        $this->empresa = $empresa;
        $this->date = $date;
    }

    public function view(): View
    {
        return view('exports.relatorio_registro_inventario', [
            'data' => $this->data,
            'livro' => $this->livro,
            'empresa' => $this->empresa,
            'date' => $this->date
        ]);
    }
}
