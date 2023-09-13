<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('X-localization')) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.localization.required'),
            ],400);
        }

        // Get the 'X-localization' header value
        $local = $request->header('X-localization');

        // Validate the localization value
        $allowedLocales = ['en', 'fr', 'es']; // Add the allowed locales here
        if (!in_array($local, $allowedLocales)) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.localization.invalid'),
            ],400);
        }

        // Check header request and determine localizaton
        $local = ($request->hasHeader('X-localization')) ? (strlen($request->header('X-localization')) > 0 ? $request->header('X-localization'): 'en'): 'en';

        // set laravel localization
        App::setLocale($local);
        // continue request
        return $next($request);
    }
}
