<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Item::with(['category', 'supplier', 'unit', 'storageLocation'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $items,
        ]);
    }

    public function show(Item $item): JsonResponse
    {
        $item->load(['category', 'supplier', 'unit', 'storageLocation']);

        return response()->json([
            'status' => true,
            'data'   => $item,
        ]);
    }

    public function store(StoreItemRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $item = Item::create($data);
        $item->load(['category', 'supplier', 'unit', 'storageLocation']);

        return response()->json([
            'status'  => true,
            'message' => 'Barang berhasil ditambahkan.',
            'data'    => $item,
        ], 201);
    }

    public function update(UpdateItemRequest $request, Item $item): JsonResponse
    {
        $item->update($request->validated());
        $item->load(['category', 'supplier', 'unit', 'storageLocation']);

        return response()->json([
            'status'  => true,
            'message' => 'Barang berhasil diupdate.',
            'data'    => $item,
        ]);
    }

    public function destroy(Item $item): JsonResponse
    {
        $item->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Barang berhasil dihapus.',
        ]);
    }
}