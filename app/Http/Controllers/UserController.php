<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::select(['id', 'name', 'email', 'role', 'is_active', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $users,
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => $user,
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data             = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'User berhasil ditambahkan.',
            'data'    => $user,
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'User berhasil diupdate.',
            'data'    => $user,
        ]);
    }

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