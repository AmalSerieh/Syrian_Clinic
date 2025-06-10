<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale($this->resolveLocale($request));
        return $next($request);
    }

    protected function resolveLocale(Request $request): string
    {
        return $this->getLocaleFromHeader($request)
            ?? $this->getLocaleFromAuthenticatedUser()
            ?? config('app.fallback_locale', 'en');
    }

    protected function getLocaleFromHeader(Request $request): ?string
    {
        $header = $request->header('Accept-Language');
        if (!$header) return null;

        $locales = explode(',', $header);
        $primary = trim(explode(';', $locales[0])[0]);
        $lang = strtolower(substr($primary, 0, 2));
        return in_array($lang, ['ar', 'en']) ? $lang : null;
    }

    protected function getLocaleFromAuthenticatedUser(): ?string
    {
        $user = auth()->user();
        $language = optional($user)->language;
        return in_array($language, ['ar', 'en']) ? $language : null;
    }
}

