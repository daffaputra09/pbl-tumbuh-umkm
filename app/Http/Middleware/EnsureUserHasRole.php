<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Batasi route ke satu atau beberapa peran.
     *
     * Pakai alias `role` di route, misalnya `role:officer` atau
     * `role:officer,village_head`. Jangan menyalin cek role di controller.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->hasRole(...$roles)) {
            abort(403);
        }

        return $next($request);
    }
}
