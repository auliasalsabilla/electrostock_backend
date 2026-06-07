<?php

namespace App\Http\Controllers;

use App\Models\StorageLocation;
use App\Http\Requests\StoreStorageLocationRequest;
use App\Http\Requests\UpdateStorageLocationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class StorageLocationController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = Cache::remember('storage_locations', 300, function () {
            return StorageLocation::orderBy('name')->get();
        });

        return response()->json([
            'status' => true,
            'data'   => $locations,
        ]);
    }

    public function show(StorageLocation $storageLocation): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => $storageLocation,
        ]);
    }

    public function store(StoreStorageLocationRequest $request): JsonResponse
    {
        $location = StorageLocation::create($request->validated());

        Cache::forget('storage_locations'); // clear cache setelah tambah

        return response()->json([
            'status'  => true,
            'message' => 'Lokasi penyimpanan berhasil ditambahkan.',
            'data'    => $location,
        ], 201);
    }

    public function update(UpdateStorageLocationRequest $request, StorageLocation $storageLocation): JsonResponse
    {
        $storageLocation->update($request->validated());

        Cache::forget('storage_locations'); // clear cache setelah update

        return response()->json([
            'status'  => true,
            'message' => 'Lokasi penyimpanan berhasil diupdate.',
            'data'    => $storageLocation,
        ]);
    }

    public function destroy(StorageLocation $storageLocation): JsonResponse
    {
        $storageLocation->delete();

        Cache::forget('storage_locations'); // clear cache setelah hapus

        return response()->json([
            'status'  => true,
            'message' => 'Lokasi penyimpanan berhasil dihapus.',
        ]);
    }
}