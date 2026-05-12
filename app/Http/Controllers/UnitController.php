<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use Illuminate\Http\JsonResponse;

class UnitController extends Controller
{
    public function index(): JsonResponse
    {
        $units = Unit::orderBy('name')->get();

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

        return response()->json([
            'status'  => true,
            'message' => 'Satuan berhasil ditambahkan.',
            'data'    => $unit,
        ], 201);
    }

    public function update(UpdateUnitRequest $request, Unit $unit): JsonResponse
    {
        $unit->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Satuan berhasil diupdate.',
            'data'    => $unit,
        ]);
    }

    public function destroy(Unit $unit): JsonResponse
    {
        $unit->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Satuan berhasil dihapus.',
        ]);
    }
}