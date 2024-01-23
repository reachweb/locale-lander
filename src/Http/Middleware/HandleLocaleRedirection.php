<?php 

namespace Reach\LocaleLander\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Intl\Languages;

class HandleLocaleRedirection
{
    public function handle(Request $request, Closure $next)
    {
        if (config('locale-lander.enable') === false) {
            return $next($request);
        }
        
        $browserLanguage = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        ray(Languages::getName($browserLanguage));

        // if (config('locale-lander.enable')) {
        //     $locales = config('statamic.sites')
        //         ->map(function ($site) {
        //             return $site['locale'];
        //         })
        //         ->toArray();

        //     if (! in_array($browserLanguage, $locales)) {
        //         $locale = config('app.locale');

        //         if (isset($_SERVER['HTTP_REFERER'])) {
        //             $referer = parse_url($_SERVER['HTTP_REFERER']);
        //             $refererLocale = explode('/', $referer['path'])[1];

        //             if (in_array($refererLocale, $locales)) {
        //                 $locale = $refererLocale;
        //             }
        //         }

        //         $locale = $request->session()->get('locale', $locale);

        //         $request->session()->put('locale', $locale);

        //         return redirect($locale);
        //     }
        // }

        return $next($request);
    }

}
