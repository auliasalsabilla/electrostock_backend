<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! in_array($request->user()->role, $roles)) {
            return response()->json([
                'status'  => false,
                'message' => 'Akses ditolak. Role kamu tidak diizinkan.',
            ], 403);
        }

        return $next($request);
    }
}