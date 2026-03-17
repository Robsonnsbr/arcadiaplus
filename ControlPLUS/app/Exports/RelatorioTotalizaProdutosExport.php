<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RelatorioTotalizaProdutosExport implements FromView
{
    protected $data;
    protected $localId;
    protected $local;

    public function __construct($data, $localId, $local)
    {
        $this->data = $data;
        $this->localId = $localId;
        $this->local = $local;
    }

    public function view(): View
    {
        return view('exports.relatorio_totaliza_produtos', [
            'data' => $this->data,
            'local_id' => $this->localId,
            'local' => $this->local
        ]);
    }
}
