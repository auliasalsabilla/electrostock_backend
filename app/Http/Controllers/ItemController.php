<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ItemController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Cache::remember('items', 300, function () {
            return Item::with([
                    'category:id,name',
                    'supplier:id,name',
                    'unit:id,name',
                    'storageLocation:id,name'
                ])
                ->select([
                    'id', 'category_id', 'supplier_id', 'unit_id',
                    'storage_location_id', 'code', 'name', 'brand',
                    'image', 'stock', 'stock_minimum', 'stock_maximum',
                    'purchase_price', 'is_active', 'created_at'
                ])
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    $item->image_url = $item->image
                        ? asset('storage/' . $item->image)
                        : null;
                    return $item;
                });
        });

        return response()->json([
            'status' => true,
            'data'   => $items,
        ]);
    }

    public function show(Item $item): JsonResponse
    {
        $item->load([
            'category:id,name',
            'supplier:id,name',
            'unit:id,name',
            'storageLocation:id,name'
        ]);
        $item->image_url = $item->image ? asset('storage/' . $item->image) : null;

        return response()->json([
            'status' => true,
            'data'   => $item,
        ]);
    }

    public function store(StoreItemRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['is_active']  = filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        $item = Item::create($data);
        $item->load([
            'category:id,name',
            'supplier:id,name',
            'unit:id,name',
            'storageLocation:id,name'
        ]);
        $item->image_url = $item->image ? asset('storage/' . $item->image) : null;

        Cache::forget('items'); // clear cache setelah tambah data

        return response()->json([
            'status'  => true,
            'message' => 'Barang berhasil ditambahkan.',
            'data'    => $item,
        ], 201);
    }

    public function update(UpdateItemRequest $request, Item $item): JsonResponse
    {
        $data = $request->validated();
        $data['is_active'] = filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($data);
        $item->load([
            'category:id,name',
            'supplier:id,name',
            'unit:id,name',
            'storageLocation:id,name'
        ]);
        $item->image_url = $item->image ? asset('storage/' . $item->image) : null;

        Cache::forget('items'); // clear cache setelah update data

        return response()->json([
            'status'  => true,
            'message' => 'Barang berhasil diupdate.',
            'data'    => $item,
        ]);
    }

    public function destroy(Item $item): JsonResponse
    {
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        Cache::forget('items'); // clear cache setelah hapus data

        return response()->json([
            'status'  => true,
            'message' => 'Barang berhasil dihapus.',
        ]);
    }
}