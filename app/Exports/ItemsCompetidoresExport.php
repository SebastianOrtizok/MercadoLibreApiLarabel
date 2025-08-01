<?php

namespace App\Exports;

use App\Models\ItemCompetidor;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ItemsCompetidoresExport implements FromCollection, WithHeadings, WithEvents, WithStyles, WithCustomStartCell
{
    public function collection()
    {
        // Obtener el ID del usuario logueado
        $userId = Auth::id();

        // Obtener los ítems de los competidores asociados al usuario logueado
        $items = ItemCompetidor::with('competidor')
            ->whereHas('competidor', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();

        return $items->map(function ($item) {
            return [
                'Competidor' => $item->competidor->nombre ?? '',
                'Seller ID' => $item->competidor->seller_id ?? '',
                'Publicaciones' => $item->item_id ?? '',
                'Título' => $item->titulo ?? '--',
                'Categorías' => $item->categorias ?? 'N/A',
                'Precio Original' => $item->precio ? number_format($item->precio, 2, ',', '.') : '-',
                'Precio con Descuento' => $item->precio_descuento ? number_format($item->precio_descuento, 2, ',', '.') : '-',
                'Precio sin Impuestos' => $item->precio_sin_impuestos ? number_format($item->precio_sin_impuestos, 2, ',', '.') : '-',
                'Información de Cuotas' => $item->info_cuotas ?? '-',
                'URL' => $item->url ?? '',
                'Es Full' => $item->es_full ? 'Sí' : 'No',
                'Cantidad Disponible' => $item->cantidad_disponible !== null ? $item->cantidad_disponible : 'N/A',
                'Cantidad Vendida' => $item->cantidad_vendida !== null ? $item->cantidad_vendida : 'N/A',
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
            'Publicaciones',
            'Título',
            'Categorías',
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

    public function startCell(): string
    {
        return 'A2'; // Los datos comienzan en A2, dejando A1 para el título
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para la fila de encabezados (A2:P2)
        return [
            2 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFCCCCCC'], // Gris claro
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Agregar título en A1
                $sheet->setCellValue('A1', 'Publicaciones de la Competencia');
                $sheet->mergeCells('A1:P1'); // Combinar celdas para el título (ajustado para 16 columnas)
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => 'FF000000'], // Negro
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Aplicar colores alternados a las filas de datos (desde A3)
                $highestRow = $sheet->getHighestRow();
                for ($row = 3; $row <= $highestRow; $row++) {
                    if ($row % 2 == 1) { // Filas impares
                        $sheet->getStyle("A{$row}:P{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF0F0F0'], // Gris muy suave
                            ],
                        ]);
                    }
                }

                // Crear una tabla de Excel con filtros
                $sheet->setAutoFilter('A2:P2'); // Filtros en la fila de encabezados (ajustado para 16 columnas)
                $tableRange = "A2:P{$highestRow}";
                $sheet->getParent()->addNamedRange(
                    new \PhpOffice\PhpSpreadsheet\NamedRange(
                        'CompetidoresTable',
                        $sheet,
                        $tableRange
                    )
                );
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Ajustar el ancho de las columnas automáticamente
                foreach (range('A', 'P') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
