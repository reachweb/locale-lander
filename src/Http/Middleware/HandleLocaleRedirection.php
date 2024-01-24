<?php

namespace Reach\LocaleLander\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Statamic\Facades\Data;
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

        // Skip if we have already been redirected once at this session
        if (session('locale_lander') === 'redirected') {
            return $next($request);
        }

        $browserLocale = locale_accept_from_http($request->header('Accept-Language'));
        $browserLanguage = Locale::getPrimaryLanguage($browserLocale);

        // Skip if we are currently on the correct locale
        if (Site::current()->locale() === $browserLocale) {
            $this->setRedirected();

            return $next($request);
        }

        // Set the session to redirect if the locale is in the sites config
        $site = Site::all()->first(function ($site) use ($browserLocale, $browserLanguage) {
            return $site->locale() === $browserLocale || $site->lang() === $browserLanguage;
        });

        // Skip if there is no site for the browser locale
        if (! $site) {
            return $next($request);
        }

        if ($data = Data::findByRequestUrl($request->url())) {
            if ($entry = Entry::find($data->id())) {
                if ($redirectTo = $entry->in($site->handle())) {
                    $this->setRedirected();

                    return redirect($redirectTo->url());
                }
            }
        }

        return $next($request);
    }

    public function setRedirected()
    {
        session(['locale_lander' => 'redirected']);
    }
}
