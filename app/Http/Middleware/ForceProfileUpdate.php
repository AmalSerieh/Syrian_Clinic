<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceProfileUpdate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->wasCreatedByAnother()) {
            if (!$request->routeIs('profile.edit')) {
                return redirect()->route('profile.edit')->with('message', 'يرجى تحديث معلومات حسابك.');
            }
        }
        return $next($request);
    }
}
