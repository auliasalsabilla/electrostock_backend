<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    // GET /api/users - ambil semua user
    public function index(): JsonResponse
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'data'   => $users,
        ]);
    }

    // GET /api/users/{id} - ambil satu user
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => $user,
        ]);
    }

    // POST /api/users - tambah user baru
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'User berhasil ditambahkan.',
            'data'    => $user,
        ], 201);
    }

    // PUT /api/users/{id} - update user
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'User berhasil diupdate.',
            'data'    => $user,
        ]);
    }

    // DELETE /api/users/{id} - hapus user
    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'status'  => false,
                'message' => 'Tidak bisa menghapus akun sendiri.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => 'User berhasil dihapus.',
        ]);
    }
}