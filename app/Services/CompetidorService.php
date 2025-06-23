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

    public function scrapeItemsBySeller($sellerId, $sellerName, $officialStoreId = null)
{
    $items = [];
    $page = 1;
    $maxItems = 500; // Mantener límite de 500 ítems
    $itemsPerPage = $officialStoreId ? 48 : 50;
    $continueScraping = true;

    $baseUrl = $officialStoreId
        ? "https://listado.mercadolibre.com.ar"
        : "https://listado.mercadolibre.com.ar";

    while ($continueScraping && count($items) < $maxItems) {
        $offset = ($page - 1) * $itemsPerPage + 1;
        $url = $officialStoreId
            ? ($page === 1 ? "{$baseUrl}/_Tienda_{$sellerName}_NoIndex_True?official_store_id={$officialStoreId}"
                : "{$baseUrl}/_Desde_{$offset}_Tienda_{$sellerName}_NoIndex_True?official_store_id={$officialStoreId}")
            : "{$baseUrl}/_CustId_{$sellerId}_Desde_{$offset}_NoIndex_True";

        \Log::info("Intentando scrapeo de página {$page} con URL: {$url}");

        try {
            $response = $this->client->get($url, ['timeout' => 10]);
            \Log::info("Respuesta recibida", ['status' => $response->getStatusCode(), 'url' => $url]);
            if ($response->getStatusCode() !== 200) {
                \Log::warning("Código de estado no esperado: {$response->getStatusCode()}");
                break;
            }

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // Detectar número total de ítems (opcional, si está disponible en el HTML)
            $totalItems = $crawler->filter('.ui-search-search-result__quantity-results')->count()
                ? (int) preg_replace('/[^0-9]/', '', $crawler->filter('.ui-search-search-result__quantity-results')->text())
                : null;
            if ($totalItems) {
                \Log::info("Total ítems detectados: {$totalItems}");
                $maxPages = ceil($totalItems / $itemsPerPage);
                if ($page > $maxPages) {
                    $continueScraping = false;
                }
            }

            $itemNodes = $crawler->filter('li.ui-search');
            \Log::info("Ítems encontrados en página {$page}: " . $itemNodes->count());

            if ($itemNodes->count() === 0) {
                \Log::info("No se encontraron más ítems en la página {$page}");
                $continueScrap = false;
                break;
            }

            $itemNodes->each(function (Crawler $node) use (&$items, $maxItems) {
                if (count($items) >= $maxItems) {
                    return false;
                }

                $title = $node->filter('h3.ui-title-wrapper a')->count()
                    ? $node->filter('h3.ui-title-wrapper a')->text()
                    : 'Sin título';
                $originalPrice = $node->filter('s.andes-money-amount--previous .andes-money-amount')->count()
                    ? $this->normalizePrice($node->filter('s.andes-money-amount--previous .andes-money-amount')->text())
                    : null;
                $currentPrice = $node->filter('.poly-price-current .andes-money-amount-price')->count()
                    ? $this->normalizePrice($node->filter('.poly-price-current .andes-money-amount-price')->text())
                    : ($originalPrice ?? 0.0);
                $postLink = $node->filter('h3.ui-title-wrapper a')->count()
                    ? $node->filter('h3.ui-title-wrapper a')->attr('href')
                    : 'Sin enlace';
                $isFull = $node->filter('span.poly-component-full svg[aria-label="FULL"]')->count() > 0;
                $hasFreeShipping = $node->filter('.poly-component__shipping:contains("Envío libre")')->count() > 0;
                $installments = $node->filter('.poly-price-installments')->count()
                    ? trim($node->filter('.installments')->text())
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
                ];

                \Log::info("Ítem scrapeado", [$itemData]);

                $items[] = $itemData;
            });

            $page++;
            sleep(3); // Aumentar retraso para evitar bloqueos
        } catch (RequestException $e) {
            \Log::error("Error al intentar scrapear para el vendedor {$sellerId}", [
                'error' => $e->getMessage(),
                'url' => $url,
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? substr($e->getResponse()->getBody()->getContents(), 0, 500) : 'No response'
            ]);
            $continueScraping = false;
        }
    }

    \Log::info("Scraping finalizado. Total ítems scrapeados: " . count($items));
    return $items;
}

    protected function extractItemIdFromLink($link)
    {
        if (preg_match('/(MLA\d+)/', $link, $matches)) {
            return $matches[1];
        }
        $parts = parse_url($link);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query['item_id']) && preg_match('/(MLA\d+)/', $query['item_id'], $matches)) {
                return $matches[1];
            }
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
