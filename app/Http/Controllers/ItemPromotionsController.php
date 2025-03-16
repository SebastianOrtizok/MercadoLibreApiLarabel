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

        $query = DB::table('item_promotions')
            ->join('articulos', 'item_promotions.ml_product_id', '=', 'articulos.ml_product_id')
            ->join('mercadolibre_tokens', 'articulos.user_id', '=', 'mercadolibre_tokens.ml_account_id')
            ->whereIn('articulos.user_id', $mlAccounts)
            ->select(
                'item_promotions.ml_product_id',
                'item_promotions.promotion_id',
                'item_promotions.type',
                'item_promotions.status',
                'item_promotions.original_price',
                'item_promotions.new_price',
                'item_promotions.start_date',
                'item_promotions.finish_date',
                'item_promotions.name',
                'articulos.titulo',
                'mercadolibre_tokens.seller_name'
            );

        if ($request->filled('ml_account_id')) {
            $query->where('articulos.user_id', $request->ml_account_id);
        }
        if ($request->filled('status')) {
            $query->where('item_promotions.status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('item_promotions.type', $request->input('type'));
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_promotions.ml_product_id', 'like', "%$search%")
                  ->orWhere('articulos.titulo', 'like', "%$search%");
            });
        }

        $limit = $request->input('limit', 30);
        $promotions = $query->orderBy('item_promotions.finish_date', 'asc')
            ->paginate($limit);

        $promotions->getCollection()->transform(function ($promo) {
            if ($promo->finish_date) {
                $finishDate = Carbon::parse($promo->finish_date);
                $today = Carbon::today();
                $promo->days_remaining = $today->diffInDays($finishDate, false);
            } else {
                $promo->days_remaining = null;
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
