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
            ? ($page === 1 ? "{$baseUrl}/_Tienda_{$sellerName}_NoIndex_True" : "{$baseUrl}/_Desde_{$offset}_Tienda_{$sellerName}_NoIndex_True")
            : ($page === 1 ? "{$baseUrl}/_CustId_{$sellerId}_NoIndex_True" : "{$baseUrl}/_CustId_{$sellerId}_Desde_{$offset}_NoIndex_True");
        if ($categoria) {
            $categoria = str_replace(' ', '-', strtolower(trim($categoria)));
            $url = "{$baseUrl}/{$categoria}/" . ltrim($url, '/');
            \Log::info("URL ajustada con categoría para página {$page}", ['url' => $url, 'categoria' => $categoria]);
        } else {
            \Log::info("URL sin categoría para página {$page}", ['url' => $url]);
        }

        \Log::info("Intentando scrapeo de página {$page} con URL: {$url}", ['seller_id' => $sellerId]);

        try {
            $response = $this->client->get($url, ['timeout' => 15]);
            if ($response->getStatusCode() !== 200) {
                \Log::warning("Código de estado no esperado: {$response->getStatusCode()}", ['url' => $url]);
                break;
            }

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $itemNodes = $crawler->filter('li.ui-search-layout__item');
            if ($itemNodes->count() === 0) {
                \Log::info("No se encontraron ítems en la página {$page}. Deteniendo scraping.", ['url' => $url]);
                break;
            }

            $hasSellerItems = false;

            // Verificar los primeros $initialCheckLimit ítems
            $itemNodes->slice(0, $initialCheckLimit)->each(function (Crawler $node) use ($sellerId, &$hasSellerItems) {
                $itemSellerId = $node->filter('.poly-component__seller')->count()
                    ? trim($node->filter('.poly-component__seller')->attr('data-seller-id'))
                    : null;

                if ($itemSellerId === $sellerId) {
                    $hasSellerItems = true;
                    return false;
                }
            });

            if (!$hasSellerItems) {
                \Log::info("No se encontraron ítems del vendedor {$sellerId} en los primeros {$initialCheckLimit} ítems. Deteniendo scraping.", ['url' => $url]);
                break;
            }

            // Procesar todos los ítems si pasamos la verificación
            $itemNodes->each(function (Crawler $node) use (&$items, $maxItems, $categoria, $sellerId) {
                if (count($items) >= $maxItems) return false;

                $itemSellerId = $node->filter('.poly-component__seller')->count()
                    ? trim($node->filter('.poly-component__seller')->attr('data-seller-id'))
                    : null;

                if ($itemSellerId !== $sellerId) {
                    \Log::debug("Ítem descartado: seller_id {$itemSellerId} no coincide con {$sellerId}");
                    return;
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
                if ($node->filter('.ui-search-breadcrumb a')->count() > 0) {
                    $categoriaItem = trim($node->filter('.ui-search-breadcrumb a')->last()->text());
                } elseif ($node->filter('.ui-search-item__group__element')->count() > 0) {
                    $categoriaItem = trim($node->filter('.ui-search-item__group__element')->text());
                } else {
                    $categoriaItem = $categoria ?: 'Sin categoría';
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
                \Log::info("Ítem scrapeado", ['item' => $items[count($items) - 1]]);
            });

            $page++;
            sleep(rand(5, 10));
        } catch (RequestException $e) {
            \Log::error("Error al scrapeo para el vendedor {$sellerId}", ['error' => $e->getMessage(), 'url' => $url]);
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
