<?php

namespace App\Http\Controllers;

use App\Models\StorageLocation;
use App\Http\Requests\StoreStorageLocationRequest;
use App\Http\Requests\UpdateStorageLocationRequest;
use Illuminate\Http\JsonResponse;

class StorageLocationController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = StorageLocation::orderBy('name')->get();

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

        return response()->json([
            'status'  => true,
            'message' => 'Lokasi penyimpanan berhasil ditambahkan.',
            'data'    => $location,
        ], 201);
    }

    public function update(UpdateStorageLocationRequest $request, StorageLocation $storageLocation): JsonResponse
    {
        $storageLocation->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Lokasi penyimpanan berhasil diupdate.',
            'data'    => $storageLocation,
        ]);
    }

    public function destroy(StorageLocation $storageLocation): JsonResponse
    {
        $storageLocation->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Lokasi penyimpanan berhasil dihapus.',
        ]);
    }
}