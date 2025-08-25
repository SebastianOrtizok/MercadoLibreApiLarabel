<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;

class CompetidorService
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

public function scrapeItemsBySeller($sellerId, $sellerName, $officialStoreId = null, $categoria = null)
{
    $items = [];
    $page = 1;
    $maxPages = 5;
    $maxItems = 500;
    $itemsPerPage = $officialStoreId ? 48 : 50;

    $baseUrl = "https://listado.mercadolibre.com.ar";

    while ($page <= $maxPages && count($items) < $maxItems) {
        $offset = ($page - 1) * $itemsPerPage;
        $url = $officialStoreId
            ? ($page === 1 ? "{$baseUrl}/_Tienda_{$sellerName}_NoIndex_True?official_store_id={$officialStoreId}"
                : "{$baseUrl}/_Desde_{$offset}_Tienda_{$sellerName}_NoIndex_True?official_store_id={$officialStoreId}")
            : ($page === 1 ? "{$baseUrl}/_CustId_{$sellerId}_NoIndex_True"
                : "{$baseUrl}/_CustId_{$sellerId}_Desde_{$offset}_NoIndex_True");

        \Log::info("Intentando scrapeo de página {$page} con URL: {$url}");

        try {
            $response = $this->client->get($url, [
                'timeout' => 15,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'es-AR,es;q=0.9',
                    'Referer' => 'https://www.mercadolibre.com.ar/',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1',
                ],
            ]);
            \Log::info("Respuesta recibida", ['status' => $response->getStatusCode(), 'url' => $url]);
            if ($response->getStatusCode() !== 200) {
                \Log::warning("Código de estado no esperado: {$response->getStatusCode()}");
                break;
            }

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $itemNodes = $crawler->filter('li.ui-search-layout__item');
            \Log::info("Ítems encontrados en página {$page}: " . $itemNodes->count());

            if ($itemNodes->count() === 0) {
                \Log::info("No se encontraron más ítems en la página {$page}, contenido HTML: " . substr($html, 0, 1000));
                break;
            }

            $itemNodes->each(function (Crawler $node) use (&$items, $maxItems, $categoria) {
                if (count($items) >= $maxItems) {
                    return false;
                }

                $title = $node->filter('h2.ui-search-item__title')->count()
                    ? $node->filter('h2.ui-search-item__title')->text()
                    : 'Sin título';

                $originalPriceNode = $node->filter('s.ui-search-price__original-value .andes-money-amount__fraction');
                $originalPrice = $originalPriceNode->count() ? $this->normalizePrice($originalPriceNode->text()) : null;

                $currentPriceNode = $node->filter('div.ui-search-price__second-line .andes-money-amount__fraction');
                $currentPrice = $currentPriceNode->count()
                    ? $this->normalizePrice($currentPriceNode->text())
                    : 0.0;

                $postLink = $node->filter('a.ui-search-link')->count()
                    ? $node->filter('a.ui-search-link')->attr('href')
                    : 'Sin enlace';

                $isFull = $node->filter('img.ui-search-icon--full')->count() > 0 || $node->filter('p.ui-search-item__fulfillment')->count() > 0;

                $hasFreeShipping = $node->filter('p.ui-search-item__shipping.ui-search-item__shipping--free')->count() > 0;

                $installments = $node->filter('div.ui-search-installments span.andes-money-amount__fraction')->count()
                    ? trim($node->filter('div.ui-search-installments span.andes-money-amount__fraction')->text())
                    : null;

                $itemId = $this->extractItemIdFromLink($postLink);

                $itemData = [
                    'item_id' => $itemId,
                    'titulo' => $title,
                    'precio' => $originalPrice ?? $currentPrice,
                    'precio_descuento' => $currentPrice,
                    'info_cuotas' => $installments,
                    'url' => $postLink,
                    'es_full' => $isFull,
                    'envio_gratis' => $hasFreeShipping,
                    'categorias' => $categoria ?: 'Sin categoría',
                ];

                \Log::info("Ítem scrapeado", $itemData);

                $items[] = $itemData;
            });

            $page++;
            sleep(rand(5, 10));
        } catch (RequestException $e) {
            \Log::error("Error al scrapeo para el vendedor {$sellerId}", [
                'error' => $e->getMessage(),
                'url' => $url,
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response'
            ]);
            break;
        }
    }

    \Log::info("Scraping finalizado. Total ítems scrapeados: " . count($items));
    return $items;
}

protected function extractItemIdFromLink($link)
{
    if (preg_match('/MLA-?(\d+)/', $link, $matches)) {
        return 'MLA' . $matches[1];
    }
    return 'UNKNOWN';
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
