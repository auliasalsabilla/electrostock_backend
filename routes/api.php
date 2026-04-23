<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::post('/login', function (Request $request) {
    $validated = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
        'role' => ['required', 'in:admin,staff,manager'],
    ]);

    $user = User::where('email', strtolower($validated['email']))
        ->where('role', $validated['role'])
        ->first();

    if (! $user || ! Hash::check($validated['password'], $user->password)) {
        return response()->json([
            'message' => 'Login gagal. Periksa email, password, dan role Anda.',
        ], 401);
    }

    return response()->json([
        'email' => $user->email,
        'name' => $user->name,
        'role' => $user->role,
    ]);
});
