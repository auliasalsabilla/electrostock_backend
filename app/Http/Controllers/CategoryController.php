<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Cache::remember('categories', 300, function () {
            return Category::orderBy('name')->get();
        });

        return response()->json([
            'status' => true,
            'data'   => $categories,
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => $category,
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category = Category::create($data);

        Cache::forget('categories'); // clear cache setelah tambah

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data'    => $category,
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        Cache::forget('categories'); // clear cache setelah update

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil diupdate.',
            'data'    => $category,
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        Cache::forget('categories'); // clear cache setelah hapus

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}