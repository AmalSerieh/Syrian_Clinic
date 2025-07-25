<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDoctorCredentials
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       $user = auth()->user();

    if (
        $user &&
        $user->role === 'doctor' &&
        !$user->has_changed_credentials &&
        !$request->routeIs('doctor.first-login', 'doctor.first-login.update')
    ) {
        return redirect()->route('doctor.first-login');
    }


        return $next($request);
    }
}
