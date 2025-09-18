<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\ConnectException;

class CompetidorService
{
    protected $client;
    protected $proxies;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'es-AR,es;q=0.9',
                'Referer' => 'https://www.mercadolibre.com.ar/',
            ],
            'cookies' => true,
        ]);
        $this->proxies = explode(',', env('SCRAPER_PROXIES', '')) ?: [];
    }

    protected function isProxyAlive($proxy)
    {
        try {
            $testClient = new Client(['timeout' => 5]);
            $response = $testClient->get('http://icanhazip.com', ['proxy' => $proxy]);
            \Log::info("Proxy $proxy está vivo", ['status' => $response->getStatusCode()]);
            return true;
        } catch (\Exception $e) {
            \Log::warning("Proxy $proxy no responde", ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function scrapeItemsBySeller($sellerId, $sellerName, $officialStoreId = null, $categoria = null)
    {
        $items = [];
        $page = 1;
        $maxPages = 5;
        $maxItems = 500;
        $itemsPerPage = $officialStoreId ? 48 : 50;
        $baseUrl = "https://listado.mercadolibre.com.ar";
        $maxRetries = 3; // Reintentos por proxy fallido

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

            $attempt = 0;
            $success = false;
            $usedProxies = [];

            while ($attempt < $maxRetries && !$success && !empty($this->proxies)) {
                // Seleccionar proxy vivo
                $proxy = null;
                while (!empty($this->proxies)) {
                    $proxy = $this->proxies[array_rand($this->proxies)];
                    if (!in_array($proxy, $usedProxies) && $this->isProxyAlive($proxy)) {
                        break;
                    }
                    $usedProxies[] = $proxy;
                    $this->proxies = array_diff($this->proxies, [$proxy]);
                    $proxy = null;
                }

                if (!$proxy) {
                    \Log::error("No hay proxies vivos disponibles", ['seller_id' => $sellerId]);
                    break;
                }

                try {
                    \Log::info("Usando proxy: {$proxy}");
                    $options = ['timeout' => 30, 'proxy' => $proxy];
                    $response = $this->client->get($url, $options);

                    \Log::info("Respuesta recibida", [
                        'status' => $response->getStatusCode(),
                        'url' => $url,
                        'proxy' => $proxy
                    ]);

                    if ($response->getStatusCode() == 200) {
                        $success = true;
                    } else {
                        \Log::warning("Respuesta no exitosa", ['status' => $response->getStatusCode(), 'proxy' => $proxy]);
                        $usedProxies[] = $proxy;
                        $attempt++;
                        continue;
                    }

                    $html = $response->getBody()->getContents();
                    $crawler = new Crawler($html);

                    // Extraer cantidad de resultados
                    $resultCount = $crawler->filter('.ui-search-search-result__quantity-results')->count() > 0
                        ? (int)str_replace('.', '', trim($crawler->filter('.ui-search-search-result__quantity-results')->text()))
                        : 0;
                    \Log::info("Cantidad de resultados encontrados: {$resultCount}", ['url' => $url]);

                    if ($resultCount > 50000) {
                        \Log::warning("Cantidad de resultados ({$resultCount}) mayor a 50,000. No se encontraron productos del vendedor en esta categoría.", ['url' => $url]);
                        break;
                    }

                    $itemNodes = $crawler->filter('li.ui-search-layout__item');
                    if ($itemNodes->count() === 0) {
                        \Log::info("No se encontraron ítems en la página {$page}. Deteniendo scraping.", ['url' => $url]);
                        break;
                    }

                    $itemNodes->each(function (Crawler $node) use (&$items, $maxItems, $categoria) {
                        if (count($items) >= $maxItems) return false;

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
                        } elseif ($node->filter('div.seo-ui-extended-menu__header h3.seo-ui-extended-menu__header__title')->count() > 0) {
                            $categoriaItem = trim($node->filter('div.seo-ui-extended-menu__header h3.seo-ui-extended-menu__header__title')->text());
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
                } catch (ConnectException $e) {
                    \Log::error("Error de conexión con proxy $proxy", ['error' => $e->getMessage(), 'url' => $url]);
                    $usedProxies[] = $proxy;
                    $attempt++;
                } catch (RequestException $e) {
                    \Log::error("Error al scrapear con proxy $proxy", ['error' => $e->getMessage(), 'url' => $url]);
                    $usedProxies[] = $proxy;
                    $attempt++;
                }
            }

            if (!$success) {
                \Log::error("No se pudo scrapear la página {$page} tras {$maxRetries} intentos", ['url' => $url]);
                break;
            }
        }

        \Log::info("Scraping finalizado. Total ítems scrapeados: " . count($items), ['seller_id' => $sellerId]);
        return $items;
    }

    protected function extractItemIdFromLink($link)
    {
        if (preg_match('/\/(MLA-\d+)-/', $link, $matches)) {
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
