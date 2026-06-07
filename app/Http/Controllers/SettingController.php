<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // GET /api/settings - ambil semua settings
    public function index(): JsonResponse
    {
        $settings = Setting::all();

        return response()->json([
            'status' => true,
            'data'   => $settings,
        ]);
    }

    // GET /api/settings/{key} - ambil setting by key
    public function show(string $key): JsonResponse
    {
        $setting = Setting::where('key', $key)->firstOrFail();

        return response()->json([
            'status' => true,
            'data'   => $setting,
        ]);
    }

    // POST /api/settings - tambah setting baru
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key'   => ['required', 'string', 'unique:settings,key'],
            'value' => ['nullable', 'string'],
        ]);

        $setting = Setting::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Setting berhasil ditambahkan.',
            'data'    => $setting,
        ], 201);
    }

    // PUT /api/settings/{id} - update setting
    public function update(Request $request, Setting $setting): JsonResponse
    {
        $validated = $request->validate([
            'key'   => ['sometimes', 'string', "unique:settings,key,{$setting->id}"],
            'value' => ['nullable', 'string'],
        ]);

        $setting->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Setting berhasil diupdate.',
            'data'    => $setting,
        ]);
    }

    // DELETE /api/settings/{id} - hapus setting
    public function destroy(Setting $setting): JsonResponse
    {
        $setting->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Setting berhasil dihapus.',
        ]);
    }
}
