<?php

namespace Reach\LocaleLander\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Reach\LocaleLander\Facades\LocaleHelper;
use Statamic\Facades\Data;
use Statamic\Facades\Entry;

class HandleLocaleRedirection
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->shouldSkip()) {
            return $next($request);
        }

        $helper = LocaleHelper::boot($request);

        if ($helper->isCurrentLocaleCorrect()) {
            $this->setCompleted();

            return $next($request);
        }

        $site = $helper->getMatchingSite();
        if (! $site) {
            return $next($request);
        }

        return $this->handleLocaleContent($request, $site) ?: $next($request);
    }

    private function shouldSkip(): bool
    {
        return config('locale-lander.enable_redirection') === false || session('locale_lander') === 'completed';
    }

    private function handleLocaleContent(Request $request, $site)
    {
        if ($data = Data::findByRequestUrl($request->url())) {
            if ($entry = Entry::find($data->id())) {
                if ($content = $entry->in($site->handle())) {
                    if (config('locale-lander.type') === 'redirect') {
                        $this->setCompleted();

                        return redirect($content->url());
                    }
                }
            }
        }
    }

    public function setCompleted(): void
    {
        session(['locale_lander' => 'completed']);
    }
}
