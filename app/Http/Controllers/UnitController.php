<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class UnitController extends Controller
{
    public function index(): JsonResponse
    {
        $units = Cache::remember('units', 300, function () {
            return Unit::orderBy('name')->get();
        });

        return response()->json([
            'status' => true,
            'data'   => $units,
        ]);
    }

    public function show(Unit $unit): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => $unit,
        ]);
    }

    public function store(StoreUnitRequest $request): JsonResponse
    {
        $unit = Unit::create($request->validated());

        Cache::forget('units'); // clear cache setelah tambah

        return response()->json([
            'status'  => true,
            'message' => 'Satuan berhasil ditambahkan.',
            'data'    => $unit,
        ], 201);
    }

    public function update(UpdateUnitRequest $request, Unit $unit): JsonResponse
    {
        $unit->update($request->validated());

        Cache::forget('units'); // clear cache setelah update

        return response()->json([
            'status'  => true,
            'message' => 'Satuan berhasil diupdate.',
            'data'    => $unit,
        ]);
    }

    public function destroy(Unit $unit): JsonResponse
    {
        $unit->delete();

        Cache::forget('units'); // clear cache setelah hapus

        return response()->json([
            'status'  => true,
            'message' => 'Satuan berhasil dihapus.',
        ]);
    }
}