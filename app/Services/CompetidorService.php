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
    $maxPages = 2; // Reducido a 2 páginas para pruebas y seguridad
    $maxItems = 100; // Limitado a 100 ítems para reducir carga
    $itemsPerPage = $officialStoreId ? 48 : 50;

    $baseUrl = "https://listado.mercadolibre.com.ar";

    while ($page <= $maxPages && count($items) < $maxItems) {
        $offset = ($page - 1) * $itemsPerPage;

        // Construir URL con seller_id y categoría de forma explícita
        $url = $categoria
            ? "{$baseUrl}/{$categoria}/_CustId_{$sellerId}_NoIndex_True"
            : "{$baseUrl}/_CustId_{$sellerId}_NoIndex_True";
        if ($page > 1) {
            $url .= "_Desde_{$offset}";
        }

        \Log::info("Intentando scrapeo de página {$page} con URL: {$url}", ['seller_id' => $sellerId, 'categoria' => $categoria]);

        try {
            $response = $this->client->get($url, ['timeout' => 15]);
            if ($response->getStatusCode() !== 200) {
                \Log::warning("Código de estado no esperado para página {$page}: {$response->getStatusCode()}", ['url' => $url]);
                break;
            }

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $itemNodes = $crawler->filter('li.ui-search-layout__item');
            if ($itemNodes->count() === 0) {
                \Log::info("No se encontraron más ítems en la página {$page}", ['url' => $url]);
                break;
            }

            $itemNodes->each(function (Crawler $node) use (&$items, $maxItems, $categoria, $sellerId) {
                if (count($items) >= $maxItems) {
                    return false;
                }

                $title = $node->filter('h3.poly-component__title-wrapper a.poly-component__title')->count()
                    ? $node->filter('h3.poly-component__title-wrapper a.poly-component__title')->text()
                    : 'Sin título';
                $originalPrice = $node->filter('s.andes-money-amount--previous .andes-money-amount__fraction')->count()
                    ? $this->normalizePrice($node->filter('s.andes-money-amount--previous .andes-money-amount__fraction')->text())
                    : null;
                $currentPrice = $node->filter('.poly-price__current .andes-money-amount__fraction')->count()
                    ? $this->normalizePrice($node->filter('.poly-price__current .andes-money-amount__fraction')->text())
                    : ($originalPrice ?? 0.0);
                $postLink = $node->filter('h3.poly-component__title-wrapper a.poly-component__title')->count()
                    ? $node->filter('h3.poly-component__title-wrapper a.poly-component__title')->attr('href')
                    : 'Sin enlace';
                $isFull = $node->filter('span.poly-component__shipped-from svg[aria-label="FULL"]')->count() > 0;
                $hasFreeShipping = $node->filter('.poly-component__shipping:contains("Envío gratis")')->count() > 0;
                $installments = $node->filter('.poly-price__installments')->count()
                    ? trim($node->filter('.poly-price__installments')->text())
                    : null;

                $itemId = $this->extractItemIdFromLink($postLink);
            $categoriaItem = null;
                if ($node->filter('.ui-search-breadcrumb a')->count()) {
                    $categoriaItem = trim($node->filter('.ui-search-breadcrumb a')->last()->text());
                } elseif ($node->filter('.ui-search-item__group__element')->count()) {
                    $categoriaItem = trim($node->filter('.ui-search-item__group__element')->text());
                }

                $items[] = [
                    'item_id' => $itemId,
                    'titulo' => $title,
                    'precio' => $originalPrice ?? $currentPrice,
                    'precio_descuento' => $currentPrice,
                    'info_cuotas' => $installments,
                    'url' => $postLink,
                    'es_full' => $isFull,
                    'envio_gratis' => $hasFreeShipping,
                    'categorias' => $categoriaItem,
                ];
            });

            $page++;
            sleep(rand(5, 10)); // Mantener delay para evitar detección
        } catch (RequestException $e) {
            \Log::error("Error al scrapeo para el vendedor {$sellerId} en página {$page}", [
                'error' => $e->getMessage(),
                'url' => $url,
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response'
            ]);
            break;
        }
    }

    \Log::info("Scraping finalizado. Total ítems scrapeados: " . count($items), ['seller_id' => $sellerId]);
    return $items;
}

    protected function extractItemIdFromLink($link)
    {
        \Log::debug("Extrayendo item_id de link: {$link}");
        if (preg_match('/(?:^|\/)MLA-?(\d+)/i', $link, $matches)) {
            $extractedId = 'MLA' . $matches[1];
            \Log::debug("Item_id extraído: {$extractedId}");
            return $extractedId;
        }
        \Log::warning("No se pudo extraer item_id, link procesado: {$link}");
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
