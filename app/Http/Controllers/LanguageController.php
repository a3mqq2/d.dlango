<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Available locales
     */
    protected array $availableLocales = ['ar', 'en'];

    /**
     * Switch the application language
     */
    public function switch(string $locale)
    {
        // Validate locale
        if (!in_array($locale, $this->availableLocales)) {
            $locale = 'ar';
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Redirect back with cookie
        return redirect()->back()->withCookie(cookie()->forever('locale', $locale));
    }
}
