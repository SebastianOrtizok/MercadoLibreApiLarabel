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
                'Competidor' => $item->competidor_nombre ?? 'N/A',
                'Seller ID' => $item->competidor_seller_id ?? 'N/A',
                'Publicación' => $item->item_id,
                'Título' => $item->titulo ?? '--',
                'Precio Original' => $item->precio ?? '-',
                'Precio con Descuento' => $item->precio_descuento ?? '-',
                'Precio sin Impuestos' => $item->precio_sin_impuestos ?? '-',
                'Información de Cuotas' => $item->info_cuotas ?? '-',
                'URL' => $item->url ?? '',
                'Es Full' => $item->es_full ? 'Sí' : 'No',
                'Cantidad Disponible' => $item->cantidad_disponible ?? '0',
                'Cantidad Vendida' => $item->cantidad_vendida ?? '0',
                'Envío Gratis' => $item->envio_gratis ? 'Sí' : 'No',
                'Following' => $item->following ? '1' : '0',
                'Última Actualización' => $item->ultima_actualizacion ? date('d/m/Y H:i', strtotime($item->ultima_actualizacion)) : 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Competidor',
            'Seller ID',
            'Publicación',
            'Título',
            'Precio Original',
            'Precio con Descuento',
            'Precio sin Impuestos',
            'Información de Cuotas',
            'URL',
            'Es Full',
            'Cantidad Disponible',
            'Cantidad Vendida',
            'Envío Gratis',
            'Following',
            'Última Actualización',
        ];
    }
}
