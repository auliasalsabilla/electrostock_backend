<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Item;
use App\Http\Requests\StoreTransactionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class TransactionController extends Controller
{
    public function index(): JsonResponse
    {
        $transactions = Cache::remember('transactions', 120, function () {
            return Transaction::with([
                    'item:id,name,code',
                    'user:id,name'
                ])
                ->select([
                    'id', 'code', 'type', 'item_id', 'user_id',
                    'quantity', 'unit', 'price', 'note',
                    'transaction_date', 'created_at'
                ])
                ->orderBy('transaction_date', 'desc')
                ->limit(500)
                ->get();
        });

        return response()->json([
            'status' => true,
            'data'   => $transactions,
        ]);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $data            = $request->validated();
        $data['user_id'] = auth()->id();
        $data['code']    = 'TRX-' . strtoupper(Str::random(8));

        $item = Item::findOrFail($data['item_id']);

        if ($data['type'] === 'out' && $item->stock < $data['quantity']) {
            return response()->json([
                'status'  => false,
                'message' => 'Stok tidak mencukupi.',
            ], 422);
        }

        if ($data['type'] === 'in') {
            $item->increment('stock', $data['quantity']);
        } else {
            $item->decrement('stock', $data['quantity']);
        }

        $transaction = Transaction::create($data);

        Cache::forget('transactions'); // clear cache setelah tambah
        Cache::forget('items');        // clear cache item karena stok berubah

        return response()->json([
            'status'  => true,
            'message' => 'Transaksi berhasil disimpan.',
            'data'    => $transaction->load(['item:id,name,code', 'user:id,name']),
        ], 201);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => $transaction->load(['item:id,name,code', 'user:id,name']),
        ]);
    }

    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $request->validate([
            'quantity'         => ['sometimes', 'integer', 'min:1'],
            'price'            => ['nullable', 'numeric'],
            'note'             => ['nullable', 'string'],
            'transaction_date' => ['sometimes', 'date'],
        ]);

        $item = Item::findOrFail($transaction->item_id);

        // Kembalikan stok lama
        if ($transaction->type === 'in') {
            $item->decrement('stock', $transaction->quantity);
        } else {
            $item->increment('stock', $transaction->quantity);
        }

        // Terapkan stok baru
        $newQty = $request->quantity ?? $transaction->quantity;
        if ($transaction->type === 'in') {
            $item->increment('stock', $newQty);
        } else {
            if ($item->stock < $newQty) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Stok tidak mencukupi.',
                ], 422);
            }
            $item->decrement('stock', $newQty);
        }

        $transaction->update($request->only(['quantity', 'price', 'note', 'transaction_date']));

        Cache::forget('transactions'); // clear cache setelah update
        Cache::forget('items');        // clear cache item karena stok berubah

        return response()->json([
            'status'  => true,
            'message' => 'Transaksi berhasil diupdate.',
            'data'    => $transaction->load(['item:id,name,code', 'user:id,name']),
        ]);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $item = Item::findOrFail($transaction->item_id);

        // Kembalikan stok
        if ($transaction->type === 'in') {
            $item->decrement('stock', $transaction->quantity);
        } else {
            $item->increment('stock', $transaction->quantity);
        }

        $transaction->delete();

        Cache::forget('transactions'); // clear cache setelah hapus
        Cache::forget('items');        // clear cache item karena stok berubah

        return response()->json([
            'status'  => true,
            'message' => 'Transaksi berhasil dihapus.',
        ]);
    }
}