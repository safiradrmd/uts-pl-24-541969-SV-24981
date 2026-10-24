<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'cash_received' => ['required', 'integer', 'min:0'],
        ]);

        $sale = DB::transaction(function () use ($request, $validated) {
            $products = [];
            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::query()->whereKey($item['product_id'])->lockForUpdate()->first();

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => ['Salah satu produk sudah tidak tersedia. Muat ulang daftar produk.'],
                    ]);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Stok {$product->name} tidak mencukupi."],
                    ]);
                }

                $lineSubtotal = (int) $product->price * (int) $item['quantity'];
                $subtotal += $lineSubtotal;
                $products[] = [$product, (int) $item['quantity'], $lineSubtotal];
            }

            if ($validated['cash_received'] < $subtotal) {
                throw ValidationException::withMessages([
                    'cash_received' => ['Uang yang diterima belum mencukupi total belanja.'],
                ]);
            }

            $sale = Sale::create([
                'reference' => 'TRX-' . now()->format('ymd') . '-' . Str::upper(Str::random(6)),
                'user_id' => $request->user()->id,
                'subtotal' => $subtotal,
                'cash_received' => $validated['cash_received'],
                'change_due' => $validated['cash_received'] - $subtotal,
            ]);

            foreach ($products as [$product, $quantity, $lineSubtotal]) {
                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $lineSubtotal,
                ]);

                $product->decrement('stock', $quantity);
            }

            return $sale->load('items');
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi berhasil disimpan.',
            'data' => $sale,
        ], 201);
    }
}
