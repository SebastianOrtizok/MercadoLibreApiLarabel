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

        if ($categoria) {
            $categoria = str_replace(' ', '-', strtolower(trim($categoria)));
            $url = "{$baseUrl}/{$categoria}/_CustId_{$sellerId}_NoIndex_True" . ($page > 1 ? "_Desde_{$offset}" : '');
            \Log::info("URL ajustada con categoría para página {$page}", ['url' => $url, 'categoria' => $categoria]);
        } else {
            $url = $officialStoreId
                ? ($page === 1 ? "{$baseUrl}/_Tienda_{$sellerName}_NoIndex_True" : "{$baseUrl}/_Desde_{$offset}_Tienda_{$sellerName}_NoIndex_True")
                : ($page === 1 ? "{$baseUrl}/_CustId_{$sellerId}_NoIndex_True" : "{$baseUrl}/_CustId_{$sellerId}_Desde_{$offset}_NoIndex_True");
            \Log::info("URL sin categoría para página {$page}", ['url' => $url]);
        }

        \Log::info("Intentando scrapeo de página {$page} con URL: {$url}", ['seller_id' => $sellerId]);

        try {
            sleep(5); // Retraso inicial de 5 segundos
            $response = $this->client->get($url, ['timeout' => 15]);
            if ($response->getStatusCode() !== 200) {
                \Log::warning("Código de estado no esperado: {$response->getStatusCode()}", ['url' => $url]);
                break;
            }

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // Extraer cantidad de resultados (como referencia)
            $resultCount = $crawler->filter('.ui-search-search-result__quantity-results')->count() > 0
                ? (int)str_replace('.', '', trim($crawler->filter('.ui-search-search-result__quantity-results')->text()))
                : 0;
            \Log::info("Cantidad de resultados encontrados: {$resultCount}", ['url' => $url]);

            // Forzar búsqueda de ítems incluso si la cantidad es 0
            $itemNodes = $crawler->filter('li.ui-search-layout__item');
            if ($itemNodes->count() === 0) {
                \Log::info("No se encontraron ítems en la página {$page}. Deteniendo scraping.", ['url' => $url, 'html_sample' => substr($html, 0, 3000)]);
                break;
            }

            // Procesar ítems y validar que pertenezcan al vendedor
            $itemNodes->each(function (Crawler $node) use (&$items, $maxItems, $sellerId, $categoria) {
                if (count($items) >= $maxItems) return false;

                $postLink = $node->filter('h3.poly-component__title-wrapper a.poly-component__title')->count()
                    ? $node->filter('h3.poly-component__title-wrapper a.poly-component__title')->attr('href')
                    : 'Sin enlace';
                $itemId = $this->extractItemIdFromLink($postLink);

                // Validar que el ítem incluya el sellerId en la URL o tracking (aproximación)
                if (strpos($postLink, "searchVariation=") === false || strpos($postLink, $sellerId) !== false) {
                    $title = $node->filter('h3.poly-component__title-wrapper a.poly-component__title')->count()
                        ? $node->filter('h3.poly-component__title-wrapper a.poly-component__title')->text()
                        : 'Sin título';
                    $originalPrice = $node->filter('s.andes-money-amount--previous .andes-money-amount__fraction')->count()
                        ? $this->normalizePrice($node->filter('s.andes-money-amount--previous .andes-money-amount__fraction')->text())
                        : null;
                    $currentPrice = $node->filter('.poly-price__current .andes-money-amount__fraction')->count()
                        ? $this->normalizePrice($node->filter('.poly-price__current .andes-money-amount__fraction')->text())
                        : ($originalPrice ?? 0.0);
                    $isFull = $node->filter('span.poly-component__shipped-from svg[aria-label="FULL"]')->count() > 0;
                    $hasFreeShipping = $node->filter('.poly-component__shipping:contains("Envío gratis")')->count() > 0;
                    $installments = $node->filter('.poly-price__installments')->count()
                        ? trim($node->filter('.poly-price__installments')->text())
                        : null;

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
                }
            });

            // Umbral después de procesar
            if ($resultCount > 50000) {
                \Log::warning("Cantidad de resultados ({$resultCount}) mayor a 50,000. Deteniendo scraping para evitar datos no deseados.", ['url' => $url]);
                break;
            }

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
