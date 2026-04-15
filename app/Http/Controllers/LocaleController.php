<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        $supportedLocales = array_keys(config('app.supported_locales', []));

        abort_unless(in_array($locale, $supportedLocales, true), 404);

        session(['locale' => $locale]);

        $redirectTo = $request->query('redirect', url()->previous());

        return redirect()->to($redirectTo)->cookie('locale', $locale, 60 * 24 * 365);
    }
}
