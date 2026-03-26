<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioEstoqueExport implements FromView
{	
	protected $data;
    protected $estoqueCritico;
    protected $deposito;
	public function __construct($data, $estoqueCritico = null, $deposito = null)
    {
        $this->data = $data;
        $this->estoqueCritico = $estoqueCritico;
        $this->deposito = $deposito;
    }
    public function view(): View
    {
        return view('exports.relatorio_estoque', [
            'data' => $this->data,
            'estoque_critico' => $this->estoqueCritico,
            'deposito' => $this->deposito
        ]);
    }
}
