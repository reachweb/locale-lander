<?php 

namespace Reach\LocaleLander\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Symfony\Component\Intl\Locale;

class HandleLocaleRedirection
{
    public function handle(Request $request, Closure $next)
    {
        // Skip if disabled
        if (config('locale-lander.enable') === false) {
            return $next($request);
        }

        $browserLocale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        // Skip if we are currently on the correct locale
        if (Site::current()->locale() ===  $browserLocale) {
            return $next($request);
        }
        $path = $request->path() === '/' ? '' : $request->path();
        // Find the page or skip if we are unable  FIX HERE ONLY WORKS FOR DEFAULT
        if (! $page = Entry::findByUri('/'.$path)) {
            return $next($request);
        }

        // Find the entry or skip if we are unable to
        // if (! $entry = Entry::find($page->reference())) {
        //     return $next($request);
        // }


        $locales = collect(config('statamic.sites')['sites'])
            ->map(function ($site) {
                if (isset ($site['lang'])) {
                    return $site['lang'];
                }
                return Locale::getPrimaryLanguage($site['locale']);
            })->values();


        dd(Site::current(), $locales, $browserLocale);


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
