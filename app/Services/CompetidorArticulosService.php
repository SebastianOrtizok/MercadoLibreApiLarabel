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
            sleep(1); // Retraso para evitar bloqueos
            $response = $this->client->get($url, ['timeout' => 10]);
            \Log::info("Respuesta recibida", ['status' => $response->getStatusCode(), 'url' => $url]);

            if ($response->getStatusCode() !== 200) {
                \Log::warning("Código de estado no esperado: {$response->getStatusCode()}", ['url' => $url]);
                return [];
            }

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // Log para depurar selectores
            \Log::info("Resultados de los selectores", [
                'title_count' => $crawler->filter('h1.ui-pdp-title')->count(),
                'breadcrumb_count' => $crawler->filter('.andes-breadcrumb__item')->count(),
                'available_quantity_count' => $crawler->filter('.ui-pdp-buybox__quantity__available')->count(),
                'available_quantity_alt_count' => $crawler->filter('p.ui-pdp-color--BLACK.ui-pdp-size--MEDIUM.ui-pdp-family--SEMIBOLD')->count(),
                'sold_quantity_count' => $crawler->filter('.ui-pdp-seller__header__subtitle')->count(),
                'sold_quantity_alt_count' => $crawler->filter('.ui-pdp-seller__subtitle')->count(),
            ]);

            $title = $crawler->filter('h1.ui-pdp-title')->count() ? trim($crawler->filter('h1.ui-pdp-title')->text()) : 'Sin título';
            $originalPrice = $crawler->filter('.andes-money-amount__original-value .andes-money-amount__fraction')->count()
                ? $this->normalizePrice($crawler->filter('.andes-money-amount__original-value .andes-money-amount__fraction')->text())
                : null;
            $currentPrice = $crawler->filter('.ui-pdp-price__second-line .andes-money-amount__fraction')->count()
                ? $this->normalizePrice($crawler->filter('.ui-pdp-price__second-line .andes-money-amount__fraction')->text())
                : ($originalPrice ?? 0.0);
            $installments = $crawler->filter('#pricing_price_subtitle')->count() ? trim($crawler->filter('#pricing_price_subtitle')->text()) : null;
            $isFull = $crawler->filter('span.shg__shipping-method:contains("FULL")')->count() > 0;
            $hasFreeShipping = $crawler->filter('.ui-pdp-shipping__label--free')->count() > 0;
            $priceSinImpuestos = $crawler->filter('#no_taxes_price_subtitle .andes-money-amount__fraction')->count()
                ? $this->normalizePrice($crawler->filter('#no_taxes_price_subtitle .andes-money-amount__fraction')->text())
                : null;
            $categorias = $this->scrapeCategorias($crawler);
            $cantidadDisponible = $this->extractAvailableQuantity($crawler);
            $cantidadVendida = $crawler->filter('.ui-pdp-seller__header__subtitle')->count()
                ? $this->extractSoldQuantity($crawler->filter('.ui-pdp-seller__header__subtitle')->text())
                : ($crawler->filter('.ui-pdp-seller__subtitle')->count()
                    ? $this->extractSoldQuantity($crawler->filter('.ui-pdp-seller__subtitle')->text())
                    : null);

            $data = [
                'titulo' => $title,
                'precio' => $originalPrice ?? $currentPrice,
                'precio_descuento' => $currentPrice,
                'precio_sin_impuestos' => $priceSinImpuestos,
                'info_cuotas' => $installments,
                'url' => $url,
                'categorias' => $categorias,
                'es_full' => $isFull,
                'envio_gratis' => $hasFreeShipping,
                'cantidad_disponible' => $cantidadDisponible,
                'cantidad_vendida' => $cantidadVendida,
            ];

            \Log::info("Datos scrapeados", ['data' => $data, 'item_id' => $itemId]);
            return $data;
        } catch (RequestException $e) {
            \Log::error("Error al scrapeo de detalle de artículo", [
                'error' => $e->getMessage(),
                'url' => $url,
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response',
                'headers' => $e->hasResponse() ? $e->getResponse()->getHeaders() : []
            ]);
            return [];
        }
    }

    protected function scrapeCategorias(Crawler $crawler, $selector = '.andes-breadcrumb__item')
    {
        $categorias = [];
        try {
            $crawler->filter($selector)->each(function (Crawler $node) use (&$categorias) {
                $text = trim($node->filter('a')->count() ? $node->filter('a')->text() : $node->text());
                if ($text) {
                    $categorias[] = $text;
                }
            });
            return implode(' > ', $categorias);
        } catch (\Exception $e) {
            \Log::warning("Error al extraer categorías", ['error' => $e->getMessage(), 'selector' => $selector]);
            return '';
        }
    }

    protected function extractAvailableQuantity(Crawler $crawler)
    {
        // Selector principal
        if ($crawler->filter('.ui-pdp-buybox__quantity__available')->count()) {
            $text = $crawler->filter('.ui-pdp-buybox__quantity__available')->text();
            preg_match('/\+(\d+)\s*disponibles?/', $text, $matches);
            return isset($matches[1]) ? (int) $matches[1] : null;
        }
        // Selector alternativo para "¡Última disponible!"
        if ($crawler->filter('p.ui-pdp-color--BLACK.ui-pdp-size--MEDIUM.ui-pdp-family--SEMIBOLD')->count()) {
            $text = $crawler->filter('p.ui-pdp-color--BLACK.ui-pdp-size--MEDIUM.ui-pdp-family--SEMIBOLD')->text();
            if (stripos($text, 'Última disponible') !== false) {
                return 1; // Asumir 1 unidad disponible
            }
        }
        return null;
    }

    protected function extractSoldQuantity($text)
    {
        // Manejar formatos como "+50mil ventas" o "+50000 ventas"
        $text = str_replace('mil', '000', $text); // Convertir "50mil" a "50000"
        preg_match('/\+(\d+)\s*ventas?/', $text, $matches);
        return isset($matches[1]) ? (int) $matches[1] : null;
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
