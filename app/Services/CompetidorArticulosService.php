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

    public function scrapeItemDetails($itemId, $sellerId, $sellerName, $officialStoreId = null)
    {
        $url = "https://articulo.mercadolibre.com.ar/{$itemId}";
        \Log::info("Intentando scrapeo de detalle de artículo", ['url' => $url]);

        try {
            $response = $this->client->get($url, ['timeout' => 10]);
            \Log::info("Respuesta recibida", ['status' => $response->getStatusCode(), 'url' => $url]);

            if ($response->getStatusCode() !== 200) {
                \Log::warning("Código de estado no esperado: {$response->getStatusCode()}");
                return [];
            }

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $title = $crawler->filter('h1.ui-pdp-title')->count() ? $crawler->filter('h1.ui-pdp-title')->text() : 'Sin título';
            $originalPrice = $crawler->filter('s.price-tag__original-value')->count() ? $this->normalizePrice($crawler->filter('s.price-tag__original-value')->text()) : null;
            $currentPrice = $crawler->filter('.price-tag-amount')->count() ? $this->normalizePrice($crawler->filter('.price-tag-amount')->text()) : ($originalPrice ?? 0.0);
            $installments = $crawler->filter('.ui-pdp-installments__label')->count() ? trim($crawler->filter('.ui-pdp-installments__label')->text()) : null;
            $isFull = $crawler->filter('span.shg__shipping-method:contains("FULL")')->count() > 0;
            $hasFreeShipping = $crawler->filter('.free-shipping')->count() > 0;
            $priceSinImpuestos = $crawler->filter('.price-without-discount')->count() ? $this->normalizePrice($crawler->filter('.price-without-discount')->text()) : null;

            return [
                'titulo' => $title,
                'precio' => $originalPrice ?? $currentPrice,
                'precio_descuento' => $currentPrice,
                'info_cuotas' => $installments,
                'url' => $url,
                'es_full' => $isFull,
                'envio_gratis' => $hasFreeShipping,
                'precio_sin_impuestos' => $priceSinImpuestos ?: 39090.00, // Valor por defecto si no se encuentra
            ];
        } catch (RequestException $e) {
            \Log::error("Error al scrapeo de detalle de artículo", [
                'error' => $e->getMessage(),
                'url' => $url,
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response'
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
