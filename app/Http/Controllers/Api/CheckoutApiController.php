<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutApiController extends Controller
{
    /**
     * Proses checkout dari Flutter kasir.
     *
     * Payload:
     * {
     *   "items": [
     *     { "product_id": 1, "quantity": 2 },
     *     { "product_id": 3, "quantity": 1 }
     *   ],
     *   "customer_id": null,
     *   "customer_name": "Walk-in",
     *   "is_loyalty": false,
     *   "payment_method": "cash",
     *   "paid_amount": 50000,
     *   "discount_amount": 0,
     *   "notes": ""
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,transfer,qris,tempo',
            'paid_amount' => 'required_unless:payment_method,tempo|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'is_loyalty' => 'boolean',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'due_date' => 'required_if:payment_method,tempo|nullable|date|after:today',
        ]);

        return DB::transaction(function () use ($request) {
            $user = $request->user();
            $isLoyalty = $request->boolean('is_loyalty', false);
            $discountAmount = $request->input('discount_amount', 0);

            // Hitung subtotal dari items
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->input('items') as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Cek stok
                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'message' => "Stok {$product->name} tidak mencukupi. Tersisa: {$product->stock}",
                    ], 422);
                }

                $price = $product->getEffectivePrice($isLoyalty);
                $itemSubtotal = $price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $grandTotal = $subtotal - $discountAmount;
            $paidAmount = $request->input('paid_amount', 0);
            $changeAmount = max(0, $paidAmount - $grandTotal);

            // Tentukan payment status
            $paymentStatus = 'paid';
            if ($request->input('payment_method') === 'tempo') {
                $paymentStatus = $paidAmount >= $grandTotal ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');
            }

            // Buat transaksi
            $transaction = Transaction::create([
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'user_id' => $user->id,
                'customer_id' => $request->input('customer_id'),
                'customer_name' => $request->input('customer_name', 'Walk-in'),
                'is_loyalty' => $isLoyalty,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => 0,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $request->input('payment_method'),
                'payment_status' => $paymentStatus,
                'due_date' => $request->input('due_date'),
                'status' => 'completed',
                'notes' => $request->input('notes'),
            ]);

            // Buat item transaksi & kurangi stok
            foreach ($itemsData as $data) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $data['product']->id,
                    'product_name' => $data['product']->name,
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                    'subtotal' => $data['subtotal'],
                ]);

                // Kurangi stok
                $data['product']->decrement('stock', $data['quantity']);

                // Catat mutasi stok
                StockMutation::create([
                    'product_id' => $data['product']->id,
                    'user_id' => $user->id,
                    'type' => 'out',
                    'quantity' => $data['quantity'],
                    'stock_before' => $data['product']->stock + $data['quantity'],
                    'stock_after' => $data['product']->stock,
                    'reference' => $transaction->invoice_number,
                    'notes' => 'Penjualan via POS App',
                ]);
            }

            // Load relations untuk response
            $transaction->load('items');

            return response()->json([
                'message' => 'Transaksi berhasil',
                'data' => [
                    'id' => $transaction->id,
                    'invoice_number' => $transaction->invoice_number,
                    'customer_name' => $transaction->customer_name,
                    'subtotal' => (float) $transaction->subtotal,
                    'discount_amount' => (float) $transaction->discount_amount,
                    'grand_total' => (float) $transaction->grand_total,
                    'paid_amount' => (float) $transaction->paid_amount,
                    'change_amount' => (float) $transaction->change_amount,
                    'payment_method' => $transaction->payment_method,
                    'payment_status' => $transaction->payment_status,
                    'cashier_name' => $user->name,
                    'items' => $transaction->items->map(function ($item) {
                        return [
                            'product_name' => $item->product_name,
                            'quantity' => $item->quantity,
                            'price' => (float) $item->price,
                            'subtotal' => (float) $item->subtotal,
                        ];
                    }),
                    'created_at' => $transaction->created_at->toIso8601String(),
                ],
            ], 201);
        });
    }
}
