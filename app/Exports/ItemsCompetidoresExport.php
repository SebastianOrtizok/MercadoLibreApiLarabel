<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsCompetidoresExport implements FromCollection, WithHeadings
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items->map(function ($item) {
            return [
                'Competidor' => $item->competidor->nombre ?? 'N/A',
                'Publicación' => $item->item_id,
                'Título' => $item->titulo,
                'Precio Original' => $item->precio,
                'Precio con Descuento' => $item->precio_descuento ?? '-',
                'Información de Cuotas' => $item->info_cuotas ?? '-',
                'URL' => $item->url,
                'Es Full' => $item->es_full ? 'Sí' : 'No',
                'Envío Gratis' => $item->envio_gratis ? 'Sí' : 'No',
                'Última Actualización' => $item->ultima_actualizacion ? $item->ultima_actualizacion->format('d/m/Y H:i') : 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Competidor',
            'Publicación',
            'Título',
            'Precio Original',
            'Precio con Descuento',
            'Información de Cuotas',
            'URL',
            'Es Full',
            'Envío Gratis',
            'Última Actualización',
        ];
    }
}
