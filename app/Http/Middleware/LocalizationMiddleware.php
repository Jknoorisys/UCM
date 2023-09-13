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
        // $request->validate([
        //     'X-localization' => ['required', Rule::in(['en','hi','ur','bn','ar','in','ms','tr','fa','fr','de','es']),],
        // ]);

        $validator = Validator::make($request->all(), [
            'X-localization' => ['required', Rule::in(['en','hi','ur','bn','ar','in','ms','tr','fa','fr','de','es']),],
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 'failed',
                'message'   => __('msg.validation'),
                'errors'    => $validator->errors()
            ],400);
        }

        // Check header request and determine localizaton
        $local = ($request->hasHeader('X-localization')) ? (strlen($request->header('X-localization'))>0?$request->header('X-localization'): 'en'): 'en';

        // set laravel localization
        App::setLocale($local);
        // continue request
        return $next($request);
    }
}
