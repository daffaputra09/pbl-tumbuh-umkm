<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessOwnerHasProfile
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->hasRole(User::ROLE_BUSINESS_OWNER) || $user->hasBusinessProfile()) {
            return $next($request);
        }

        if ($request->routeIs('umkm.profil', 'umkm.profil.save')) {
            return $next($request);
        }

        return redirect()->route('umkm.profil');
    }
}
