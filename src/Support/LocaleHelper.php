<?php

namespace Reach\LocaleLander\Support;

use Statamic\Facades\Data;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Symfony\Component\Intl\Locale;

class LocaleHelper
{
    protected $request;

    protected $browserLocale;

    protected $browserLanguage;

    public function boot($request)
    {
        $this->request = $request;

        $this->setBrowserLocaleFromRequest($request)
            ->setBrowserLanguage();

        return $this;
    }

    public function setBrowserLocaleFromRequest(): self
    {
        $this->browserLocale = locale_accept_from_http($this->request->header('Accept-Language'));

        return $this;
    }

    public function setBrowserLanguage(): self
    {
        $this->browserLanguage = Locale::getPrimaryLanguage($this->browserLocale);

        return $this;
    }

    public function isCurrentLocaleCorrect(): bool
    {
        return $this->siteMatchesLocale(Site::current());
    }

    public function getMatchingSite(): ?\Statamic\Sites\Site
    {
        return Site::all()->first(function ($site) {
            return $this->siteMatchesLocale($site);
        });
    }

    public function siteMatchesLocale($site)
    {
        return $site->locale() === $this->browserLocale || $site->lang() === $this->browserLanguage;
    }

    public function findContentInLocale($site)
    {
        if ($data = Data::findByRequestUrl($this->request->url())) {
            if ($entry = Entry::find($data->id())) {
                if ($content = $entry->in($site->handle())) {
                    if ($this->isSafe($content)) {
                        return $content;
                    }
                }
            }
        }

        return false;
    }

    public function browserLocale()
    {
        return $this->browserLocale;
    }

    public function browserLanguage()
    {
        return $this->browserLanguage;
    }

    public function isSafe($content)
    {
        return $content->published()
            && ! $content->private()
            && $content->url();
    }

    public function hasCookie(): bool
    {
        // Workaround because $request->hasCookie() is not working
        $cookies = collect(request()->cookies->all());

        return $cookies->has('locale_banner_closed');
    }

    public function setCompleted(): void
    {
        session(['locale_lander' => 'completed']);
    }
}
