<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Available locales
     */
    protected array $availableLocales = ['ar', 'en'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is in session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // Check if locale is in cookie
        elseif ($request->hasCookie('locale')) {
            $locale = $request->cookie('locale');
        }
        // Default to Arabic
        else {
            $locale = config('app.locale', 'ar');
        }

        // Validate locale
        if (!in_array($locale, $this->availableLocales)) {
            $locale = 'ar';
        }

        // Set the application locale
        App::setLocale($locale);

        // Store in session for persistence
        Session::put('locale', $locale);

        return $next($request);
    }
}
