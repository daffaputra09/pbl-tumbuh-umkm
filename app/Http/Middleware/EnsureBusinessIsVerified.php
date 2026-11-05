<?php

namespace App\Http\Middleware;

use App\Models\Business;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessIsVerified
{
    public const MESSAGE = 'Usaha belum diverifikasi petugas desa. Produk, kendala, dan kegiatan lain terbuka setelah data usaha diverifikasi.';

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->hasRole(User::ROLE_BUSINESS_OWNER)) {
            return $next($request);
        }

        $business = $user->business;

        if ($business instanceof Business && $business->isVerified()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, self::MESSAGE);
        }

        return redirect()
            ->route('umkm.dashboard')
            ->with('status', self::MESSAGE);
    }
}
