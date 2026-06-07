<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = Cache::remember('suppliers', 300, function () {
            return Supplier::orderBy('name')->get();
        });

        return response()->json([
            'status' => true,
            'data'   => $suppliers,
        ]);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => $supplier,
        ]);
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $supplier = Supplier::create($data);

        Cache::forget('suppliers'); // clear cache setelah tambah

        return response()->json([
            'status'  => true,
            'message' => 'Supplier berhasil ditambahkan.',
            'data'    => $supplier,
        ], 201);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier->update($request->validated());

        Cache::forget('suppliers'); // clear cache setelah update

        return response()->json([
            'status'  => true,
            'message' => 'Supplier berhasil diupdate.',
            'data'    => $supplier,
        ]);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();

        Cache::forget('suppliers'); // clear cache setelah hapus

        return response()->json([
            'status'  => true,
            'message' => 'Supplier berhasil dihapus.',
        ]);
    }
}