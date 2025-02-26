<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderDbService
{
    public function syncOrders($mlAccountId, $accessToken, $dateFrom = null, $dateTo = null)
    {
        try {
            $offset = 0;
            $limit = 50;
            $ordersProcessed = 0;

            // Solo el día actual (25 de febrero)
            $dateFrom = $dateFrom ?? Carbon::today()->startOfDay()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
            $dateTo = $dateTo ?? Carbon::today()->endOfDay()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

            Log::info("Sincronizando órdenes para cuenta: $mlAccountId, desde: $dateFrom, hasta: $dateTo");

            do {
                $params = [
                    'seller' => $mlAccountId,
                    'offset' => $offset,
                    'limit' => $limit,
                    'sort' => 'date_desc',
                    'order.status' => 'paid',
                    'order.date_created.from' => $dateFrom,
                    'order.date_created.to' => $dateTo
                ];

                $url = "https://api.mercadolibre.com/orders/search?" . http_build_query($params);
                Log::info("Consultando órdenes en la URL: $url");

                $response = Http::withToken($accessToken)->get($url);

                if (!$response->successful()) {
                    throw new \Exception("Error en la API: " . $response->body());
                }

                $data = $response->json();
                $orders = $data['results'] ?? [];

                Log::info("Respuesta API para offset $offset: Total: {$data['paging']['total']}, Órdenes: " . count($orders));

                if (empty($orders)) {
                    break;
                }

                foreach ($orders as $order) {
                    $orderDate = Carbon::parse($order['date_created']);
                    foreach ($order['order_items'] as $item) {
                        DB::table('ordenes')->updateOrInsert(
                            [
                                'ml_order_id' => $order['id'],
                                'ml_product_id' => $item['item']['id']
                            ],
                            [
                                'ml_account_id' => $mlAccountId,
                                'cantidad' => $item['quantity'],
                                'precio_unitario' => $item['unit_price'],
                                'estado_orden' => $order['status'],
                                'fecha_venta' => $orderDate->toDateTimeString(),
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );
                        $ordersProcessed++;
                    }
                }

                Log::info("Cuenta $mlAccountId: Procesadas $ordersProcessed órdenes (offset: $offset)");
                $offset += $limit;
                sleep(1);

            } while (count($orders) === $limit);

            Log::info("Fin de sincronización para cuenta: $mlAccountId. Total procesadas: $ordersProcessed");
            return ['orders_processed' => $ordersProcessed];

        } catch (\Exception $e) {
            Log::error("Error al sincronizar órdenes para $mlAccountId: " . $e->getMessage());
            throw $e;
        }
    }
}
