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
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->shouldSkip()) {
            return $next($request);
        }

        $browserLocale = locale_accept_from_http($request->header('Accept-Language'));
        $browserLanguage = Locale::getPrimaryLanguage($browserLocale);

        if ($this->isCurrentLocaleCorrect($browserLocale)) {
            $this->setCompleted();

            return $next($request);
        }

        $site = $this->getMatchingSite($browserLocale, $browserLanguage);
        if (! $site) {
            return $next($request);
        }

        return $this->handleLocaleContent($request, $site) ?: $next($request);
    }

    private function shouldSkip(): bool
    {
        return config('locale-lander.enable') === false || session('locale_lander') === 'completed';
    }

    private function isCurrentLocaleCorrect($browserLocale): bool
    {
        return Site::current()->locale() === $browserLocale;
    }

    private function getMatchingSite($browserLocale, $browserLanguage): ?\Statamic\Sites\Site
    {
        return Site::all()->first(function ($site) use ($browserLocale, $browserLanguage) {
            return $site->locale() === $browserLocale || $site->lang() === $browserLanguage;
        });
    }

    private function handleLocaleContent(Request $request, $site): void
    {
        if ($data = Data::findByRequestUrl($request->url())) {
            if ($entry = Entry::find($data->id())) {
                if ($content = $entry->in($site->handle())) {
                    if (config('locale-lander.type') === 'redirect') {
                        $this->setCompleted();

                        return redirect($content->url());
                    } elseif (config('locale-lander.type') === 'popup') {
                        $this->setPopup();
                    }
                }
            }
        }
    }

    public function setCompleted(): void
    {
        session(['locale_lander' => 'completed']);
    }

    public function setPopup(): void
    {
        session(['locale_lander' => 'popup']);
    }
}
