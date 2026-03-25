<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioEstoqueExport implements FromView
{	
	protected $data;
    protected $estoqueCritico;
	public function __construct($data, $estoqueCritico = null)
    {
        $this->data = $data;
        $this->estoqueCritico = $estoqueCritico;
    }
    public function view(): View
    {
        return view('exports.relatorio_estoque', [
            'data' => $this->data,
            'estoque_critico' => $this->estoqueCritico
        ]);
    }
}
