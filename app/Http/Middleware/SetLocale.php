<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = array_keys(config('app.supported_locales', []));
        $fallbackLocale = config('app.locale', 'es');

        $locale = session('locale');

        if (! $locale) {
            $locale = $request->cookie('locale');
        }

        if (! $locale) {
            $preferred = $request->getPreferredLanguage($supportedLocales);
            $locale = in_array($preferred, $supportedLocales, true) ? $preferred : $fallbackLocale;
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = $fallbackLocale;
        }

        App::setLocale($locale);

        return $next($request);
    }
}
