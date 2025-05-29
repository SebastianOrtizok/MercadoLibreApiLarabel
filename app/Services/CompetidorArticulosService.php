<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;

class CompetidorArticulosService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
            ],
            'cookies' => true
        ]);
    }

    public function scrapeItemDetails($itemId, $sellerId, $sellerName, $url, $officialStoreId = null)
    {
        \Log::info("Intentando scrapeo de detalle de artículo", ['url' => $url, 'item_id' => $itemId]);

        try {
            $response = $this->client->get($url, ['timeout' => 10]);
            \Log::info("Respuesta recibida", ['status' => $response->getStatusCode(), 'url' => $url]);

            if ($response->getStatusCode() !== 200) {
                \Log::warning("Código de estado no esperado: {$response->getStatusCode()}", ['url' => $url]);
                return [];
            }

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // Agregar logs para depurar los selectores
            \Log::info("Resultados de los selectores", [
                'title_count' => $crawler->filter('h1.ui-pdp-title')->count(),
                'original_price_count' => $crawler->filter('.andes-money-amount__original-value .andes-money-amount__fraction')->count(),
                'current_price_count' => $crawler->filter('.ui-pdp-price__second-line .andes-money-amount__fraction')->count(),
                'installments_count' => $crawler->filter('#pricing_price_subtitle')->count(),
                'price_sin_impuestos_count' => $crawler->filter('#no_taxes_price_subtitle .andes-money-amount__fraction')->count(),
                'full_count' => $crawler->filter('span.shg__shipping-method:contains("FULL")')->count(),
                'free_shipping_count' => $crawler->filter('.ui-pdp-shipping__label--free')->count(),
            ]);

            $title = $crawler->filter('h1.ui-pdp-title')->count() ? trim($crawler->filter('h1.ui-pdp-title')->text()) : 'Sin título';
            $originalPrice = $crawler->filter('.andes-money-amount__original-value .andes-money-amount__fraction')->count() ? $this->normalizePrice($crawler->filter('.andes-money-amount__original-value .andes-money-amount__fraction')->text()) : null;
            $currentPrice = $crawler->filter('.ui-pdp-price__second-line .andes-money-amount__fraction')->count() ? $this->normalizePrice($crawler->filter('.ui-pdp-price__second-line .andes-money-amount__fraction')->text()) : ($originalPrice ?? 0.0);
            $installments = $crawler->filter('#pricing_price_subtitle')->count() ? trim($crawler->filter('#pricing_price_subtitle')->text()) : null;
            $isFull = $crawler->filter('span.shg__shipping-method:contains("FULL")')->count() > 0;
            $hasFreeShipping = $crawler->filter('.ui-pdp-shipping__label--free')->count() > 0;
            $priceSinImpuestos = $crawler->filter('#no_taxes_price_subtitle .andes-money-amount__fraction')->count() ? $this->normalizePrice($crawler->filter('#no_taxes_price_subtitle .andes-money-amount__fraction')->text()) : null;

            $data = [
                'titulo' => $title,
                'precio' => $originalPrice ?? $currentPrice,
                'precio_descuento' => $currentPrice,
                'info_cuotas' => $installments,
                'url' => $url,
                'es_full' => $isFull,
                'envio_gratis' => $hasFreeShipping,
                'precio_sin_impuestos' => $priceSinImpuestos, // Sin valor por defecto
            ];

            \Log::info("Datos scrapeados", ['data' => $data, 'item_id' => $itemId]);

            return $data;
        } catch (RequestException $e) {
            \Log::error("Error al scrapeo de detalle de artículo", [
                'error' => $e->getMessage(),
                'url' => $url,
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? substr($e->getResponse()->getBody()->getContents(), 0, 1000) : 'No response'
            ]);
            return [];
        }
    }

    protected function normalizePrice($priceText)
    {
        $priceText = trim($priceText);
        $priceText = str_replace('.', '', $priceText);
        $priceText = str_replace(',', '.', $priceText);
        $price = (float) $priceText;
        return $price;
    }
}
