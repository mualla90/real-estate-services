<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'ar'];

        $locale = $request->query('lang');

        if (! $locale) {
            $acceptLanguage = $request->header('Accept-Language');

            if ($acceptLanguage) {
                $locale = substr($acceptLanguage, 0, 2);
            }
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
