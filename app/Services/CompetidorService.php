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
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
    }

    public function scrapeItemsBySeller($sellerId)
    {
        $items = [];
        $page = 1;
        $baseUrl = 'https://listado.mercadolibre.com.ar'; // Argentina por defecto

        while (true) {
            $url = "{$baseUrl}/_CustId_{$sellerId}_Desde_" . (($page - 1) * 50 + 1) . "_NoIndex_True";
            \Log::info("Scrapeando página {$page}: {$url}");

            try {
                $response = $this->client->get($url);
                \Log::info("Respuesta recibida", ['status' => $response->getStatusCode(), 'url' => $url]);
                $html = $response->getBody()->getContents();
                $crawler = new Crawler($html);

                $itemNodes = $crawler->filter('li.ui-search-layout__item');
                if ($itemNodes->count() === 0) {
                    break; // No hay más ítems, salimos del bucle
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

                    // Extraer el item_id desde el enlace (por ejemplo, MLA38481106)
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
                    'url' => $url
                ]);
                break;
            }
        }

        return $items;
    }

    protected function extractItemIdFromLink($link)
    {
        preg_match('/(MLA\d+)/', $link, $matches);
        return $matches[1] ?? $link; // Si no se encuentra un MLA, devolvemos el enlace completo como fallback
    }
}
