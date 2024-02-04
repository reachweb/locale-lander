<?php

namespace Reach\LocaleLander\Support;

use Statamic\Facades\Site;
use Symfony\Component\Intl\Locale;

class LocaleHelper
{
    protected $browserLocale;

    protected $browserLanguage;

    public function boot($request)
    {
        $this->setBrowserLocaleFromRequest($request)
            ->setBrowserLanguage();

        return $this;
    }

    public function setBrowserLocaleFromRequest($request): self
    {
        $this->browserLocale = locale_accept_from_http($request->header('Accept-Language'));

        return $this;
    }

    public function setBrowserLanguage(): self
    {
        $this->browserLanguage = Locale::getPrimaryLanguage($this->browserLocale);

        return $this;
    }

    public function isCurrentLocaleCorrect(): bool
    {
        return Site::current()->locale() === $this->browserLocale;
    }

    public function getMatchingSite(): ?\Statamic\Sites\Site
    {
        return Site::all()->first(function ($site) {
            return $site->locale() === $this->browserLocale || $site->lang() === $this->browserLanguage;
        });
    }

    public function browserLocale()
    {
        return $this->browserLocale;
    }

    public function browserLanguage()
    {
        return $this->browserLanguage;
    }
}
