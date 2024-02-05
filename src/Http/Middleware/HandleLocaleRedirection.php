<?php

namespace Reach\LocaleLander\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Reach\LocaleLander\Facades\LocaleHelper;

class HandleLocaleRedirection
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->shouldSkip()) {
            return $next($request);
        }

        $helper = LocaleHelper::boot($request);

        if ($helper->isCurrentLocaleCorrect()) {
            $helper->setCompleted();

            return $next($request);
        }

        $site = $helper->getMatchingSite();
        if (! $site) {
            return $next($request);
        }

        $content = $helper->findContentInLocale($request, $site);
        if ($content) {
            $helper->setCompleted();

            return redirect($content->url());
        }

        return $next($request);
    }

    private function shouldSkip(): bool
    {
        return config('locale-lander.enable_redirection') === false || session('locale_lander') === 'completed';
    }
}
