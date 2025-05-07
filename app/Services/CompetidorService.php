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
        $maxPages = 5; // Límite de 5 páginas (aproximadamente 240 ítems para tiendas oficiales)
        $itemsPerPage = $officialStoreId ? 48 : 50; // Tiendas oficiales usan 48 ítems por página

        // Determinar la base URL según si es una tienda oficial
        $baseUrl = $officialStoreId
            ? "https://tienda.mercadolibre.com.ar/{$sellerName}"
            : "https://listado.mercadolibre.com.ar";

        while ($page <= $maxPages) {
            $offset = ($page - 1) * $itemsPerPage + 1;
            $url = $officialStoreId
                ? "{$baseUrl}" . ($page > 1 ? "_Desde_{$offset}" : "")
                : "{$baseUrl}/_CustId_{$sellerId}_Desde_{$offset}_NoIndex_True";

            \Log::info("Scrapeando página {$page}: {$url}");

            try {
                $response = $this->client->get($url);
                \Log::info("Respuesta recibida", ['status' => $response->getStatusCode(), 'url' => $url]);
                if ($response->getStatusCode() !== 200) {
                    \Log::warning("Código de estado no esperado: {$response->getStatusCode()}");
                    break;
                }

                $html = $response->getBody()->getContents();
                $crawler = new Crawler($html);

                $itemNodes = $crawler->filter('li.ui-search-layout__item');
                if ($itemNodes->count() === 0) {
                    \Log::info("No se encontraron más ítems en la página {$page}");
                    break;
                }

                $itemNodes->each(function (Crawler $node) use (&$items, $sellerId) {
                    $title = $node->filter('h3.poly-component__title-wrapper')->count()
                        ? $node->filter('h3.poly-component__title-wrapper')->text()
                        : 'Sin título';
                    $price = $node->filter('span.andes-money-amount__fraction')->count()
                        ? floatval(str_replace(',', '', $node->filter('span.andes-money-amount__fraction')->text()))
                        : 0.0;
                    $postLink = $node->filter('a')->count()
                        ? $node->filter('a')->attr('href')
                        : 'Sin enlace';

                    $itemId = $this->extractItemIdFromLink($postLink);

                    $items[] = [
                        'item_id' => $itemId,
                        'titulo' => $title,
                        'precio' => $price,
                        'post_link' => $postLink,
                        'seller_id' => $sellerId,
                    ];
                });

                $page++;
                sleep(2); // Pausa para evitar bloqueos
            } catch (RequestException $e) {
                \Log::error("Error al scrapear ítems para el vendedor {$sellerId}", [
                    'error' => $e->getMessage(),
                    'url' => $url,
                    'code' => $e->getCode()
                ]);
                break;
            }
        }

        return $items;
    }

    protected function extractItemIdFromLink($link)
    {
        preg_match('/(MLA\d+)/', $link, $matches);
        return $matches[1] ?? $link;
    }
}
