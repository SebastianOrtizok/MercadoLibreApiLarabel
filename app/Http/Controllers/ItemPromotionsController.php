<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ItemPromotionsService;
use App\Jobs\SyncItemPromotionsJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemPromotionsController extends Controller
{
    private $itemPromotionsService;

    public function __construct(ItemPromotionsService $itemPromotionsService)
    {
        $this->itemPromotionsService = $itemPromotionsService;
    }

    public function syncPromotions(Request $request)
    {
        // Este método no necesita cambios
        try {
            $userId = auth()->user()->id;

            $mlAccounts = DB::table('mercadolibre_tokens')
                ->where('user_id', $userId)
                ->select('ml_account_id', 'access_token')
                ->get();

            if ($mlAccounts->isEmpty()) {
                return redirect()->back()->with('error', 'El usuario no tiene cuentas asociadas.');
            }

            $products = DB::table('articulos')
                ->where('estado', 'active')
                ->whereIn('user_id', $mlAccounts->pluck('ml_account_id'))
                ->whereNotNull('deal_ids')
                ->select('ml_product_id', 'user_id', 'precio', 'precio_original', 'imagen', 'permalink')
                ->get();

            if ($products->isEmpty()) {
                return redirect()->back()->with('error', 'No se encontraron productos con deal_ids no nulos.');
            }

            Log::info("Iniciando sincronización de " . $products->count() . " productos");

            foreach ($mlAccounts as $account) {
                $accountProducts = $products->where('user_id', $account->ml_account_id);
                if ($accountProducts->isEmpty()) {
                    continue;
                }

                foreach ($accountProducts->chunk(200) as $chunk) {
                    SyncItemPromotionsJob::dispatch($chunk, $account->access_token, $account->ml_account_id);
                }
            }

            return redirect()->back()->with('success', 'Sincronización iniciada en segundo plano. Revisa el estado en unos minutos.');
        } catch (\Exception $e) {
            Log::error("Error al iniciar sincronización: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al iniciar sincronización: ' . $e->getMessage());
        }
    }

    public function showPromotions(Request $request)
    {
        $userId = auth()->user()->id;

        $mlAccounts = DB::table('mercadolibre_tokens')
            ->where('user_id', $userId)
            ->pluck('ml_account_id');

        $promotionFilter = $request->input('promotion_filter', 'with_promotions');

        $query = DB::table('articulos')
            ->leftJoin('item_promotions', 'articulos.ml_product_id', '=', 'item_promotions.ml_product_id')
            ->join('mercadolibre_tokens', 'articulos.user_id', '=', 'mercadolibre_tokens.ml_account_id')
            ->whereIn('articulos.user_id', $mlAccounts)
            ->where('articulos.estado', 'active')
            ->select(
                'articulos.ml_product_id',
                'item_promotions.promotion_id',
                'item_promotions.type',
                'item_promotions.status',
                'item_promotions.original_price',
                'item_promotions.new_price',
                'item_promotions.start_date',
                'item_promotions.finish_date',
                'item_promotions.name',
                'articulos.titulo',
                'articulos.imagen',
                'articulos.permalink',
                'mercadolibre_tokens.seller_name',
                'articulos.precio',
                'articulos.precio_original',
                'articulos.deal_ids'
            );

        if ($promotionFilter === 'with_promotions') {
            $query->where(function ($q) {
                $q->whereNotNull('item_promotions.promotion_id') // Promoción oficial en item_promotions
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('articulos.precio_original')
                         ->whereColumn('articulos.precio', '<', 'articulos.precio_original'); // Descuento visible
                  })
                  ->orWhere(function ($q3) {
                      $q3->whereNotNull('articulos.deal_ids')
                         ->where('articulos.deal_ids', '!=', '[]'); // Promoción oficial con deal_ids
                  });
            });
        } elseif ($promotionFilter === 'without_promotions') {
            $query->whereNull('item_promotions.promotion_id') // Sin promoción oficial
                  ->where(function ($q) {
                      $q->whereNull('articulos.precio_original') // Sin precio original
                        ->orWhereColumn('articulos.precio', '=', 'articulos.precio_original'); // Sin descuento
                  })
                  ->whereNull('articulos.deal_ids'); // Solo nulo, no vacío
        }

        // Otros filtros
        if ($request->filled('ml_account_id')) {
            $query->where('articulos.user_id', $request->ml_account_id);
        }
        if ($request->filled('status') && $promotionFilter !== 'without_promotions') {
            $query->where('item_promotions.status', $request->status);
        }
        if ($request->filled('type') && $promotionFilter !== 'without_promotions') {
            $query->where('item_promotions.type', $request->input('type'));
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('articulos.ml_product_id', 'like', "%$search%")
                  ->orWhere('articulos.titulo', 'like', "%$search%");
            });
        }

        $limit = $request->input('limit', 30);
        $promotions = $query->orderBy('articulos.titulo', 'asc')
            ->paginate($limit);

        $promotions->getCollection()->transform(function ($promo) {
            if ($promo->finish_date && $promo->promotion_id) {
                $finishDate = Carbon::parse($promo->finish_date);
                $today = Carbon::today();
                $promo->days_remaining = $today->diffInDays($finishDate, false);
            } else {
                $promo->days_remaining = 'N/A';
            }
            if (!$promo->promotion_id) {
                $promo->original_price = $promo->precio_original ?? $promo->precio;
                $promo->new_price = $promo->precio;
                $promo->type = $promo->deal_ids === '[]' && $promo->precio < $promo->precio_original ? 'Descuento Manual' : 'Sin Promoción';
                $promo->status = 'N/A';
                $promo->start_date = 'N/A';
                $promo->finish_date = 'N/A';
                $promo->name = $promo->deal_ids === '[]' && $promo->precio < $promo->precio_original ? 'Descuento del Vendedor' : 'Sin Promoción';
            }
            return $promo;
        });

        return view('dashboard.item_promotions', [
            'promotions' => $promotions,
            'currentPage' => $promotions->currentPage(),
            'totalPages' => $promotions->lastPage(),
            'limit' => $limit,
        ]);
    }
}
