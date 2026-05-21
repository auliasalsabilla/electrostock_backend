<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Item::with(['category', 'supplier', 'unit', 'storageLocation'])
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                $item->image_url = $item->image
                    ? asset('storage/' . $item->image)
                    : null;
                return $item;
            });

        return response()->json([
            'status' => true,
            'data'   => $items,
        ]);
    }

    public function show(Item $item): JsonResponse
    {
        $item->load(['category', 'supplier', 'unit', 'storageLocation']);
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
        $item->load(['category', 'supplier', 'unit', 'storageLocation']);
        $item->image_url = $item->image ? asset('storage/' . $item->image) : null;

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
        $item->load(['category', 'supplier', 'unit', 'storageLocation']);
        $item->image_url = $item->image ? asset('storage/' . $item->image) : null;

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

        return response()->json([
            'status'  => true,
            'message' => 'Barang berhasil dihapus.',
        ]);
    }
}