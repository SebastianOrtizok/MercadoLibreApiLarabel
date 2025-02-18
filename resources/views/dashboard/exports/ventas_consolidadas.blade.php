<h2 style="text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 20px;">Reporte de Ventas Consolidadas</h2>
<br>
<table style="width: 100%; border-collapse: collapse;">
    <thead style="background-color: #f2f2f2; font-weight: bold;">
        <tr>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Producto</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Título</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Cantidad Vendida</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Tipo Publicación</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Fecha Venta</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Estado Orden</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Vendedor</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Última Venta</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Stock</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">SKU</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Estado</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Días de Stock</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">URL</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ventas as $venta)
        <tr>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ $venta['producto'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ $venta['titulo'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $venta['cantidad_vendida'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ $venta['tipo_publicacion'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $venta['fecha_venta'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $venta['order_status'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ $venta['seller_nickname'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $venta['fecha_ultima_venta'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $venta['stock'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $venta['sku'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $venta['estado'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $venta['dias_stock'] }}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                <a href="{{ $venta['url'] }}" target="_blank">Ver</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
