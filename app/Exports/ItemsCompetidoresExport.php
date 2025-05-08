<?php

namespace App\Exports;

use App\Models\ItemCompetidor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsCompetidoresExport implements FromCollection, WithHeadings, WithMapping
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            'Seguir',
            'Competidor',
            'Publicación',
            'Título',
            'Precio Original',
            'Precio con Descuento',
            'URL',
            'Es Full',
            'Envío Gratis',
            'Última Actualización',
        ];
    }

    public function map($item): array
    {
        return [
            $item->following ? 'Sí' : 'No',
            $item->competidor->nombre ?? 'N/A',
            $item->item_id,
            $item->titulo,
            number_format($item->precio, 2),
            $item->precio_descuento ? number_format($item->precio_descuento, 2) : '-',
            $item->url ?? '-',
            $item->es_full ? 'Sí' : 'No',
            $item->envio_gratis ? 'Sí' : 'No',
            $item->ultima_actualizacion ? $item->ultima_actualizacion->format('d/m/Y H:i') : 'N/A',
        ];
    }
}
