<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ConsolidadoVentasExport implements FromView
{
    protected $ventas;

    public function __construct($ventas)
    {
        $this->ventas = $ventas;
    }

    public function view(): View
    {
        return view('dashboard.exports.ventas_consolidadas', [
            'ventas' => $this->ventas
        ]);
    }
}
